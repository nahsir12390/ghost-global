<?php

namespace App\Livewire\Admin\Products;

use App\Models\Category;
use App\Models\Product;
use App\Services\ImageUploadService;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Livewire\Component;
use Livewire\WithFileUploads;

class Create extends Component
{
    use WithFileUploads;

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
            'sku' => 'nullable|string|max:100|unique:products,sku',
            'images' => 'array|max:10',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif,webp,heic,heif|max:10240',
            'digital_file' => 'nullable|file|max:51200|mimes:pdf,zip,rar,doc,docx,ppt,pptx,xls,xlsx,mp4,mp3',
            'download_link' => 'nullable|url:http,https|max:2048',
            'course_access_url' => 'nullable|url:http,https|max:2048',
            'access_instructions' => 'nullable|string|max:5000',
            'is_featured' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    protected $messages = [
        'name.required' => 'Product name is required.',
        'category_id.required' => 'Please select a category.',
        'price.required' => 'Regular price is required.',
        'compare_price.gte' => 'Compare price must be greater than or equal to the regular price.',
        'quantity.required' => 'Quantity is required for physical products.',
        'images.max' => 'A product can have at most 10 images.',
        'images.*.image' => 'Each file must be an image.',
        'images.*.mimes' => 'Images must be JPEG, PNG, GIF, WEBP, or HEIC format.',
        'images.*.max' => 'Each image must not exceed 10MB.',
    ];

    public function mount()
    {
        abort_unless(auth()->user()?->fresh()?->canManageProducts(), 403);
        $this->categories = Category::where('is_active', true)->orderBy('name')->get();
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

    public function updatedImages(): void { $this->validateOnly('images.*'); }
    public function updatedDigitalFile(): void { $this->validateOnly('digital_file'); }

    public function save()
    {
        abort_unless(auth()->user()?->fresh()?->canManageProducts(), 403);
        $this->prepareFieldsForSelectedType();
        $this->validate();
        $this->validateProductTypeConfiguration();

        $imagePaths = [];
        if ($this->images) {
            $imagePaths = (new ImageUploadService)->uploadAndCompressMultiple($this->images, 'products');
            if (count($imagePaths) !== count($this->images)) {
                session()->flash('error', 'One or more product images could not be processed. Please try again.');
                return;
            }
        }

        $digitalFilePath = $this->digital_file ? $this->digital_file->store('digital-products', 'public') : null;

        Product::create([
            'delivery_countries' => $this->product_type === Product::TYPE_PHYSICAL ? $this->delivery_countries : [],
            'processing_min_days' => $this->product_type === Product::TYPE_PHYSICAL ? $this->processing_min_days : 0,
            'processing_max_days' => $this->product_type === Product::TYPE_PHYSICAL ? $this->processing_max_days : 0,
            'category_id' => $this->category_id,
            'name' => $this->name,
            'slug' => Product::uniqueSlug($this->name),
            'description' => $this->description,
            'product_type' => $this->product_type,
            'price' => $this->price,
            'compare_price' => $this->compare_price,
            'quantity' => $this->product_type === Product::TYPE_PHYSICAL ? (int) $this->quantity : 0,
            'sku' => filled($this->sku) ? $this->sku : Product::uniqueSku(),
            'images' => $imagePaths,
            'download_file_path' => $this->product_type === Product::TYPE_DIGITAL ? $digitalFilePath : null,
            'download_link' => $this->product_type === Product::TYPE_DIGITAL ? $this->download_link : null,
            'course_access_url' => $this->product_type === Product::TYPE_COURSE ? $this->course_access_url : null,
            'access_instructions' => $this->product_type === Product::TYPE_COURSE ? $this->access_instructions : null,
            'is_featured' => $this->is_featured,
            'is_active' => $this->is_active,
        ]);

        session()->flash('success', 'Product created successfully.');
        return redirect()->route('admin.products.index');
    }

    public function removeImage($index)
    {
        if (isset($this->images[$index])) {
            if (is_object($this->images[$index]) && method_exists($this->images[$index], 'delete')) $this->images[$index]->delete();
            unset($this->images[$index]);
            $this->images = array_values($this->images);
        }
    }

    private function validateProductTypeConfiguration(): void
    {
        if ($this->product_type === Product::TYPE_DIGITAL && ! $this->digital_file && blank($this->download_link)) {
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
        return view('livewire.admin.products.create');
    }
}
