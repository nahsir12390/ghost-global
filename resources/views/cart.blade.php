@extends('layouts.app')

@section('title', 'Shopping Cart')

@section('content')
    <div x-data="cartPage({
            cart: { items: @js($cartData['items'] ?? []), summary: @js($cartData['summary'] ?? []) },
            checkoutUrl: @js(auth()->check() ? route('checkout') : route('login')),
            isAuthenticated: @js(auth()->check())
        })"
        class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="flex flex-col lg:flex-row gap-8">
            <div class="lg:w-2/3">
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h1 class="text-2xl font-bold text-gray-900">Shopping Cart</h1>
                        <span class="text-sm text-gray-500" x-show="summary.line_items > 0" x-text="summary.line_items + ' ' + (summary.line_items === 1 ? 'item' : 'items')"></span>
                    </div>

                    <template x-if="itemsArray.length">
                        <div>
                            <div class="space-y-4">
                                <template x-for="item in itemsArray" :key="item.product_id">
                                    <div class="flex flex-col sm:flex-row items-start sm:items-center space-y-3 sm:space-y-0 sm:space-x-4 p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                                        <div class="flex-shrink-0 w-20 h-20 bg-gray-100 rounded-md overflow-hidden">
                                            <template x-if="item.image_url">
                                                <img :src="item.image_url" :alt="item.name" class="w-full h-full object-cover">
                                            </template>
                                            <template x-if="!item.image_url">
                                                <div class="w-full h-full flex items-center justify-center text-gray-400">
                                                    <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 24 24">
                                                        <path d="M4 5h16a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V6a1 1 0 0 1 1-1zM4 7v10h16V7H4zm8 2l5 4H7l5-4z"/>
                                                    </svg>
                                                </div>
                                            </template>
                                        </div>

                                        <div class="flex-1 min-w-0">
                                            <h3 class="text-lg font-medium text-gray-900 truncate">
                                                <a :href="productUrl(item.slug)" class="hover:text-red-600 transition-colors" x-text="item.name"></a>
                                            </h3>
                                            <p class="text-sm text-gray-500" x-text="money(item.price) + ' each'"></p>
                                            <template x-if="item.vendor_name">
                                                <p class="mt-1 text-xs" :class="item.vendor_is_active ? 'text-green-600' : 'text-red-600'" x-text="item.vendor_name + ' · ' + (item.vendor_is_active ? 'Vendor active' : 'Vendor inactive')"></p>
                                            </template>
                                            <template x-if="item.unavailable_reason">
                                                <p class="mt-1 rounded-md bg-red-50 px-2 py-1 text-xs font-medium text-red-700" x-text="item.unavailable_reason"></p>
                                            </template>
                                            <p class="mt-2 text-lg font-semibold text-red-600" x-text="money(item.line_total)"></p>
                                        </div>

                                        <div class="flex items-center space-x-3 w-full sm:w-auto">
                                            <div class="flex items-center border border-gray-300 rounded-md flex-1 sm:flex-none overflow-hidden">
                                                <button @click="decrement(item.product_id)" :disabled="busy[item.product_id]" class="px-3 py-2 text-gray-600 hover:text-red-600 hover:bg-red-50 disabled:opacity-50 transition-colors">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
                                                    </svg>
                                                </button>
                                                <div class="w-16 text-center">
                                                    <span class="text-lg font-medium py-1 block" x-text="item.quantity"></span>
                                                </div>
                                                <button @click="increment(item.product_id)" :disabled="busy[item.product_id] || item.quantity >= item.stock" class="px-3 py-2 text-gray-600 hover:text-red-600 hover:bg-red-50 disabled:opacity-50 transition-colors">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                                    </svg>
                                                </button>
                                            </div>

                                            <button @click="remove(item.product_id)" :disabled="busy[item.product_id]" class="text-red-600 hover:text-red-800 p-2 disabled:opacity-50 transition-colors flex-shrink-0" title="Remove item">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                </template>
                            </div>

                            <div class="mt-6 flex flex-col sm:flex-row justify-between items-center gap-4">
                                <a href="{{ route('shop') }}" class="text-base text-gray-600 hover:text-red-600 font-medium transition-colors w-full sm:w-auto text-center">
                                    Continue Shopping
                                </a>
                                <button @click="clearCart" :disabled="clearing" class="text-base text-red-600 hover:text-red-800 font-medium disabled:opacity-50 transition-colors w-full sm:w-auto">
                                    Clear Cart
                                </button>
                            </div>
                        </div>
                    </template>

                    <template x-if="!itemsArray.length">
                        <div class="text-center py-12">
                            <svg class="mx-auto h-24 w-24 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                            <h3 class="mt-4 text-lg font-medium text-gray-900">Your cart is empty</h3>
                            <p class="mt-2 text-base text-gray-500">Add some products to get started.</p>
                            <div class="mt-6">
                                <a href="{{ route('shop') }}" class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-red-600 hover:bg-red-700 transition-colors">
                                    Continue Shopping
                                </a>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            <div class="lg:w-1/3" x-show="itemsArray.length">
                <div class="bg-white rounded-lg shadow-sm p-6 lg:sticky lg:top-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Order Summary</h2>

                    <div class="space-y-3 mb-6">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Subtotal</span>
                            <span class="text-gray-900" x-text="money(summary.subtotal)"></span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600" x-text="'Platform Service Fee (' + summary.service_fee_rate + '%)'"></span>
                            <span class="text-gray-900" x-text="money(summary.tax)"></span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Delivery Fee</span>
                            <span class="text-gray-900" x-text="summary.shipping === 0 ? 'Free' : money(summary.shipping)"></span>
                        </div>
                        <div class="border-t border-gray-200 pt-3 flex justify-between text-lg font-semibold">
                            <span class="text-gray-900">Total</span>
                            <span class="text-red-600" x-text="money(summary.total)"></span>
                        </div>
                    </div>

                    <div class="mb-6 p-3 bg-blue-50 rounded-md text-sm text-blue-700">
                        <template x-if="summary.shipping === 0">
                            <span>Free delivery applied!</span>
                        </template>
                        <template x-if="summary.shipping !== 0 && summary.free_shipping_threshold > 0">
                            <span x-text="'Add ' + money(summary.free_shipping_remaining) + ' more for free delivery'"></span>
                        </template>
                    </div>

                    <template x-if="summary.unavailable_count > 0">
                        <div class="mb-4 rounded-lg border border-red-200 bg-red-50 p-3 text-sm text-red-700">
                            Remove unavailable vendor items before checkout.
                        </div>
                    </template>

                    <a :href="summary.can_checkout ? checkoutUrl : '#'"
                       :class="summary.can_checkout ? 'bg-red-600 hover:bg-red-700' : 'bg-gray-400 pointer-events-none cursor-not-allowed'"
                       class="block w-full text-white py-3 px-4 rounded-md text-base font-medium text-center transition-colors">
                        <span x-text="isAuthenticated ? 'Proceed to Checkout' : 'Login to Checkout'"></span>
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection
