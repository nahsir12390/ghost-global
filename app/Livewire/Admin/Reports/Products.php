<?php

namespace App\Livewire\Admin\Reports;

use Livewire\Component;
use App\Models\Product;
use App\Models\Category;
use App\Models\OrderItem;
use Illuminate\Support\Facades\DB;

class Products extends Component
{
    public $timePeriod = 'last_30_days';
    public $categoryId = '';
    public $startDate;
    public $endDate;

    protected $listeners = ['refreshProductsReport' => '$refresh'];

    public function mount()
    {
        $this->setDefaultDates();
    }

    private function setDefaultDates()
    {
        $today = now();
        
        switch ($this->timePeriod) {
            case 'last_30_days':
                $this->startDate = $today->subDays(30)->format('Y-m-d');
                $this->endDate = $today->format('Y-m-d');
                break;
            case 'last_90_days':
                $this->startDate = $today->subDays(90)->format('Y-m-d');
                $this->endDate = $today->format('Y-m-d');
                break;
            case 'this_year':
                $this->startDate = $today->startOfYear()->format('Y-m-d');
                $this->endDate = $today->format('Y-m-d');
                break;
            case 'last_year':
                $lastYear = $today->subYear();
                $this->startDate = $lastYear->startOfYear()->format('Y-m-d');
                $this->endDate = $lastYear->endOfYear()->format('Y-m-d');
                break;
            case 'all_time':
                $this->startDate = '2020-01-01';
                $this->endDate = $today->format('Y-m-d');
                break;
        }
    }

    public function updatedTimePeriod()
    {
        $this->setDefaultDates();
        $this->dispatch('refreshProductsReport');
    }

    public function updatedCategoryId()
    {
        $this->dispatch('refreshProductsReport');
    }

    public function getStats()
    {
        $totalProducts = Product::count();
        
        // Product growth this month
        $startOfMonth = now()->startOfMonth();
        $productsThisMonth = Product::where('created_at', '>=', $startOfMonth)->count();
        $productGrowth = $totalProducts > 0 ? ($productsThisMonth / $totalProducts) * 100 : 0;

        // Stock status
        $lowStockThreshold = 10;
        $lowStockCount = Product::where('quantity', '>', 0)
            ->where('quantity', '<=', $lowStockThreshold)
            ->count();
        
        $outOfStockCount = Product::where('quantity', '<=', 0)->count();
        $inStockCount = Product::where('quantity', '>', $lowStockThreshold)->count();

        // Best selling product
        $bestSelling = $this->getTopProducts(1)->first();
        $bestSellingProduct = $bestSelling ? $bestSelling->name : 'N/A';
        $bestSellingCount = $bestSelling ? $bestSelling->total_sold : 0;

        // Calculate percentages
        $inStockPercentage = $totalProducts > 0 ? ($inStockCount / $totalProducts) * 100 : 0;
        $lowStockPercentage = $totalProducts > 0 ? ($lowStockCount / $totalProducts) * 100 : 0;
        $outOfStockPercentage = $totalProducts > 0 ? ($outOfStockCount / $totalProducts) * 100 : 0;

        return [
            'total_products' => $totalProducts,
            'product_growth' => round($productGrowth, 2),
            'best_selling_product' => $bestSellingProduct,
            'best_selling_count' => $bestSellingCount,
            'low_stock_count' => $lowStockCount,
            'out_of_stock_count' => $outOfStockCount,
            'in_stock_count' => $inStockCount,
            'in_stock_percentage' => round($inStockPercentage, 1),
            'low_stock_percentage' => round($lowStockPercentage, 1),
            'out_of_stock_percentage' => round($outOfStockPercentage, 1),
        ];
    }

    public function getTopProducts($limit = 10)
    {
        $query = Product::query()
            ->with('category')
            ->withCount(['orderItems as total_sold' => function($query) {
                $query->select(DB::raw('COALESCE(SUM(quantity), 0)'))
                    ->whereHas('order', function($q) {
                        $q->where('payment_status', 'paid')
                          ->whereBetween('orders.created_at', [$this->startDate . ' 00:00:00', $this->endDate . ' 23:59:59']);
                    });
            }])
            ->withSum(['orderItems as revenue' => function($query) {
                $query->select(DB::raw('COALESCE(SUM(quantity * price), 0)'))
                    ->whereHas('order', function($q) {
                        $q->where('payment_status', 'paid')
                          ->whereBetween('orders.created_at', [$this->startDate . ' 00:00:00', $this->endDate . ' 23:59:59']);
                    });
            }], 'quantity');

        if ($this->categoryId) {
            $query->where('category_id', $this->categoryId);
        }

        return $query->orderBy('total_sold', 'desc')
            ->orderBy('revenue', 'desc')
            ->take($limit)
            ->get()
            ->map(function($product) {
                $product->revenue = $product->revenue ?: 0;
                return $product;
            });
    }

    public function getCategoriesData()
    {
        $startDate = $this->startDate . ' 00:00:00';
        $endDate = $this->endDate . ' 23:59:59';

        return Category::query()
            ->withCount('products')
            ->get()
            ->map(function($category) use ($startDate, $endDate) {
                // Calculate revenue
                $category->revenue = DB::table('order_items')
                    ->join('orders', 'order_items.order_id', '=', 'orders.id')
                    ->join('products', 'order_items.product_id', '=', 'products.id')
                    ->where('products.category_id', $category->id)
                    ->where('orders.payment_status', 'paid')
                    ->whereBetween('orders.created_at', [$startDate, $endDate])
                    ->sum(DB::raw('order_items.quantity * order_items.price'));
                
                // Calculate average price
                $category->avg_price = DB::table('products')
                    ->where('category_id', $category->id)
                    ->where('price', '>', 0)
                    ->avg('price') ?: 0;
                
                return $category;
            })
            ->sortByDesc('revenue');
    }

    public function getRestockSuggestions()
    {
        return Product::query()
            ->where('quantity', '<=', 5)
            ->where('quantity', '>', 0)
            ->with(['category'])
            ->orderBy('quantity')
            ->take(5)
            ->get()
            ->map(function($product) {
                // Calculate suggested quantity based on sales velocity
                $soldLastMonth = OrderItem::where('product_id', $product->id)
                    ->whereHas('order', function($q) {
                        $monthAgo = now()->subMonth();
                        $q->where('payment_status', 'paid')
                          ->where('created_at', '>=', $monthAgo);
                    })
                    ->sum('quantity');
                
                $product->suggested_quantity = max(10, $soldLastMonth * 2);
                $product->days_of_supply = $product->quantity > 0 && $soldLastMonth > 0 
                    ? round($product->quantity / ($soldLastMonth / 30), 1)
                    : 0;
                
                return $product;
            });
    }

    public function getAllCategories()
    {
        return Category::where('is_active', true)->orderBy('name')->get();
    }

    public function render()
    {
        $stats = $this->getStats();
        $topProducts = $this->getTopProducts(10);
        $categoriesData = $this->getCategoriesData();
        $restockSuggestions = $this->getRestockSuggestions();
        $categories = $this->getAllCategories();

        return view('livewire.admin.reports.products', [
            'stats' => $stats,
            'topProducts' => $topProducts,
            'categoriesData' => $categoriesData,
            'restockSuggestions' => $restockSuggestions,
            'categories' => $categories,
        ]);
    }
}