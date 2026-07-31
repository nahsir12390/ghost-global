<?php

namespace App\Livewire\Admin\Products;

use Livewire\Component;
use App\Models\Product;
use App\Models\Category;
use App\Models\User;
use App\Services\ImageUploadService;
use Illuminate\Support\Str;
use Livewire\WithFileUploads;

class Create extends Component
{
    use WithFileUploads;

    public $categories = [];
    public $vendors = [];
    public $name;
    public $vendor_id;
    public $category_id;
    public $description;
    public $product_type = Product::TYPE_PHYSICAL;
    public $price;
    public $compare_price;
    public $quantity;
    public $sku;
    public $images = [];
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
            'vendor_id' => 'nullable|exists:users,id',
            'category_id' => 'required|exists:categories,id',
            'description' => 'nullable|string',
            'product_type' => 'required|in:physical,digital,course',
            'price' => 'required|numeric|min:0',
            'compare_price' => 'nullable|numeric|min:0',
            'quantity' => 'nullable|integer|min:0',
            'sku' => 'nullable|string|max:100|unique:products,sku',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif,webp,heic,heif|max:10240',
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
        'vendor_id.exists' => 'Please choose a valid vendor account.',
        'category_id.required' => 'Please select a category.',
        'price.required' => 'Regular price is required.',
        'price.numeric' => 'Price must be a valid number.',
        'quantity.integer' => 'Quantity must be a whole number.',
        'images.*.image' => 'Each file must be an image.',
        'images.*.mimes' => 'Images must be JPEG, PNG, GIF, WEBP, or HEIC format.',
        'images.*.max' => 'Each image must not exceed 10MB.',
    ];

    public function mount()
    {
        $user = auth()->user()?->fresh();

        if ($user?->isVendor() && !$user->isVendorVerified()) {
            abort(403, 'Your vendor account is not verified yet.');
        }

        $this->categories = Category::where('is_active', true)
            ->orderBy('name')
            ->get();

        $this->vendors = $this->availableVendors();

        if ($user?->isVendor()) {
            $this->vendor_id = $user->id;
        }
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

    public function updatedImages(): void
    {
        $this->validateOnly('images.*');
    }

    public function updatedDigitalFile(): void
    {
        $this->validateOnly('digital_file');
    }

    public function save()
    {
        $user = auth()->user()?->fresh();

        if ($user?->isVendor() && !$user->isVendorVerified()) {
            abort(403, 'Your vendor account is not verified yet.');
        }

        $this->prepareFieldsForSelectedType();

        $validated = $this->validate();
        $assignedVendorId = $this->resolveVendorId($user);

        $this->validateProductTypeConfiguration();
        $this->validatePricingConfiguration();

        $slug = $this->generateUniqueSlug($this->name);

        $imagePaths = [];
        if (!empty($this->images) && is_array($this->images)) {
            $imageService = new ImageUploadService();
            $uploadedPaths = $imageService->uploadAndCompressMultiple($this->images, 'products');
            
            if (empty($uploadedPaths)) {
                session()->flash('error', 'Failed to upload and compress images.');
                return;
            }
            
            $imagePaths = $uploadedPaths;
        }

        $sku = $this->sku;
        if (empty($sku)) {
            $sku = 'PROD-' . strtoupper(Str::random(8));
        } else {
            $existingSku = Product::where('sku', $sku)->first();
            if ($existingSku) {
                $this->addError('sku', 'This SKU already exists. Please use a different SKU.');
                return;
            }
        }

        $digitalFilePath = $this->digital_file
            ? $this->digital_file->store('digital-products', 'public')
            : null;

        try {
            $product = Product::create([
                'vendor_id' => $assignedVendorId,
                'category_id' => $this->category_id,
                'name' => $this->name,
                'slug' => $slug,
                'description' => $this->description,
                'product_type' => $this->product_type,
                'price' => $this->price,
                'compare_price' => $this->compare_price,
                'quantity' => $this->product_type === Product::TYPE_PHYSICAL ? (int) $this->quantity : 0,
                'sku' => $sku,
                'images' => !empty($imagePaths) ? json_encode($imagePaths) : null,
                'download_file_path' => $digitalFilePath,
                'download_link' => $this->product_type === Product::TYPE_DIGITAL ? $this->download_link : null,
                'course_access_url' => $this->product_type === Product::TYPE_COURSE ? $this->course_access_url : null,
                'access_instructions' => $this->access_instructions,
                'is_featured' => $this->is_featured,
                'is_active' => $this->is_active,
            ]);

            session()->flash('success', 'Product created successfully.');
            
            return redirect()->route('admin.products.index');
        } catch (\Exception $e) {
            \Log::error('Failed to create product', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            session()->flash('error', 'Failed to create product. Please check the form and try again.');
            return;
        }
    }

    public function removeImage($index)
    {
        if (isset($this->images[$index])) {
            // If it's a temporary uploaded file, delete it
            if (is_object($this->images[$index]) && method_exists($this->images[$index], 'delete')) {
                $this->images[$index]->delete();
            }
            unset($this->images[$index]);
            $this->images = array_values($this->images);
        }
    }

    private function validateProductTypeConfiguration(): void
    {
        if ($this->product_type === Product::TYPE_PHYSICAL && (! is_numeric($this->quantity) || (int) $this->quantity < 0)) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'quantity' => 'Quantity is required for physical products.',
            ]);
        }

        if ($this->product_type === Product::TYPE_DIGITAL && ! $this->digital_file && blank($this->download_link)) {
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

    private function resolveVendorId(?User $user): int
    {
        if (! $user) {
            abort(403);
        }

        if ($user->isVendor()) {
            return (int) $user->id;
        }

        if (! $user->canManageProducts()) {
            abort(403);
        }

        $vendorId = (int) $this->vendor_id;

        if ($vendorId <= 0) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'vendor_id' => 'Select the vendor store this product belongs to.',
            ]);
        }

        $vendorExists = User::query()
            ->whereKey($vendorId)
            ->where('role', 'vendor')
            ->where(function ($query) {
                $query->where('verification_status', 'approved')
                    ->orWhereNotNull('verified_at');
            })
            ->exists();

        if (! $vendorExists) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'vendor_id' => 'Choose an approved vendor so this product appears in the correct store.',
            ]);
        }

        return $vendorId;
    }

    private function availableVendors()
    {
        return User::query()
            ->where('role', 'vendor')
            ->where(function ($query) {
                $query->where('verification_status', 'approved')
                    ->orWhereNotNull('verified_at');
            })
            ->orderByRaw('COALESCE(store_name, name)')
            ->get(['id', 'name', 'store_name', 'email', 'vendor_is_active']);
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

        while (Product::where('slug', $slug)->exists()) {
            $slug = $baseSlug.'-'.$counter;
            $counter++;
        }

        return $slug;
    }

    public function render()
    {
        return view('livewire.admin.products.create', [
            'categories' => $this->categories,
            'vendors' => $this->vendors,
        ]);
    }
}
