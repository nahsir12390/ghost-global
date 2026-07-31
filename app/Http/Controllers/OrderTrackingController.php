<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class OrderTrackingController extends Controller
{
    /**
     * Show the order tracking search form
     */
    public function index()
    {
        return view('tracking.index');
    }

    /**
     * Search for order by order number and email/phone
     */
    public function search(Request $request)
    {
        $request->validate([
            'order_number' => 'required|string|max:50',
            'email_or_phone' => 'required|string|max:100',
        ], [
            'order_number.required' => 'Order number is required',
            'email_or_phone.required' => 'Email or phone number is required',
        ]);

        $orderNumber = $request->input('order_number');
        $emailOrPhone = $request->input('email_or_phone');

        Log::info('Order tracking search', [
            'order_number' => $orderNumber,
            'email_or_phone' => $emailOrPhone,
        ]);

        // Search for order
        $order = Order::where('order_number', $orderNumber)
            ->where(function ($query) use ($emailOrPhone) {
                $query->where('shipping_email', $emailOrPhone)
                      ->orWhere('shipping_phone', $emailOrPhone)
                      ->orWhere('billing_email', $emailOrPhone);
            })
            ->first();

        if (!$order) {
            Log::warning('Order not found for tracking', [
                'order_number' => $orderNumber,
                'email_or_phone' => $emailOrPhone,
            ]);

            return redirect()
                ->route('tracking.index')
                ->with('error', 'Order not found. Please check your order number and email/phone.');
        }

        // Load related data for display
        $order->load(['items.product.vendor', 'statusHistory']);

        Log::info('Order found for tracking', [
            'order_id' => $order->id,
            'order_number' => $order->order_number,
        ]);

        return view('tracking.show', compact('order'));
    }

    /**
     * Display order details for tracking
     */
    public function show(Order $order)
    {
        // This allows direct access to tracking if you have the order
        $order->load(['items.product.vendor', 'statusHistory']);
        
        return view('tracking.show', compact('order'));
    }
}
