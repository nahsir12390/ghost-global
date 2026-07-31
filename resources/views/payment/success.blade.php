@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50 py-12 px-4">
    <div class="max-w-2xl mx-auto">
        <!-- Success Message -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden mb-8">
            <div class="bg-green-500 text-white p-8 text-center">
                <svg class="w-20 h-20 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                <h1 class="text-4xl font-bold mb-2">Payment Successful!</h1>
                <p class="text-green-100">Your order has been confirmed and payment received.</p>
            </div>
            
            <!-- Order Details -->
            <div class="p-8">
                <div class="mb-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">Order Confirmation</h2>
                    <div class="bg-gray-50 rounded-lg p-6">
                        <div class="grid grid-cols-2 gap-6">
                            <div>
                                <p class="text-gray-600 text-sm">Order Number</p>
                                <p class="text-2xl font-bold text-gray-900">{{ $order->order_number }}</p>
                            </div>
                            <div>
                                <p class="text-gray-600 text-sm">Order Date</p>
                                <p class="text-lg font-semibold text-gray-900">{{ $order->created_at->format('M d, Y') }}</p>
                            </div>
                            <div>
                                <p class="text-gray-600 text-sm">Payment Status</p>
                                <p class="text-lg font-semibold text-green-600">{{ ucfirst($order->payment_status) }}</p>
                            </div>
                            <div>
                                <p class="text-gray-600 text-sm">Order Status</p>
                                <p class="text-lg font-semibold text-blue-600">{{ ucfirst($order->status) }}</p>
                            </div>
                            @if($order->tracking_number)
                            <div>
                                <p class="text-gray-600 text-sm">Tracking Code</p>
                                <p class="text-lg font-bold text-indigo-600">{{ $order->tracking_number }}</p>
                            </div>
                            @endif
                            <div>
                                <p class="text-gray-600 text-sm">Total Amount</p>
                                <p class="text-lg font-semibold text-gray-900">₦{{ number_format($order->total, 2) }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Order Items -->
                <div class="mb-8">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Order Items</h3>
                    <div class="border rounded-lg overflow-hidden">
                        <table class="w-full">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Product</th>
                                    <th class="px-6 py-3 text-right text-sm font-semibold text-gray-900">Quantity</th>
                                    <th class="px-6 py-3 text-right text-sm font-semibold text-gray-900">Price</th>
                                    <th class="px-6 py-3 text-right text-sm font-semibold text-gray-900">Total</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y">
                                @foreach($order->items as $item)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 text-sm text-gray-900">{{ $item->product_name }}</td>
                                    <td class="px-6 py-4 text-right text-sm text-gray-600">{{ $item->quantity }}</td>
                                    <td class="px-6 py-4 text-right text-sm text-gray-600">₦{{ number_format($item->price, 2) }}</td>
                                    <td class="px-6 py-4 text-right text-sm font-semibold text-gray-900">₦{{ number_format($item->total, 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Order Summary -->
                <div class="mb-8">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Order Summary</h3>
                    <div class="bg-gray-50 rounded-lg p-6 space-y-3">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Subtotal:</span>
                            <span class="font-semibold">₦{{ number_format($order->subtotal, 2) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Delivery Fee:</span>
                            <span class="font-semibold">₦{{ number_format($order->shipping, 2) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Platform Service Fee:</span>
                            <span class="font-semibold">₦{{ number_format($order->tax, 2) }}</span>
                        </div>
                        <hr class="my-4">
                        <div class="flex justify-between">
                            <span class="text-lg font-bold text-gray-900">Total:</span>
                            <span class="text-lg font-bold text-green-600">₦{{ number_format($order->total, 2) }}</span>
                        </div>
                    </div>
                </div>

                <!-- Delivery Address -->
                <div class="mb-8">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Delivery Address</h3>
                    <div class="bg-gray-50 rounded-lg p-6">
                        <p class="text-gray-900 mb-2">
                            <strong>{{ Auth::user()->name }}</strong><br>
                            {{ $order->shipping_address }}<br>
                            {{ $order->shipping_city }}, {{ $order->shipping_state }} {{ $order->shipping_postal_code }}<br>
                            {{ $order->shipping_country }}<br>
                            {{ Auth::user()->phone ?? 'N/A' }}
                        </p>
                    </div>
                </div>

                <!-- Info Message -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-6 mb-8">
                    <h3 class="font-semibold text-blue-900 mb-2">What Happens Next?</h3>
                    <ul class="text-sm text-blue-800 space-y-2">
                        <li>✓ A confirmation email has been sent to {{ $order->shipping_email }}</li>
                        <li>✓ Your order will be processed shortly</li>
                        @if($order->tracking_number)
                        <li>✓ Your tracking code is: <strong>{{ $order->tracking_number }}</strong></li>
                        @else
                        <li>✓ You will receive a tracking number once your order ships</li>
                        @endif
                        <li>✓ Expected delivery: {{ \App\Helpers\SettingsHelper::estimatedDeliveryDays() }} business days</li>
                    </ul>
                </div>

                <!-- Action Buttons -->
                <div class="flex gap-4">
                    <a href="{{ route('shop') }}" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-4 rounded-lg text-center transition">
                        Continue Shopping
                    </a>
                    <a href="{{ route('my.orders.show', $order) }}" class="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-900 font-bold py-3 px-4 rounded-lg text-center transition">
                        View Order Details
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
