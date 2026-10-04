<?php

namespace App\Livewire\Admin\Products;

use App\Models\Category;
use App\Models\Product;
use App\Services\ImageUploadService;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithFileUploads;

class Edit extends Component
{
    use WithFileUploads;

    public $product;

    public $productId;

    public $categories = [];

    public $name;

    public $category_id;

    public array $delivery_countries = [];

    public int $processing_min_days = 0;

    public int $processing_max_days = 0;

    public $description;

    public $product_type = Product::TYPE_PHYSICAL;

    public $price;

    public $compare_price;

    public $quantity;

    public $sku;

    public $existingImages = [];

    public $newImages = [];

    public $digital_file;

    public $download_link;

    public $course_access_url;

    public $access_instructions;

    public $is_featured = false;

    public $is_active = true;

    protected function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'delivery_countries' => ['array'],
            'delivery_countries.*' => [\Illuminate\Validation\Rule::in(array_keys(config('countries')))],
            'processing_min_days' => 'required|integer|min:0|max:365',
            'processing_max_days' => 'required|integer|gte:processing_min_days|max:365',
            'category_id' => 'required|exists:categories,id',
            'description' => 'nullable|string',
            'product_type' => 'required|in:physical,digital,course',
            'price' => 'required|numeric|min:0',
            'compare_price' => 'nullable|numeric|min:0',
            'quantity' => 'nullable|integer|min:0',
            'sku' => 'nullable|string|max:100|unique:products,sku,'.$this->productId,
            'newImages.*' => 'image|mimes:jpeg,png,jpg,gif,webp,heic,heif|max:10240',
            'digital_file' => 'nullable|file|max:51200|mimes:pdf,zip,rar,doc,docx,ppt,pptx,xls,xlsx,mp4,mp3',
            'download_link' => 'nullable|url|max:2048',
            'course_access_url' => 'nullable|url|max:2048',
            'access_instructions' => 'nullable|string|max:5000',
            'is_featured' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    protected $messages = [
        'name.required' => 'Product name is required.',
        'category_id.required' => 'Please select a category.',
        'price.required' => 'Regular price is required.',
        'price.numeric' => 'Price must be a valid number.',
        'quantity.integer' => 'Quantity must be a whole number.',
        'newImages.*.image' => 'Each file must be an image.',
        'newImages.*.mimes' => 'Images must be JPEG, PNG, GIF, WEBP, or HEIC format.',
        'newImages.*.max' => 'Each image must not exceed 10MB.',
    ];

    public function mount($product)
    {
        $user = auth()->user()?->fresh();

        abort_unless($user?->canManageProducts(), 403);

        abort_unless($product->canBeManagedBy($user), 403);

        $this->product = $product;
        $this->productId = $product->id;

        // Load product data
        $this->name = $product->name;
        $this->category_id = $product->category_id;
        $this->delivery_countries = $product->delivery_countries ?? [];
        $this->processing_min_days = $product->processing_min_days ?? 0;
        $this->processing_max_days = $product->processing_max_days ?? 0;
        $this->description = $product->description;
        $this->product_type = $product->product_type ?: Product::TYPE_PHYSICAL;
        $this->price = $product->price;
        $this->compare_price = $product->compare_price;
        $this->quantity = $product->quantity;
        $this->sku = $product->sku;
        $this->existingImages = $product->images ?? [];
        $this->download_link = $product->download_link;
        $this->course_access_url = $product->course_access_url;
        $this->access_instructions = $product->access_instructions;
        $this->is_featured = (bool) $product->is_featured;
        $this->is_active = (bool) $product->is_active;
        $this->categories = Category::where('is_active', true)
            ->orderBy('name')
            ->get();
    }

    public function regenerateSku()
    {
        $this->sku = 'PROD-'.strtoupper(Str::random(8));
        session()->flash('success', 'New SKU generated: '.$this->sku);
    }

    public function updatedProductType($value): void
    {
        if ($value === Product::TYPE_PHYSICAL) {
            $this->digital_file = null;
            $this->download_link = null;
            $this->course_access_url = null;
            $this->access_instructions = null;
        }

        if ($value === Product::TYPE_DIGITAL) {
            $this->course_access_url = null;
            $this->quantity = null;
        }

        if ($value === Product::TYPE_COURSE) {
            $this->digital_file = null;
            $this->download_link = null;
            $this->quantity = null;
        }

        $this->resetValidation([
            'quantity',
            'digital_file',
            'download_link',
            'course_access_url',
            'access_instructions',
        ]);
    }

    public function updatedNewImages(): void
    {
        $this->validateOnly('newImages.*');
    }

    public function updatedDigitalFile(): void
    {
        $this->validateOnly('digital_file');
    }

