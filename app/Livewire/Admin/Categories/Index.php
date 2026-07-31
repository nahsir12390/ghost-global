<?php

namespace App\Livewire\Admin\Categories;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Category;

class Index extends Component
{
    use WithPagination;

    public $search = '';
    public $statusFilter = '';
    public $sortField = 'created_at';
    public $sortDirection = 'desc';
    public $selectedCategories = [];
    public $selectAll = false;
    public $showDeleteModal = false;
    public $categoryToDelete = null;

    protected $listeners = ['categoryUpdated' => '$refresh'];

    public function updatingSearch()
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

    public function toggleSelectAll()
    {
        if ($this->selectAll) {
            $this->selectedCategories = $this->getQuery()->pluck('id')->toArray();
        } else {
            $this->selectedCategories = [];
        }
    }

    public function updatedSelectAll(): void
    {
        $this->toggleSelectAll();
    }

    public function updatedSelectedCategories(): void
    {
        $totalCategories = (clone $this->getQuery())->count();
        $this->selectAll = $totalCategories > 0 && count($this->selectedCategories) === $totalCategories;
    }

    public function toggleCategoryStatus($categoryId)
    {
        $category = Category::find($categoryId);
        if ($category) {
            $category->update(['is_active' => !$category->is_active]);
            $this->dispatch('categoryUpdated');
        }
    }

    public function confirmDelete($categoryId = null)
    {
        if ($categoryId) {
            $this->categoryToDelete = Category::find($categoryId);
        }
        $this->showDeleteModal = true;
    }

    public function deleteCategory()
    {
        if ($this->categoryToDelete) {
            // Check if category has products
            if ($this->categoryToDelete->products()->count() > 0) {
                session()->flash('error', 'Cannot delete category with existing products.');
                $this->showDeleteModal = false;
                return;
            }

            // Delete image if exists
            if ($this->categoryToDelete->image) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($this->categoryToDelete->image);
            }

            $this->categoryToDelete->delete();
            session()->flash('success', 'Category deleted successfully.');
        } else if (!empty($this->selectedCategories)) {
            // Check if any selected category has products
            $categoriesWithProducts = Category::whereIn('id', $this->selectedCategories)
                ->whereHas('products')
                ->count();

            if ($categoriesWithProducts > 0) {
                session()->flash('error', 'Some categories have products and cannot be deleted.');
                $this->showDeleteModal = false;
                return;
            }

            // Delete categories
            $categories = Category::whereIn('id', $this->selectedCategories)->get();
            foreach ($categories as $category) {
                if ($category->image) {
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($category->image);
                }
            }

            Category::whereIn('id', $this->selectedCategories)->delete();
            
            $this->selectedCategories = [];
            $this->selectAll = false;
            session()->flash('success', 'Selected categories deleted successfully.');
        }

        $this->showDeleteModal = false;
        $this->categoryToDelete = null;
    }

    public function getQuery()
    {
        return Category::query()
            ->withCount('products')
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('description', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->statusFilter !== '', function ($query) {
                $query->where('is_active', $this->statusFilter);
            })
            ->orderBy($this->sortField, $this->sortDirection);
    }

    public function render()
    {
        return view('livewire.admin.categories.index', [
            'categories' => $this->getQuery()->paginate(10),
        ]);
    }
}
