<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    protected function user()
    {
        return request()->user();
    }

    protected function visibleOrdersQuery()
    {
        return Order::query()->visibleTo($this->user());
    }

    protected function visibleProductsQuery()
    {
        return Product::query()->forManager($this->user());
    }

    protected function visibleOrderItemsQuery()
    {
        $query = OrderItem::query()->with('order');

        return $query;
    }

    /**
     * Display admin dashboard with statistics.
     */
    public function index()
    {

        // Cache stats for better performance
        $stats = Cache::remember('dashboard_stats', now()->addMinutes(5), function () {
            return [
                'totalOrders' => Order::count(),
                'totalSales' => Order::where('payment_status', 'paid')->sum('total'),
                'totalProducts' => Product::count(),
                'totalCustomers' => User::where('is_admin', false)->count(),
                'pendingOrders' => Order::where('status', 'pending')->count(),
                'outOfStockProducts' => Product::where('quantity', 0)->count(),
                'processingOrders' => Order::where('status', 'processing')->count(),
                'todayOrders' => Order::whereDate('created_at', today())->count(),
                'todaySales' => Order::whereDate('created_at', today())
                    ->where('payment_status', 'paid')
                    ->sum('total'),
                'lowStockProducts' => Product::where('quantity', '>', 0)
                    ->where('quantity', '<=', 10)
                    ->count(),
                'totalCategories' => Category::count(),
                'featuredProducts' => Product::where('is_featured', true)->count(),
            ];
        });

        // Recent orders (not cached for real-time data)
        $recentOrders = Order::with(['user', 'items.product'])
            ->latest()
            ->take(10)
            ->get();

        // Top selling products
        $topProducts = Cache::remember('top_products_30days', now()->addHours(1), function () {
            return DB::table('order_items')
                ->select(
                    'products.id',
                    'products.name',
                    'products.price',
                    'products.images',
                    DB::raw('SUM(order_items.quantity) as total_quantity'),
                    DB::raw('SUM(order_items.total) as total_sales')
                )
                ->join('products', 'order_items.product_id', '=', 'products.id')
                ->join('orders', 'order_items.order_id', '=', 'orders.id')
                ->where('orders.created_at', '>=', now()->subDays(30))
                ->where('orders.payment_status', 'paid')
                ->groupBy('order_items.product_id', 'products.id', 'products.name', 'products.price', 'products.images')
                ->orderByDesc('total_quantity')
                ->limit(10)
                ->get();
        });

        // Sales chart data (last 30 days)
        $salesData = Cache::remember('sales_data_30days', now()->addMinutes(10), function () {
            return Order::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as orders_count'),
                DB::raw('SUM(total) as sales_total')
            )
                ->where('created_at', '>=', now()->subDays(30))
                ->where('payment_status', 'paid')
                ->groupBy('date')
                ->orderBy('date')
                ->get();
        });

        // Prepare chart data
        $chartLabels = $salesData->pluck('date')->map(function ($date) {
            return date('M d', strtotime($date));
        })->toArray();

        $chartOrders = $salesData->pluck('orders_count')->toArray();
        $chartSales = $salesData->pluck('sales_total')->toArray();

        // Order status distribution
        $orderStatusDistribution = Cache::remember('order_status_distribution', now()->addMinutes(15), function () {
            return Order::select('status', DB::raw('COUNT(*) as count'))
                ->groupBy('status')
                ->pluck('count', 'status');
        });

        // Payment status distribution
        $paymentStatusDistribution = Cache::remember('payment_status_distribution', now()->addMinutes(15), function () {
            return Order::select('payment_status', DB::raw('COUNT(*) as count'))
                ->groupBy('payment_status')
                ->pluck('count', 'payment_status');
        });

        // Low stock products
        $lowStockProducts = Product::where('quantity', '>', 0)
            ->where('quantity', '<=', 10)
            ->with('category')
            ->orderBy('quantity')
            ->take(5)
            ->get();

        // Recent customers
        $recentCustomers = User::where('is_admin', false)
            ->latest()
            ->take(5)
            ->get();

        $walletSummary = Cache::remember('dashboard_wallet_summary', now()->addMinutes(5), function () {
            return [
                'wallet_users' => Wallet::count(),
                'total_balance' => (float) Wallet::sum('balance'),
                'topups_today' => (float) WalletTransaction::where('type', WalletTransaction::TYPE_TOPUP)
                    ->where('status', 'completed')
                    ->whereDate('completed_at', today())
                    ->sum('amount'),
                'transactions_today' => WalletTransaction::whereDate('created_at', today())->count(),
            ];
        });

        $referralSummary = Cache::remember('dashboard_referral_summary', now()->addMinutes(5), function () {
            return [
                'total_referrers' => User::whereNotNull('referral_code')
                    ->whereHas('referrals')
                    ->count(),
                'referred_users' => User::whereNotNull('referred_by_id')->count(),
                'rewarded_referrals' => User::whereNotNull('referral_rewarded_at')->count(),
                'referral_payouts' => (float) WalletTransaction::where('type', WalletTransaction::TYPE_REFERRAL_BONUS)
                    ->where('status', 'completed')
                    ->sum('amount'),
            ];
        });

        $recentWalletTransactions = WalletTransaction::with('user')
            ->latest()
            ->take(8)
            ->get();

        $topReferrers = User::whereNotNull('referral_code')
            ->withCount([
                'referrals',
                'referrals as rewarded_referrals_count' => fn ($query) => $query->whereNotNull('referral_rewarded_at'),
            ])
            ->orderByDesc('rewarded_referrals_count')
            ->orderByDesc('referrals_count')
            ->take(6)
            ->get();

        return view('admin.dashboard.index', array_merge($stats, [
            'recentOrders' => $recentOrders,
            'topProducts' => $topProducts,
            'chartLabels' => $chartLabels,
            'chartOrders' => $chartOrders,
            'chartSales' => $chartSales,
            'orderStatusDistribution' => $orderStatusDistribution,
            'paymentStatusDistribution' => $paymentStatusDistribution,
            'lowStockProducts' => $lowStockProducts,
            'recentCustomers' => $recentCustomers,
            'walletSummary' => $walletSummary,
            'referralSummary' => $referralSummary,
            'recentWalletTransactions' => $recentWalletTransactions,
            'topReferrers' => $topReferrers,
        ]));
    }

    /**
     * Display modern dashboard.
     */
    public function modern()
    {
        return $this->index();
    }

    /**
     * Get dashboard stats via AJAX for real-time updates.
     */
    public function getStats(Request $request)
    {
        $period = $request->input('period', 'today'); // today, week, month, year

        $today = now();
        $startDate = match ($period) {
            'today' => $today->copy()->startOfDay(),
            'week' => $today->copy()->startOfWeek(),
            'month' => $today->copy()->startOfMonth(),
            'year' => $today->copy()->startOfYear(),
            default => $today->copy()->startOfDay(),
        };

        // Period stats
        $ordersCount = $this->visibleOrdersQuery()->where('created_at', '>=', $startDate)->count();
        $salesTotal = Order::where('created_at', '>=', $startDate)
            ->where('payment_status', 'paid')
            ->sum('total');

        // New customers
        $newCustomers = User::where('is_admin', false)
            ->where('created_at', '>=', $startDate)
            ->count();

        // Average order value
        $avgOrderValue = Order::where('created_at', '>=', $startDate)
            ->where('payment_status', 'paid')
            ->avg('total') ?? 0;

        // Conversion rate (simplified - orders / estimated visitors)
        $estimatedVisitors = 1000; // This should come from analytics
        $conversionRate = $ordersCount > 0 ? (($ordersCount / $estimatedVisitors) * 100) : 0;

        // Customer growth (new customers this period vs previous period)
        $previousPeriodStart = $startDate->copy()->subDays($startDate->diffInDays(now()));
        $previousPeriodOrders = $this->visibleOrdersQuery()
            ->where('created_at', '>=', $previousPeriodStart)
            ->where('created_at', '<', $startDate)
            ->count();
        $customerGrowth = $previousPeriodOrders > 0 ?
            ((($ordersCount - $previousPeriodOrders) / $previousPeriodOrders) * 100) : 0;

        // Inventory value
        $inventoryValue = $this->visibleProductsQuery()->sum(DB::raw('price * quantity'));

        return response()->json([
            'success' => true,
            'data' => [
                'orders_count' => $ordersCount,
                'sales_total' => $salesTotal,
                'new_customers' => $newCustomers,
                'avg_order_value' => $avgOrderValue,
                'conversion_rate' => $conversionRate,
                'customer_growth' => $customerGrowth,
                'inventory_value' => $inventoryValue,
                'wallet_balance_total' => (float) Wallet::sum('balance'),
                'referred_users' => User::whereNotNull('referred_by_id')->count(),
                'total_customers' => User::where('is_admin', false)->count(),
                'period' => ucfirst($period),
            ],
        ]);
    }

    /**
     * Get sales chart data.
     */
    public function getSalesChartData(Request $request)
    {
        $period = $request->input('period', '30days'); // 7days, 30days, 90days, 1year

        $days = match ($period) {
            '7days' => 7,
            '30days' => 30,
            '90days' => 90,
            '1year' => 365,
            default => 30,
        };

        $cacheKey = "sales_chart_{$period}_".now()->format('Y-m-d');
        $salesData = Cache::remember($cacheKey, now()->addMinutes(5), function () use ($days) {
            return Order::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as orders_count'),
                DB::raw('SUM(total) as sales_total')
            )
                ->where('created_at', '>=', now()->subDays($days))
                ->where('payment_status', 'paid')
                ->groupBy('date')
                ->orderBy('date')
                ->get();
        });

        $labels = $salesData->pluck('date')->map(function ($date) {
            return date('M d', strtotime($date));
        })->toArray();

        $orders = $salesData->pluck('orders_count')->toArray();
        $sales = $salesData->pluck('sales_total')->toArray();

        return response()->json([
            'success' => true,
            'data' => [
                'labels' => $labels,
                'orders' => $orders,
                'sales' => $sales,
                'period' => $period,
            ],
        ]);
    }

    /**
     * Get order status summary.
     */
    public function getOrderStatusSummary()
    {
        $summary = Cache::remember('order_status_summary', now()->addMinutes(10), function () {
            return Order::select('status', DB::raw('COUNT(*) as count'))
                ->groupBy('status')
                ->orderByDesc('count')
                ->get()
                ->mapWithKeys(function ($item) {
                    return [$item->status => $item->count];
                });
        });

        return response()->json([
            'success' => true,
            'data' => $summary,
        ]);
    }

    /**
     * Get top performing products.
     */
    public function getTopProducts(Request $request)
    {
        $limit = $request->input('limit', 5);

        $topProducts = DB::table('order_items')
            ->select(
                'products.id',
                'products.name',
                'products.price',
                'products.images',
                DB::raw('SUM(order_items.quantity) as total_quantity'),
                DB::raw('SUM(order_items.total) as total_sales')
            )
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->where('orders.created_at', '>=', now()->subDays(30))
            ->where('orders.payment_status', 'paid')
            ->groupBy('order_items.product_id', 'products.id', 'products.name', 'products.price', 'products.images')
            ->orderByDesc('total_sales')
            ->limit($limit)
            ->get()
            ->map(function ($product) {
                $product->image_url = $product->images ?
                    (is_array($product->images) ? $product->images[0] ?? null : json_decode($product->images, true)[0] ?? null) :
                    null;

                return $product;
            });

        return response()->json([
            'success' => true,
            'data' => $topProducts,
        ]);
    }

    /**
     * Get recent orders.
     */
    public function getRecentOrders(Request $request)
    {
        $limit = $request->input('limit', 10);

        $recentOrders = $this->visibleOrdersQuery()
            ->with(['user', 'items.product'])
            ->latest()
            ->take($limit)
            ->get()
            ->map(function ($order) {
                $customerName = trim(($order->shipping_first_name ?? '').' '.($order->shipping_last_name ?? ''));

                if ($customerName === '') {
                    $customerName = $order->user?->name ?: 'Customer';
                }

                return [
                    'id' => $order->id,
                    'order_number' => $order->order_number,
                    'total' => $order->total,
                    'status' => $order->status,
                    'payment_status' => $order->payment_status,
                    'customer_name' => $customerName,
                    'shipping_first_name' => $order->shipping_first_name,
                    'shipping_last_name' => $order->shipping_last_name,
                    'created_at' => $order->created_at->format('Y-m-d H:i:s'),
                    'created_at_human' => $order->created_at->diffForHumans(),
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $recentOrders,
        ]);
    }

    /**
     * Refresh dashboard cache.
     */
    public function refreshCache()
    {
        Cache::forget('dashboard_stats');
        Cache::forget('top_products_30days');
        Cache::forget('sales_data_30days');
        Cache::forget('order_status_distribution');
        Cache::forget('payment_status_distribution');
        Cache::forget('order_status_summary');
        Cache::forget('dashboard_wallet_summary');
        Cache::forget('dashboard_referral_summary');

        return response()->json([
            'success' => true,
            'message' => 'Dashboard cache refreshed successfully.',
        ]);
    }
}
