<?php

namespace App\Livewire\Admin\Categories;

use Livewire\Component;
use App\Models\Category;
use Illuminate\Support\Str;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;

class Edit extends Component
{
    use WithFileUploads;

    public Category $category;
    public int $categoryId;
    public string $name = '';
    public string $description = '';
    public $existingImage;
    public $newImage;
    public $is_active = true;

    protected $rules = [
        'name' => 'required|string|max:255|unique:categories,name,',
        'description' => 'nullable|string',
        'newImage' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        'is_active' => 'boolean',
    ];

    public function mount(int $categoryId)
    {
        $category = Category::findOrFail($categoryId);

        $this->category = $category;
        $this->categoryId = $category->id;
        $this->rules['name'] .= $this->categoryId;
        
        $this->name = $category->name;
        $this->description = $category->description;
        $this->existingImage = $category->image;
        $this->is_active = $category->is_active;
    }

    public function update()
    {
        $this->validate();

        $this->category = Category::findOrFail($this->categoryId);

        $imagePath = $this->existingImage;
        
        if ($this->newImage) {
            // Delete old image if exists
            if ($this->existingImage) {
                Storage::disk('public')->delete($this->existingImage);
            }
            $imagePath = $this->newImage->store('categories', 'public');
        }

        $this->category->update([
            'name' => $this->name,
            'slug' => Str::slug($this->name),
            'description' => $this->description,
            'image' => $imagePath,
            'is_active' => $this->is_active,
        ]);

        session()->flash('success', 'Category updated successfully!');
        return redirect()->route('admin.categories.index');
    }

    public function removeImage()
    {
        $this->category = Category::findOrFail($this->categoryId);

        if ($this->existingImage) {
            Storage::disk('public')->delete($this->existingImage);
            $this->existingImage = null;
            $this->newImage = null;
            $this->category->update(['image' => null]);
        }
    }

    public function render()
    {
        $this->category = Category::findOrFail($this->categoryId);

        return view('livewire.admin.categories.edit', [
            'category' => $this->category,
        ]);
    }
}
