@extends('layouts.admin')

@section('title', 'Order Invoice - ' . $order->order_number)
@section('breadcrumb', 'Orders / Invoice')

@section('content')
<div class="max-w-4xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
    <!-- Invoice Header -->
    <div class="bg-white shadow-sm rounded-lg overflow-hidden">
        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Invoice</h1>
                    <p class="text-sm text-gray-600">Order #{{ $order->order_number }}</p>
                </div>
                <div class="text-right">
                    <p class="text-sm text-gray-600">Date: {{ $order->created_at->format('M d, Y') }}</p>
                    <p class="text-sm text-gray-600">Status: <span class="px-2 py-1 text-xs font-medium rounded-full
                        @if($order->status === 'completed') bg-green-100 text-green-800
                        @elseif($order->status === 'processing') bg-yellow-100 text-yellow-800
                        @elseif($order->status === 'pending') bg-blue-100 text-blue-800
                        @else bg-gray-100 text-gray-800 @endif">
                        {{ ucfirst($order->status) }}
                    </span></p>
                </div>
            </div>
        </div>

        <!-- Company & Customer Info -->
        <div class="px-6 py-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- From -->
                <div>
                    <h3 class="text-sm font-medium text-gray-900 mb-2">From</h3>
                    <div class="text-sm text-gray-600">
                        <p class="font-medium">{{ config('app.name') }}</p>
                        <p>{{ \App\Helpers\SettingsHelper::get('site_address', '123 Business St, City, Country') }}</p>
                        <p>{{ \App\Helpers\SettingsHelper::get('site_email', config('mail.from.address')) }}</p>
                        <p>{{ \App\Helpers\SettingsHelper::get('site_phone', '+1 234 567 8900') }}</p>
                    </div>
                </div>

                <!-- To -->
                <div>
                    <h3 class="text-sm font-medium text-gray-900 mb-2">To</h3>
                    <div class="text-sm text-gray-600">
                        <p class="font-medium">{{ $order->shipping_full_name }}</p>
                        <p>{{ $order->shipping_address }}</p>
                        <p>{{ $order->shipping_city }}, {{ $order->shipping_state }} {{ $order->shipping_postal_code }}</p>
                        <p>{{ $order->shipping_country }}</p>
                        <p>{{ $order->shipping_email }}</p>
                        <p>{{ $order->shipping_phone }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Order Items -->
        <div class="px-6 py-4">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Order Items</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantity</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($order->items as $item)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $item->product_name }}</div>
                                @if($item->product)
                                    <div class="text-sm text-gray-500">{{ $item->product->sku }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $item->quantity }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ \App\Helpers\SettingsHelper::currency($item->price) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ \App\Helpers\SettingsHelper::currency($item->total) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Order Summary -->
        <div class="px-6 py-4 bg-gray-50">
            <div class="flex justify-end">
                <div class="w-64">
                    <div class="space-y-2">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Subtotal:</span>
                            <span class="text-gray-900">{{ \App\Helpers\SettingsHelper::currency($order->subtotal) }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Platform Service Fee:</span>
                            <span class="text-gray-900">{{ \App\Helpers\SettingsHelper::currency($order->tax) }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Delivery Fee:</span>
                            <span class="text-gray-900">{{ \App\Helpers\SettingsHelper::currency($order->shipping) }}</span>
                        </div>
                        <div class="border-t border-gray-300 pt-2">
                            <div class="flex justify-between text-lg font-medium">
                                <span class="text-gray-900">Total:</span>
                                <span class="text-gray-900">{{ \App\Helpers\SettingsHelper::currency($order->total) }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Payment Info -->
        <div class="px-6 py-4 border-t border-gray-200">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h3 class="text-sm font-medium text-gray-900 mb-2">Payment Information</h3>
                    <div class="text-sm text-gray-600">
                        <p><span class="font-medium">Method:</span> {{ ucfirst(str_replace('_', ' ', $order->payment_method)) }}</p>
                        <p><span class="font-medium">Status:</span>
                            <span class="px-2 py-1 text-xs font-medium rounded-full
                                @if($order->payment_status === 'paid') bg-green-100 text-green-800
                                @elseif($order->payment_status === 'pending') bg-yellow-100 text-yellow-800
                                @else bg-gray-100 text-gray-800 @endif">
                                {{ ucfirst($order->payment_status) }}
                            </span>
                        </p>
                        @if($order->payment_reference)
                            <p><span class="font-medium">Reference:</span> {{ $order->payment_reference }}</p>
                        @endif
                    </div>
                </div>
                <div>
                    <h3 class="text-sm font-medium text-gray-900 mb-2">Shipping Information</h3>
                    <div class="text-sm text-gray-600">
                        <p><span class="font-medium">Carrier:</span> {{ $order->shipping_carrier ?: 'Standard Shipping' }}</p>
                        <p><span class="font-medium">Tracking:</span> {{ $order->tracking_number ?: 'Not available' }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Actions -->
    <div class="mt-6 flex justify-center space-x-4">
        <a href="{{ route('admin.orders.show', $order) }}"
           class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Back to Order
        </a>
        <button onclick="window.print()"
                class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
            </svg>
            Print Invoice
        </button>
    </div>
</div>

<style>
@media print {
    .no-print { display: none !important; }
    body { background: white !important; }
}
</style>
@endsection
