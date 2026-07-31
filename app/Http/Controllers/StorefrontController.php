<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class StorefrontController extends Controller
{
    public function show(Request $request, string $storeSlug)
    {
        $vendor = User::query()
            ->where('role', 'vendor')
            ->where('store_slug', $storeSlug)
            ->firstOrFail();

        abort_unless($vendor->hasPublicStorefront(), 404);

        $search = trim((string) $request->string('search'));
        $sortBy = $request->string('sort')->toString() ?: 'latest';
        $categoryFilter = (int) $request->integer('category_id');
        $typeFilter = $request->string('type')->toString();

        $allowedSorts = ['latest', 'popular', 'price_low', 'price_high', 'name'];
        if (! in_array($sortBy, $allowedSorts, true)) {
            $sortBy = 'latest';
        }

        $allowedTypes = [
            Product::TYPE_PHYSICAL,
            Product::TYPE_DIGITAL,
            Product::TYPE_COURSE,
        ];

        if (! in_array($typeFilter, $allowedTypes, true)) {
            $typeFilter = '';
        }

        $storeMetrics = Cache::remember(
            sprintf('storefront_metrics_%s', $vendor->id),
            now()->addMinutes(15),
            function () use ($vendor) {
                $totals = Product::query()
                    ->where('vendor_id', $vendor->id)
                    ->where('is_active', true)
                    ->selectRaw('COUNT(*) as active_products')
                    ->selectRaw('SUM(CASE WHEN product_type = ? THEN 1 ELSE 0 END) as digital_products', [Product::TYPE_DIGITAL])
                    ->selectRaw('SUM(CASE WHEN product_type = ? THEN 1 ELSE 0 END) as course_products', [Product::TYPE_COURSE])
                    ->selectRaw('SUM(CASE WHEN product_type = ? AND quantity > 0 THEN 1 ELSE 0 END) as in_stock_products', [Product::TYPE_PHYSICAL])
                    ->first();

                $totalSold = Product::query()
                    ->where('products.vendor_id', $vendor->id)
                    ->join('order_items', 'order_items.product_id', '=', 'products.id')
                    ->sum('order_items.quantity');

                return [
                    'active_products' => (int) ($totals->active_products ?? 0),
                    'digital_products' => (int) ($totals->digital_products ?? 0),
                    'course_products' => (int) ($totals->course_products ?? 0),
                    'in_stock_products' => (int) ($totals->in_stock_products ?? 0),
                    'total_sold' => (int) $totalSold,
                ];
            }
        );

        $categoryBreakdown = Cache::remember(
            sprintf('storefront_categories_%s', $vendor->id),
            now()->addMinutes(15),
            function () use ($vendor) {
                return Product::query()
                    ->join('categories', 'categories.id', '=', 'products.category_id')
                    ->where('products.vendor_id', $vendor->id)
                    ->where('products.is_active', true)
                    ->where('categories.is_active', true)
                    ->groupBy('categories.id', 'categories.name', 'categories.slug')
                    ->orderByDesc(DB::raw('COUNT(products.id)'))
                    ->take(6)
                    ->get([
                        'categories.id',
                        'categories.name',
                        'categories.slug',
                        DB::raw('COUNT(products.id) as products_count'),
                    ]);
            }
        );

        $availableTypes = Cache::remember(
            sprintf('storefront_types_%s', $vendor->id),
            now()->addMinutes(15),
            function () use ($vendor) {
                return Product::query()
                    ->where('vendor_id', $vendor->id)
                    ->where('is_active', true)
                    ->whereNotNull('product_type')
                    ->select('product_type')
                    ->distinct()
                    ->pluck('product_type')
                    ->filter()
                    ->values();
            }
        );

        $reviewSummary = Cache::remember(
            sprintf('storefront_reviews_%s', $vendor->id),
            now()->addMinutes(15),
            function () use ($vendor) {
                $summary = Comment::query()
                    ->join('products', 'products.id', '=', 'comments.product_id')
                    ->where('products.vendor_id', $vendor->id)
                    ->where('products.is_active', true)
                    ->where('comments.is_approved', true)
                    ->selectRaw('COUNT(comments.id) as total_reviews')
                    ->selectRaw('COALESCE(AVG(comments.rating), 0) as average_rating')
                    ->selectRaw('SUM(CASE WHEN comments.rating = 5 THEN 1 ELSE 0 END) as five_star_reviews')
                    ->selectRaw('SUM(CASE WHEN comments.rating >= 4 THEN 1 ELSE 0 END) as positive_reviews')
                    ->first();

                $totalReviews = (int) ($summary->total_reviews ?? 0);
                $positiveReviews = (int) ($summary->positive_reviews ?? 0);

                return [
                    'total_reviews' => $totalReviews,
                    'average_rating' => round((float) ($summary->average_rating ?? 0), 1),
                    'five_star_reviews' => (int) ($summary->five_star_reviews ?? 0),
                    'positive_reviews' => $positiveReviews,
                    'positive_rate' => $totalReviews > 0
                        ? (int) round(($positiveReviews / $totalReviews) * 100)
                        : 0,
                ];
            }
        );

        $recentReviews = Cache::remember(
            sprintf('storefront_recent_reviews_%s', $vendor->id),
            now()->addMinutes(15),
            function () use ($vendor) {
                return Comment::query()
                    ->with([
                        'product:id,name,slug,vendor_id',
                        'user:id,name',
                    ])
                    ->whereHas('product', function ($query) use ($vendor) {
                        $query->where('vendor_id', $vendor->id)
                            ->where('is_active', true);
                    })
                    ->where('is_approved', true)
                    ->latest()
                    ->take(4)
                    ->get();
            }
        );

        $digitalOfferings = (int) (($storeMetrics['digital_products'] ?? 0) + ($storeMetrics['course_products'] ?? 0));

        $trustSignals = [
            [
                'label' => 'Verified Seller',
                'value' => $vendor->isVendorVerified() ? 'Approved' : 'Pending',
                'tone' => $vendor->isVendorVerified() ? 'emerald' : 'amber',
                'description' => $vendor->isVendorVerified()
                    ? 'This store passed vendor verification before publishing.'
                    : 'Verification is still being completed.',
            ],
            [
                'label' => 'Store Rating',
                'value' => ($reviewSummary['total_reviews'] ?? 0) > 0
                    ? number_format((float) ($reviewSummary['average_rating'] ?? 0), 1) . '/5'
                    : 'New Store',
                'tone' => ($reviewSummary['total_reviews'] ?? 0) > 0 ? 'sky' : 'slate',
                'description' => ($reviewSummary['total_reviews'] ?? 0) > 0
                    ? number_format($reviewSummary['total_reviews']) . ' shopper reviews across this vendor catalog.'
                    : 'This store is still building up its review history.',
            ],
            [
                'label' => 'Order Volume',
                'value' => number_format($storeMetrics['total_sold'] ?? 0),
                'tone' => ($storeMetrics['total_sold'] ?? 0) > 0 ? 'violet' : 'slate',
                'description' => ($storeMetrics['total_sold'] ?? 0) > 0
                    ? 'Products from this store have already been purchased on the marketplace.'
                    : 'This store is ready for its first orders.',
            ],
            [
                'label' => 'Catalog Mix',
                'value' => $digitalOfferings > 0 ? 'Physical + Digital' : 'Physical Store',
                'tone' => $digitalOfferings > 0 ? 'amber' : 'rose',
                'description' => $digitalOfferings > 0
                    ? 'Shoppers can find both shipped and instant-access items here.'
                    : 'Focused product lineup for straightforward shopping.',
            ],
        ];

        $products = Product::query()
            ->select([
                'id',
                'vendor_id',
                'category_id',
                'name',
                'slug',
                'description',
                'price',
                'compare_price',
                'quantity',
                'images',
                'is_active',
                'created_at',
            ])
            ->with([
                'category:id,name,slug',
                'vendor:id,name,store_name,store_slug,vendor_is_active,verified_at,verification_status',
            ])
            ->where('vendor_id', $vendor->id)
            ->where('is_active', true)
            ->when($search !== '', function ($query) use ($search) {
                $term = '%' . $search . '%';

                $query->where(function ($searchQuery) use ($term) {
                    $searchQuery->where('name', 'like', $term)
                        ->orWhere('description', 'like', $term);
                });
            })
            ->when($categoryFilter > 0, fn ($query) => $query->where('category_id', $categoryFilter))
            ->when($typeFilter !== '', fn ($query) => $query->where('product_type', $typeFilter))
            ->when($sortBy === 'popular', function ($query) {
                $query->withCount(['orderItems as total_sold'])
                    ->orderByDesc('total_sold')
                    ->orderByDesc('created_at');
            })
            ->when($sortBy === 'price_low', fn ($query) => $query->orderBy('price'))
            ->when($sortBy === 'price_high', fn ($query) => $query->orderByDesc('price'))
            ->when($sortBy === 'name', fn ($query) => $query->orderBy('name'))
            ->when(! in_array($sortBy, ['popular', 'price_low', 'price_high', 'name'], true), function ($query) {
                $query->latest();
            })
            ->paginate(12)
            ->withQueryString();

        $featuredProducts = Cache::remember(
            sprintf('storefront_featured_%s', $vendor->id),
            now()->addMinutes(15),
            function () use ($vendor) {
                $baseQuery = Product::query()
                    ->select([
                        'id',
                        'vendor_id',
                        'category_id',
                        'name',
                        'slug',
                        'description',
                        'price',
                        'compare_price',
                        'quantity',
                        'images',
                        'is_active',
                        'created_at',
                        'product_type',
                    ])
                    ->with([
                        'category:id,name,slug',
                        'vendor:id,name,store_name,store_slug,vendor_is_active,verified_at,verification_status',
                    ])
                    ->where('vendor_id', $vendor->id)
                    ->where('is_active', true);

                $featured = (clone $baseQuery)
                    ->where(function ($query) {
                        $query->where('is_featured', true)
                            ->orWhereNotNull('compare_price');
                    })
                    ->latest()
                    ->take(4)
                    ->get();

                if ($featured->count() >= 4) {
                    return $featured;
                }

                $fallback = (clone $baseQuery)
                    ->whereNotIn('id', $featured->pluck('id'))
                    ->latest()
                    ->take(4 - $featured->count())
                    ->get();

                return $featured->concat($fallback);
            }
        );

        return view('storefront.show', [
            'vendor' => $vendor,
            'products' => $products,
            'storeMetrics' => $storeMetrics,
            'featuredProducts' => $featuredProducts,
            'categoryBreakdown' => $categoryBreakdown,
            'availableTypes' => $availableTypes,
            'selectedCategoryId' => $categoryFilter,
            'selectedType' => $typeFilter,
            'sortBy' => $sortBy,
            'search' => $search,
            'reviewSummary' => $reviewSummary,
            'recentReviews' => $recentReviews,
            'trustSignals' => $trustSignals,
        ]);
    }
}
