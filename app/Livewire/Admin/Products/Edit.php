<?php

namespace App\Livewire\Admin\Products;

use App\Models\Category;
use App\Models\Product;
use App\Services\ImageUploadService;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
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
            'delivery_countries.*' => [Rule::in(array_keys(config('countries')))],
            'processing_min_days' => 'required|integer|min:0|max:365',
            'processing_max_days' => 'required|integer|gte:processing_min_days|max:365',
            'category_id' => 'required|exists:categories,id',
            'description' => 'nullable|string',
            'product_type' => 'required|in:physical,digital,course',
            'price' => 'required|numeric|min:0|max:999999999',
            'compare_price' => 'nullable|numeric|gte:price|max:999999999',
            'quantity' => $this->product_type === Product::TYPE_PHYSICAL ? 'required|integer|min:0' : 'nullable|integer|min:0',
            'sku' => 'nullable|string|max:100|unique:products,sku,'.$this->productId,
            'newImages' => 'array|max:10',
            'newImages.*' => 'image|mimes:jpeg,png,jpg,gif,webp,heic,heif|max:10240',
            'digital_file' => 'nullable|file|max:51200|mimes:pdf,zip,rar,doc,docx,ppt,pptx,xls,xlsx,mp4,mp3',
            'download_link' => 'nullable|url:http,https|max:2048',
            'course_access_url' => 'nullable|url:http,https|max:2048',
            'access_instructions' => 'nullable|string|max:5000',
            'is_featured' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function mount($product)
    {
        $user = auth()->user()?->fresh();
        abort_unless($user?->canManageProducts() && $product->canBeManagedBy($user), 403);

        $this->product = $product;
        $this->productId = $product->id;
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
        $this->categories = Category::where('is_active', true)->orderBy('name')->get();
    }

    public function regenerateSku()
    {
        $this->sku = Product::uniqueSku();
        session()->flash('success', 'New SKU generated: '.$this->sku);
    }

    public function updatedProductType($value): void
    {
        if ($value === Product::TYPE_PHYSICAL) {
            $this->digital_file = $this->download_link = $this->course_access_url = $this->access_instructions = null;
        } elseif ($value === Product::TYPE_DIGITAL) {
            $this->course_access_url = $this->access_instructions = null;
            $this->quantity = null;
            $this->delivery_countries = [];
            $this->processing_min_days = $this->processing_max_days = 0;
        } elseif ($value === Product::TYPE_COURSE) {
            $this->digital_file = $this->download_link = null;
            $this->quantity = null;
            $this->delivery_countries = [];
            $this->processing_min_days = $this->processing_max_days = 0;
        }
        $this->resetValidation();
    }

    public function updatedNewImages(): void { $this->validateOnly('newImages.*'); }
    public function updatedDigitalFile(): void { $this->validateOnly('digital_file'); }

    public function update()
    {
        $user = auth()->user()?->fresh();
        abort_unless($user?->canManageProducts() && $this->product->canBeManagedBy($user), 403);

        $this->prepareFieldsForSelectedType();
        $this->validate();
        $this->validateProductTypeConfiguration();

        $allImages = $this->existingImages;
        if (! empty($this->newImages)) {
            if (count($allImages) + count($this->newImages) > 10) {
                throw ValidationException::withMessages(['newImages' => 'A product can have at most 10 images.']);
            }
            $uploadedPaths = (new ImageUploadService)->uploadAndCompressMultiple($this->newImages, 'products');
            if (count($uploadedPaths) !== count($this->newImages)) {
                session()->flash('error', 'One or more product images could not be processed. Please try again.');
                return;
            }
            $allImages = array_merge($allImages, $uploadedPaths);
        }

        $oldDigitalPath = $this->product->download_file_path;
        $digitalFilePath = $oldDigitalPath;
        if ($this->product_type === Product::TYPE_DIGITAL && $this->digital_file) {
            $digitalFilePath = $this->digital_file->store('digital-products', 'public');
        }

        $this->product->update([
            'delivery_countries' => $this->product_type === Product::TYPE_PHYSICAL ? $this->delivery_countries : [],
            'processing_min_days' => $this->product_type === Product::TYPE_PHYSICAL ? $this->processing_min_days : 0,
            'processing_max_days' => $this->product_type === Product::TYPE_PHYSICAL ? $this->processing_max_days : 0,
            'category_id' => $this->category_id,
            'name' => $this->name,
            'slug' => Product::uniqueSlug($this->name, $this->productId),
            'description' => $this->description,
            'product_type' => $this->product_type,
            'price' => $this->price,
            'compare_price' => $this->compare_price,
            'quantity' => $this->product_type === Product::TYPE_PHYSICAL ? (int) $this->quantity : 0,
            'sku' => filled($this->sku) ? $this->sku : Product::uniqueSku(),
            'images' => $allImages,
            'download_file_path' => $this->product_type === Product::TYPE_DIGITAL ? $digitalFilePath : null,
            'download_link' => $this->product_type === Product::TYPE_DIGITAL ? $this->download_link : null,
            'course_access_url' => $this->product_type === Product::TYPE_COURSE ? $this->course_access_url : null,
            'access_instructions' => $this->product_type === Product::TYPE_COURSE ? $this->access_instructions : null,
            'is_featured' => $this->is_featured,
            'is_active' => $this->is_active,
        ]);

        if ($oldDigitalPath && ($this->product_type !== Product::TYPE_DIGITAL || ($this->digital_file && $oldDigitalPath !== $digitalFilePath))) {
            Storage::disk('public')->delete($oldDigitalPath);
        }

        session()->flash('message', 'Product updated successfully.');
        return redirect()->route('admin.products.index');
    }

    public function removeExistingImage($index)
    {
        if (! isset($this->existingImages[$index])) return;
        Storage::disk('public')->delete($this->existingImages[$index]);
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
        if ($this->product_type === Product::TYPE_DIGITAL && ! $this->digital_file && blank($this->download_link) && blank($this->product->download_file_path)) {
            throw ValidationException::withMessages(['digital_file' => 'Upload a file or add a download link for digital products.']);
        }
        if ($this->product_type === Product::TYPE_COURSE && blank($this->course_access_url) && blank($this->access_instructions)) {
            throw ValidationException::withMessages(['course_access_url' => 'Add a course access URL or instructions for course products.']);
        }
    }

    private function prepareFieldsForSelectedType(): void
    {
        if ($this->product_type !== Product::TYPE_PHYSICAL) {
            $this->quantity = null;
            $this->delivery_countries = [];
            $this->processing_min_days = $this->processing_max_days = 0;
        }
        if ($this->product_type !== Product::TYPE_DIGITAL) {
            $this->digital_file = null;
            $this->download_link = null;
        }
        if ($this->product_type !== Product::TYPE_COURSE) {
            $this->course_access_url = null;
            $this->access_instructions = null;
        }
    }

    public function render()
    {
        return view('livewire.admin.products.edit', ['categories' => $this->categories]);
    }
}
