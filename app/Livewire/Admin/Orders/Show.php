<?php

namespace App\Livewire\Admin\Orders;

use Livewire\Component;
use App\Models\Order;
use App\Services\ReferralService;

class Show extends Component
{
    public $order;
    public $orderId;
    public $status;
    public $payment_status;
    public $notes;
    public $tracking_number;
    public $shipping_carrier;

    protected $rules = [
        'status' => 'required|in:ordered,confirmed,picked_up,on_the_way,delivered,cancelled,failed',
        'payment_status' => 'required|in:pending,paid,failed,refunded',
        'notes' => 'nullable|string',
        'tracking_number' => 'nullable|string|max:100',
        'shipping_carrier' => 'nullable|string|max:100',
    ];

    public function mount($order)
    {
        abort_unless($order->canBeManagedBy(auth()->user()), 403);

        $this->order = $order;
        $this->orderId = $order->id;
        
        $this->status = $order->status;
        $this->payment_status = $order->payment_status;
        $this->notes = $order->notes;
        $this->tracking_number = $order->tracking_number;
        $this->shipping_carrier = $order->shipping_carrier;
    }

    public function update()
    {
        if (auth()->user()->isVendor()) {
            return;
        }

        $this->validate();

        $previousStatus = $this->order->status;
        $previousPaymentStatus = $this->order->payment_status;

        $this->order->update([
            'status' => $this->status,
            'payment_status' => $this->payment_status,
            'notes' => $this->notes,
            'tracking_number' => $this->tracking_number,
            'shipping_carrier' => $this->shipping_carrier,
        ]);

        // If status changed to delivered and payment wasn't marked paid, mark it as paid
        if ($this->status === 'delivered' && $this->payment_status !== 'paid') {
            $this->order->update(['payment_status' => 'paid']);
            $this->payment_status = 'paid';
        }

        if ($previousPaymentStatus !== 'paid' && $this->order->fresh()->payment_status === 'paid') {
            app(ReferralService::class)->rewardReferrerForFirstPaidOrder($this->order);
        }

        // Refresh the order from database to show updated values
        $this->order = $this->order->fresh();
        
        // Show success notification
        session()->flash('success', 'Order updated successfully!');
        
        $this->dispatch('notify', [
            'type' => 'success',
            'message' => 'Order updated successfully!'
        ]);
    }

    public function render()
    {
        $this->order->load(['user', 'items.product', 'items.vendor']);
        
        $statuses = [
            'ordered' => 'Ordered',
            'confirmed' => 'Confirmed',
            'picked_up' => 'Picked Up',
            'on_the_way' => 'On The Way',
            'delivered' => 'Delivered',
            'cancelled' => 'Cancelled',
            'failed' => 'Failed',
        ];

        $paymentStatuses = [
            'pending' => 'Pending',
            'paid' => 'Paid',
            'failed' => 'Failed',
            'refunded' => 'Refunded',
        ];

        // Group items by vendor for better display
        $itemsByVendor = $this->order->items->groupBy(function ($item) {
            return $item->vendor_id ?: 'unknown';
        });

        return view('livewire.admin.orders.show', [
            'order' => $this->order,
            'statuses' => $statuses,
            'paymentStatuses' => $paymentStatuses,
            'itemsByVendor' => $itemsByVendor,
        ]);
    }
}
