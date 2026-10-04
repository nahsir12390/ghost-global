@extends('layouts.app')

@section('title', 'Select Payment Method - ' . config('app.name'))

@section('content')
<div class="min-h-screen bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-2xl mx-auto">
        <!-- Header -->
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Payment</h1>
            <p class="text-gray-600">Order #{{ $order->order_number }}</p>
        </div>

        <!-- Order Summary -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Order Summary</h2>
            
            <div class="space-y-3 mb-6">
                <div class="flex justify-between">
                    <span class="text-gray-600">Subtotal</span>
                    <span>{{ \App\Helpers\SettingsHelper::currency($order->subtotal) }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Platform Service Fee</span>
                    <span>{{ \App\Helpers\SettingsHelper::currency($order->tax) }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Delivery Fee</span>
                    <span>{{ \App\Helpers\SettingsHelper::currency($order->shipping) }}</span>
                </div>
                <div class="border-t border-gray-200 pt-3">
                    <div class="flex justify-between">
                        <span class="font-semibold">Total</span>
                        <span class="font-bold text-lg text-red-600">{{ \App\Helpers\SettingsHelper::currency($order->total) }}</span>
                    </div>
                </div>
            </div>

            <!-- Shipping Address -->
            <div class="bg-gray-50 rounded-lg p-4">
                <h3 class="font-semibold text-gray-900 mb-2">Shipping To:</h3>
                <p class="text-sm text-gray-600">
                    {{ $order->shipping_first_name }} {{ $order->shipping_last_name }}<br>
                    {{ $order->shipping_address }}<br>
                    {{ $order->shipping_city }}, {{ $order->shipping_state }} {{ $order->shipping_postal_code }}<br>
                    {{ $order->shipping_country }}
                </p>
            </div>
        </div>

        <!-- Payment Methods -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Select Payment Method</h2>
            
            <div class="space-y-4">
                @if(isset($paymentMethods['paystack']))
                <form action="{{ route('payment.process', $order) }}" method="POST" class="block">
                    @csrf
                    <input type="hidden" name="payment_method" value="paystack">
                    <button type="submit" class="w-full p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-red-300 transition text-left">
                        <span class="font-semibold text-gray-900">Paystack</span>
                        <p class="text-sm text-gray-600 mt-1">Secure payment with debit/credit card, bank transfer, or USSD</p>
                    </button>
                </form>
                @endif

                @if(isset($paymentMethods['cash_on_delivery']))
                <form action="{{ route('payment.process', $order) }}" method="POST" class="block">
                    @csrf
                    <input type="hidden" name="payment_method" value="cash_on_delivery">
                    <button type="submit" class="w-full p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-green-300 transition text-left">
                        <span class="font-semibold text-gray-900">Cash on Delivery</span>
                        <p class="text-sm text-gray-600 mt-1">Pay when you receive your items at your doorstep</p>
                    </button>
                </form>
                @endif
            </div>

            <!-- Submit Button -->
            <div class="mt-6">
                <a href="{{ route('checkout') }}" class="block w-full px-4 py-3 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition text-center">
                    Back to Checkout
                </a>
            </div>
        </div>

        <!-- Security Notice -->
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 text-center">
            <p class="text-sm text-gray-600">
                🔒 Your order and payment information are secure and encrypted.
            </p>
        </div>
    </div>
</div>
@endsection