    public function update()
    {
        abort_unless($this->product->canBeManagedBy(auth()->user()), 403);
        $user = auth()->user()?->fresh();

        abort_unless($user?->canManageProducts(), 403);

        $this->prepareFieldsForSelectedType();

        $this->validate();
        $this->validateProductTypeConfiguration();
        $this->validatePricingConfiguration();

        // Combine existing and new images
        $allImages = $this->existingImages;

        // Handle new image uploads with compression
        if (! empty($this->newImages)) {
            $imageService = new ImageUploadService;
            $uploadedPaths = $imageService->uploadAndCompressMultiple($this->newImages, 'products');

            if (! empty($uploadedPaths)) {
                $allImages = array_merge($allImages, $uploadedPaths);
            } else {
                session()->flash('error', 'Failed to upload and compress one or more images.');

                return;
            }
        }

        $digitalFilePath = $this->product->download_file_path;
        if ($this->digital_file) {
            if ($digitalFilePath) {
                Storage::disk('public')->delete($digitalFilePath);
            }

            $digitalFilePath = $this->digital_file->store('digital-products', 'public');
        }

        if ($this->product_type !== Product::TYPE_DIGITAL && $digitalFilePath) {
            Storage::disk('public')->delete($digitalFilePath);
            $digitalFilePath = null;
        }

        $slug = $this->generateUniqueSlug($this->name);

        $this->product->update([
            'delivery_countries' => $this->delivery_countries,
            'processing_min_days' => $this->processing_min_days,
            'processing_max_days' => $this->processing_max_days,
            'category_id' => $this->category_id,
            'name' => $this->name,
            'slug' => $slug,
            'description' => $this->description,
            'product_type' => $this->product_type,
            'price' => $this->price,
            'compare_price' => $this->compare_price,
            'quantity' => $this->product_type === Product::TYPE_PHYSICAL ? (int) $this->quantity : 0,
            'sku' => $this->sku ?? 'PROD-'.strtoupper(Str::random(8)),
            'images' => ! empty($allImages) ? $allImages : null,
            'download_file_path' => $this->product_type === Product::TYPE_DIGITAL ? $digitalFilePath : null,
            'download_link' => $this->product_type === Product::TYPE_DIGITAL ? $this->download_link : null,
            'course_access_url' => $this->product_type === Product::TYPE_COURSE ? $this->course_access_url : null,
            'access_instructions' => $this->access_instructions,
            'is_featured' => $this->is_featured,
            'is_active' => $this->is_active,
        ]);

        session()->flash('message', 'Product updated successfully.');

        return redirect()->route('admin.products.index');
    }

    public function removeExistingImage($index)
    {
        // Delete image from storage
        $imagePath = $this->existingImages[$index];
        Storage::disk('public')->delete($imagePath);

        // Remove from array
        unset($this->existingImages[$index]);
        $this->existingImages = array_values($this->existingImages);
    }

    public function removeNewImage($index)
    {
        unset($this->newImages[$index]);
        $this->newImages = array_values($this->newImages);
    }

    private function validateProductTypeConfiguration(): void
    {
        if ($this->product_type === Product::TYPE_PHYSICAL && (! is_numeric($this->quantity) || (int) $this->quantity < 0)) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'quantity' => 'Quantity is required for physical products.',
            ]);
        }

        if ($this->product_type === Product::TYPE_DIGITAL && ! $this->digital_file && blank($this->download_link) && blank($this->product->download_file_path)) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'digital_file' => 'Upload a file or add a download link for digital products.',
                'download_link' => 'Upload a file or add a download link for digital products.',
            ]);
        }

        if ($this->product_type === Product::TYPE_COURSE && blank($this->course_access_url) && blank($this->access_instructions)) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'course_access_url' => 'Add a course access URL or instructions for course products.',
                'access_instructions' => 'Add a course access URL or instructions for course products.',
            ]);
        }
    }

    private function validatePricingConfiguration(): void
    {
        if (filled($this->compare_price) && (float) $this->compare_price < (float) $this->price) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'compare_price' => 'Compare at price should be greater than or equal to the regular price.',
            ]);
        }
    }

    private function prepareFieldsForSelectedType(): void
    {
        if ($this->product_type !== Product::TYPE_PHYSICAL) {
            $this->quantity = null;
        }

        if ($this->product_type !== Product::TYPE_DIGITAL) {
            $this->digital_file = null;
            $this->download_link = null;
        }

        if ($this->product_type !== Product::TYPE_COURSE) {
            $this->course_access_url = null;
        }

        if ($this->product_type === Product::TYPE_PHYSICAL) {
            $this->access_instructions = null;
        }
    }

    private function generateUniqueSlug(string $name): string
    {
        $baseSlug = Str::slug($name);
        $slug = $baseSlug;
        $counter = 1;

        while (
            Product::where('slug', $slug)
                ->whereKeyNot($this->productId)
                ->exists()
        ) {
            $slug = $baseSlug.'-'.$counter;
            $counter++;
        }

        return $slug;
    }

    public function render()
    {
        return view('livewire.admin.products.edit', [
            'categories' => $this->categories,
        ]);
    }
}
