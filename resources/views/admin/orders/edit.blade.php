@extends('layouts.admin')

@section('title', 'Edit Order: ' . $order->order_number)

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="md:flex md:items-center md:justify-between mb-6">
            <div class="flex-1 min-w-0">
                <div class="flex items-center">
                    <h1 class="text-2xl font-bold text-gray-900">
                        Edit Order #{{ $order->order_number }}
                    </h1>
                    <span class="ml-3 {{ $order->status_badge['status'] }} text-xs font-semibold px-3 py-1 rounded-full">
                        {{ ucfirst($order->status) }}
                    </span>
                    <span class="ml-2 {{ $order->status_badge['payment'] }} text-xs font-semibold px-3 py-1 rounded-full">
                        {{ ucfirst($order->payment_status) }}
                    </span>
                </div>
                <p class="mt-1 text-sm text-gray-500">
                    Placed on {{ $order->created_at->format('F d, Y \a\t H:i') }}
                </p>
            </div>
            <div class="mt-4 flex md:mt-0 md:ml-4 space-x-3">
                <a href="{{ route('admin.orders.show', $order) }}" 
                   class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                    <svg class="-ml-1 mr-2 h-5 w-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                    View Details
                </a>
                <a href="{{ route('admin.orders.index') }}" 
                   class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                    <svg class="-ml-1 mr-2 h-5 w-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Back to Orders
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Status Update Form -->
                <div class="bg-white shadow rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                        <h2 class="text-lg font-semibold text-gray-900">Update Order Status</h2>
                        <p class="text-sm text-gray-600 mt-1">Change the order status and add notes for the customer</p>
                    </div>
                    <form action="{{ route('admin.orders.update', $order) }}" method="POST" class="px-6 py-4 space-y-4">
                        @csrf
                        @method('PUT')

                        <!-- Current Status -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Current Status</label>
                            <div class="p-3 bg-gray-50 rounded-lg border border-gray-200">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                                    @if($order->status === 'pending') bg-yellow-100 text-yellow-800
                                    @elseif($order->status === 'processing') bg-blue-100 text-blue-800
                                    @elseif($order->status === 'packed') bg-indigo-100 text-indigo-800
                                    @elseif($order->status === 'shipped') bg-purple-100 text-purple-800
                                    @elseif($order->status === 'on_the_way') bg-cyan-100 text-cyan-800
                                    @elseif($order->status === 'delivered') bg-green-100 text-green-800
                                    @elseif($order->status === 'cancelled') bg-red-100 text-red-800
                                    @elseif($order->status === 'failed') bg-red-100 text-red-800
                                    @else bg-gray-100 text-gray-800 @endif">
                                    {{ ucfirst(str_replace('_', ' ', $order->status)) }}
                                </span>
                            </div>
                        </div>

                        <!-- New Status Dropdown -->
                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Change Status to:</label>
                            <select name="status" id="status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent" required>
                                <option value="">Select new status...</option>
                                <option value="pending" @if($order->status === 'pending') selected @endif>🔵 Pending</option>
                                <option value="processing" @if($order->status === 'processing') selected @endif>🔄 Processing</option>
                                <option value="packed" @if($order->status === 'packed') selected @endif>📦 Packed</option>
                                <option value="shipped" @if($order->status === 'shipped') selected @endif>🚚 Shipped</option>
                                <option value="on_the_way" @if($order->status === 'on_the_way') selected @endif>🛣️ On The Way</option>
                                <option value="delivered" @if($order->status === 'delivered') selected @endif>✅ Delivered</option>
                                <option value="cancelled" @if($order->status === 'cancelled') selected @endif>❌ Cancelled</option>
                                <option value="failed" @if($order->status === 'failed') selected @endif>⚠️ Failed</option>
                            </select>
                            @error('status')
                                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Payment Status -->
                        <div>
                            <label for="payment_status" class="block text-sm font-medium text-gray-700 mb-2">Payment Status:</label>
                            <select name="payment_status" id="payment_status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent" required>
                                <option value="pending" @if($order->payment_status === 'pending') selected @endif>Pending</option>
                                <option value="paid" @if($order->payment_status === 'paid') selected @endif>Paid</option>
                                <option value="failed" @if($order->payment_status === 'failed') selected @endif>Failed</option>
                                <option value="refunded" @if($order->payment_status === 'refunded') selected @endif>Refunded</option>
                            </select>
                            @error('payment_status')
                                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Tracking Information -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="tracking_number" class="block text-sm font-medium text-gray-700 mb-2">Tracking Number:</label>
                                <input type="text" name="tracking_number" id="tracking_number" 
                                       value="{{ old('tracking_number', $order->tracking_number) }}"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent"
                                       placeholder="e.g., ORD-12345">
                                @error('tracking_number')
                                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="shipping_carrier" class="block text-sm font-medium text-gray-700 mb-2">Shipping Carrier:</label>
                                <input type="text" name="shipping_carrier" id="shipping_carrier"
                                       value="{{ old('shipping_carrier', $order->shipping_carrier) }}"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent"
                                       placeholder="e.g., DHL, FedEx, UPS">
                                @error('shipping_carrier')
                                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Admin Notes -->
                        <div>
                            <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">Admin Notes (shown to customer):</label>
                            <textarea name="notes" id="notes" rows="4"
                                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent"
                                      placeholder="Add notes about this order update...">{{ old('notes', $order->notes) }}</textarea>
                            @error('notes')
                                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex justify-end space-x-3 pt-4 border-t border-gray-200">
                            <a href="{{ route('admin.orders.show', $order) }}"
                               class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors duration-200">
                                Cancel
                            </a>
                            <button type="submit" 
                                    class="px-6 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors duration-200 font-medium">
                                <svg class="-ml-1 mr-2 h-4 w-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                Update Order
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Recent Status History -->
                <div class="bg-white shadow rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                        <h2 class="text-lg font-semibold text-gray-900">Status History</h2>
                    </div>
                    <div class="divide-y divide-gray-200">
                        @forelse($order->statusHistory()->orderBy('created_at', 'desc')->get() as $history)
                            <div class="px-6 py-4">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <p class="text-sm font-medium text-gray-900">
                                            {{ ucfirst(str_replace('_', ' ', $history->new_status)) }}
                                        </p>
                                        <p class="text-xs text-gray-500 mt-1">
                                            {{ $history->created_at->format('M d, Y \a\t H:i') }}
                                        </p>
                                        @if($history->old_status)
                                            <p class="text-xs text-gray-600 mt-1">
                                                From: <span class="font-medium">{{ ucfirst(str_replace('_', ' ', $history->old_status)) }}</span>
                                            </p>
                                        @endif
                                        @if($history->notes)
                                            <p class="text-sm text-gray-700 mt-2 p-2 bg-blue-50 rounded border border-blue-100">
                                                <strong class="text-blue-900">Note:</strong> {{ $history->notes }}
                                            </p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="px-6 py-4">
                                <p class="text-gray-600 text-sm">No status history yet.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Sidebar: Order Summary -->
            <div class="space-y-6">
                <!-- Order Summary Card -->
                <div class="bg-white shadow rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                        <h2 class="text-lg font-semibold text-gray-900">Order Summary</h2>
                    </div>
                    <div class="px-6 py-4 space-y-3">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Subtotal:</span>
                            <span class="text-gray-900 font-medium">₦{{ number_format($order->subtotal, 2) }}</span>
                        </div>
                        @if($order->tax > 0)
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Platform Service Fee:</span>
                                <span class="text-gray-900 font-medium">₦{{ number_format($order->tax, 2) }}</span>
                            </div>
                        @endif
                        @if($order->shipping > 0)
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Delivery Fee:</span>
                                <span class="text-gray-900 font-medium">₦{{ number_format($order->shipping, 2) }}</span>
                            </div>
                        @endif
                        <div class="border-t border-gray-200 pt-3 flex justify-between">
                            <span class="font-semibold text-gray-900">Total:</span>
                            <span class="text-lg font-bold text-red-600">₦{{ number_format($order->total, 2) }}</span>
                        </div>
                    </div>
                </div>

                <!-- Customer Info -->
                <div class="bg-white shadow rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                        <h2 class="text-sm font-semibold text-gray-900 uppercase tracking-wide">Customer</h2>
                    </div>
                    <div class="px-6 py-4 space-y-2 text-sm">
                        <p class="text-gray-900 font-medium">{{ $order->user->name }}</p>
                        <p class="text-gray-600 break-all">{{ $order->user->email }}</p>
                        @if($order->shipping_phone)
                            <p class="text-gray-600">{{ $order->shipping_phone }}</p>
                        @endif
                    </div>
                </div>

                <!-- Items Count -->
                <div class="bg-white shadow rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                        <h2 class="text-sm font-semibold text-gray-900 uppercase tracking-wide">Items</h2>
                    </div>
                    <div class="px-6 py-4">
                        <p class="text-2xl font-bold text-gray-900">{{ $order->items()->count() }}</p>
                        <p class="text-xs text-gray-600 mt-1">Items in this order</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    select, input, textarea {
        transition: all 0.2s ease;
    }
    
    select:focus, input:focus, textarea:focus {
        box-shadow: 0 0 0 3px rgba(220, 38, 38, 0.1);
    }
</style>
@endpush

@endsection
