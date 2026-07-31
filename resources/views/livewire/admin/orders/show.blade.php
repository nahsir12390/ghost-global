<div>
    @php
        $visibleItems = auth()->user()->isVendor()
            ? $order->items->where('vendor_id', auth()->id())
            : $order->items;
        $visibleSubtotal = $visibleItems->sum('total');
    @endphp

    <!-- Success Alert -->
    @if(session('success'))
        <div class="mb-6 rounded-xl bg-green-50 border border-green-200 p-4 flex items-start gap-3" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)">
            <div class="flex-shrink-0 pt-0.5">
                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div class="flex-1">
                <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
            </div>
            <button @click="show = false" class="text-green-600 hover:text-green-700">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
    @endif

    <!-- Header -->
    <div class="mb-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <div class="flex items-center gap-3 mb-2">
                    <h1 class="text-3xl font-bold text-gray-900">Order {{ $order->order_number }}</h1>
                    <span class="{{ $order->status_badge['status'] }} text-sm font-semibold px-4 py-2 rounded-full">
                        {{ ucfirst(str_replace('_', ' ', $order->status)) }}
                    </span>
                </div>
                <p class="text-gray-600">Created {{ $order->created_at->diffForHumans() }}</p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-8">
            <!-- Order Items Card with Vendor Grouping -->
            <div class="card-modern p-6 sm:p-8">
                <div class="flex items-center mb-6 pb-6 border-b border-gray-200">
                    <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg flex items-center justify-center mr-3">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14a2 2 0 012 2v7a2 2 0 01-2 2H5a2 2 0 01-2-2v-7a2 2 0 012-2z" />
                        </svg>
                    </div>
                    <h2 class="text-xl font-bold text-gray-900">{{ auth()->user()->isVendor() ? 'Your Order Items' : 'Order Items by Vendor' }}</h2>
                </div>

                @if(!auth()->user()->isVendor() && isset($itemsByVendor))
                    <!-- Group items by vendor for admin view -->
                    @foreach($itemsByVendor as $vendorId => $vendorItems)
                        @php
                            $vendor = $vendorItems->first()->vendor;
                        @endphp
                        <div class="mb-8 last:mb-0">
                            <!-- Vendor Header -->
                            <div class="flex items-center justify-between mb-4 pb-3 border-b-2 border-purple-200">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 bg-gradient-to-br from-purple-500 to-purple-600 rounded-lg flex items-center justify-center">
                                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                        </svg>
                                    </div>
                                    <div>
                                        <h3 class="text-lg font-bold text-gray-900">
                                            {{ $vendor ? ($vendor->store_name ?: $vendor->name) : 'Unknown Vendor' }}
                                        </h3>
                                        @if($vendor)
                                            <div class="flex items-center gap-2 mt-1">
                                                @if($vendor->isVendorVerified())
                                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                                        </svg>
                                                        Verified Vendor
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                        Pending Verification
                                                    </span>
                                                @endif
                                                <a href="{{ route('admin.users.show', $vendor) }}" 
                                                   class="text-xs text-purple-600 hover:text-purple-800 font-medium">
                                                    View Profile →
                                                </a>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                <div class="text-right">
                                    <div class="text-lg font-bold text-gray-900">
                                        ₦{{ number_format($vendorItems->sum('total'), 2) }}
                                    </div>
                                    <div class="text-xs text-gray-500">{{ $vendorItems->count() }} item(s)</div>
                                </div>
                            </div>

                            <!-- Vendor Items -->
                            <div class="space-y-4">
                                @foreach($vendorItems as $item)
                                    <div class="flex items-center gap-4 pb-4 border-b border-gray-100 last:pb-0 last:border-0">
                                        <!-- Product Image -->
                                        <div class="flex-shrink-0">
                                            @if($item->product && $item->product->main_image)
                                                <img src="{{ Storage::url($item->product->main_image) }}" alt="{{ $item->product_name }}" 
                                                     class="h-20 w-20 rounded-xl object-cover shadow-sm">
                                            @else
                                                <div class="h-20 w-20 rounded-xl bg-gray-200 flex items-center justify-center">
                                                    <svg class="h-10 w-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                    </svg>
                                                </div>
                                            @endif
                                        </div>

                                        <!-- Product Info -->
                                        <div class="flex-1 min-w-0">
                                            <h4 class="text-base font-semibold text-gray-900 truncate">{{ $item->product_name }}</h4>
                                            <div class="mt-1 flex flex-wrap gap-3">
                                                <div class="text-sm">
                                                    <span class="text-gray-600">Qty:</span>
                                                    <span class="font-semibold text-gray-900 ml-1">{{ $item->quantity }}</span>
                                                </div>
                                                <div class="text-sm">
                                                    <span class="text-gray-600">Unit Price:</span>
                                                    <span class="font-semibold text-gray-900 ml-1">{{ $item->formatted_price }}</span>
                                                </div>
                                            </div>
                                            @if($item->product)
                                                <a href="{{ route('admin.products.show', $item->product) }}" 
                                                   class="inline-block mt-2 text-sm text-red-600 hover:text-red-800 font-medium">
                                                    View Product →
                                                </a>
                                            @endif
                                        </div>

                                        <!-- Total -->
                                        <div class="flex-shrink-0 text-right">
                                            <div class="text-xl font-bold text-gray-900">{{ $item->formatted_total }}</div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                @else
                    <!-- Simple list for vendor view or when no grouping -->
                    <div class="space-y-6">
                        @foreach($visibleItems as $item)
                            <div class="flex items-center gap-4 pb-6 border-b border-gray-100 last:pb-0 last:border-0">
                                <div class="flex-shrink-0">
                                    @if($item->product && $item->product->main_image)
                                        <img src="{{ Storage::url($item->product->main_image) }}" alt="{{ $item->product_name }}" 
                                             class="h-20 w-20 rounded-xl object-cover shadow-sm">
                                    @else
                                        <div class="h-20 w-20 rounded-xl bg-gray-200 flex items-center justify-center">
                                            <svg class="h-10 w-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                        </div>
                                    @endif
                                </div>

                                <div class="flex-1 min-w-0">
                                    <h3 class="text-lg font-semibold text-gray-900 truncate">{{ $item->product_name }}</h3>
                                    <div class="mt-2 flex flex-wrap gap-3">
                                        <div class="text-sm">
                                            <span class="text-gray-600">Qty:</span>
                                            <span class="font-semibold text-gray-900 ml-1">{{ $item->quantity }}</span>
                                        </div>
                                        @if($item->vendor && auth()->user()->isVendor())
                                            <div class="text-sm">
                                                <span class="text-gray-600">Vendor:</span>
                                                <span class="font-semibold text-gray-900 ml-1">{{ $item->vendor->store_name ?: $item->vendor->name }}</span>
                                            </div>
                                        @endif
                                    </div>
                                    @if($item->product)
                                        <a href="{{ route('admin.products.show', $item->product) }}" 
                                           class="inline-block mt-2 text-sm text-red-600 hover:text-red-800 font-medium">
                                            View Product →
                                        </a>
                                    @endif
                                </div>

                                <div class="flex-shrink-0 text-right">
                                    <div class="text-2xl font-bold text-gray-900">{{ $item->formatted_price }}</div>
                                    <div class="text-sm text-gray-500 mt-1">Total: {{ $item->formatted_total }}</div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif

                <!-- Summary -->
                <div class="mt-8 pt-8 border-t-2 border-gray-200">
                    <div class="space-y-4">
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600 font-medium">{{ auth()->user()->isVendor() ? 'Your Items Total' : 'Subtotal' }}</span>
                            <span class="text-xl font-bold text-gray-900">₦{{ number_format(auth()->user()->isVendor() ? $visibleSubtotal : $order->subtotal, 2) }}</span>
                        </div>

                        @if(!auth()->user()->isVendor())
                            @if($order->tax > 0)
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-600 font-medium">Platform Service Fee</span>
                                    <span class="text-lg font-semibold text-gray-900">₦{{ number_format($order->tax, 2) }}</span>
                                </div>
                            @endif

                            @if($order->shipping > 0)
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-600 font-medium">Shipping</span>
                                    <span class="text-lg font-semibold text-gray-900">₦{{ number_format($order->shipping, 2) }}</span>
                                </div>
                            @endif

                            <div class="flex justify-between items-center pt-4 border-t-2 border-gray-200">
                                <span class="text-lg font-bold text-gray-900">Total Amount</span>
                                <span class="text-2xl font-bold bg-gradient-to-r from-red-600 to-red-700 bg-clip-text text-transparent">
                                    ₦{{ number_format($order->total, 2) }}
                                </span>
                            </div>
                        @else
                            <div class="rounded-xl bg-blue-50 px-4 py-3 text-sm text-blue-700 border border-blue-200 mt-6">
                                <div class="flex items-start gap-2">
                                    <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/>
                                    </svg>
                                    <p>This vendor view only shows products from your store for this order. Overall payment and delivery status remain under admin control.</p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Shipping Information Card -->
            <div class="card-modern p-6 sm:p-8">
                <div class="flex items-center mb-6 pb-6 border-b border-gray-200">
                    <div class="w-10 h-10 bg-gradient-to-br from-green-500 to-emerald-600 rounded-lg flex items-center justify-center mr-3">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </div>
                    <h2 class="text-xl font-bold text-gray-900">Shipping Information</h2>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-semibold text-gray-600 mb-2">Full Name</label>
                        <p class="text-gray-900 font-medium">{{ $order->shipping_full_name }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-600 mb-2">Email</label>
                        <p class="text-gray-900 font-medium">{{ $order->shipping_email }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-600 mb-2">Phone</label>
                        <p class="text-gray-900 font-medium">{{ $order->shipping_phone }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-600 mb-2">Address</label>
                        <p class="text-gray-900 font-medium">{{ $order->shipping_address }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-600 mb-2">City</label>
                        <p class="text-gray-900 font-medium">{{ $order->shipping_city }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-600 mb-2">State</label>
                        <p class="text-gray-900 font-medium">{{ $order->shipping_state }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-600 mb-2">Country</label>
                        <p class="text-gray-900 font-medium">{{ $order->shipping_country }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-600 mb-2">Postal Code</label>
                        <p class="text-gray-900 font-medium">{{ $order->shipping_postal_code }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-8">
            <!-- Order Status Card (Admin Only) -->
            @if(!auth()->user()->isVendor())
                <div class="card-modern p-6 sm:p-8 sticky top-8">
                    <div class="flex items-center mb-6 pb-6 border-b border-gray-200">
                        <div class="w-10 h-10 bg-gradient-to-br from-purple-500 to-purple-600 rounded-lg flex items-center justify-center mr-3">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold text-gray-900">Status Updates</h3>
                    </div>

                    <form wire:submit="update" class="space-y-5">
                        <div>
                            <label for="status" class="block text-sm font-semibold text-gray-700 mb-2">Order Status</label>
                            <select id="status" wire:model.change="status" class="w-full form-input-modern rounded-xl">
                                @foreach($statuses as $value => $label)
                                    <option value="{{ $value }}" @selected($value === $status)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="payment_status" class="block text-sm font-semibold text-gray-700 mb-2">Payment Status</label>
                            <select id="payment_status" wire:model.change="payment_status" class="w-full form-input-modern rounded-xl">
                                @foreach($paymentStatuses as $value => $label)
                                    <option value="{{ $value }}" @selected($value === $payment_status)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="tracking_number" class="block text-sm font-semibold text-gray-700 mb-2">Tracking Number</label>
                            <input type="text" id="tracking_number" wire:model.lazy="tracking_number" 
                                   class="w-full form-input-modern rounded-xl" 
                                   placeholder="Add tracking number..." 
                                   value="{{ $order->tracking_number }}">
                        </div>

                        <div>
                            <label for="shipping_carrier" class="block text-sm font-semibold text-gray-700 mb-2">Shipping Carrier</label>
                            <input type="text" id="shipping_carrier" wire:model.lazy="shipping_carrier" 
                                   class="w-full form-input-modern rounded-xl" 
                                   placeholder="Add carrier name..." 
                                   value="{{ $order->shipping_carrier }}">
                        </div>

                        <div>
                            <label for="notes" class="block text-sm font-semibold text-gray-700 mb-2">Internal Notes</label>
                            <textarea id="notes" wire:model.lazy="notes" rows="4" 
                                      class="w-full form-input-modern rounded-xl" 
                                      placeholder="Add internal notes about this order...">{{ $order->notes }}</textarea>
                        </div>

                        <button type="submit" wire:loading.attr="disabled" class="w-full btn-primary rounded-xl py-3 font-semibold transition-all disabled:opacity-50 disabled:cursor-not-allowed">
                            <span wire:loading.remove>Update Order</span>
                            <span wire:loading>
                                <svg class="animate-spin h-5 w-5 inline-block mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Updating...
                            </span>
                        </button>
                    </form>
                </div>
            @endif

            <!-- Order Information Card -->
            <div class="card-modern p-6 sm:p-8">
                <div class="flex items-center mb-6 pb-6 border-b border-gray-200">
                    <div class="w-10 h-10 bg-gradient-to-br from-yellow-500 to-amber-600 rounded-lg flex items-center justify-center mr-3">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900">Order Details</h3>
                </div>

                <div class="space-y-4">
                    <div class="pb-4 border-b border-gray-100 last:pb-0 last:border-0">
                        <p class="text-xs font-semibold text-gray-600 uppercase tracking-wide mb-1">Order Number</p>
                        <p class="text-lg font-bold text-gray-900">{{ $order->order_number }}</p>
                    </div>

                    <div class="pb-4 border-b border-gray-100 last:pb-0 last:border-0">
                        <p class="text-xs font-semibold text-gray-600 uppercase tracking-wide mb-1">Created</p>
                        <p class="text-gray-900 font-medium">{{ $order->created_at->format('M d, Y • H:i A') }}</p>
                    </div>

                    <div class="pb-4 border-b border-gray-100 last:pb-0 last:border-0">
                        <p class="text-xs font-semibold text-gray-600 uppercase tracking-wide mb-1">Last Updated</p>
                        <p class="text-gray-900 font-medium">{{ $order->updated_at->format('M d, Y • H:i A') }}</p>
                    </div>

                    <div class="pb-4 border-b border-gray-100 last:pb-0 last:border-0">
                        <p class="text-xs font-semibold text-gray-600 uppercase tracking-wide mb-1">Payment Method</p>
                        <p class="text-gray-900 font-medium">{{ ucfirst($order->payment_method ?? 'N/A') }}</p>
                    </div>

                    @if($order->payment_reference)
                        <div class="pb-4 border-b border-gray-100 last:pb-0 last:border-0">
                            <p class="text-xs font-semibold text-gray-600 uppercase tracking-wide mb-1">Payment Reference</p>
                            <p class="text-gray-900 font-medium break-all">{{ $order->payment_reference }}</p>
                        </div>
                    @endif

                    <div class="pb-4 border-b border-gray-100 last:pb-0 last:border-0">
                        <p class="text-xs font-semibold text-gray-600 uppercase tracking-wide mb-1">Customer</p>
                        @if($order->user)
                            <a href="{{ route('admin.users.show', $order->user) }}" 
                               class="text-red-600 hover:text-red-800 font-semibold">
                                {{ $order->user->name }}
                            </a>
                        @else
                            <p class="text-gray-900 font-medium">Guest</p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Vendor Summary Card (Admin Only) -->
            @if(!auth()->user()->isVendor() && isset($itemsByVendor) && $itemsByVendor->count() > 1)
                <div class="card-modern p-6 sm:p-8">
                    <div class="flex items-center mb-6 pb-6 border-b border-gray-200">
                        <div class="w-10 h-10 bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-lg flex items-center justify-center mr-3">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold text-gray-900">Vendor Summary</h3>
                    </div>

                    <div class="space-y-3">
                        @foreach($itemsByVendor as $vendorId => $vendorItems)
                            @php
                                $vendor = $vendorItems->first()->vendor;
                                $vendorTotal = $vendorItems->sum('total');
                            @endphp
                            <div class="flex items-center justify-between py-2 border-b border-gray-100 last:border-0">
                                <div>
                                    <p class="font-semibold text-gray-900">
                                        {{ $vendor ? ($vendor->store_name ?: $vendor->name) : 'Unknown Vendor' }}
                                    </p>
                                    <p class="text-xs text-gray-500">{{ $vendorItems->count() }} item(s)</p>
                                </div>
                                <div class="text-right">
                                    <p class="font-bold text-gray-900">₦{{ number_format($vendorTotal, 2) }}</p>
                                </div>
                            </div>
                        @endforeach
                        
                        <div class="pt-3 mt-3 border-t-2 border-gray-200">
                            <div class="flex items-center justify-between">
                                <p class="font-bold text-gray-900">Total</p>
                                <p class="font-bold text-xl text-red-600">₦{{ number_format($order->total, 2) }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Quick Actions -->
            @if(!auth()->user()->isVendor())
                <div class="card-modern p-6 sm:p-8">
                    <div class="flex items-center mb-6 pb-6 border-b border-gray-200">
                        <div class="w-10 h-10 bg-gradient-to-br from-red-500 to-red-600 rounded-lg flex items-center justify-center mr-3">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold text-gray-900">Actions</h3>
                    </div>

                    <div class="space-y-3">
                        <a href="{{ route('admin.orders.invoice', $order) }}" target="_blank" 
                           class="w-full flex items-center justify-center px-4 py-3 border border-gray-300 rounded-xl shadow-sm text-sm font-semibold text-gray-700 hover:bg-gray-50 hover:border-gray-400 transition-all duration-200">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 3v5a2 2 0 002 2h5" />
                            </svg>
                            View Invoice
                        </a>

                        <button onclick="confirm('Are you sure you want to delete this order? This action cannot be undone.') && Livewire.dispatch('deleteOrder', {id: {{ $order->id }}})" 
                                class="w-full flex items-center justify-center px-4 py-3 border-2 border-red-200 rounded-xl text-sm font-semibold text-red-600 hover:bg-red-50 hover:border-red-300 transition-all duration-200">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                            Delete Order
                        </button>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
