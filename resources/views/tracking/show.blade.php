@extends('layouts.app')

@section('title', 'Order Tracking - ' . $order->order_number)

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 via-white to-gray-50 py-8">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Back Button -->
        <a href="{{ route('tracking.index') }}" class="inline-flex items-center text-red-600 hover:text-red-700 mb-8 font-semibold transition-colors">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Back to Search
        </a>

        <!-- Order Header -->
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden mb-8 border border-gray-100">
            <div class="px-6 py-10 sm:px-10 sm:py-12 bg-gradient-to-r from-red-600 to-red-700 text-white relative overflow-hidden">
                <div class="absolute -top-20 -right-20 w-40 h-40 bg-red-500 rounded-full mix-blend-multiply filter blur-3xl opacity-20"></div>
                <div class="absolute -bottom-20 -left-20 w-40 h-40 bg-red-400 rounded-full mix-blend-multiply filter blur-3xl opacity-20"></div>
                
                <div class="relative z-10">
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <h1 class="text-4xl font-bold mb-2">📦 Order {{ $order->order_number }}</h1>
                            <p class="text-red-100">Placed on {{ $order->created_at->format('F d, Y') }}</p>
                        </div>
                        <div class="text-right">
                            <div class="text-5xl font-bold">₦{{ number_format($order->total, 2) }}</div>
                            <p class="text-red-100 text-sm mt-1">Total Amount</p>
                        </div>
                    </div>

                    <!-- Status Badge -->
                    <div class="flex items-center space-x-3 pt-6 border-t border-red-400">
                        @php
                            $statusColors = [
                                'pending' => 'bg-gray-100 text-gray-800',
                                'processing' => 'bg-blue-100 text-blue-800',
                                'packed' => 'bg-purple-100 text-purple-800',
                                'shipped' => 'bg-indigo-100 text-indigo-800',
                                'on_the_way' => 'bg-orange-100 text-orange-800',
                                'delivered' => 'bg-green-100 text-green-800',
                                'cancelled' => 'bg-red-200 text-red-900',
                                'failed' => 'bg-red-200 text-red-900',
                                'paid' => 'bg-green-100 text-green-800',
                            ];
                            $status = $order->status;
                            $colors = $statusColors[$status] ?? 'bg-gray-100 text-gray-800';
                        @endphp
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        <span class="inline-block px-4 py-2 rounded-full {{ $colors }} font-bold text-sm">
                            {{ ucfirst(str_replace('_', ' ', $order->status)) }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Order Info Grid -->
            <div class="px-6 py-10 sm:px-10 grid grid-cols-1 md:grid-cols-3 gap-8 border-b border-gray-200">
                <div>
                    <h3 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-3">👤 Customer</h3>
                    <p class="text-lg font-bold text-gray-900">{{ $order->shipping_first_name }} {{ $order->shipping_last_name }}</p>
                    <p class="text-gray-600 text-sm">{{ $order->shipping_email }}</p>
                    @if($order->shipping_phone)
                        <p class="text-gray-600 text-sm">{{ $order->shipping_phone }}</p>
                    @endif
                </div>

                <div>
                    <h3 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-3">📦 Tracking Number</h3>
                    @if($order->tracking_number)
                        <p class="text-lg font-mono font-bold text-red-600">{{ $order->tracking_number }}</p>
                        @if($order->shipping_carrier)
                            <p class="text-gray-600 text-sm">{{ $order->shipping_carrier }}</p>
                        @endif
                    @else
                        <p class="text-gray-500 italic text-sm">Not yet assigned</p>
                    @endif
                </div>

                <div>
                    <h3 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-3">💳 Payment Status</h3>
                    @php
                        $paymentStatusColors = [
                            'pending' => 'bg-yellow-100 text-yellow-800',
                            'paid' => 'bg-green-100 text-green-800',
                            'failed' => 'bg-red-100 text-red-800',
                            'refunded' => 'bg-blue-100 text-blue-800',
                        ];
                        $paymentColors = $paymentStatusColors[$order->payment_status] ?? 'bg-gray-100 text-gray-800';
                    @endphp
                    <span class="inline-block px-3 py-1 rounded-full {{ $paymentColors }} font-bold text-sm">
                        {{ ucfirst($order->payment_status) }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Status Timeline -->
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden mb-8 border border-gray-100">
            <div class="px-6 py-10 sm:px-10">
                <h2 class="text-3xl font-bold text-gray-900 mb-10">🚀 Delivery Progress</h2>

                @php
                    $statuses = [
                        'processing' => ['label' => 'Processing', 'icon' => '🔄'],
                        'packed' => ['label' => 'Packed', 'icon' => '📦'],
                        'shipped' => ['label' => 'Shipped', 'icon' => '🚚'],
                        'on_the_way' => ['label' => 'On The Way', 'icon' => '🛣️'],
                        'delivered' => ['label' => 'Delivered', 'icon' => '✅'],
                    ];
                    $statusOrder = array_keys($statuses);
                    $currentIndex = array_search($order->status, $statusOrder);
                @endphp

                <!-- Progress Bar -->
                <div class="mb-12">
                    <div class="flex justify-between mb-6">
                        @foreach($statusOrder as $i => $status)
                            <div class="text-center flex-1">
                                @php
                                    $isCompleted = $i <= $currentIndex;
                                    $isCurrent = $i === $currentIndex;
                                @endphp
                                <div class="flex justify-center mb-4">
                                    <div class="w-14 h-14 rounded-full flex items-center justify-center text-2xl transition-all {{ $isCompleted ? 'bg-red-100 text-red-600 shadow-lg ring-4 ring-red-50' : 'bg-gray-100 text-gray-400' }} {{ $isCurrent ? 'scale-110 ring-4 ring-red-200' : '' }}">
                                        {{ $statuses[$status]['icon'] }}
                                    </div>
                                </div>
                                <p class="font-bold text-sm {{ $isCompleted ? 'text-gray-900' : 'text-gray-500' }}">
                                    {{ $statuses[$status]['label'] }}
                                </p>
                                @php
                                    $statusHistory = $order->statusHistory()->where('new_status', $status)->latest()->first();
                                @endphp
                                @if($statusHistory)
                                    <p class="text-xs text-gray-600 mt-1">
                                        {{ $statusHistory->created_at->format('M d, Y') }}
                                    </p>
                                @endif
                            </div>
                            @if($i < count($statusOrder) - 1)
                                <div class="flex items-center px-2 mt-8">
                                    <div class="flex-1 h-2 rounded-full {{ $i < $currentIndex ? 'bg-gradient-to-r from-red-500 to-red-400' : 'bg-gray-200' }}"></div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>

                <!-- Detailed Timeline -->
                <div class="space-y-4 border-t border-gray-200 pt-10">
                    <h3 class="text-xl font-bold text-gray-900 mb-8">📋 Timeline Details</h3>
                    
                    @forelse($order->statusHistory()->orderBy('created_at', 'desc')->get() as $history)
                        <div class="flex items-start pb-8 border-b border-gray-200 last:border-b-0">
                            <div class="flex-shrink-0 mr-5">
                                <div class="flex items-center justify-center h-12 w-12 rounded-full
                                    @if($history->new_status === 'delivered') bg-green-100 text-green-600
                                    @elseif($history->new_status === 'cancelled' || $history->new_status === 'failed') bg-red-100 text-red-600
                                    @elseif($history->new_status === 'shipped' || $history->new_status === 'on_the_way') bg-blue-100 text-blue-600
                                    @else bg-yellow-100 text-yellow-600 @endif ring-4 ring-white shadow-md">
                                    @if($history->new_status === 'delivered')
                                        ✅
                                    @elseif($history->new_status === 'cancelled' || $history->new_status === 'failed')
                                        ❌
                                    @elseif($history->new_status === 'shipped' || $history->new_status === 'on_the_way')
                                        📦
                                    @else
                                        ⏳
                                    @endif
                                </div>
                            </div>
                            <div class="flex-1">
                                <p class="font-bold text-gray-900 text-lg">
                                    {{ ucfirst(str_replace('_', ' ', $history->new_status)) }}
                                </p>
                                <p class="text-sm text-gray-600 mt-1">
                                    {{ $history->created_at->format('F d, Y g:i A') }}
                                </p>
                                @if($history->notes)
                                    <div class="bg-blue-50 border-l-4 border-blue-400 p-4 mt-4 rounded">
                                        <p class="text-sm text-blue-800">💬 {{ $history->notes }}</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @empty
                        <p class="text-gray-600 text-center py-12 text-lg">No status history yet. Your order will be updated soon.</p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Order Items -->
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden mb-8 border border-gray-100">
            <div class="px-6 py-10 sm:px-10">
                <h2 class="text-3xl font-bold text-gray-900 mb-8">🛒 Order Items</h2>

                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="bg-gradient-to-r from-gray-50 to-gray-100 border-b-2 border-gray-200">
                                <th class="px-6 py-4 text-left text-sm font-bold text-gray-900">Product</th>
                                <th class="px-6 py-4 text-center text-sm font-bold text-gray-900">Qty</th>
                                <th class="px-6 py-4 text-right text-sm font-bold text-gray-900">Price</th>
                                <th class="px-6 py-4 text-right text-sm font-bold text-gray-900">Total</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse($order->items as $item)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-5">
                                        <div class="flex items-center space-x-4">
                                            @if($item->product && $item->product->featured_image)
                                                <img src="{{ asset('storage/' . $item->product->featured_image) }}" alt="{{ $item->product->name }}" class="h-12 w-12 rounded-lg object-cover shadow-md">
                                            @else
                                                <div class="h-12 w-12 rounded-lg bg-gradient-to-br from-gray-200 to-gray-300"></div>
                                            @endif
                                            <div>
                                                <p class="font-bold text-gray-900">
                                                    @if($item->product)
                                                        <a href="{{ route('product.show', $item->product->slug) }}" class="text-red-600 hover:text-red-700 transition-colors">
                                                            {{ $item->product->name }}
                                                        </a>
                                                    @else
                                                        {{ $item->product_name }}
                                                    @endif
                                                </p>
                                                @if($item->product?->vendor && $item->product->vendor->storefrontUrl())
                                                    <p class="mt-1 text-xs text-gray-600">
                                                        Store:
                                                        <a href="{{ $item->product->vendor->storefrontUrl() }}" class="font-semibold text-red-600 transition hover:text-red-700">
                                                            {{ $item->product->vendor->publicStoreName() }}
                                                        </a>
                                                    </p>
                                                @endif
                                                <p class="text-xs text-gray-600 mt-1">SKU: {{ $item->product_sku ?? 'N/A' }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-5 text-center">
                                        <span class="inline-block px-3 py-1 rounded-full bg-red-100 text-red-700 font-bold text-sm">{{ $item->quantity }}</span>
                                    </td>
                                    <td class="px-6 py-5 text-right font-semibold text-gray-900">₦{{ number_format($item->price, 2) }}</td>
                                    <td class="px-6 py-5 text-right font-bold text-gray-900">₦{{ number_format($item->total, 2) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-12 text-center text-gray-600">No items in this order</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Order Summary & Shipping -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            <!-- Order Summary -->
            <div class="bg-white rounded-2xl shadow-lg overflow-hidden border border-gray-100">
                <div class="px-6 py-10 sm:px-10">
                    <h2 class="text-2xl font-bold text-gray-900 mb-8">💰 Order Summary</h2>

                    <div class="space-y-4">
                        <div class="flex justify-between items-center pb-4 border-b border-gray-200">
                            <span class="text-gray-700 font-medium">Subtotal:</span>
                            <span class="font-bold text-gray-900">₦{{ number_format($order->subtotal, 2) }}</span>
                        </div>
                        @if($order->tax > 0)
                            <div class="flex justify-between items-center pb-4 border-b border-gray-200">
                                <span class="text-gray-700 font-medium">Platform Service Fee:</span>
                                <span class="font-bold text-gray-900">₦{{ number_format($order->tax, 2) }}</span>
                            </div>
                        @endif
                        @if($order->shipping > 0)
                            <div class="flex justify-between items-center pb-4 border-b border-gray-200">
                                <span class="text-gray-700 font-medium">Delivery Fee:</span>
                                <span class="font-bold text-gray-900">₦{{ number_format($order->shipping, 2) }}</span>
                            </div>
                        @endif
                        <div class="flex justify-between items-center pt-4 bg-gradient-to-r from-red-50 to-orange-50 px-4 py-3 rounded-lg">
                            <span class="text-lg font-bold text-gray-900">Total:</span>
                            <span class="text-2xl font-bold text-red-600">₦{{ number_format($order->total, 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Shipping Address -->
            <div class="bg-white rounded-2xl shadow-lg overflow-hidden border border-gray-100">
                <div class="px-6 py-10 sm:px-10">
                    <h2 class="text-2xl font-bold text-gray-900 mb-8">📍 Shipping Address</h2>

                    <div class="bg-gradient-to-br from-red-50 to-orange-50 rounded-xl p-6 border border-red-100">
                        <p class="font-bold text-gray-900 mb-3 text-lg">
                            {{ $order->shipping_first_name }} {{ $order->shipping_last_name }}
                        </p>
                        <div class="space-y-2 text-gray-700 text-sm">
                            <p>{{ $order->shipping_address }}</p>
                            <p>{{ $order->shipping_city }}, {{ $order->shipping_state }} {{ $order->shipping_postal_code }}</p>
                            <p>{{ $order->shipping_country }}</p>
                            @if($order->shipping_phone)
                                <p class="pt-2 border-t border-red-200 mt-2 font-semibold">📞 {{ $order->shipping_phone }}</p>
                            @endif
                            @if($order->shipping_email)
                                <p class="font-semibold">📧 {{ $order->shipping_email }}</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Contact Support -->
        <div class="bg-gradient-to-r from-red-600 to-red-700 rounded-2xl px-8 py-12 text-center text-white shadow-xl border border-red-500">
            <h3 class="text-2xl font-bold mb-3">🆘 Need Help?</h3>
            <p class="text-red-100 mb-6 max-w-xl mx-auto">Have questions about your order or delivery? Our support team is here to help!</p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ route('contact') }}" class="inline-flex items-center px-6 py-3 bg-white text-red-600 font-bold rounded-lg hover:bg-red-50 transition-all shadow-lg">
                    📝 Contact Support
                </a>
                <a href="{{ route('cart') }}" class="inline-flex items-center px-6 py-3 border-2 border-white text-white font-bold rounded-lg hover:bg-white/10 transition-all">
                    🛍️ Continue Shopping
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
