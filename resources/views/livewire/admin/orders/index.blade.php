<div>
    @php
        $isVendorView = auth()->user()->isVendor();
        $statusOptions = ['ordered', 'confirmed', 'picked_up', 'on_the_way', 'delivered', 'cancelled'];
        $paymentOptions = ['pending', 'paid', 'failed', 'refunded'];
    @endphp

    <div class="mb-8 grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-6">
        <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
            <div class="flex items-center justify-between">
                <div>
                    <p class="mb-2 text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">{{ $isVendorView ? 'Store Orders' : 'Total Orders' }}</p>
                    <p class="text-3xl font-bold tracking-tight text-slate-900">{{ $stats['total'] }}</p>
                </div>
                <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-gradient-to-br from-slate-900 to-slate-700">
                    <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14a2 2 0 012 2v7a2 2 0 01-2 2H5a2 2 0 01-2-2v-7a2 2 0 012-2z" />
                    </svg>
                </div>
            </div>
        </div>

        <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
            <div class="flex items-center justify-between">
                <div>
                    <p class="mb-2 text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">{{ $isVendorView ? 'Paid Sales' : 'Total Sales' }}</p>
                    <p class="text-2xl font-bold tracking-tight text-emerald-600">NGN {{ number_format($stats['total_sales'], 2) }}</p>
                </div>
                <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-gradient-to-br from-emerald-500 to-emerald-700">
                    <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </div>

        <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
            <div class="flex items-center justify-between">
                <div>
                    <p class="mb-2 text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">Ordered</p>
                    <p class="text-3xl font-bold tracking-tight text-amber-600">{{ $stats['ordered'] }}</p>
                </div>
                <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-gradient-to-br from-amber-500 to-orange-600">
                    <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14a2 2 0 012 2v7a2 2 0 01-2 2H5a2 2 0 01-2-2v-7a2 2 0 012-2z" />
                    </svg>
                </div>
            </div>
        </div>

        <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
            <div class="flex items-center justify-between">
                <div>
                    <p class="mb-2 text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">Confirmed</p>
                    <p class="text-3xl font-bold tracking-tight text-sky-600">{{ $stats['confirmed'] }}</p>
                </div>
                <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-gradient-to-br from-sky-500 to-indigo-600">
                    <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
            </div>
        </div>

        <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
            <div class="flex items-center justify-between">
                <div>
                    <p class="mb-2 text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">Picked Up</p>
                    <p class="text-3xl font-bold tracking-tight text-violet-600">{{ $stats['picked_up'] }}</p>
                </div>
                <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-gradient-to-br from-violet-500 to-fuchsia-600">
                    <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3v-9" />
                    </svg>
                </div>
            </div>
        </div>

        <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
            <div class="flex items-center justify-between">
                <div>
                    <p class="mb-2 text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">{{ $isVendorView ? 'Completed' : 'Delivered' }}</p>
                    <p class="text-3xl font-bold tracking-tight text-teal-600">{{ $stats['delivered'] }}</p>
                </div>
                <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-gradient-to-br from-teal-500 to-emerald-600">
                    <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <div class="mb-8 rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
        <div class="flex flex-col gap-4">
            @if($isVendorView)
                <div class="rounded-2xl border border-amber-200 bg-amber-50 px-4 py-4 text-sm text-amber-900">
                    Review fresh orders, track deliveries, and keep an eye on paid order flow from one place.
                </div>
            @endif

            <div class="flex flex-col gap-4 lg:flex-row">
                <div class="flex-1">
                    <label class="mb-2 block text-sm font-medium text-slate-700">{{ $isVendorView ? 'Search Store Orders' : 'Search Orders' }}</label>
                    <div class="relative">
                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4">
                            <svg class="h-5 w-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                        <input
                            wire:model.live.debounce.850ms="search"
                            type="search"
                            placeholder="{{ $isVendorView ? 'Search by order number, customer, phone, or email...' : 'Search by order number, customer, or vendor...' }}"
                            class="form-input-modern w-full rounded-2xl border border-slate-200 py-3 pl-11 pr-4"
                        >
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-5">
                <div>
                    <label class="mb-2 block text-sm font-medium text-slate-700">Order Status</label>
                    <select wire:model.change="statusFilter" class="form-input-modern w-full rounded-2xl border border-slate-200">
                        <option value="">All Statuses</option>
                        @foreach($statusOptions as $status)
                            <option value="{{ $status }}">{{ ucfirst(str_replace('_', ' ', $status)) }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="mb-2 block text-sm font-medium text-slate-700">Payment Status</label>
                    <select wire:model.change="paymentStatusFilter" class="form-input-modern w-full rounded-2xl border border-slate-200">
                        <option value="">All Payments</option>
                        @foreach($paymentOptions as $paymentStatus)
                            <option value="{{ $paymentStatus }}">{{ ucfirst($paymentStatus) }}</option>
                        @endforeach
                    </select>
                </div>

                @if(!$isVendorView && $vendors->count() > 0)
                    <div>
                        <label class="mb-2 block text-sm font-medium text-slate-700">Filter by Vendor</label>
                        <select wire:model.change="vendorFilter" class="form-input-modern w-full rounded-2xl border border-slate-200">
                            <option value="">All Vendors</option>
                            @foreach($vendors as $vendor)
                                <option value="{{ $vendor->id }}">{{ $vendor->store_name ?: $vendor->name }}</option>
                            @endforeach
                        </select>
                    </div>
                @endif

                <div>
                    <label class="mb-2 block text-sm font-medium text-slate-700">From Date</label>
                    <input type="date" wire:model.change="dateFrom" class="form-input-modern w-full rounded-2xl border border-slate-200">
                </div>

                <div>
                    <label class="mb-2 block text-sm font-medium text-slate-700">To Date</label>
                    <input type="date" wire:model.change="dateTo" class="form-input-modern w-full rounded-2xl border border-slate-200">
                </div>
            </div>

            <div class="flex gap-2">
                <button
                    wire:click="resetFilters"
                    class="rounded-2xl border border-slate-300 px-4 py-2.5 text-sm font-medium text-slate-700 transition hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-red-500"
                >
                    <svg class="mr-2 inline-block h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                    Reset Filters
                </button>
            </div>
        </div>
    </div>

    @if($orders->count() > 0)
        <div class="hidden overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm md:block">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200">
                    <thead class="bg-slate-50/80">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wide text-slate-600">Order #</th>
                            <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wide text-slate-600">Customer</th>
                            @if(!$isVendorView)
                                <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wide text-slate-600">Vendor(s)</th>
                            @endif
                            <th class="cursor-pointer px-6 py-4 text-left text-xs font-bold uppercase tracking-wide text-slate-600" wire:click="sortBy('total')">
                                <div class="flex items-center gap-1">
                                    <span>Amount</span>
                                    @if($sortField === 'total')
                                        <svg class="h-4 w-4 transition-transform {{ $sortDirection === 'asc' ? 'rotate-180' : '' }}" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M10 3a1 1 0 01.707.293l4 4a1 1 0 11-1.414 1.414L10 5.414 6.707 8.707A1 1 0 015.293 7.293l4-4A1 1 0 0110 3z" clip-rule="evenodd" />
                                        </svg>
                                    @endif
                                </div>
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wide text-slate-600">Status</th>
                            <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wide text-slate-600">Payment</th>
                            <th class="cursor-pointer px-6 py-4 text-left text-xs font-bold uppercase tracking-wide text-slate-600" wire:click="sortBy('created_at')">
                                <div class="flex items-center gap-1">
                                    <span>Date</span>
                                    @if($sortField === 'created_at')
                                        <svg class="h-4 w-4 transition-transform {{ $sortDirection === 'asc' ? 'rotate-180' : '' }}" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M10 3a1 1 0 01.707.293l4 4a1 1 0 11-1.414 1.414L10 5.414 6.707 8.707A1 1 0 015.293 7.293l4-4A1 1 0 0110 3z" clip-rule="evenodd" />
                                        </svg>
                                    @endif
                                </div>
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wide text-slate-600">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        @foreach($orders as $order)
                            @php
                                $vendorsInOrder = $order->items->pluck('vendor')->filter()->unique('id');
                            @endphp
                            <tr class="transition hover:bg-slate-50">
                                <td class="whitespace-nowrap px-6 py-4">
                                    <a href="{{ route('admin.orders.show', $order) }}" class="font-semibold text-red-600 transition hover:text-red-800">
                                        {{ $order->order_number }}
                                    </a>
                                </td>
                                <td class="whitespace-nowrap px-6 py-4">
                                    <div>
                                        <div class="text-sm font-medium text-slate-900">{{ $order->shipping_first_name }} {{ $order->shipping_last_name }}</div>
                                        <div class="text-xs text-slate-500">{{ $order->shipping_email }}</div>
                                        @if($isVendorView)
                                            <div class="mt-1 text-xs text-slate-500">{{ $order->shipping_phone }}</div>
                                        @endif
                                    </div>
                                </td>
                                @if(!$isVendorView)
                                    <td class="px-6 py-4">
                                        <div class="space-y-1">
                                            @forelse($vendorsInOrder as $vendor)
                                                <div class="text-sm">
                                                    <span class="font-medium text-violet-600">{{ $vendor->store_name ?: $vendor->name }}</span>
                                                    @if(!$vendor->isVendorVerified())
                                                        <span class="ml-2 inline-flex items-center rounded-full bg-yellow-100 px-1.5 py-0.5 text-xs font-medium text-yellow-800">Pending</span>
                                                    @endif
                                                </div>
                                            @empty
                                                <span class="text-sm text-slate-400">No vendor info</span>
                                            @endforelse
                                        </div>
                                    </td>
                                @endif
                                <td class="whitespace-nowrap px-6 py-4">
                                    <div class="text-sm font-bold text-slate-900">NGN {{ number_format($isVendorView ? $order->items->sum('total') : $order->total, 2) }}</div>
                                    <div class="text-xs text-slate-500">{{ $order->items->count() }} item(s)</div>
                                </td>
                                <td class="whitespace-nowrap px-6 py-4">
                                    <div class="flex items-center gap-2">
                                        <span class="{{ $order->status_badge['status'] }} rounded-full px-3 py-1 text-xs font-semibold">
                                            {{ ucfirst(str_replace('_', ' ', $order->status)) }}
                                        </span>
                                        @if(!$isVendorView)
                                            <div class="relative" x-data="{ open: false }">
                                                <button @click="open = !open" class="rounded p-1 text-slate-400 transition hover:bg-slate-100 hover:text-slate-600">
                                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                                    </svg>
                                                </button>
                                                <div x-show="open" @click.away="open = false" class="absolute z-10 mt-2 w-48 rounded-2xl border border-slate-200 bg-white shadow-xl">
                                                    <div class="py-2">
                                                        @foreach($statusOptions as $status)
                                                            <button wire:click="updateOrderStatus({{ $order->id }}, '{{ $status }}')" @click="open = false" class="block w-full px-4 py-2 text-left text-sm text-slate-700 transition hover:bg-red-50 hover:text-red-600">
                                                                {{ ucfirst(str_replace('_', ' ', $status)) }}
                                                            </button>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </td>
                                <td class="whitespace-nowrap px-6 py-4">
                                    <div class="flex items-center gap-2">
                                        <span class="{{ $order->status_badge['payment'] }} rounded-full px-3 py-1 text-xs font-semibold">
                                            {{ ucfirst($order->payment_status) }}
                                        </span>
                                        @if(!$isVendorView)
                                            <div class="relative" x-data="{ open: false }">
                                                <button @click="open = !open" class="rounded p-1 text-slate-400 transition hover:bg-slate-100 hover:text-slate-600">
                                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                                    </svg>
                                                </button>
                                                <div x-show="open" @click.away="open = false" class="absolute z-10 mt-2 w-48 rounded-2xl border border-slate-200 bg-white shadow-xl">
                                                    <div class="py-2">
                                                        @foreach($paymentOptions as $paymentStatus)
                                                            <button wire:click="updatePaymentStatus({{ $order->id }}, '{{ $paymentStatus }}')" @click="open = false" class="block w-full px-4 py-2 text-left text-sm text-slate-700 transition hover:bg-emerald-50 hover:text-emerald-600">
                                                                {{ ucfirst($paymentStatus) }}
                                                            </button>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm">
                                    <div class="font-medium text-slate-900">{{ $order->created_at->format('M d, Y') }}</div>
                                    <div class="text-xs text-slate-500">{{ $order->created_at->format('H:i A') }}</div>
                                </td>
                                <td class="whitespace-nowrap px-6 py-4">
                                    <div class="flex items-center gap-1">
                                        <a href="{{ route('admin.orders.show', $order) }}" class="inline-flex items-center justify-center rounded-xl p-2 text-slate-600 transition hover:bg-slate-100 hover:text-slate-900" title="View details">
                                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                        </a>
                                        @if(!$isVendorView)
                                            <a href="{{ route('admin.orders.edit', $order) }}" class="inline-flex items-center justify-center rounded-xl p-2 text-blue-600 transition hover:bg-blue-50 hover:text-blue-900" title="Edit order">
                                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                            </a>
                                            <button wire:click="confirmDelete({{ $order->id }})" class="inline-flex items-center justify-center rounded-xl p-2 text-red-600 transition hover:bg-red-50 hover:text-red-900" title="Delete order">
                                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="space-y-4 md:hidden">
            @foreach($orders as $order)
                @php
                    $vendorsInOrder = $order->items->pluck('vendor')->filter()->unique('id');
                @endphp
                <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white p-5 shadow-sm transition hover:shadow-md">
                    <div class="mb-4 flex items-start justify-between gap-4">
                        <div>
                            <a href="{{ route('admin.orders.show', $order) }}" class="text-lg font-bold text-red-600 transition hover:text-red-800">
                                {{ $order->order_number }}
                            </a>
                            <p class="mt-1 text-sm text-slate-500">{{ $order->shipping_first_name }} {{ $order->shipping_last_name }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-lg font-bold text-slate-900">NGN {{ number_format($isVendorView ? $order->items->sum('total') : $order->total, 2) }}</p>
                            <p class="text-xs text-slate-500">{{ $order->items->count() }} item(s)</p>
                        </div>
                    </div>

                    @if(!$isVendorView && $vendorsInOrder->count() > 0)
                        <div class="mb-4 rounded-2xl bg-violet-50 p-3">
                            <p class="mb-2 text-xs font-semibold text-violet-600">Vendor(s)</p>
                            <div class="flex flex-wrap gap-2">
                                @foreach($vendorsInOrder as $vendor)
                                    <span class="rounded-full bg-violet-100 px-2.5 py-1 text-xs font-medium text-violet-700">
                                        {{ $vendor->store_name ?: $vendor->name }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    @if($isVendorView)
                        <div class="mb-4 rounded-2xl bg-slate-50 px-4 py-3">
                            <p class="text-xs font-semibold uppercase tracking-[0.16em] text-slate-500">Customer Contact</p>
                            <p class="mt-1 text-sm font-medium text-slate-900">{{ $order->shipping_email }}</p>
                            <p class="mt-1 text-xs text-slate-500">{{ $order->shipping_phone }}</p>
                        </div>
                    @endif

                    <div class="mb-4 grid grid-cols-2 gap-3">
                        <div>
                            <p class="mb-1 text-xs font-medium text-slate-500">Status</p>
                            <span class="{{ $order->status_badge['status'] }} inline-block rounded-full px-3 py-1.5 text-xs font-semibold">
                                {{ ucfirst(str_replace('_', ' ', $order->status)) }}
                            </span>
                        </div>
                        <div>
                            <p class="mb-1 text-xs font-medium text-slate-500">Payment</p>
                            <span class="{{ $order->status_badge['payment'] }} inline-block rounded-full px-3 py-1.5 text-xs font-semibold">
                                {{ ucfirst($order->payment_status) }}
                            </span>
                        </div>
                    </div>

                    <div class="mb-4 border-b border-slate-100 pb-4 text-xs text-slate-500">
                        {{ $order->created_at->format('M d, Y') }} • {{ $order->created_at->format('H:i A') }}
                    </div>

                    <div class="flex items-center justify-between gap-2">
                        <a href="{{ route('admin.orders.show', $order) }}" class="flex-1 rounded-2xl bg-red-600 px-3 py-2.5 text-center text-sm font-medium text-white transition hover:bg-red-700">
                            {{ $isVendorView ? 'Open Order' : 'View' }}
                        </a>
                        @if(!$isVendorView)
                            <a href="{{ route('admin.orders.edit', $order) }}" class="flex-1 rounded-2xl bg-blue-600 px-3 py-2.5 text-center text-sm font-medium text-white transition hover:bg-blue-700">
                                Edit
                            </a>
                            <button wire:click="confirmDelete({{ $order->id }})" class="flex-1 rounded-2xl bg-red-100 px-3 py-2.5 text-sm font-medium text-red-600 transition hover:bg-red-200">
                                Delete
                            </button>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="rounded-3xl border border-dashed border-slate-300 bg-slate-50 p-12 text-center">
            <svg class="mx-auto mb-4 h-16 w-16 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
            </svg>
            <h3 class="mb-2 text-lg font-semibold text-slate-900">No Orders Found</h3>
            <p class="text-slate-500">
                @if($search || $statusFilter || $paymentStatusFilter || $vendorFilter)
                    Try adjusting your filters to find what you're looking for.
                @else
                    {{ $isVendorView ? 'Your store has no matching orders yet.' : 'No orders have been placed yet.' }}
                @endif
            </p>
        </div>
    @endif

    @if($orders->hasPages())
        <div class="mt-8">
            {{ $orders->links() }}
        </div>
    @endif

    @if($showDeleteModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex min-h-screen items-end justify-center px-4 pb-20 pt-4 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-black/50 transition-opacity" aria-hidden="true"></div>
                <div class="inline-block transform overflow-hidden rounded-2xl bg-white text-left align-bottom shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg sm:align-middle">
                    <div class="bg-white px-6 pb-4 pt-6 sm:p-8">
                        <div class="sm:flex sm:items-start">
                            <div class="mx-auto flex h-14 w-14 flex-shrink-0 items-center justify-center rounded-full bg-red-100 sm:mx-0 sm:h-12 sm:w-12">
                                <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.346 16.5c-.77.833.192 2.5 1.732 2.5z" />
                                </svg>
                            </div>
                            <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left">
                                <h3 class="text-xl font-bold leading-6 text-slate-900" id="modal-title">Delete Order</h3>
                                <div class="mt-3">
                                    <p class="text-sm text-slate-500">Are you sure you want to delete this order? This action cannot be undone.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-slate-50 px-6 py-4 sm:flex sm:flex-row-reverse sm:gap-3 sm:px-8">
                        <button type="button" wire:click="deleteOrder" class="btn-danger inline-flex w-full justify-center sm:w-auto">
                            <svg class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                            Delete
                        </button>
                        <button type="button" wire:click="$set('showDeleteModal', false)" class="btn-secondary mt-3 inline-flex w-full justify-center sm:mt-0 sm:w-auto">
                            Cancel
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
