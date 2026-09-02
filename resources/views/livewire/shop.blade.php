<div x-data="{ filtersOpen: false }" class="bg-gray-50">
    @php
        $cartCount = 0;
        $cartTotal = 0;

        foreach ($cart as $item) {
            $cartCount += $item['quantity'];
            $cartTotal += $item['price'] * $item['quantity'];
        }

        $hasFilters = $search !== '' || $category !== '' || $minPrice > 0 || $maxPrice < 100000 || $sortBy !== 'latest';
    @endphp

    <section class="border-b border-gray-200 bg-white">
        <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
            <div class="overflow-hidden rounded-3xl border border-gray-200 bg-white shadow-sm">
                <div class="relative overflow-hidden bg-gradient-to-br from-slate-950 via-slate-900 to-red-900 px-6 py-8 text-white sm:px-8 lg:px-10 lg:py-10">
                    <div class="absolute inset-0 opacity-10" style="background-image: radial-gradient(circle at 1px 1px, rgba(255,255,255,0.4) 1px, transparent 1px); background-size: 24px 24px;"></div>

                    <div class="relative grid gap-8 lg:grid-cols-[minmax(0,1.4fr)_340px] lg:items-center">
                        <div>
                            <div class="inline-flex items-center rounded-full border border-white/15 bg-white/10 px-4 py-2 text-xs font-semibold uppercase tracking-[0.22em] text-white">
                                Curated Marketplace
                            </div>
                            <h1 class="mt-5 text-4xl font-bold tracking-tight text-white sm:text-5xl">Shop smarter, faster, and with more confidence.</h1>
                            <p class="mt-4 max-w-2xl text-sm leading-7 text-slate-100 sm:text-base">
                                Browse verified products, compare prices quickly, and jump into checkout with a cleaner shopping experience built for mobile and desktop.
                            </p>

                            <div class="mt-6 flex flex-wrap gap-3">
                                <span class="rounded-full border border-white/15 bg-white/10 px-4 py-2 text-sm font-medium text-white">
                                    {{ number_format($products->total()) }} products available
                                </span>
                                <span class="rounded-full border border-white/15 bg-white/10 px-4 py-2 text-sm font-medium text-white">
                                    {{ $categories->count() }} active categories
                                </span>
                                <span class="rounded-full border border-white/15 bg-white/10 px-4 py-2 text-sm font-medium text-white">
                                    Fast cart updates
                                </span>
                            </div>
                        </div>

                        <div class="rounded-3xl border border-white/10 bg-white/10 p-5 text-white backdrop-blur">
                            <p class="text-xs font-semibold uppercase tracking-[0.22em] text-slate-200">Shop summary</p>
                            <div class="mt-4 grid grid-cols-1 gap-3 min-[430px]:grid-cols-2">
                                <div class="rounded-2xl bg-white/10 p-4">
                                    <p class="text-xs uppercase tracking-[0.18em] text-slate-300">Showing</p>
                                    <p class="mt-2 text-3xl font-bold text-white">{{ $products->count() }}</p>
                                    <p class="mt-1 text-xs text-slate-200">products on this page</p>
                                </div>
                                <div class="rounded-2xl bg-white/10 p-4">
                                    <p class="text-xs uppercase tracking-[0.18em] text-slate-300">Results</p>
                                    <p class="mt-2 text-3xl font-bold text-white">{{ number_format($products->total()) }}</p>
                                    <p class="mt-1 text-xs text-slate-200">matching items total</p>
                                </div>
                                <div class="rounded-2xl bg-white/10 p-4">
                                    <p class="text-xs uppercase tracking-[0.18em] text-slate-300">Cart</p>
                                    <p class="mt-2 text-3xl font-bold text-white">{{ number_format($cartCount) }}</p>
                                    <p class="mt-1 text-xs text-slate-200">items saved already</p>
                                </div>
                                <div class="rounded-2xl bg-white/10 p-4">
                                    <p class="text-xs uppercase tracking-[0.18em] text-slate-300">Sort</p>
                                    <p class="mt-2 text-lg font-semibold text-white">
                                        @switch($sortBy)
                                            @case('popular') Popular @break
                                            @case('price_low') Price Low @break
                                            @case('price_high') Price High @break
                                            @case('name') Name A-Z @break
                                            @default Latest
                                        @endswitch
                                    </p>
                                    <p class="mt-1 text-xs text-slate-200">current browsing mode</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="grid gap-4 border-t border-gray-200 bg-gray-50 px-6 py-6 sm:px-8 lg:grid-cols-4 lg:px-10">
                    <div class="rounded-3xl border border-gray-200 bg-white p-5 shadow-sm">
                        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-gray-400">Verified listings</p>
                        <p class="mt-3 text-sm leading-6 text-gray-600">Browse products in a cleaner catalog connected to active sellers and live store pages.</p>
                    </div>
                    <div class="rounded-3xl border border-gray-200 bg-white p-5 shadow-sm">
                        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-gray-400">Responsive browsing</p>
                        <p class="mt-3 text-sm leading-6 text-gray-600">The shop is organized for better scanning on mobile, tablet, and desktop layouts.</p>
                    </div>
                    <div class="rounded-3xl border border-gray-200 bg-white p-5 shadow-sm">
                        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-gray-400">Faster product actions</p>
                        <p class="mt-3 text-sm leading-6 text-gray-600">Add items to cart and open vendor stores without unnecessary page friction.</p>
                    </div>
                    <div class="rounded-3xl border border-gray-200 bg-white p-5 shadow-sm">
                        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-gray-400">Secure checkout flow</p>
                        <p class="mt-3 text-sm leading-6 text-gray-600">Move from discovery to checkout with wallet, Paystack, and order tracking support.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="py-8">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="grid gap-6 lg:grid-cols-[300px_minmax(0,1fr)]">
                <aside class="space-y-5">
                    <button
                        type="button"
                        @click="filtersOpen = !filtersOpen"
                        class="flex w-full items-center justify-between rounded-2xl border border-gray-200 bg-white px-5 py-4 text-left shadow-sm lg:hidden"
                    >
                        <span>
                            <span class="block text-sm font-semibold text-gray-900">Filters and categories</span>
                            <span class="mt-1 block text-xs text-gray-500">Refine the catalog faster</span>
                        </span>
                        <svg class="h-5 w-5 text-gray-500 transition" :class="{ 'rotate-180': filtersOpen }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    <div class="space-y-5" :class="{ 'hidden lg:block': !filtersOpen, 'block': filtersOpen }">
                        <div class="rounded-3xl border border-gray-200 bg-white p-5 shadow-sm">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-red-500">Search</p>
                                    <h2 class="mt-2 text-xl font-semibold text-gray-900">Find the right product</h2>
                                </div>
                                @if($hasFilters)
                                    <span class="rounded-full bg-red-50 px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.18em] text-red-600">
                                        Active filters
                                    </span>
                                @endif
                            </div>

                            <div class="mt-5 space-y-4">
                                <div>
                                    <label for="shop-search" class="mb-2 block text-sm font-medium text-gray-700">Search products</label>
                                    <div class="relative">
                                        <input
                                            id="shop-search"
                                            type="text"
                                            wire:model.live.debounce.850ms="search"
                                            placeholder="Search by name or description"
                                            class="w-full rounded-2xl border-gray-300 px-4 py-3 pl-11 text-sm focus:border-red-500 focus:ring-red-500"
                                        >
                                        <svg class="pointer-events-none absolute left-4 top-3.5 h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                        </svg>
                                    </div>
                                </div>

                                <div>
                                    <label for="shop-category" class="mb-2 block text-sm font-medium text-gray-700">Category</label>
                                    <select
                                        id="shop-category"
                                        wire:model.change="category"
                                        class="w-full rounded-2xl border-gray-300 px-4 py-3 text-sm focus:border-red-500 focus:ring-red-500"
                                    >
                                        <option value="">All categories</option>
                                        @foreach($categories as $categoryItem)
                                            <option value="{{ $categoryItem->slug }}">
                                                {{ $categoryItem->name }} ({{ $categoryItem->products_count }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div>
                                    <label for="shop-sort" class="mb-2 block text-sm font-medium text-gray-700">Sort by</label>
                                    <select
                                        id="shop-sort"
                                        wire:model.change="sortBy"
                                        class="w-full rounded-2xl border-gray-300 px-4 py-3 text-sm focus:border-red-500 focus:ring-red-500"
                                    >
                                        <option value="latest">Newest</option>
                                        <option value="popular">Popular</option>
                                        <option value="price_low">Price: Low to High</option>
                                        <option value="price_high">Price: High to Low</option>
                                        <option value="name">Name: A to Z</option>
                                    </select>
                                </div>

                                <div class="grid grid-cols-1 gap-3 min-[430px]:grid-cols-2">
                                    <div>
                                        <label for="shop-min-price" class="mb-2 block text-sm font-medium text-gray-700">Min price</label>
                                        <input
                                            id="shop-min-price"
                                            type="number"
                                            wire:model.blur="minPrice"
                                            min="0"
                                            class="w-full rounded-2xl border-gray-300 px-4 py-3 text-sm focus:border-red-500 focus:ring-red-500"
                                        >
                                    </div>
                                    <div>
                                        <label for="shop-max-price" class="mb-2 block text-sm font-medium text-gray-700">Max price</label>
                                        <input
                                            id="shop-max-price"
                                            type="number"
                                            wire:model.blur="maxPrice"
                                            min="0"
                                            class="w-full rounded-2xl border-gray-300 px-4 py-3 text-sm focus:border-red-500 focus:ring-red-500"
                                        >
                                    </div>
                                </div>

                                <button
                                    wire:click="clearFilters"
                                    type="button"
                                    class="inline-flex w-full items-center justify-center rounded-2xl border border-gray-300 bg-white px-5 py-3 text-sm font-semibold text-gray-700 transition hover:bg-gray-50"
                                >
                                    Clear all filters
                                </button>
                            </div>
                        </div>

                        <div class="rounded-3xl border border-gray-200 bg-white p-5 shadow-sm">
                            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-red-500">Your cart</p>
                            <h3 class="mt-2 text-xl font-semibold text-gray-900">Quick summary</h3>

                            @if($cartCount > 0)
                                <div class="mt-5 space-y-3">
                                    <div class="flex items-center justify-between rounded-2xl border border-gray-200 bg-gray-50 px-4 py-3">
                                        <span class="text-sm text-gray-600">Items</span>
                                        <span class="text-sm font-semibold text-gray-900">{{ $cartCount }}</span>
                                    </div>
                                    <div class="flex items-center justify-between rounded-2xl border border-gray-200 bg-gray-50 px-4 py-3">
                                        <span class="text-sm text-gray-600">Total</span>
                                        <span class="text-sm font-semibold text-gray-900">N{{ number_format($cartTotal, 2) }}</span>
                                    </div>
                                    <a href="{{ route('cart') }}" class="inline-flex w-full items-center justify-center rounded-2xl bg-gray-900 px-5 py-3 text-sm font-semibold text-white transition hover:bg-black">
                                        View cart
                                    </a>
                                </div>
                            @else
                                <div class="mt-5 rounded-2xl border border-dashed border-gray-300 bg-gray-50 px-4 py-6 text-center">
                                    <p class="text-sm text-gray-600">Your cart is empty right now.</p>
                                    <p class="mt-2 text-xs text-gray-500">Add a few products and they will appear here instantly.</p>
                                </div>
                            @endif
                        </div>

                        <div class="rounded-3xl border border-gray-200 bg-white p-5 shadow-sm">
                            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-red-500">Why shop here</p>
                            <div class="mt-4 space-y-3">
                                <div class="rounded-2xl border border-gray-200 bg-gray-50 p-4">
                                    <p class="text-sm font-semibold text-gray-900">Verified sellers</p>
                                    <p class="mt-1 text-xs leading-5 text-gray-600">Vendor storefronts make it easier to inspect who is selling each product.</p>
                                </div>
                                <div class="rounded-2xl border border-gray-200 bg-gray-50 p-4">
                                    <p class="text-sm font-semibold text-gray-900">Fast discovery</p>
                                    <p class="mt-1 text-xs leading-5 text-gray-600">Use search, category, and price filters without leaving the page.</p>
                                </div>
                                <div class="rounded-2xl border border-gray-200 bg-gray-50 p-4">
                                    <p class="text-sm font-semibold text-gray-900">Responsive shopping</p>
                                    <p class="mt-1 text-xs leading-5 text-gray-600">Everything is organized to feel smoother on phones and larger screens.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </aside>

                <div class="space-y-6">
                    <div class="rounded-3xl border border-gray-200 bg-white p-5 shadow-sm">
                        <div class="flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-red-500">Marketplace catalog</p>
                                <h2 class="mt-2 text-2xl font-semibold text-gray-900">Browse all active products</h2>
                                <p class="mt-2 text-sm text-gray-500">
                                    Showing {{ $products->firstItem() ?? 0 }} to {{ $products->lastItem() ?? 0 }} of {{ $products->total() }} results.
                                </p>
                                @if($search !== '' || $category !== '')
                                    <p class="mt-2 text-xs uppercase tracking-[0.18em] text-gray-400">
                                        @if($search !== '')
                                            Search: "{{ $search }}"
                                        @endif
                                        @if($search !== '' && $category !== '')
                                            |
                                        @endif
                                        @if($category !== '')
                                            Category filter applied
                                        @endif
                                    </p>
                                @endif
                            </div>

                            <div class="flex items-center gap-3">
                                <label for="shop-per-page" class="text-sm font-medium text-gray-600">Show</label>
                                <select
                                    id="shop-per-page"
                                    wire:model.change="perPage"
                                    class="rounded-2xl border-gray-300 px-4 py-3 text-sm focus:border-red-500 focus:ring-red-500"
                                >
                                    <option value="12">12</option>
                                    <option value="24">24</option>
                                    <option value="48">48</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    @if($products->count() > 0)
                        <div class="grid grid-cols-2 gap-3 md:grid-cols-3 md:gap-4 xl:grid-cols-4">
                            @foreach($products as $product)
                                <x-instant-product-card :product="$product" />
                            @endforeach
                        </div>

                        <div class="rounded-3xl border border-gray-200 bg-white p-5 shadow-sm">
                            {{ $products->links() }}
                        </div>
                    @else
                        <div class="rounded-3xl border border-gray-200 bg-white px-6 py-14 text-center shadow-sm">
                            <p class="text-sm font-semibold uppercase tracking-[0.2em] text-red-500">No products found</p>
                            <h3 class="mt-3 text-3xl font-semibold text-gray-900">Nothing matches this filter right now.</h3>
                            <p class="mx-auto mt-3 max-w-2xl text-sm leading-6 text-gray-500">
                                Try clearing the search or adjusting your price range and category filters to discover more products.
                            </p>
                            <div class="mt-6">
                                <button
                                    wire:click="clearFilters"
                                    type="button"
                                    class="inline-flex items-center justify-center rounded-2xl bg-gray-900 px-5 py-3 text-sm font-semibold text-white transition hover:bg-black"
                                >
                                    Clear filters
                                </button>
                            </div>
                        </div>
                    @endif

                    <div class="grid gap-4 md:grid-cols-3">
                        <div class="rounded-3xl border border-gray-200 bg-white p-5 text-center shadow-sm">
                            <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-2xl bg-red-50 text-red-600">
                                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
                                </svg>
                            </div>
                            <h3 class="mt-4 text-lg font-semibold text-gray-900">Free shipping support</h3>
                            <p class="mt-2 text-sm leading-6 text-gray-600">Orders above N{{ number_format(\App\Helpers\SettingsHelper::freeShippingThreshold(), 0) }} can qualify for free delivery rules.</p>
                        </div>

                        <div class="rounded-3xl border border-gray-200 bg-white p-5 text-center shadow-sm">
                            <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-2xl bg-red-50 text-red-600">
                                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                </svg>
                            </div>
                            <h3 class="mt-4 text-lg font-semibold text-gray-900">Secure payments</h3>
                            <p class="mt-2 text-sm leading-6 text-gray-600">Customers can complete checkout with Paystack or supported wallet flows in a secure payment path.</p>
                        </div>

                        <div class="rounded-3xl border border-gray-200 bg-white p-5 text-center shadow-sm">
                            <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-2xl bg-red-50 text-red-600">
                                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 15v-1a4 4 0 00-4-4H8m0 0l3 3m-3-3l3-3m9 14V5a2 2 0 00-2-2H6a2 2 0 00-2 2v16l4-2 4 2 4-2 4 2z" />
                                </svg>
                            </div>
                            <h3 class="mt-4 text-lg font-semibold text-gray-900">Easy returns flow</h3>
                            <p class="mt-2 text-sm leading-6 text-gray-600">The shop is structured to support clearer ordering, delivery, and post-purchase management.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
