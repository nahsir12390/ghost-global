<?php

namespace App\Livewire\Admin\Orders;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use App\Services\ReferralService;
use Illuminate\Support\Facades\DB;

class Index extends Component
{
    use WithPagination;

    public $search = '';
    public $statusFilter = '';
    public $paymentStatusFilter = '';
    public $vendorFilter = '';
    public $dateFrom = '';
    public $dateTo = '';
    public $sortField = 'created_at';
    public $sortDirection = 'desc';
    public $selectedOrders = [];
    public $selectAll = false;
    public $showDeleteModal = false;
    public $orderToDelete = null;

    protected $queryString = [
        'search' => ['except' => ''],
        'statusFilter' => ['except' => ''],
        'paymentStatusFilter' => ['except' => ''],
        'vendorFilter' => ['except' => ''],
        'dateFrom' => ['except' => ''],
        'dateTo' => ['except' => ''],
    ];

    protected $listeners = ['orderUpdated' => '$refresh'];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function updatingPaymentStatusFilter()
    {
        $this->resetPage();
    }

    public function updatingVendorFilter()
    {
        $this->resetPage();
    }

    public function updatingDateFrom()
    {
        $this->resetPage();
    }

    public function updatingDateTo()
    {
        $this->resetPage();
    }

    public function mount()
    {
        if (!$this->dateFrom && !$this->dateTo) {
            $this->dateFrom = now()->startOfMonth()->format('Y-m-d');
            $this->dateTo = now()->endOfMonth()->format('Y-m-d');
        }
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function toggleSelectAll()
    {
        if ($this->selectAll) {
            $this->selectedOrders = $this->getQuery()->pluck('id')->toArray();
        } else {
            $this->selectedOrders = [];
        }
    }

    public function updateOrderStatus($orderId, $status)
    {
        if (auth()->user()->isVendor()) {
            return;
        }

        $order = Order::visibleTo(auth()->user())->find($orderId);
        if ($order) {
            $previousPaymentStatus = $order->payment_status;
            $order->update(['status' => $status]);

            if ($status === 'delivered' && $order->payment_status !== 'paid') {
                $order->update(['payment_status' => 'paid']);
            }

            if ($previousPaymentStatus !== 'paid' && $order->fresh()->payment_status === 'paid') {
                app(ReferralService::class)->rewardReferrerForFirstPaidOrder($order);
            }

            $this->dispatch('orderUpdated');

            $this->dispatch('notify', [
                'type' => 'success',
                'message' => 'Order status updated successfully!'
            ]);
        }
    }

    public function updatePaymentStatus($orderId, $paymentStatus)
    {
        if (auth()->user()->isVendor()) {
            return;
        }

        $order = Order::visibleTo(auth()->user())->find($orderId);
        if ($order) {
            $previousPaymentStatus = $order->payment_status;
            $order->update(['payment_status' => $paymentStatus]);

            if ($previousPaymentStatus !== 'paid' && $order->fresh()->payment_status === 'paid') {
                app(ReferralService::class)->rewardReferrerForFirstPaidOrder($order);
            }

            $this->dispatch('orderUpdated');

            $this->dispatch('notify', [
                'type' => 'success',
                'message' => 'Payment status updated successfully!'
            ]);
        }
    }

    public function confirmDelete($orderId = null)
    {
        if (auth()->user()->isVendor()) {
            return;
        }

        $this->orderToDelete = $orderId
            ? Order::visibleTo(auth()->user())->find($orderId)
            : null;

        if ($orderId && !$this->orderToDelete) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'The selected order could not be found.'
            ]);
            return;
        }

        $this->showDeleteModal = true;
    }

    public function deleteOrder()
    {
        if (auth()->user()->isVendor()) {
            return;
        }

        if ($this->orderToDelete) {
            DB::transaction(function () {
                $this->orderToDelete->items()->delete();
                $this->orderToDelete->delete();
            });

            $this->dispatch('notify', [
                'type' => 'success',
                'message' => 'Order deleted successfully!'
            ]);
        } elseif (!empty($this->selectedOrders)) {
            $orders = Order::visibleTo(auth()->user())
                ->whereIn('id', $this->selectedOrders)
                ->get();

            DB::transaction(function () use ($orders) {
                foreach ($orders as $order) {
                    $order->items()->delete();
                    $order->delete();
                }
            });

            $this->dispatch('notify', [
                'type' => 'success',
                'message' => 'Selected orders deleted successfully!'
            ]);
        }

        $this->selectedOrders = [];
        $this->selectAll = false;
        $this->showDeleteModal = false;
        $this->orderToDelete = null;
        $this->resetPage();
    }

    public function resetFilters()
    {
        $this->search = '';
        $this->statusFilter = '';
        $this->paymentStatusFilter = '';
        $this->vendorFilter = '';
        $this->dateFrom = now()->startOfMonth()->format('Y-m-d');
        $this->dateTo = now()->endOfMonth()->format('Y-m-d');
        $this->resetPage();
    }

    public function getStats()
    {
        $query = $this->getBaseQuery();

        $stats = [
            'total' => $query->count(),
            'ordered' => (clone $query)->where('status', 'ordered')->count(),
            'confirmed' => (clone $query)->where('status', 'confirmed')->count(),
            'picked_up' => (clone $query)->where('status', 'picked_up')->count(),
            'on_the_way' => (clone $query)->where('status', 'on_the_way')->count(),
            'delivered' => (clone $query)->where('status', 'delivered')->count(),
        ];

        // Calculate total sales based on user role
        if (auth()->user()->isVendor()) {
            $stats['total_sales'] = OrderItem::where('vendor_id', auth()->id())
                ->whereHas('order', function ($orderQuery) {
                    $orderQuery
                        ->when($this->dateFrom, fn ($q) => $q->whereDate('created_at', '>=', $this->dateFrom))
                        ->when($this->dateTo, fn ($q) => $q->whereDate('created_at', '<=', $this->dateTo))
                        ->where('payment_status', 'paid');
                })
                ->sum('total');
        } else {
            $stats['total_sales'] = (clone $query)->where('payment_status', 'paid')->sum('total');
        }

        return $stats;
    }

    public function getVendors()
    {
        if (auth()->user()->isVendor()) {
            return collect();
        }

        return User::where('role', 'vendor')
            ->whereNotNull('store_name')
            ->orderBy('store_name')
            ->get(['id', 'name', 'store_name']);
    }

    private function getBaseQuery()
    {
        return Order::query()
            ->visibleTo(auth()->user())
            ->when($this->dateFrom, function ($query) {
                $query->whereDate('created_at', '>=', $this->dateFrom);
            })
            ->when($this->dateTo, function ($query) {
                $query->whereDate('created_at', '<=', $this->dateTo);
            });
    }

    public function getQuery()
    {
        $query = $this->getBaseQuery()
            ->with(['user', 'items' => function ($query) {
                if (auth()->user()->isVendor()) {
                    $query->where('vendor_id', auth()->id());
                }
                $query->with('vendor');
            }])
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('order_number', 'like', '%' . $this->search . '%')
                      ->orWhere('shipping_email', 'like', '%' . $this->search . '%')
                      ->orWhere('shipping_phone', 'like', '%' . $this->search . '%')
                      ->orWhereHas('user', function ($userQuery) {
                          $userQuery->where('name', 'like', '%' . $this->search . '%')
                                   ->orWhere('email', 'like', '%' . $this->search . '%');
                      })
                      ->orWhereHas('items.vendor', function ($vendorQuery) {
                          $vendorQuery->where('name', 'like', '%' . $this->search . '%')
                                     ->orWhere('store_name', 'like', '%' . $this->search . '%');
                      });
                });
            })
            ->when($this->statusFilter, function ($query) {
                $query->where('status', $this->statusFilter);
            })
            ->when($this->paymentStatusFilter, function ($query) {
                $query->where('payment_status', $this->paymentStatusFilter);
            })
            ->when($this->vendorFilter && !auth()->user()->isVendor(), function ($query) {
                $query->whereHas('items', function ($q) {
                    $q->where('vendor_id', $this->vendorFilter);
                });
            })
            ->orderBy($this->sortField, $this->sortDirection);

        return $query;
    }

    public function render()
    {
        $stats = $this->getStats();
        $vendors = $this->getVendors();
        $orders = $this->getQuery()->paginate(15);

        return view('livewire.admin.orders.index', [
            'orders' => $orders,
            'stats' => $stats,
            'vendors' => $vendors,
        ]);
    }
}
