@props(['product'])

@php
    $initialQuantity = (int) data_get(session('cart', []), $product->id . '.quantity', 0);

    $productData = [
        'id' => $product->id,
        'name' => $product->name,
        'price' => $product->price,
        'image' => $product->main_image,
        'slug' => $product->slug,
        'compare_price' => $product->compare_price,
    ];

    $discountPercent = null;

    if ($product->compare_price && $product->compare_price > $product->price) {
        $discountPercent = round((($product->compare_price - $product->price) / $product->compare_price) * 100);
    }
@endphp

<div
    x-data="{
        productId: {{ $product->id }},
        quantity: {{ $initialQuantity }},
        busy: false,
        maxStock: {{ max((int) $product->quantity, 0) }},
        vendorAvailable: @js($product->vendorIsAvailable()),
        productData: {{ json_encode($productData) }},

        init() {
            window.addEventListener('cart:changed', (event) => {
                const items = event.detail?.items || {};
                const item = items[String(this.productId)] || items[this.productId] || null;
                this.quantity = item ? Number(item.quantity || 0) : 0;
            });
        },

        async addToCart() {
            if (this.busy || this.quantity > 0 || !this.vendorAvailable || this.maxStock < 1) {
                return;
            }

            this.busy = true;

            try {
                const response = await window.KeffiCart.add(this.productId, 1);
                this.quantity = Number(response?.item?.quantity || 1);
                window.KeffiCart.notify(response.message, 'success');
            } catch (error) {
                window.KeffiCart.notify(error.message, 'error');
            } finally {
                this.busy = false;
            }
        },

        async increment() {
            if (this.busy || this.quantity >= this.maxStock) {
                return;
            }

            this.busy = true;

            try {
                const nextQuantity = this.quantity + 1;
                const response = await window.KeffiCart.update(this.productId, nextQuantity);
                this.quantity = Number(response?.item?.quantity || nextQuantity);
            } catch (error) {
                window.KeffiCart.notify(error.message, 'error');
            } finally {
                this.busy = false;
            }
        },

        async decrement() {
            if (this.busy || this.quantity <= 0) {
                return;
            }

            this.busy = true;

            try {
                const nextQuantity = Math.max(this.quantity - 1, 0);
                const response = await window.KeffiCart.update(this.productId, nextQuantity);
                this.quantity = Number(response?.item?.quantity || 0);
            } catch (error) {
                window.KeffiCart.notify(error.message, 'error');
            } finally {
                this.busy = false;
            }
        }
    }"
    class="instant-product-card group relative min-w-0 overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-sm transition duration-300 hover:shadow-xl"
    data-tilt-card
