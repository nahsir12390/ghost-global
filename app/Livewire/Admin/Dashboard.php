<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Models\Category;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class Dashboard extends Component
{
    public $stats = [];
    public $chartData = [];
    public $recentOrders = [];
    public $topProducts = [];
    public $recentCustomers = [];
    public $lowStockProducts = [];
    public $orderStatusSummary = [];
    public $pendingVerificationVendors = [];
    public $pendingBankVerifications = [];
    
    public $chartPeriod = '30days';
    public $salesData = [];
    public $ordersData = [];
    public $chartLabels = [];
    
    public $selectedPeriod = 'today';
    
    public $isLoading = false;
    public $refreshing = false;
    
    protected $listeners = [
        'refreshDashboard' => 'refreshData',
        'periodChanged' => 'changePeriod',
        'chartPeriodChanged' => 'changeChartPeriod'
    ];
    
    public function mount()
    {
        $this->loadData();
    }
    
    public function loadData()
    {
        $this->isLoading = true;
        
        $this->loadStats();
        $this->loadChartData();
        $this->loadRecentOrders();
        $this->loadTopProducts();
        $this->loadRecentCustomers();
        $this->loadLowStockProducts();
        $this->loadOrderStatusSummary();
        $this->loadVerificationQueues();
        
        $this->isLoading = false;
    }
    
    public function loadStats()
    {
        $today = now();
        $startDate = match($this->selectedPeriod) {
            'today' => $today->copy()->startOfDay(),
            'week' => $today->copy()->startOfWeek(),
            'month' => $today->copy()->startOfMonth(),
            'year' => $today->copy()->startOfYear(),
            default => $today->copy()->startOfDay(),
        };
        
        // Use cache for better performance
        $this->stats = Cache::remember("dashboard_stats_{$this->selectedPeriod}", now()->addMinutes(5), function () use ($startDate) {
            return [
                'total_orders' => Order::count(),
                'total_sales' => Order::where('payment_status', 'paid')->sum('total'),
                'total_products' => Product::count(),
                'total_customers' => User::where('is_admin', false)->count(),
                'pending_orders' => Order::where('status', 'pending')->count(),
                'processing_orders' => Order::where('status', 'processing')->count(),
                'today_orders' => Order::whereDate('created_at', today())->count(),
                'today_sales' => Order::whereDate('created_at', today())->where('payment_status', 'paid')->sum('total'),
                'period_orders' => Order::where('created_at', '>=', $startDate)->count(),
                'period_sales' => Order::where('created_at', '>=', $startDate)->where('payment_status', 'paid')->sum('total'),
                'out_of_stock' => Product::where('quantity', 0)->count(),
                'low_stock' => Product::where('quantity', '>', 0)->where('quantity', '<=', 10)->count(),
                'total_categories' => Category::count(),
                'featured_products' => Product::where('is_featured', true)->count(),
                'avg_order_value' => Order::where('payment_status', 'paid')->avg('total') ?? 0,
                'total_revenue' => Order::where('payment_status', 'paid')->sum('total'),
                'new_customers_today' => User::where('is_admin', false)
                    ->whereDate('created_at', today())
                    ->count(),
            ];
        });
    }
    
    public function loadChartData($period = null)
    {
        if ($period) {
            $this->chartPeriod = $period;
        }
        
        $days = match($this->chartPeriod) {
            '7days' => 7,
            '30days' => 30,
            '90days' => 90,
            '1year' => 365,
            default => 30,
        };
        
        $cacheKey = "sales_chart_{$this->chartPeriod}_" . now()->format('Y-m-d');
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
        
        $this->chartLabels = $salesData->pluck('date')->map(function ($date) {
            return date('M d', strtotime($date));
        })->toArray();
        
        $this->ordersData = $salesData->pluck('orders_count')->toArray();
        $this->salesData = $salesData->pluck('sales_total')->toArray();
    }
    
    public function loadRecentOrders()
    {
        $this->recentOrders = Order::with(['user', 'items.product'])
            ->latest()
            ->take(10)
            ->get();
    }
    
    public function loadTopProducts()
    {
        $cacheKey = 'top_products_30days_' . now()->format('Y-m-d');
        $this->topProducts = Cache::remember($cacheKey, now()->addHours(1), function () {
            return DB::table('order_items')
                ->select('products.id', 'products.name', 'products.price', 'products.images',
                         DB::raw('SUM(order_items.quantity) as total_quantity'),
                         DB::raw('SUM(order_items.total) as total_sales'))
                ->join('products', 'order_items.product_id', '=', 'products.id')
                ->join('orders', 'order_items.order_id', '=', 'orders.id')
                ->where('orders.created_at', '>=', now()->subDays(30))
                ->where('orders.payment_status', 'paid')
                ->groupBy('order_items.product_id', 'products.id', 'products.name', 'products.price', 'products.images')
                ->orderByDesc('total_quantity')
                ->limit(5)
                ->get()
                ->map(function ($product) {
                    $product->image_url = $product->images ? 
                        (is_array($product->images) ? $product->images[0] ?? null : json_decode($product->images, true)[0] ?? null) : 
                        null;
                    return $product;
                });
        });
    }
    
    public function loadRecentCustomers()
    {
        $this->recentCustomers = User::where('is_admin', false)
            ->latest()
            ->take(5)
            ->get();
    }
    
    public function loadLowStockProducts()
    {
        $this->lowStockProducts = Product::where('quantity', '>', 0)
            ->where('quantity', '<=', 10)
            ->with('category')
            ->orderBy('quantity')
            ->take(5)
            ->get();
    }
    
    public function loadOrderStatusSummary()
    {
        $this->orderStatusSummary = Cache::remember('order_status_summary', now()->addMinutes(10), function () {
            return Order::select('status', DB::raw('COUNT(*) as count'))
                ->groupBy('status')
                ->orderByDesc('count')
                ->get();
        });
    }

    public function loadVerificationQueues()
    {
        $this->pendingVerificationVendors = User::where('role', 'vendor')
            ->where('verification_status', 'pending')
            ->latest()
            ->take(5)
            ->get();

        $this->pendingBankVerifications = User::where('role', 'vendor')
            ->whereNotNull('bank_account_number')
            ->where('bank_verification_status', 'pending')
            ->latest()
            ->take(5)
            ->get();
    }
    
    public function updateChartPeriod($period)
    {
        $this->chartPeriod = $period;
        $this->loadChartData();
        $this->dispatch('chartUpdated', [
            'labels' => $this->chartLabels,
            'sales' => $this->salesData,
            'orders' => $this->ordersData
        ]);
    }
    
    public function changeChartPeriod($period)
    {
        $this->updateChartPeriod($period);
    }
    
    public function updatePeriod($period)
    {
        $this->selectedPeriod = $period;
        $this->loadStats();
    }
    
    public function changePeriod($period)
    {
        $this->updatePeriod($period);
    }
    
    public function refreshData()
    {
        $this->refreshing = true;
        
        // Clear relevant caches
        Cache::forget("dashboard_stats_{$this->selectedPeriod}");
        Cache::forget("sales_chart_{$this->chartPeriod}_" . now()->format('Y-m-d'));
        Cache::forget('top_products_30days_' . now()->format('Y-m-d'));
        Cache::forget('order_status_summary');
        
        $this->loadData();
        
        $this->refreshing = false;
        $this->dispatch('notify', [
            'type' => 'success',
            'message' => 'Dashboard data refreshed successfully!'
        ]);
    }
    
    public function exportReport($type)
    {
        // Implement export logic here
        $this->dispatch('notify', [
            'type' => 'info',
            'message' => 'Export feature coming soon!'
        ]);
    }
    
    public function render()
    {
        return view('livewire.admin.dashboard');
    }
}
