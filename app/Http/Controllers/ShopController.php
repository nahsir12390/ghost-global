<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ShopController extends Controller
{
    /**
     * Display the shop page.
     */
    public function index(Request $request)
    {
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'category' => ['nullable', 'string', 'max:120'],
            'min_price' => ['nullable', 'numeric', 'min:0'],
            'max_price' => ['nullable', 'numeric', 'min:0'],
            'sort' => ['nullable', 'in:latest,popular,price_low,price_high,name'],
            'per_page' => ['nullable', 'integer', 'in:12,24,48'],
        ]);

        $search = trim((string) ($filters['search'] ?? ''));
        $sort = $filters['sort'] ?? 'latest';
        $perPage = (int) ($filters['per_page'] ?? 12);

        $query = Product::query()
            ->select([
                'id', 'vendor_id', 'category_id', 'name', 'slug', 'price', 'compare_price',
                'quantity', 'images', 'product_type', 'is_active', 'created_at',
            ])
            ->with([
                'category:id,name,slug',
            ])
            ->where('is_active', true)
            ->when($search !== '', function ($productQuery) use ($search) {
                $term = '%'.$search.'%';

                $productQuery->where(function ($searchQuery) use ($term) {
                    $searchQuery->where('name', 'like', $term)
                        ->orWhere('description', 'like', $term);
                });
            })
            ->when(filled($filters['category'] ?? null), function ($productQuery) use ($filters) {
                $productQuery->whereHas('category', fn ($categoryQuery) => $categoryQuery->where('slug', $filters['category']));
            })
            ->when(array_key_exists('min_price', $filters) && $filters['min_price'] !== null, fn ($productQuery) => $productQuery->where('price', '>=', $filters['min_price']))
            ->when(array_key_exists('max_price', $filters) && $filters['max_price'] !== null, fn ($productQuery) => $productQuery->where('price', '<=', $filters['max_price']));

        match ($sort) {
            'popular' => $query->withCount(['orderItems as total_sold'])->orderByDesc('total_sold')->orderByDesc('created_at'),
            'price_low' => $query->orderBy('price'),
            'price_high' => $query->orderByDesc('price'),
            'name' => $query->orderBy('name'),
            default => $query->latest(),
        };

        $products = $query->paginate($perPage)->withQueryString();
        $categories = Cache::remember(
            'shop_filter_categories',
            now()->addMinutes(15),
            fn () => Category::query()->where('is_active', true)->orderBy('name')->get(['id', 'name', 'slug'])
        );

        return view('shop', compact('products', 'categories', 'search', 'sort', 'perPage'));
    }

    /**
     * Display product details page.
     */
    public function show(Product $product)
    {
        if (! $product->is_active) {
            abort(404);
        }

        // Don't need to load 'images' relationship since images is a JSON field
        $product->load('category');
        $relatedProducts = Product::where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->where('is_active', true)
            ->with('category') // Remove 'images' from here too
            ->limit(4)
            ->get();

        // Pass the slug to the view
        return view('product', compact('product', 'relatedProducts'));
    }

    /**
     * Display category page.
     */
    public function category(Category $category)
    {
        if (! $category->is_active) {
            abort(404);
        }

        $products = Product::where('category_id', $category->id)
            ->where('is_active', true)
            ->with('category') // Remove 'images' from here
            ->paginate(12);

        return view('category', compact('category', 'products'));
    }
}
