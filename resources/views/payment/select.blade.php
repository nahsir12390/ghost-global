@extends('layouts.app')

@section('title', 'Select Payment Method')

@section('content')
<div class="min-h-screen bg-gray-50 py-12 px-4">
    <div class="mx-auto max-w-3xl">
        <div class="rounded-3xl bg-white p-8 shadow-sm">
            <div class="mb-8">
                <p class="text-sm font-semibold uppercase tracking-[0.25em] text-red-600">Payment</p>
                <h1 class="mt-3 text-3xl font-semibold text-gray-900">Choose how you want to pay</h1>
                <p class="mt-2 text-sm text-gray-500">Order #{{ $order->order_number }}</p>
            </div>

            <div class="grid gap-6 lg:grid-cols-[1.1fr_0.9fr]">
                <div class="space-y-4">
                    @foreach($paymentMethods as $method => $label)
                        <form action="{{ route('payment.process', $order) }}" method="POST" class="block">
                            @csrf
                            <input type="hidden" name="payment_method" value="{{ $method }}">
                            <button type="submit" class="group w-full rounded-3xl border border-gray-200 bg-white p-5 text-left transition hover:border-red-200 hover:bg-red-50/40">
                                <div class="flex items-start justify-between gap-4">
                                    <div class="flex items-start gap-4">
                                        <span class="inline-flex h-12 w-12 items-center justify-center rounded-2xl {{ $method === 'paystack' ? 'bg-blue-100 text-blue-700' : ($method === 'wallet' ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700') }}">
                                            @if($method === 'paystack')
                                                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-2.21 0-4 1.79-4 4v4h8v-4c0-2.21-1.79-4-4-4zm0 0V6m-6 4h12" />
                                                </svg>
                                            @elseif($method === 'wallet')
                                                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-2m0-6h2a2 2 0 012 2v2a2 2 0 01-2 2h-2m0-6h-4a2 2 0 100 4h4" />
                                                </svg>
                                            @else
                                                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                                                </svg>
                                            @endif
                                        </span>
                                        <div>
                                            <h2 class="text-lg font-semibold text-gray-900">{{ $label }}</h2>
                                            <p class="mt-1 text-sm text-gray-500">
                                                @if($method === 'paystack')
                                                    Pay securely with card, transfer, or supported Paystack channels.
                                                @elseif($method === 'wallet')
                                                    Use your stored wallet balance for a faster instant checkout.
                                                @else
                                                    Confirm the order now and pay when it is delivered.
                                                @endif
                                            </p>
                                        </div>
                                    </div>
                                    <svg class="mt-1 h-5 w-5 text-gray-400 transition group-hover:text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                    </svg>
                                </div>
                            </button>
                        </form>
                    @endforeach
                </div>

                <div class="rounded-3xl bg-slate-50 p-6">
                    <h3 class="text-lg font-semibold text-gray-900">Order Summary</h3>
                    <div class="mt-5 space-y-3 text-sm">
                        <div class="flex items-center justify-between">
                            <span class="text-gray-500">Subtotal</span>
                            <span class="font-medium text-gray-900">{{ \App\Helpers\SettingsHelper::currency($order->subtotal) }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-gray-500">Delivery</span>
                            <span class="font-medium text-gray-900">{{ \App\Helpers\SettingsHelper::currency($order->shipping) }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-gray-500">Platform Fee</span>
                            <span class="font-medium text-gray-900">{{ \App\Helpers\SettingsHelper::currency($order->tax) }}</span>
                        </div>
                        <div class="border-t border-slate-200 pt-3 flex items-center justify-between">
                            <span class="font-semibold text-gray-900">Total</span>
                            <span class="text-lg font-semibold text-gray-900">{{ \App\Helpers\SettingsHelper::currency($order->total) }}</span>
                        </div>
                    </div>

                    @if($order->shipping_address)
                        <div class="mt-6 rounded-2xl border border-slate-200 bg-white p-4">
                            <p class="text-sm font-semibold text-gray-900">Delivery Address</p>
                            <p class="mt-2 text-sm text-gray-500">
                                {{ $order->shipping_address }}<br>
                                {{ $order->shipping_city }}, {{ $order->shipping_state }} {{ $order->shipping_postal_code }}<br>
                                {{ $order->shipping_country }}
                            </p>
                        </div>
                    @endif
                </div>
            </div>

            <div class="mt-8">
                <a href="{{ route('checkout') }}" class="text-sm font-medium text-gray-500 transition hover:text-red-600">
                    ← Back to checkout
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
