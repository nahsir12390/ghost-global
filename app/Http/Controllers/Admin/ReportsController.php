<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportsController extends Controller
{
    /**
     * Display sales reports.
     */
    public function sales()
    {
        // Get sales data for the last 30 days
        $thirtyDaysAgo = now()->subDays(30);
        
        // Total sales
        $totalSales = Order::where('payment_status', 'paid')
            ->where('created_at', '>=', $thirtyDaysAgo)
            ->sum('total');
        
        // Total orders
        $totalOrders = Order::where('created_at', '>=', $thirtyDaysAgo)->count();
        
        // Average order value
        $avgOrderValue = $totalOrders > 0 ? $totalSales / $totalOrders : 0;
        
        // Conversion rate (placeholder - you would need visitor data)
        $conversionRate = 2.5;
        
        // Recent orders
        $recentOrders = Order::with(['user', 'items'])
            ->latest()
            ->take(10)
            ->get();
        
        return view('admin.reports.sales', compact(
            'totalSales',
            'totalOrders',
            'avgOrderValue',
            'conversionRate',
            'recentOrders'
        ));
    }

    /**
     * Display products reports.
     */
    public function products()
    {
        // Total products
        $totalProducts = Product::count();
        
        // Product growth this month
        $startOfMonth = now()->startOfMonth();
        $productsThisMonth = Product::where('created_at', '>=', $startOfMonth)->count();
        $productGrowth = $totalProducts > 0 ? ($productsThisMonth / $totalProducts) * 100 : 0;
        
        // Best selling product (placeholder - you would need sales data)
        $bestSellingProduct = 'iPhone 15 Pro';
        $bestSellingCount = 45;
        
        // Stock status
        $lowStockThreshold = 10;
        $lowStockCount = Product::where('quantity', '>', 0)
            ->where('quantity', '<=', $lowStockThreshold)
            ->count();
        
        $outOfStockCount = Product::where('quantity', '<=', 0)->count();
        $inStockCount = Product::where('quantity', '>', $lowStockThreshold)->count();
        
        // Calculate percentages
        $inStockPercentage = $totalProducts > 0 ? ($inStockCount / $totalProducts) * 100 : 0;
        $lowStockPercentage = $totalProducts > 0 ? ($lowStockCount / $totalProducts) * 100 : 0;
        $outOfStockPercentage = $totalProducts > 0 ? ($outOfStockCount / $totalProducts) * 100 : 0;
        
        // Top products (placeholder data)
        $topProducts = Product::with('category')
            ->inRandomOrder()
            ->take(5)
            ->get()
            ->map(function($product) {
                $product->sold_count = rand(10, 100);
                $product->revenue = $product->sold_count * $product->price;
                return $product;
            });
        
        // Categories data
        $categories = Category::withCount('products')->get();
        
        // Categories with revenue data (placeholder)
        $categoriesData = $categories->map(function($category) {
            $category->revenue = rand(10000, 1000000);
            $category->avg_price = rand(5000, 50000);
            return $category;
        });
        
        // Restock suggestions
        $restockSuggestions = Product::where('quantity', '<=', 5)
            ->inRandomOrder()
            ->take(3)
            ->get()
            ->map(function($product) {
                $product->suggested_quantity = rand(10, 50);
                return $product;
            });
        
        return view('admin.reports.products', compact(
            'totalProducts',
            'productGrowth',
            'bestSellingProduct',
            'bestSellingCount',
            'lowStockCount',
            'outOfStockCount',
            'inStockCount',
            'inStockPercentage',
            'lowStockPercentage',
            'outOfStockPercentage',
            'topProducts',
            'categories',
            'categoriesData',
            'restockSuggestions'
        ));
    }

    /**
     * Get sales data for charts (API endpoint).
     */
    public function getSalesData(Request $request)
    {
        $request->validate([
            'period' => 'required|in:daily,weekly,monthly',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        $period = $request->input('period', 'monthly');
        $startDate = $request->input('start_date') ?: now()->subDays(30);
        $endDate = $request->input('end_date') ?: now();

        $query = Order::where('payment_status', 'paid')
            ->whereBetween('created_at', [$startDate, $endDate]);

        if ($period === 'daily') {
            $data = $query->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(total) as total_sales'),
                DB::raw('COUNT(*) as order_count')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();
        } elseif ($period === 'weekly') {
            $data = $query->select(
                DB::raw('YEARWEEK(created_at, 1) as week'),
                DB::raw('SUM(total) as total_sales'),
                DB::raw('COUNT(*) as order_count')
            )
            ->groupBy('week')
            ->orderBy('week')
            ->get();
        } else { // monthly
            $data = $query->select(
                DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
                DB::raw('SUM(total) as total_sales'),
                DB::raw('COUNT(*) as order_count')
            )
            ->groupBy('month')
            ->orderBy('month')
            ->get();
        }

        return response()->json([
            'success' => true,
            'data' => $data,
            'period' => $period,
            'start_date' => $startDate,
            'end_date' => $endDate
        ]);
    }

    /**
     * Get top products data (API endpoint).
     */
    public function getTopProducts(Request $request)
    {
        $request->validate([
            'limit' => 'nullable|integer|min:1|max:50',
            'category_id' => 'nullable|exists:categories,id',
        ]);

        $limit = $request->input('limit', 10);
        $categoryId = $request->input('category_id');

        $query = Product::with(['category'])
            ->withCount(['orderItems as total_sold' => function($query) {
                $query->select(DB::raw('SUM(quantity)'));
            }])
            ->orderBy('total_sold', 'desc');

        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }

        $products = $query->take($limit)->get();

        return response()->json([
            'success' => true,
            'data' => $products,
            'total' => $products->count()
        ]);
    }
}