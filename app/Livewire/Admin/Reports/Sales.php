<?php

namespace App\Livewire\Admin\Reports;

use Livewire\Component;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\DB;

class Sales extends Component
{
    public $dateRange = 'last_30_days';
    public $startDate;
    public $endDate;
    public $period = 'daily';
    
    protected $listeners = ['refreshSalesReport' => '$refresh'];

    public function mount()
    {
        $this->setDefaultDates();
    }

    private function setDefaultDates()
    {
        $today = now();
        
        switch ($this->dateRange) {
            case 'today':
                $this->startDate = $today->format('Y-m-d');
                $this->endDate = $today->format('Y-m-d');
                break;
            case 'yesterday':
                $yesterday = $today->subDay();
                $this->startDate = $yesterday->format('Y-m-d');
                $this->endDate = $yesterday->format('Y-m-d');
                break;
            case 'last_7_days':
                $this->startDate = $today->subDays(7)->format('Y-m-d');
                $this->endDate = $today->format('Y-m-d');
                break;
            case 'last_30_days':
                $this->startDate = $today->subDays(30)->format('Y-m-d');
                $this->endDate = $today->format('Y-m-d');
                break;
            case 'this_month':
                $this->startDate = $today->startOfMonth()->format('Y-m-d');
                $this->endDate = $today->format('Y-m-d');
                break;
            case 'last_month':
                $lastMonth = $today->subMonth();
                $this->startDate = $lastMonth->startOfMonth()->format('Y-m-d');
                $this->endDate = $lastMonth->endOfMonth()->format('Y-m-d');
                break;
            case 'this_year':
                $this->startDate = $today->startOfYear()->format('Y-m-d');
                $this->endDate = $today->format('Y-m-d');
                break;
            default:
                if (!$this->startDate || !$this->endDate) {
                    $this->startDate = $today->subDays(30)->format('Y-m-d');
                    $this->endDate = $today->format('Y-m-d');
                }
                break;
        }
    }

    public function updatedDateRange()
    {
        $this->setDefaultDates();
        $this->dispatch('refreshSalesReport');
    }

    public function updatedStartDate()
    {
        $this->dateRange = 'custom';
        $this->dispatch('refreshSalesReport');
    }

    public function updatedEndDate()
    {
        $this->dateRange = 'custom';
        $this->dispatch('refreshSalesReport');
    }

    public function updatedPeriod()
    {
        $this->dispatch('refreshSalesReport');
    }

    public function getStats()
    {
        $query = Order::where('payment_status', 'paid')
            ->whereBetween('created_at', [$this->startDate . ' 00:00:00', $this->endDate . ' 23:59:59']);

        $totalSales = $query->sum('total');
        $totalOrders = $query->count();
        $avgOrderValue = $totalOrders > 0 ? $totalSales / $totalOrders : 0;

        // Previous period comparison
        $previousStartDate = date('Y-m-d', strtotime($this->startDate . ' -' . $this->getPeriodDays() . ' days'));
        $previousEndDate = date('Y-m-d', strtotime($this->endDate . ' -' . $this->getPeriodDays() . ' days'));
        
        $previousQuery = Order::where('payment_status', 'paid')
            ->whereBetween('created_at', [$previousStartDate . ' 00:00:00', $previousEndDate . ' 23:59:59']);

        $previousSales = $previousQuery->sum('total');
        $previousOrders = $previousQuery->count();
        $previousAvgOrderValue = $previousOrders > 0 ? $previousSales / $previousOrders : 0;

        // Calculate growth percentages
        $salesGrowth = $previousSales > 0 ? (($totalSales - $previousSales) / $previousSales) * 100 : 0;
        $ordersGrowth = $previousOrders > 0 ? (($totalOrders - $previousOrders) / $previousOrders) * 100 : 0;
        $avgOrderGrowth = $previousAvgOrderValue > 0 ? (($avgOrderValue - $previousAvgOrderValue) / $previousAvgOrderValue) * 100 : 0;

        return [
            'total_sales' => $totalSales,
            'total_orders' => $totalOrders,
            'avg_order_value' => $avgOrderValue,
            'sales_growth' => $salesGrowth,
            'orders_growth' => $ordersGrowth,
            'avg_order_growth' => $avgOrderGrowth,
        ];
    }

    private function getPeriodDays()
    {
        $start = strtotime($this->startDate);
        $end = strtotime($this->endDate);
        return ceil(($end - $start) / (60 * 60 * 24));
    }

    public function getSalesChartData()
    {
        $query = Order::where('payment_status', 'paid')
            ->whereBetween('created_at', [$this->startDate . ' 00:00:00', $this->endDate . ' 23:59:59']);

        if ($this->period === 'daily') {
            $data = $query->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(total) as total_sales'),
                DB::raw('COUNT(*) as order_count')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();

            $labels = $data->pluck('date')->map(function($date) {
                return date('M d', strtotime($date));
            })->toArray();

            $salesData = $data->pluck('total_sales')->toArray();
            $ordersData = $data->pluck('order_count')->toArray();

        } elseif ($this->period === 'weekly') {
            $data = $query->select(
                DB::raw('YEARWEEK(created_at, 1) as week'),
                DB::raw('MIN(DATE(created_at)) as week_start'),
                DB::raw('SUM(total) as total_sales'),
                DB::raw('COUNT(*) as order_count')
            )
            ->groupBy('week')
            ->orderBy('week')
            ->get();

            $labels = $data->pluck('week_start')->map(function($date) {
                return 'Week of ' . date('M d', strtotime($date));
            })->toArray();

            $salesData = $data->pluck('total_sales')->toArray();
            $ordersData = $data->pluck('order_count')->toArray();

        } else { // monthly
            $data = $query->select(
                DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
                DB::raw('DATE_FORMAT(created_at, "%b %Y") as month_label'),
                DB::raw('SUM(total) as total_sales'),
                DB::raw('COUNT(*) as order_count')
            )
            ->groupBy('month', 'month_label')
            ->orderBy('month')
            ->get();

            $labels = $data->pluck('month_label')->toArray();
            $salesData = $data->pluck('total_sales')->toArray();
            $ordersData = $data->pluck('order_count')->toArray();
        }

        return [
            'labels' => $labels,
            'sales' => $salesData,
            'orders' => $ordersData,
        ];
    }

    public function getPaymentMethodData()
    {
        $data = Order::where('payment_status', 'paid')
            ->whereBetween('created_at', [$this->startDate . ' 00:00:00', $this->endDate . ' 23:59:59'])
            ->select('payment_method', DB::raw('SUM(total) as total'))
            ->groupBy('payment_method')
            ->get();

        $total = $data->sum('total');

        return $data->map(function($item) use ($total) {
            $percentage = $total > 0 ? ($item->total / $total) * 100 : 0;
            return [
                'method' => ucfirst($item->payment_method ?: 'Unknown'),
                'amount' => $item->total,
                'percentage' => $percentage,
            ];
        })->toArray();
    }

    public function getRecentOrders()
    {
        return Order::with(['user', 'items'])
            ->whereBetween('created_at', [$this->startDate . ' 00:00:00', $this->endDate . ' 23:59:59'])
            ->latest()
            ->take(10)
            ->get();
    }

    public function render()
    {
        $stats = $this->getStats();
        $chartData = $this->getSalesChartData();
        $paymentMethods = $this->getPaymentMethodData();
        $recentOrders = $this->getRecentOrders();

        return view('livewire.admin.reports.sales', [
            'stats' => $stats,
            'chartData' => $chartData,
            'paymentMethods' => $paymentMethods,
            'recentOrders' => $recentOrders,
        ]);
    }
}