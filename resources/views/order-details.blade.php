@extends('layouts.app')

@section('title', 'Order Details - ' . $order->order_number)

@section('content')
@php
    $statusTone = match ($order->status) {
        'pending', 'ordered' => 'bg-yellow-100 text-yellow-800',
        'confirmed', 'processing' => 'bg-blue-100 text-blue-800',
        'picked_up', 'shipped', 'on_the_way' => 'bg-indigo-100 text-indigo-800',
        'delivered', 'completed' => 'bg-green-100 text-green-800',
        'cancelled', 'failed' => 'bg-red-100 text-red-800',
        default => 'bg-gray-100 text-gray-800',
    };

    $paymentTone = match ($order->payment_status) {
        'paid' => 'bg-green-100 text-green-800',
        'pending' => 'bg-yellow-100 text-yellow-800',
        default => 'bg-red-100 text-red-800',
    };

    $progressStatuses = ['ordered', 'confirmed', 'picked_up', 'on_the_way', 'delivered'];
    $progressLabels = [
        'ordered' => 'Ordered',
        'confirmed' => 'Confirmed',
        'picked_up' => 'Picked Up',
        'on_the_way' => 'On The Way',
        'delivered' => 'Delivered',
    ];
    $currentIndex = array_search($order->status, $progressStatuses, true);
    $currentIndex = $currentIndex === false ? -1 : $currentIndex;
    $progressPercent = $currentIndex >= 0 ? ($currentIndex / (count($progressStatuses) - 1)) * 100 : 0;
@endphp

<div class="max-w-4xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
    <div class="overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-gray-100">
        @if(session('success'))
            <div class="mx-6 mt-6 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="mx-6 mt-6 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                {{ session('error') }}
            </div>
        @endif

        <div class="border-b border-gray-100 bg-gray-50 px-6 py-5">
            <div class="flex flex-col gap-4 md:flex-row md:items-start md:justify-between">
                <div>
                    <p class="text-sm font-medium uppercase tracking-[0.2em] text-gray-500">Order Details</p>
                    <h1 class="mt-2 text-2xl font-bold text-gray-900">Order #{{ $order->order_number }}</h1>
                    <p class="mt-1 text-sm text-gray-600">Placed on {{ $order->created_at->format('F d, Y \a\t g:i A') }}</p>
                </div>

                <div class="flex flex-wrap gap-3">
                    <span class="inline-flex items-center rounded-full px-3 py-1 text-sm font-medium {{ $statusTone }}">
                        {{ ucfirst(str_replace('_', ' ', $order->status)) }}
                    </span>
                    <span class="inline-flex items-center rounded-full px-3 py-1 text-sm font-medium {{ $paymentTone }}">
                        Payment: {{ ucfirst($order->payment_status) }}
                    </span>
                </div>
            </div>
        </div>

        <div class="px-6 py-6">
            <div class="mb-8 rounded-2xl border border-gray-100 bg-white p-5">
                <h2 class="text-lg font-semibold text-gray-900">Order Progress</h2>
                <div class="relative mt-6">
                    <div class="absolute left-0 right-0 top-5 h-1 rounded-full bg-gray-200"></div>
                    <div class="absolute left-0 top-5 h-1 rounded-full bg-green-500" style="width: {{ $progressPercent }}%"></div>

                    <div class="relative z-10 flex justify-between gap-2">
                        @foreach($progressStatuses as $index => $status)
                            @php
                                $isCompleted = $currentIndex >= $index;
                                $isCurrent = $currentIndex === $index;
                            @endphp
                            <div class="flex min-w-0 flex-1 flex-col items-center">
                                <div class="flex h-10 w-10 items-center justify-center rounded-full border-4 text-xs font-bold transition-all duration-300 {{ $isCompleted ? 'border-green-500 bg-green-50 text-green-700' : 'border-gray-300 bg-white text-gray-500' }}">
                                    {{ substr($progressLabels[$status], 0, 1) }}
                                </div>
                                <p class="mt-3 text-center text-xs font-medium text-gray-700">{{ $progressLabels[$status] }}</p>
                                @if($isCurrent)
                                    <p class="mt-1 text-xs font-bold text-green-600">CURRENT</p>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="mb-8">
                <h2 class="mb-4 text-lg font-semibold text-gray-900">Order Items</h2>
                <div class="space-y-4">
                    @foreach($order->items as $item)
                        <div class="flex items-center gap-4 rounded-2xl border border-gray-200 p-4">
                            <div class="h-16 w-16 flex-shrink-0 overflow-hidden rounded-xl bg-gray-100">
                                @if($item->product && $item->product->images && count($item->product->images) > 0)
                                    <img
                                        src="{{ asset('storage/' . $item->product->images[0]) }}"
                                        alt="{{ $item->product->name }}"
                                        class="h-full w-full object-cover"
                                    >
                                @else
                                    <div class="flex h-full w-full items-center justify-center text-gray-400">
                                        <svg class="h-8 w-8" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M4 5h16a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V6a1 1 0 0 1 1-1zM4 7v10h16V7H4zm8 2l5 4H7l5-4z"/>
                                        </svg>
                                    </div>
                                @endif
                            </div>

                            <div class="min-w-0 flex-1">
                                <h3 class="text-sm font-semibold text-gray-900">
                                    @if($item->product)
                                        <a href="{{ route('product.show', $item->product->slug) }}" class="hover:text-red-600">
                                            {{ $item->product->name }}
                                        </a>
                                    @else
                                        {{ $item->product_name }}
                                    @endif
                                </h3>
                                <p class="mt-1 text-sm text-gray-500">Quantity: {{ $item->quantity }}</p>
                                @if($item->product?->vendor && $item->product->vendor->storefrontUrl())
                                    <p class="mt-1 text-xs text-gray-500">
                                        Store:
                                        <a href="{{ $item->product->vendor->storefrontUrl() }}" class="font-medium text-red-600 transition hover:text-red-700">
                                            {{ $item->product->vendor->publicStoreName() }}
                                        </a>
                                    </p>
                                @endif
                            </div>

                            <div class="text-right">
                                <p class="text-sm font-semibold text-gray-900">N{{ number_format($item->price * $item->quantity, 2) }}</p>
                                <p class="text-sm text-gray-500">N{{ number_format($item->price, 2) }} each</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="grid gap-6 lg:grid-cols-[1.1fr_0.9fr]">
                <div class="space-y-6">
                    <div class="rounded-2xl border border-gray-100 bg-gray-50 p-5">
                        <h2 class="mb-4 text-lg font-semibold text-gray-900">Status Timeline</h2>
                        <div class="space-y-4">
                            @forelse($order->statusHistory()->orderBy('created_at', 'desc')->get() as $history)
                                <div class="flex items-start gap-4">
                                    <div class="mt-1 flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-full
                                        @if($history->new_status === 'delivered') bg-green-100 text-green-700
                                        @elseif(in_array($history->new_status, ['cancelled', 'failed'])) bg-red-100 text-red-700
                                        @elseif(in_array($history->new_status, ['picked_up', 'shipped', 'on_the_way'])) bg-blue-100 text-blue-700
                                        @else bg-yellow-100 text-yellow-700 @endif">
                                        {{ strtoupper(substr(str_replace('_', ' ', $history->new_status), 0, 1)) }}
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <div class="flex flex-col gap-1 sm:flex-row sm:items-center sm:justify-between">
                                            <p class="text-sm font-semibold text-gray-900">
                                                {{ ucfirst(str_replace('_', ' ', $history->new_status)) }}
                                            </p>
                                            <p class="text-sm text-gray-500">{{ $history->created_at->format('M d, Y g:i A') }}</p>
                                        </div>
                                        @if($history->old_status)
                                            <p class="mt-1 text-xs text-gray-600">
                                                Changed from <span class="font-medium">{{ ucfirst(str_replace('_', ' ', $history->old_status)) }}</span>
                                            </p>
                                        @endif
                                        @if($history->notes)
                                            <p class="mt-1 text-sm text-gray-600">{{ $history->notes }}</p>
                                        @endif
                                    </div>
                                </div>
                            @empty
                                <p class="text-sm text-gray-600">No status history available yet.</p>
                            @endforelse
                        </div>
                    </div>
                </div>

                <div class="space-y-6">
                    <div class="rounded-2xl border border-gray-100 bg-gray-50 p-5">
                        <h2 class="mb-4 text-lg font-semibold text-gray-900">Order Summary</h2>
                        <div class="space-y-3">
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Subtotal</span>
                                <span class="font-medium text-gray-900">N{{ number_format($order->subtotal, 2) }}</span>
                            </div>
                            @if($order->tax > 0)
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600">Platform Service Fee</span>
                                    <span class="font-medium text-gray-900">N{{ number_format($order->tax, 2) }}</span>
                                </div>
                            @endif
                            @if($order->shipping > 0)
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600">Shipping</span>
                                    <span class="font-medium text-gray-900">N{{ number_format($order->shipping, 2) }}</span>
                                </div>
                            @endif
                            <div class="flex justify-between border-t border-gray-200 pt-3 text-base font-semibold">
                                <span class="text-gray-900">Total</span>
                                <span class="text-gray-900">N{{ number_format($order->total, 2) }}</span>
                            </div>
                        </div>
                    </div>

                    @if($order->shipping_address)
                        <div class="rounded-2xl border border-gray-100 bg-gray-50 p-5">
                            <h2 class="mb-4 text-lg font-semibold text-gray-900">Shipping Information</h2>
                            <div class="space-y-1 text-sm text-gray-600">
                                <p class="font-semibold text-gray-900">{{ $order->shipping_first_name }} {{ $order->shipping_last_name }}</p>
                                <p>{{ $order->shipping_address }}</p>
                                <p>{{ $order->shipping_city }}, {{ $order->shipping_state }} {{ $order->shipping_postal_code }}</p>
                                <p>{{ $order->shipping_country }}</p>
                                @if($order->shipping_phone)
                                    <p>Phone: {{ $order->shipping_phone }}</p>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <div class="mt-8 flex flex-col gap-3 border-t border-gray-100 pt-6 sm:flex-row sm:items-center sm:justify-between">
                <a
                    href="{{ route('my.orders') }}"
                    class="inline-flex items-center justify-center rounded-xl border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-50"
                >
                    <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                    Back to Orders
                </a>

                <div class="flex flex-col gap-3 sm:flex-row">
                    @if($order->canBeCancelledByCustomer(auth()->user()))
                        <form method="POST" action="{{ route('my.orders.cancel', $order) }}">
                            @csrf
                            <button
                                type="submit"
                                onclick="return confirm('Cancel this order? This action cannot be undone from your account.')"
                                class="inline-flex w-full items-center justify-center rounded-xl border border-red-200 bg-red-50 px-4 py-2 text-sm font-medium text-red-700 transition hover:bg-red-100"
                            >
                                Cancel Order
                            </button>
                        </form>
                    @endif

                    <button
                        onclick="window.print()"
                        class="inline-flex items-center justify-center rounded-xl border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-50"
                    >
                        <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                        </svg>
                        Print Order
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
