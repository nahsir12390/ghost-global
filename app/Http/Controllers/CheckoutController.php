<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCheckoutRequest;
use App\Models\Order;
use App\Services\CheckoutService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CheckoutController extends Controller
{
    public function index(): View|RedirectResponse
    {
        if (empty(session('cart', []))) {
            return redirect()->route('cart')->with('error', 'Your cart is empty.');
        }

        return view('checkout');
    }

    public function store(StoreCheckoutRequest $request, CheckoutService $checkout): RedirectResponse
    {
        $order = $checkout->place($request->validated());

        return redirect()->route('payment.index', $order);
    }

    public function success(Order $order): View
    {
        abort_unless($order->user_id === auth()->id(), 403);
        $order->loadMissing('items.product');

        return view('payment.success', compact('order'));
    }
}
