<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Services\ImageUploadService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products = Product::forManager(request()->user())
            ->with(['category', 'vendor'])
            ->latest()
            ->paginate(15);

        return view('admin.products.index', compact('products'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Category::where('is_active', true)->get();
        return view('admin.products.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'compare_price' => 'nullable|numeric|min:0',
            'quantity' => 'required|integer|min:0',
            'sku' => 'nullable|string|max:100|unique:products,sku',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif,webp|max:10240',
            'is_featured' => 'boolean',
            'is_active' => 'boolean',
        ]);

        // Handle image uploads with compression
        $imagePaths = [];
        if ($request->hasFile('images')) {
            $imageService = new ImageUploadService();
            $imagePaths = $imageService->uploadAndCompressMultiple($request->file('images'), 'products');
        }

        $product = Product::create([
            'vendor_id' => $request->user()->isVendor() ? $request->user()->id : null,
            'category_id' => $validated['category_id'],
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
            'description' => $validated['description'],
            'price' => $validated['price'],
            'compare_price' => $validated['compare_price'],
            'quantity' => $validated['quantity'],
            'sku' => $validated['sku'],
            'images' => !empty($imagePaths) ? $imagePaths : null,
            'is_featured' => $request->boolean('is_featured'),
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()->route('admin.products.index')
            ->with('success', 'Product created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        abort_unless($product->canBeManagedBy(request()->user()), 403);
        $product->load('category', 'orderItems');
        return view('admin.products.show', compact('product'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        abort_unless($product->canBeManagedBy(request()->user()), 403);
        $categories = Category::where('is_active', true)->get();
        return view('admin.products.edit', compact('product', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
        abort_unless($product->canBeManagedBy($request->user()), 403);

        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'compare_price' => 'nullable|numeric|min:0',
            'quantity' => 'required|integer|min:0',
            'sku' => 'nullable|string|max:100|unique:products,sku,' . $product->id,
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif,webp|max:10240',
            'existing_images' => 'nullable|array',
            'is_featured' => 'boolean',
            'is_active' => 'boolean',
        ]);

        // Handle existing images
        $existingImages = $request->input('existing_images', []);
        $currentImages = $product->images ?? [];
        $imagePaths = array_intersect($currentImages, $existingImages);

        // Handle new image uploads with compression
        if ($request->hasFile('images')) {
            $imageService = new ImageUploadService();
            $newImages = $imageService->uploadAndCompressMultiple($request->file('images'), 'products');
            $imagePaths = array_merge($imagePaths, $newImages);
        }

        // Remove deleted images from storage
        $imagesToDelete = array_diff($currentImages, $existingImages);
        if (!empty($imagesToDelete)) {
            $imageService = new ImageUploadService();
            $imageService->deleteImages($imagesToDelete);
        }

        $product->update([
            'category_id' => $validated['category_id'],
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
            'description' => $validated['description'],
            'price' => $validated['price'],
            'compare_price' => $validated['compare_price'],
            'quantity' => $validated['quantity'],
            'sku' => $validated['sku'],
            'images' => !empty($imagePaths) ? $imagePaths : null,
            'is_featured' => $request->boolean('is_featured'),
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()->route('admin.products.index')
            ->with('success', 'Product updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        abort_unless($product->canBeManagedBy(request()->user()), 403);

        // Delete product images from storage
        if ($product->images) {
            $imageService = new ImageUploadService();
            $images = $product->images ?? [];
            $imageService->deleteImages($images);
        }

        $product->delete();

        return redirect()->route('admin.products.index')
            ->with('success', 'Product deleted successfully.');
    }

    /**
     * Update product status (active/inactive).
     */
    public function updateStatus(Request $request, Product $product)
    {
        abort_unless($product->canBeManagedBy($request->user()), 403);

        $request->validate([
            'status' => 'required|boolean',
        ]);

        $product->update(['is_active' => $request->status]);

        return response()->json([
            'success' => true,
            'message' => 'Product status updated successfully.'
        ]);
    }

    /**
     * Update featured status.
     */
    public function updateFeatured(Request $request, Product $product)
    {
        abort_unless($product->canBeManagedBy($request->user()), 403);

        $request->validate([
            'featured' => 'required|boolean',
        ]);

        $product->update(['is_featured' => $request->featured]);

        return response()->json([
            'success' => true,
            'message' => 'Product featured status updated successfully.'
        ]);
    }
}
