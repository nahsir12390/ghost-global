<?php

namespace App\Livewire;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Cache;
use Livewire\Component;

class Home extends Component
{
    private const STOREFRONT_CACHE_TTL_MINUTES = 10;

    public function render(): View
    {
        return view('livewire.home', $this->storefrontData());
    }

    /**
     * @return array{
     *     featuredProducts: mixed,
     *     newArrivals: mixed,
     *     bestSellingProducts: mixed,
     *     categories: mixed,
     *     onSaleProducts: mixed
     * }
     */
    private function storefrontData(): array
    {
        return [
            'featuredProducts' => Cache::remember(
                'storefront_featured_products',
                now()->addMinutes(self::STOREFRONT_CACHE_TTL_MINUTES),
                function () {
                    $limit = 8;
                    $featuredProducts = $this->baseStorefrontProductQuery()
                        ->where('is_featured', true)
                        ->take($limit)
                        ->get();

                    if ($featuredProducts->count() >= $limit) {
                        return $featuredProducts;
                    }

                    return $featuredProducts->concat(
                        $this->baseStorefrontProductQuery()
                            ->whereNotIn('id', $featuredProducts->pluck('id'))
                            ->latest()
                            ->take($limit - $featuredProducts->count())
                            ->get()
                    );
                }
            ),
            'newArrivals' => Cache::remember(
                'storefront_new_arrivals',
                now()->addMinutes(self::STOREFRONT_CACHE_TTL_MINUTES),
                fn () => $this->baseStorefrontProductQuery()->latest()->take(8)->get()
            ),
            'bestSellingProducts' => Cache::remember(
                'storefront_best_sellers',
                now()->addMinutes(self::STOREFRONT_CACHE_TTL_MINUTES),
                fn () => $this->baseStorefrontProductQuery()
                    ->withCount(['orderItems as total_sold'])
                    ->orderByDesc('total_sold')
                    ->take(8)
                    ->get()
            ),
            'categories' => Cache::remember(
                'storefront_top_categories',
                now()->addMinutes(self::STOREFRONT_CACHE_TTL_MINUTES),
                fn () => Category::query()
                    ->select(['id', 'name', 'slug', 'image', 'is_active'])
                    ->where('is_active', true)
                    ->withCount([
                        'products as products_count' => fn ($query) => $query->where('is_active', true),
                    ])
                    ->orderByDesc('products_count')
                    ->take(6)
                    ->get()
            ),
            'onSaleProducts' => Cache::remember(
                'storefront_on_sale_products',
                now()->addMinutes(self::STOREFRONT_CACHE_TTL_MINUTES),
                fn () => $this->baseStorefrontProductQuery()
                    ->whereNotNull('compare_price')
                    ->whereColumn('compare_price', '>', 'price')
                    ->latest()
                    ->take(6)
                    ->get()
            ),
        ];
    }

    private function baseStorefrontProductQuery(): Builder
    {
        return Product::query()
            ->select([
                'id',
                'vendor_id',
                'category_id',
                'name',
                'slug',
                'price',
                'compare_price',
                'quantity',
                'images',
                'is_active',
                'created_at',
            ])
            ->where('is_active', true)
            ->with([
                'category:id,name,slug',
                'vendor:id,name,role,store_name,store_slug,vendor_is_active,verified_at,verification_status',
            ]);
    }
}
