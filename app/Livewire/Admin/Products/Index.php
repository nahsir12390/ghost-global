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
        abort_unless(auth()->user()?->fresh()?->canManageProducts(), 403);
        $this->categories = Category::where('is_active', true)->orderBy('name')->get();
    }

    public function updatingSearch() { $this->resetPage(); }
    public function updatingCategoryFilter() { $this->resetPage(); }
    public function updatingStatusFilter() { $this->resetPage(); }

    public function sortBy($field)
    {
        $allowed = ['name', 'price', 'quantity', 'created_at', 'is_active'];
        if (! in_array($field, $allowed, true)) return;
        if ($this->sortField === $field) $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        else { $this->sortField = $field; $this->sortDirection = 'asc'; }
    }

    public function updatedSelectAll()
    {
        $this->selectedProducts = $this->selectAll ? $this->getQuery()->pluck('id')->toArray() : [];
    }

    public function updatedSelectedProducts()
    {
        $count = $this->getQuery()->count();
        $this->selectAll = count($this->selectedProducts) === $count && $count > 0;
    }

    public function toggleProductStatus($productId)
    {
        $product = $this->managedProduct($productId);
        if (! $product) return;
        $product->update(['is_active' => ! $product->is_active]);
        $this->dispatch('productUpdated');
        session()->flash('success', 'Product status updated successfully.');
    }

    public function toggleFeatured($productId)
    {
        $product = $this->managedProduct($productId);
        if (! $product) return;
        $product->update(['is_featured' => ! $product->is_featured]);
        $this->dispatch('productUpdated');
        session()->flash('success', 'Product featured status updated successfully.');
    }

    public function deleteProduct($productId)
    {
        $product = $this->managedProduct($productId);
        if (! $product) return;

        if ($product->orderItems()->exists()) {
            $product->update(['is_active' => false, 'is_featured' => false]);
            session()->flash('success', 'This product has order history, so it was safely deactivated instead of deleted.');
            $this->dispatch('productUpdated');
            return;
        }

        $this->deleteProductFiles($product);
        $product->delete();
        $this->dispatch('productUpdated');
        session()->flash('success', 'Product deleted successfully.');
    }

    public function deleteSelectedProducts()
    {
        if (empty($this->selectedProducts)) {
            session()->flash('error', 'No products selected.');
            return;
        }

        $products = Product::forManager(auth()->user())->whereIn('id', $this->selectedProducts)->get();
        $deleted = $deactivated = 0;
        foreach ($products as $product) {
            if ($product->orderItems()->exists()) {
                $product->update(['is_active' => false, 'is_featured' => false]);
                $deactivated++;
                continue;
            }
            $this->deleteProductFiles($product);
            $product->delete();
            $deleted++;
        }

        $this->selectedProducts = [];
        $this->selectAll = false;
        $this->dispatch('productUpdated');
        session()->flash('success', $deleted.' product(s) deleted; '.$deactivated.' product(s) with order history safely deactivated.');
    }

    public function confirmDelete($productId = null)
    {
        $productId ? $this->deleteProduct($productId) : $this->deleteSelectedProducts();
    }

    public function getQuery()
    {
        return Product::query()->forManager(auth()->user())->with('category')
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%'.$this->search.'%')
                        ->orWhere('sku', 'like', '%'.$this->search.'%')
                        ->orWhere('description', 'like', '%'.$this->search.'%');
                });
            })
            ->when($this->categoryFilter, fn ($q) => $q->where('category_id', $this->categoryFilter))
            ->when($this->statusFilter !== '', fn ($q) => $q->where('is_active', $this->statusFilter))
            ->orderBy($this->sortField, $this->sortDirection);
    }

    private function managedProduct($productId): ?Product
    {
        abort_unless(auth()->user()?->fresh()?->canManageProducts(), 403);
        return Product::forManager(auth()->user())->find($productId);
    }

    private function deleteProductFiles(Product $product): void
    {
        if (! empty($product->images)) (new ImageUploadService)->deleteImages($product->images);
        if ($product->download_file_path) Storage::disk('public')->delete($product->download_file_path);
    }

    public function render()
    {
        return view('livewire.admin.products.index', [
            'products' => $this->getQuery()->paginate(10),
            'categories' => $this->categories,
        ])->layout('layouts.admin');
    }
}
