<?php

namespace App\Livewire\Admin\Products;

use App\Models\Category;
use App\Models\Product;
use App\Services\ImageUploadService;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public $search = '';

    public $categoryFilter = '';

    public $statusFilter = '';

    public $sortField = 'created_at';

    public $sortDirection = 'desc';

    public $selectedProducts = [];

    public $selectAll = false;

    public $categories = [];

    protected $queryString = [
        'search' => ['except' => ''],
        'categoryFilter' => ['except' => ''],
        'statusFilter' => ['except' => ''],
    ];

    protected $listeners = ['productUpdated' => '$refresh'];

    public function mount(): void
    {
        $this->categories = Category::where('is_active', true)->orderBy('name')->get();
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingCategoryFilter()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function updatedSelectAll()
    {
        if ($this->selectAll) {
            $this->selectedProducts = $this->getQuery()->pluck('id')->toArray();
        } else {
            $this->selectedProducts = [];
        }
    }

    public function updatedSelectedProducts()
    {
        // Update selectAll checkbox state based on individual selections
        $allProductsCount = $this->getQuery()->count();
        $this->selectAll = count($this->selectedProducts) === $allProductsCount && $allProductsCount > 0;
    }

    public function toggleProductStatus($productId)
    {
        try {
            $product = Product::forManager(auth()->user())->find($productId);
            if ($product) {
                $product->update(['is_active' => ! $product->is_active]);
                $this->dispatch('productUpdated');
                session()->flash('success', 'Product status updated successfully.');
            }
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to update product status: '.$e->getMessage());
        }
    }

    public function toggleFeatured($productId)
    {
        try {
            $product = Product::forManager(auth()->user())->find($productId);
            if ($product) {
                $product->update(['is_featured' => ! $product->is_featured]);
                $this->dispatch('productUpdated');
                session()->flash('success', 'Product featured status updated successfully.');
            }
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to update featured status: '.$e->getMessage());
        }
    }

    public function deleteProduct($productId)
    {
        try {
            $product = Product::forManager(auth()->user())->find($productId);
            if ($product) {
                // Delete images from storage
                if ($product->images) {
                    $imageService = new ImageUploadService;
                    $images = is_string($product->images) ? json_decode($product->images, true) : $product->images;
                    if (is_array($images)) {
                        $imageService->deleteImages($images);
                    }
                }
                $product->delete();
                $this->dispatch('productUpdated');
                session()->flash('success', 'Product deleted successfully.');
            }
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to delete product: '.$e->getMessage());
        }
    }

    public function deleteSelectedProducts()
    {
        try {
            if (empty($this->selectedProducts)) {
                session()->flash('error', 'No products selected.');

                return;
            }

            $products = Product::forManager(auth()->user())->whereIn('id', $this->selectedProducts)->get();
            $imageService = new ImageUploadService;

            foreach ($products as $product) {
                // Delete images from storage
                if ($product->images) {
                    $images = is_string($product->images) ? json_decode($product->images, true) : $product->images;
                    if (is_array($images)) {
                        $imageService->deleteImages($images);
                    }
                }
                $product->delete();
            }

            $this->selectedProducts = [];
            $this->selectAll = false;
            $this->dispatch('productUpdated');
            session()->flash('success', count($products).' product(s) deleted successfully.');
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to delete products: '.$e->getMessage());
        }
    }

    public function confirmDelete($productId = null)
    {
        if ($productId) {
            // Single product delete
            $this->deleteProduct($productId);
        } else {
            // Bulk delete
            $this->deleteSelectedProducts();
        }
    }

    public function getQuery()
    {
        return Product::query()
            ->forManager(auth()->user())
            ->with(['category'])
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%'.$this->search.'%')
                        ->orWhere('sku', 'like', '%'.$this->search.'%')
                        ->orWhere('description', 'like', '%'.$this->search.'%');
                });
            })
            ->when($this->categoryFilter, function ($query) {
                $query->where('category_id', $this->categoryFilter);
            })
            ->when($this->statusFilter !== '', function ($query) {
                $query->where('is_active', $this->statusFilter);
            })
            ->orderBy($this->sortField, $this->sortDirection);
    }

    public function render()
    {
        return view('livewire.admin.products.index', [
            'products' => $this->getQuery()->paginate(10),
            'categories' => $this->categories,
        ])->layout('layouts.admin');
    }
}