>
    @if($discountPercent)
        <div class="absolute left-2 top-2 z-10 rounded-lg bg-gradient-to-r from-red-600 to-rose-600 px-2 py-1 text-[10px] font-bold text-white shadow-lg sm:left-3 sm:top-3 sm:rounded-xl sm:text-xs">
            -{{ $discountPercent }}%
        </div>
    @endif

    <button
        type="button"
        onclick="event.preventDefault(); event.stopPropagation(); window.KeffiCart.share(@js(route('product.show', $product->slug)), @js($product->name));"
        class="absolute right-2 top-2 z-10 inline-flex h-9 w-9 items-center justify-center rounded-full bg-white/95 text-gray-700 shadow-lg ring-1 ring-gray-200 transition hover:bg-red-600 hover:text-white focus:outline-none focus:ring-2 focus:ring-red-500 sm:right-3 sm:top-3 sm:h-10 sm:w-10"
        title="Share product"
        aria-label="Share {{ $product->name }}"
    >
        <svg class="h-4 w-4 sm:h-5 sm:w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
            <circle cx="18" cy="5" r="3" stroke-width="2" />
            <circle cx="6" cy="12" r="3" stroke-width="2" />
            <circle cx="18" cy="19" r="3" stroke-width="2" />
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.7 10.7l6.6-4.4M8.7 13.3l6.6 4.4" />
        </svg>
    </button>

    <a href="{{ route('product.show', $product->slug) }}" class="block">
        <div class="relative aspect-square overflow-hidden bg-gray-100">
            @if($product->main_image)
                <img
                    src="{{ str_starts_with($product->main_image, 'http://') || str_starts_with($product->main_image, 'https://') ? $product->main_image : asset('storage/' . ltrim($product->main_image, '/')) }}"
                    alt="{{ $product->name }}"
                    class="h-full w-full object-cover transition duration-500 group-hover:scale-105"
                >
            @else
                <div class="flex h-full w-full items-center justify-center bg-gradient-to-br from-gray-100 to-gray-200">
                    <svg class="h-10 w-10 text-gray-300 sm:h-14 sm:w-14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>
            @endif
        </div>
    </a>

    <div class="space-y-2.5 p-3 sm:space-y-3 sm:p-4">
        <div class="min-w-0">
            <a href="{{ route('product.show', $product->slug) }}" class="block">
                <h3 class="line-clamp-2 break-words text-sm font-semibold leading-5 text-gray-900 transition group-hover:text-red-600 sm:text-base">
                    {{ $product->name }}
                </h3>
            </a>

            @if($product->vendor && $product->vendor->store_name)
                @if($product->vendor->storefrontUrl())
                    <a
                        href="{{ $product->vendor->storefrontUrl() }}"
                        class="mt-2 flex min-w-0 items-center justify-between gap-1.5 rounded-xl border border-slate-200 bg-slate-50 px-2 py-1.5 transition hover:border-red-200 hover:bg-red-50/70 sm:gap-3 sm:rounded-2xl sm:px-3 sm:py-2"
                        title="Visit {{ $product->vendor->store_name }} store"
                    >
                        <span class="flex min-w-0 flex-1 items-center gap-1.5 overflow-hidden sm:gap-2">
                            <span class="inline-flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-white text-slate-500 shadow-sm sm:h-7 sm:w-7">
                                <svg class="h-2.5 w-2.5 sm:h-3.5 sm:w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7l1.664 9.152A2 2 0 006.632 18h10.736a2 2 0 001.968-1.848L21 7M7 7V5a5 5 0 0110 0v2M5 7h14" />
                                </svg>
                            </span>
                            <span class="min-w-0 flex-1 overflow-hidden">
                                <span class="block truncate text-[9px] font-semibold leading-tight text-red-600 sm:text-xs">{{ $product->vendor->store_name }}</span>
                                <span class="block truncate text-[8px] leading-tight text-slate-400 sm:text-[11px]">View store</span>
                            </span>
                        </span>

                        <span class="flex shrink-0 items-center gap-1 sm:gap-2">
                            <span class="shrink-0 rounded-full px-1 py-0.5 text-[8px] font-semibold leading-none {{ $product->vendorIsAvailable() ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700' }} sm:px-2 sm:text-[10px]">
                                {{ $product->vendorIsAvailable() ? 'Active' : 'Inactive' }}
                            </span>
                            <svg class="h-3 w-3 shrink-0 text-slate-400 transition group-hover:text-red-500 sm:h-4 sm:w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </span>
                    </a>
                @else
                    <div class="mt-2 flex min-w-0 items-center justify-between gap-2">
                        <p class="truncate text-[11px] text-gray-500 sm:text-xs">{{ $product->vendor->store_name }}</p>
                        <span class="shrink-0 rounded-full px-1.5 py-0.5 text-[9px] font-semibold {{ $product->vendorIsAvailable() ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700' }} sm:px-2 sm:text-[10px]">
                            {{ $product->vendorIsAvailable() ? 'Active' : 'Inactive' }}
                        </span>
                    </div>
                @endif
            @endif
        </div>

        <div class="flex min-w-0 items-end gap-1.5 sm:gap-2">
            <span class="truncate text-lg font-bold text-red-600 sm:text-xl">N{{ number_format($product->price, 2) }}</span>
            @if($discountPercent)
                <span class="truncate text-xs text-gray-400 line-through sm:text-sm">N{{ number_format($product->compare_price, 2) }}</span>
            @endif
        </div>

        <template x-if="quantity === 0">
            <button
                type="button"
                x-on:click="addToCart"
                x-bind:disabled="busy || !vendorAvailable || maxStock < 1"
                class="w-full rounded-xl bg-gradient-to-r from-gray-900 to-gray-800 px-2.5 py-2 text-[11px] font-semibold leading-none text-white transition hover:from-red-600 hover:to-rose-600 disabled:cursor-not-allowed disabled:opacity-50 sm:px-4 sm:py-3 sm:text-sm"
            >
                <span x-show="!busy && vendorAvailable && maxStock > 0">Add to Cart</span>
                <span x-show="!vendorAvailable">Vendor Unavailable</span>
                <span x-show="vendorAvailable && maxStock < 1">Out of Stock</span>
                <span x-show="busy">Adding...</span>
            </button>
        </template>

        <template x-if="quantity > 0">
            <div class="rounded-2xl border border-gray-200 bg-gray-50 p-1.5 sm:p-2">
                <div class="flex items-center justify-between gap-1.5 sm:gap-2">
                    <button
                        type="button"
                        x-on:click="decrement"
                        x-bind:disabled="busy"
                        class="inline-flex h-8 w-8 items-center justify-center rounded-xl bg-white text-red-600 shadow-sm transition hover:bg-red-50 disabled:opacity-50 sm:h-10 sm:w-10"
                        aria-label="Decrease quantity"
                    >
                        <svg class="h-3 w-3 sm:h-4 sm:w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" />
                        </svg>
                    </button>

                    <div class="min-w-[2rem] flex-1 text-center sm:min-w-[3rem]">
                        <p class="text-[9px] uppercase tracking-[0.12em] text-gray-400 sm:text-xs sm:tracking-[0.18em]">In Cart</p>
                        <p class="text-sm font-bold leading-none text-gray-900 sm:text-lg" x-text="quantity"></p>
                    </div>

                    <button
                        type="button"
                        x-on:click="increment"
                        x-bind:disabled="busy || quantity >= maxStock"
                        class="inline-flex h-8 w-8 items-center justify-center rounded-xl bg-white text-red-600 shadow-sm transition hover:bg-red-50 disabled:opacity-50 sm:h-10 sm:w-10"
                        aria-label="Increase quantity"
                    >
                        <svg class="h-3 w-3 sm:h-4 sm:w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                    </button>
                </div>
            </div>
        </template>
    </div>
</div>
