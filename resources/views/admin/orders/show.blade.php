@extends('layouts.admin')

@section('title', 'Order Details: ' . $order->order_number)

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="md:flex md:items-center md:justify-between mb-6">
            <div class="flex-1 min-w-0">
                <div class="flex items-center">
                    <h1 class="text-2xl font-bold text-gray-900">
                        Order #{{ $order->order_number }}
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
                <a href="{{ route('admin.orders.invoice', $order) }}" 
                   target="_blank"
                   class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                    <svg class="-ml-1 mr-2 h-5 w-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    View Invoice
                </a>
                <a href="{{ route('admin.orders.edit', $order) }}" 
                   class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                    <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    Edit Order
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

        <!-- Livewire Component -->
        @livewire('admin.orders.show', ['order' => $order])

        <!-- Flash Messages -->
        @if (session('success'))
            <div class="rounded-md bg-green-50 p-4 mb-6">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                    </div>
                </div>
            </div>
        @endif

        @if (session('error'))
            <div class="rounded-md bg-red-50 p-4 mb-6">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.346 16.5c-.77.833.192 2.5 1.732 2.5z" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
                    </div>
                </div>
            </div>
        @endif

        <!-- Traditional View (Fallback if Livewire fails) -->
        <div class="hidden">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Left Column: Order Details -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Order Items -->
                    <div class="bg-white rounded-lg shadow">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h3 class="text-lg font-medium text-gray-900">Order Items</h3>
                        </div>
                        <div class="p-6">
                            <div class="space-y-4">
                                @foreach($order->items as $item)
                                    <div class="flex items-center justify-between py-4 border-b border-gray-100 last:border-0">
                                        <div class="flex items-center">
                                            @if($item->product && $item->product->main_image)
                                                <img src="{{ Storage::url($item->product->main_image) }}" 
                                                     alt="{{ $item->product_name }}"
                                                     class="h-16 w-16 rounded-lg object-cover">
                                            @else
                                                <div class="h-16 w-16 rounded-lg bg-gray-200 flex items-center justify-center">
                                                    <svg class="h-8 w-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                    </svg>
                                                </div>
                                            @endif
                                            <div class="ml-4">
                                                <h4 class="text-sm font-medium text-gray-900">{{ $item->product_name }}</h4>
                                                <p class="text-sm text-gray-500">Qty: {{ $item->quantity }}</p>
                                                @if($item->product)
                                                    <a href="{{ route('admin.products.show', $item->product) }}" 
                                                       class="text-xs text-red-600 hover:text-red-800">
                                                        View Product
                                                    </a>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            <p class="text-sm font-medium text-gray-900">₦{{ number_format($item->price, 2) }}</p>
                                            <p class="text-sm text-gray-500">Total: ₦{{ number_format($item->total, 2) }}</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <!-- Order Totals -->
                            <div class="mt-6 border-t border-gray-200 pt-6">
                                <dl class="space-y-3">
                                    <div class="flex justify-between">
                                        <dt class="text-sm text-gray-600">Subtotal</dt>
                                        <dd class="text-sm font-medium text-gray-900">₦{{ number_format($order->subtotal, 2) }}</dd>
                                    </div>
                                    @if($order->tax > 0)
                                        <div class="flex justify-between">
                                            <dt class="text-sm text-gray-600">Platform Service Fee</dt>
                                            <dd class="text-sm font-medium text-gray-900">₦{{ number_format($order->tax, 2) }}</dd>
                                        </div>
                                    @endif
                                    @if($order->shipping > 0)
                                        <div class="flex justify-between">
                                            <dt class="text-sm text-gray-600">Delivery Fee</dt>
                                            <dd class="text-sm font-medium text-gray-900">₦{{ number_format($order->shipping, 2) }}</dd>
                                        </div>
                                    @endif
                                    <div class="flex justify-between border-t border-gray-200 pt-3">
                                        <dt class="text-base font-medium text-gray-900">Total</dt>
                                        <dd class="text-base font-bold text-red-600">₦{{ number_format($order->total, 2) }}</dd>
                                    </div>
                                </dl>
                            </div>
                        </div>
                    </div>

                    <!-- Shipping Information -->
                    <div class="bg-white rounded-lg shadow">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h3 class="text-lg font-medium text-gray-900">Shipping Information</h3>
                        </div>
                        <div class="p-6">
                            <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Name</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $order->shipping_full_name }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Email</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $order->shipping_email }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Phone</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $order->shipping_phone }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Address</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $order->shipping_address }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">City</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $order->shipping_city }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">State</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $order->shipping_state }}</dd>
                                </div>
                                <div class="md:col-span-2">
                                    <dt class="text-sm font-medium text-gray-500">Country</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $order->shipping_country }}</dd>
                                </div>
                                <div class="md:col-span-2">
                                    <dt class="text-sm font-medium text-gray-500">Postal Code</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $order->shipping_postal_code }}</dd>
                                </div>
                            </dl>
                        </div>
                    </div>
                </div>

                <!-- Right Column: Order Management -->
                <div class="space-y-6">
                    <!-- Order Status -->
                    <div class="bg-white rounded-lg shadow">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h3 class="text-lg font-medium text-gray-900">Order Status</h3>
                        </div>
                        <div class="p-6">
                            <!-- Status Progress Timeline -->
                            <div class="mb-8 pb-8 border-b border-gray-200">
                                <h4 class="text-sm font-semibold text-gray-900 mb-6">Order Progress</h4>
                                <div class="flex justify-between items-end gap-2">
                                    @php
                                        $statuses = [
                                            'processing' => ['label' => 'Processing', 'icon' => '🔄', 'color' => 'bg-blue-100'],
                                            'packed' => ['label' => 'Packed', 'icon' => '📦', 'color' => 'bg-indigo-100'],
                                            'shipped' => ['label' => 'Shipped', 'icon' => '🚚', 'color' => 'bg-purple-100'],
                                            'on_the_way' => ['label' => 'On The Way', 'icon' => '🛣️', 'color' => 'bg-orange-100'],
                                            'delivered' => ['label' => 'Delivered', 'icon' => '✅', 'color' => 'bg-green-100'],
                                        ];
                                        $statusOrder = array_keys($statuses);
                                        $currentIndex = array_search($order->status, $statusOrder);
                                    @endphp

                                    @foreach($statuses as $status => $info)
                                        @php
                                            $index = array_search($status, $statusOrder);
                                            $isCompleted = $index <= $currentIndex && $currentIndex !== false;
                                            $isCurrent = $index === $currentIndex;
                                        @endphp
                                        <div class="flex flex-col items-center flex-1">
                                            <div class="flex justify-center mb-3 w-full">
                                                <div class="w-16 h-16 rounded-full flex items-center justify-center text-2xl font-bold border-4 {{ $isCompleted ? 'bg-blue-500 border-blue-500 text-white' : 'bg-gray-100 border-gray-200 text-gray-400' }}">
                                                    {{ $info['icon'] }}
                                                </div>
                                            </div>
                                            <p class="font-semibold text-sm text-center {{ $isCompleted ? 'text-gray-900' : 'text-gray-500' }}">
                                                {{ $info['label'] }}
                                            </p>
                                            @if($isCurrent)
                                                <span class="text-xs text-blue-600 font-bold mt-1">CURRENT</span>
                                            @endif
                                        </div>

                                        @if($loop->index < count($statuses) - 1)
                                            <div class="flex items-end pb-8 px-1">
                                                <div class="h-1 {{ $index < $currentIndex ? 'bg-blue-500' : 'bg-gray-200' }}" style="width: 100%; min-width: 20px;"></div>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            </div>

                            <form action="{{ route('admin.orders.update', $order) }}" method="POST" class="space-y-4">
                                @csrf
                                @method('PUT')
                                
                                <!-- Order Status -->
                                <div>
                                    <label for="status" class="block text-sm font-medium text-gray-700 mb-1">
                                        Order Status
                                    </label>
                                    <select id="status" name="status"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-red-500 focus:border-red-500">
                                        @foreach(['pending' => 'Pending', 'processing' => 'Processing', 'packed' => 'Packed', 'shipped' => 'Shipped', 'on_the_way' => 'On The Way', 'delivered' => 'Delivered', 'cancelled' => 'Cancelled', 'failed' => 'Failed'] as $value => $label)
                                            <option value="{{ $value }}" {{ $order->status == $value ? 'selected' : '' }}>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Payment Status -->
                                <div>
                                    <label for="payment_status" class="block text-sm font-medium text-gray-700 mb-1">
                                        Payment Status
                                    </label>
                                    <select id="payment_status" name="payment_status"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-red-500 focus:border-red-500">
                                        @foreach(['pending' => 'Pending', 'paid' => 'Paid', 'failed' => 'Failed', 'refunded' => 'Refunded'] as $value => $label)
                                            <option value="{{ $value }}" {{ $order->payment_status == $value ? 'selected' : '' }}>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Tracking Number -->
                                <div>
                                    <label for="tracking_number" class="block text-sm font-medium text-gray-700 mb-1">
                                        Tracking Number
                                    </label>
                                    <input type="text"
                                           id="tracking_number"
                                           name="tracking_number"
                                           value="{{ $order->tracking_number }}"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-red-500 focus:border-red-500"
                                           placeholder="Enter tracking number">
                                </div>

                                <!-- Shipping Carrier -->
                                <div>
                                    <label for="shipping_carrier" class="block text-sm font-medium text-gray-700 mb-1">
                                        Shipping Carrier
                                    </label>
                                    <input type="text"
                                           id="shipping_carrier"
                                           name="shipping_carrier"
                                           value="{{ $order->shipping_carrier }}"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-red-500 focus:border-red-500"
                                           placeholder="e.g., DHL, FedEx">
                                </div>

                                <!-- Notes -->
                                <div>
                                    <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">
                                        Notes
                                    </label>
                                    <textarea id="notes"
                                              name="notes"
                                              rows="3"
                                              class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-red-500 focus:border-red-500"
                                              placeholder="Add internal notes about this order">{{ $order->notes }}</textarea>
                                </div>

                                <!-- Form Actions -->
                                <div class="pt-4">
                                    <button type="submit"
                                            class="w-full inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                        Update Order
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Status History -->
                    <div class="bg-white rounded-lg shadow">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h3 class="text-lg font-medium text-gray-900">Status History</h3>
                        </div>
                        <div class="p-6">
                            @if($order->statusHistory && $order->statusHistory->count() > 0)
                                <div class="space-y-4">
                                    @foreach($order->statusHistory()->orderBy('created_at', 'desc')->get() as $history)
                                        <div class="flex items-start pb-4 border-b border-gray-100 last:border-b-0">
                                            <div class="flex-shrink-0 mr-3">
                                                <div class="flex items-center justify-center h-8 w-8 rounded-full bg-blue-100 text-blue-600">
                                                    <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                                    </svg>
                                                </div>
                                            </div>
                                            <div class="flex-1">
                                                <div class="flex items-center justify-between">
                                                    <p class="text-sm font-semibold text-gray-900">
                                                        {{ ucfirst(str_replace('_', ' ', $history->new_status)) }}
                                                    </p>
                                                    <p class="text-xs text-gray-500">
                                                        {{ $history->created_at->format('M d, Y g:i A') }}
                                                    </p>
                                                </div>
                                                @if($history->old_status)
                                                    <p class="text-xs text-gray-600 mt-1">
                                                        Changed from: <span class="font-medium">{{ ucfirst(str_replace('_', ' ', $history->old_status)) }}</span>
                                                    </p>
                                                @endif
                                                @if($history->notes)
                                                    <p class="text-sm text-gray-600 mt-2 p-2 bg-blue-50 rounded border-l-2 border-blue-500">
                                                        {{ $history->notes }}
                                                    </p>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-gray-500 text-sm text-center py-4">No status history yet</p>
                            @endif
                        </div>
                    </div>
                    <div class="bg-white rounded-lg shadow">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h3 class="text-lg font-medium text-gray-900">Order Information</h3>
                        </div>
                        <div class="p-6">
                            <dl class="space-y-3">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Order Number</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $order->order_number }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Date Created</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $order->created_at->format('M d, Y H:i') }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Last Updated</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $order->updated_at->format('M d, Y H:i') }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Payment Method</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ ucfirst($order->payment_method ?? 'N/A') }}</dd>
                                </div>
                                @if($order->payment_reference)
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Payment Reference</dt>
                                        <dd class="mt-1 text-sm text-gray-900">{{ $order->payment_reference }}</dd>
                                    </div>
                                @endif
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Customer</dt>
                                    <dd class="mt-1 text-sm text-gray-900">
                                        @if($order->user)
                                            <a href="{{ route('admin.users.show', $order->user) }}" 
                                               class="text-red-600 hover:text-red-800">
                                                {{ $order->user->name }}
                                            </a>
                                        @else
                                            Guest
                                        @endif
                                    </dd>
                                </div>
                            </dl>
                        </div>
                    </div>

                    <!-- Order Actions -->
                    <div class="bg-white rounded-lg shadow">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h3 class="text-lg font-medium text-gray-900">Quick Actions</h3>
                        </div>
                        <div class="p-6">
                            <div class="space-y-3">
                                <a href="{{ route('admin.orders.invoice', $order) }}" 
                                   target="_blank"
                                   class="w-full flex items-center justify-center px-4 py-2 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-red-500">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    View Invoice
                                </a>
                                <form action="{{ route('admin.orders.destroy', $order) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this order?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="w-full flex items-center justify-center px-4 py-2 border border-red-300 rounded-lg shadow-sm text-sm font-medium text-red-700 hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-red-500">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                        Delete Order
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
