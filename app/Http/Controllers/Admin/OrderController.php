<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Services\ReferralService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $orders = Order::visibleTo(request()->user())
            ->with(['user', 'items' => function ($query) {
                if (request()->user()->isVendor()) {
                    $query->where('vendor_id', request()->user()->id);
                }
            }])
            ->latest()
            ->paginate(20);

        // Stats
        $totalOrders = Order::visibleTo(request()->user())->count();

        if (request()->user()->isVendor()) {
            $totalSales = OrderItem::where('vendor_id', request()->user()->id)
                ->whereHas('order', fn ($query) => $query->where('payment_status', 'paid'))
                ->sum('total');
        } else {
            // Admin and staff see total sales across all orders
            $totalSales = Order::where('payment_status', 'paid')->sum('total');
        }

        $pendingOrders = Order::visibleTo(request()->user())->where('status', 'pending')->count();
        $todayOrders = Order::visibleTo(request()->user())->whereDate('created_at', today())->count();

        return view('admin.orders.index', compact('orders', 'totalOrders', 'totalSales', 'pendingOrders', 'todayOrders'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Order $order)
    {
        abort_unless($order->canBeManagedBy(request()->user()), 403);

        $order->load(['user', 'items.product']);

        // Calculate totals
        $subtotal = $order->items->sum('total');
        $shipping = $order->shipping;
        $tax = $order->tax;
        $total = $order->total;

        return view('admin.orders.show', compact('order', 'subtotal', 'shipping', 'tax', 'total'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Order $order)
    {
        abort_if(request()->user()->isVendor(), 403);
        abort_unless($order->canBeManagedBy(request()->user()), 403);

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

        $order->load(['user', 'items.product']);

        return view('admin.orders.edit', compact('order', 'statuses', 'paymentStatuses'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Order $order)
    {
        abort_if($request->user()->isVendor(), 403);
        abort_unless($order->canBeManagedBy($request->user()), 403);

        $validated = $request->validate([
            'status' => 'required|in:ordered,confirmed,picked_up,on_the_way,delivered,cancelled,failed',
            'payment_status' => 'required|in:pending,paid,failed,refunded',
            'notes' => 'nullable|string',
            'tracking_number' => 'nullable|string|max:100',
            'shipping_carrier' => 'nullable|string|max:100',
        ]);

        $previousStatus = $order->status;
        $previousPaymentStatus = $order->payment_status;
        $order->update($validated);

        if ($previousPaymentStatus !== 'paid' && $order->fresh()->payment_status === 'paid') {
            app(ReferralService::class)->rewardReferrerForFirstPaidOrder($order);
        }

        return redirect()->route('admin.orders.show', $order)
            ->with('success', 'Order updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Order $order)
    {
        abort_if(request()->user()->isVendor(), 403);
        abort_unless($order->canBeManagedBy(request()->user()), 403);

        // Use transaction to ensure data consistency
        DB::transaction(function () use ($order) {
            // Delete order items first
            $order->items()->delete();
            // Then delete the order
            $order->delete();
        });

        return redirect()->route('admin.orders.index')
            ->with('success', 'Order deleted successfully.');
    }

    /**
     * Update order status via AJAX.
     */
    public function updateStatus(Request $request, Order $order)
    {
        abort_if($request->user()->isVendor(), 403);
        abort_unless($order->canBeManagedBy($request->user()), 403);

        $request->validate([
            'status' => 'required|in:ordered,confirmed,picked_up,on_the_way,delivered,cancelled,failed',
            'notes' => 'nullable|string|max:500',
        ]);

        $previousStatus = $order->status;
        $newStatus = $request->status;

        // Update order status
        $order->update(['status' => $newStatus]);

        // Add note to status history if provided
        if ($request->notes) {
            \App\Models\OrderStatusHistory::create([
                'order_id' => $order->id,
                'old_status' => $previousStatus,
                'new_status' => $newStatus,
                'notes' => $request->notes,
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Order status updated successfully and customer notified.',
            'status' => $newStatus,
        ]);
    }

    /**
     * Update payment status via AJAX.
     */
    public function updatePaymentStatus(Request $request, Order $order)
    {
        abort_if($request->user()->isVendor(), 403);
        abort_unless($order->canBeManagedBy($request->user()), 403);

        $request->validate([
            'payment_status' => 'required|in:pending,paid,failed,refunded',
        ]);

        $previousPaymentStatus = $order->payment_status;
        $order->update(['payment_status' => $request->payment_status]);

        if ($previousPaymentStatus !== 'paid' && $order->fresh()->payment_status === 'paid') {
            app(ReferralService::class)->rewardReferrerForFirstPaidOrder($order);
        }

        return response()->json([
            'success' => true,
            'message' => 'Payment status updated successfully.',
            'payment_status' => $request->payment_status,
        ]);
    }

    /**
     * View for printing invoice.
     */
    public function invoice(Order $order)
    {
        abort_unless($order->canBeManagedBy(request()->user()), 403);
        $order->load(['user', 'items.product']);

        return view('admin.orders.invoice', compact('order'));
    }

    /**
     * Download invoice as PDF.
     */
    public function downloadInvoice(Order $order)
    {
        abort_unless($order->canBeManagedBy(request()->user()), 403);
        $order->load(['user', 'items.product']);

        // For now, we'll return a view. You can implement PDF generation later.
        return view('admin.orders.invoice-pdf', compact('order'));

        // For actual PDF generation, you can use:
        // $pdf = PDF::loadView('admin.orders.invoice-pdf', compact('order'));
        // return $pdf->download('invoice-' . $order->order_number . '.pdf');
    }

    /**
     * Filter orders by status.
     */
    public function filter(Request $request)
    {
        $status = $request->input('status');
        $paymentStatus = $request->input('payment_status');
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');

        $query = Order::visibleTo($request->user())->with(['user', 'items' => function ($itemQuery) use ($request) {
            if ($request->user()->isVendor()) {
                $itemQuery->where('vendor_id', $request->user()->id);
            }
        }]);

        if ($status) {
            $query->where('status', $status);
        }

        if ($paymentStatus) {
            $query->where('payment_status', $paymentStatus);
        }

        if ($dateFrom) {
            $query->whereDate('created_at', '>=', $dateFrom);
        }

        if ($dateTo) {
            $query->whereDate('created_at', '<=', $dateTo);
        }

        $orders = $query->latest()->paginate(20);

        return view('admin.orders.index', compact('orders'));
    }
}
