<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ShopController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'category' => ['nullable', 'string', 'max:120'],
            'min_price' => ['nullable', 'numeric', 'min:0'],
            'max_price' => ['nullable', 'numeric', 'min:0', 'gte:min_price'],
            'sort' => ['nullable', 'in:latest,popular,price_low,price_high,name'],
            'per_page' => ['nullable', 'integer', 'in:12,24,48'],
        ]);

        $search = trim((string) ($filters['search'] ?? ''));
        $sort = $filters['sort'] ?? 'latest';
        $perPage = (int) ($filters['per_page'] ?? 12);

        $query = Product::query()
            ->select(['id', 'category_id', 'name', 'slug', 'description', 'price', 'compare_price', 'quantity', 'images', 'product_type', 'is_active', 'created_at'])
            ->with('category:id,name,slug')
            ->where('is_active', true)
            ->when($search !== '', function ($productQuery) use ($search) {
                $term = '%'.$search.'%';
                $productQuery->where(fn ($q) => $q->where('name', 'like', $term)->orWhere('description', 'like', $term)->orWhere('sku', 'like', $term));
            })
            ->when(filled($filters['category'] ?? null), fn ($q) => $q->whereHas('category', fn ($categoryQuery) => $categoryQuery->where('slug', $filters['category'])))
            ->when(isset($filters['min_price']), fn ($q) => $q->where('price', '>=', $filters['min_price']))
            ->when(isset($filters['max_price']), fn ($q) => $q->where('price', '<=', $filters['max_price']));

        match ($sort) {
            'popular' => $query->withCount(['orderItems as total_sold'])->orderByDesc('total_sold')->orderByDesc('created_at'),
            'price_low' => $query->orderBy('price'),
            'price_high' => $query->orderByDesc('price'),
            'name' => $query->orderBy('name'),
            default => $query->latest(),
        };

        $products = $query->paginate($perPage)->withQueryString();
        $categories = Cache::remember('shop_filter_categories', now()->addMinutes(15), fn () => Category::query()->where('is_active', true)->orderBy('name')->get(['id', 'name', 'slug']));

        return view('shop', compact('products', 'categories', 'search', 'sort', 'perPage'));
    }

    public function show(Product $product)
    {
        abort_unless($product->is_active, 404);
        $product->load('category');
        $relatedProducts = Product::query()
            ->where('category_id', $product->category_id)
            ->whereKeyNot($product->id)
            ->where('is_active', true)
            ->with('category')
            ->latest()
            ->limit(4)
            ->get();

        return view('product', compact('product', 'relatedProducts'));
    }

    public function category(Category $category)
    {
        abort_unless($category->is_active, 404);
        $products = Product::query()->where('category_id', $category->id)->where('is_active', true)->with('category')->latest()->paginate(12);
        return view('category', compact('category', 'products'));
    }
}
