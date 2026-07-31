@extends('layouts.app')

@section('title', $vendor->publicStoreName())
@section('meta_title', $vendor->publicStoreName() . ' - ' . \App\Helpers\SettingsHelper::get('site_name', config('app.name', 'E-Commerce')))
@section('meta_description', \Illuminate\Support\Str::limit($vendor->store_description ?: ('Browse products from ' . $vendor->publicStoreName() . ' and shop directly from this verified vendor store.'), 155))
@section('canonical', route('stores.show', $vendor->store_slug))
@section('share_image', $vendor->storefrontBannerUrl() ?: ($vendor->storefrontLogoUrl() ?: asset('storage/logo.png')))

@php
    $storeUrl = route('stores.show', $vendor->store_slug);
    $siteName = \App\Helpers\SettingsHelper::get('site_name', config('app.name', 'E-Commerce'));
    $digitalOfferings = (int) (($storeMetrics['digital_products'] ?? 0) + ($storeMetrics['course_products'] ?? 0));
    $socialLinksCount = collect([
        $vendor->storefrontWhatsAppUrl(),
        $vendor->storefrontInstagramUrl(),
        $vendor->storefrontFacebookUrl(),
        $vendor->storefrontWebsiteUrl(),
    ])->filter()->count();
    $storeReviewCount = (int) ($reviewSummary['total_reviews'] ?? 0);
    $storeAverageRating = (float) ($reviewSummary['average_rating'] ?? 0);
@endphp

@section('content')
    <div x-data="storefrontExperience(@js($vendor->store_slug), @js($vendor->publicStoreName()), @js($storeUrl))" class="bg-gray-50">
        <section class="border-b border-gray-200 bg-white">
            <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8 lg:py-10">
                <div class="overflow-hidden rounded-3xl border border-gray-200 bg-white shadow-sm">
                    <div
                        class="relative overflow-hidden"
                        @if($vendor->storefrontBannerUrl())
                            style="background-image: linear-gradient(135deg, rgba(17, 24, 39, 0.92), rgba(127, 29, 29, 0.72)), url('{{ $vendor->storefrontBannerUrl() }}'); background-size: cover; background-position: center;"
                        @else
                            style="background: linear-gradient(135deg, #111827 0%, #1f2937 55%, #991b1b 100%);"
                        @endif
                    >
                        <div class="absolute inset-0 opacity-10" style="background-image: radial-gradient(circle at 1px 1px, rgba(255,255,255,0.45) 1px, transparent 1px); background-size: 24px 24px;"></div>

                        <div class="relative grid gap-8 px-5 py-7 sm:px-8 lg:grid-cols-[minmax(0,1.4fr)_320px] lg:px-10 lg:py-10">
                            <div>
                                <div class="inline-flex w-full items-center justify-center gap-2 rounded-full border border-white/15 bg-white/10 px-4 py-2 text-center text-[11px] font-semibold uppercase tracking-[0.22em] text-white sm:w-auto sm:justify-start sm:text-xs">
                                    <span class="h-2.5 w-2.5 rounded-full bg-emerald-400"></span>
                                    Verified Storefront
                                </div>

                                <div class="mt-6 flex flex-col gap-5 sm:flex-row sm:items-center">
                                    <div class="flex h-24 w-24 shrink-0 items-center justify-center overflow-hidden rounded-[28px] border border-white/15 bg-white/10 text-3xl font-bold text-white shadow-lg">
                                        @if($vendor->storefrontLogoUrl())
                                            <img src="{{ $vendor->storefrontLogoUrl() }}" alt="{{ $vendor->publicStoreName() }}" class="h-full w-full object-cover">
                                        @else
                                            {{ strtoupper(\Illuminate\Support\Str::substr($vendor->publicStoreName(), 0, 1)) }}
                                        @endif
                                    </div>

                                    <div class="min-w-0">
                                        <p class="text-sm font-semibold uppercase tracking-[0.18em] text-red-100">{{ $siteName }} Vendor Store</p>
                                        <h1 class="mt-2 text-3xl font-bold tracking-tight text-white sm:text-4xl lg:text-5xl">{{ $vendor->publicStoreName() }}</h1>
                                        <p class="mt-4 max-w-3xl text-sm leading-7 text-slate-100 sm:text-base">
                                            {{ $vendor->store_description ?: 'Explore this vendor catalog, discover trusted products quickly, and shop from one organized storefront.' }}
                                        </p>
                                    </div>
                                </div>

                                <div class="mt-6 flex flex-wrap gap-3">
                                    <span class="rounded-full border border-white/15 bg-white/10 px-3 py-2 text-xs font-medium text-white sm:px-4 sm:text-sm">
                                        {{ number_format($storeMetrics['active_products'] ?? 0) }} live products
                                    </span>
                                    <span class="rounded-full border border-white/15 bg-white/10 px-3 py-2 text-xs font-medium text-white sm:px-4 sm:text-sm">
                                        {{ number_format($storeMetrics['total_sold'] ?? 0) }} sold
                                    </span>
                                    <span class="rounded-full border border-white/15 bg-white/10 px-3 py-2 text-xs font-medium text-white sm:px-4 sm:text-sm">
                                        {{ $storeReviewCount > 0 ? number_format($storeAverageRating, 1) . '/5 rating' : 'No reviews yet' }}
                                    </span>
                                    <span class="rounded-full border border-white/15 bg-white/10 px-3 py-2 text-xs font-medium text-white sm:px-4 sm:text-sm">
                                        {{ $vendor->vendorAvailabilityLabel() }}
                                    </span>
                                </div>

                                <div class="mt-8 grid gap-3 sm:flex sm:flex-wrap">
                                    <a href="#store-products" class="inline-flex w-full items-center justify-center rounded-2xl bg-white px-5 py-3 text-sm font-semibold text-gray-900 transition hover:bg-red-50 sm:w-auto">
                                        Shop This Store
                                    </a>
                                    <button type="button" @click="toggleFollow()" class="inline-flex w-full items-center justify-center rounded-2xl border border-white/15 bg-white/10 px-5 py-3 text-sm font-semibold text-white transition hover:bg-white/15 sm:w-auto">
                                        <span x-text="isFollowing ? 'Following Store' : 'Follow Store'"></span>
                                    </button>
                                    <button type="button" @click="shareStore()" class="inline-flex w-full items-center justify-center rounded-2xl border border-white/15 bg-white/10 px-5 py-3 text-sm font-semibold text-white transition hover:bg-white/15 sm:w-auto">
                                        Share Store
                                    </button>
                                    @if($vendor->storefrontWhatsAppUrl())
                                        <a href="{{ $vendor->storefrontWhatsAppUrl() }}" target="_blank" rel="noopener" class="inline-flex w-full items-center justify-center rounded-2xl border border-emerald-300/30 bg-emerald-500/20 px-5 py-3 text-sm font-semibold text-white transition hover:bg-emerald-500/30 sm:w-auto">
                                            Chat on WhatsApp
                                        </a>
                                    @endif
                                </div>
                            </div>

                            <div class="rounded-3xl border border-white/10 bg-white/10 p-5 text-white backdrop-blur">
                                <div class="flex items-start justify-between gap-4">
                                    <div>
                                        <p class="text-xs font-semibold uppercase tracking-[0.22em] text-slate-200">Store Snapshot</p>
                                        <h2 class="mt-2 text-2xl font-semibold text-white">Performance at a glance</h2>
                                        <p class="mt-2 text-sm leading-6 text-slate-200">A quick summary of this vendor's current storefront activity and trust signals.</p>
                                    </div>
                                    <span class="rounded-2xl bg-emerald-500/20 px-3 py-2 text-xs font-semibold uppercase tracking-[0.18em] text-emerald-100">
                                        {{ $vendor->isVendorVerified() ? 'Verified' : 'Pending' }}
                                    </span>
                                </div>

                                <div class="mt-5 grid grid-cols-2 gap-3">
                                    <div class="rounded-2xl bg-white/10 p-4">
                                        <p class="text-xs uppercase tracking-[0.18em] text-slate-300">Catalog</p>
                                        <p class="mt-2 text-3xl font-bold text-white">{{ number_format($storeMetrics['active_products'] ?? 0) }}</p>
                                    </div>
                                    <div class="rounded-2xl bg-white/10 p-4">
                                        <p class="text-xs uppercase tracking-[0.18em] text-slate-300">In Stock</p>
                                        <p class="mt-2 text-3xl font-bold text-white">{{ number_format($storeMetrics['in_stock_products'] ?? 0) }}</p>
                                    </div>
                                    <div class="rounded-2xl bg-white/10 p-4">
                                        <p class="text-xs uppercase tracking-[0.18em] text-slate-300">Digital</p>
                                        <p class="mt-2 text-3xl font-bold text-white">{{ number_format($digitalOfferings) }}</p>
                                    </div>
                                    <div class="rounded-2xl bg-white/10 p-4">
                                        <p class="text-xs uppercase tracking-[0.18em] text-slate-300">Sold</p>
                                        <p class="mt-2 text-3xl font-bold text-white">{{ number_format($storeMetrics['total_sold'] ?? 0) }}</p>
                                    </div>
                                </div>

                                <div class="mt-4 grid grid-cols-1 gap-3 min-[430px]:grid-cols-3">
                                    <div class="rounded-2xl bg-black/10 px-4 py-4">
                                        <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-slate-300">Rating</p>
                                        <p class="mt-2 text-lg font-semibold text-white">{{ $storeReviewCount > 0 ? number_format($storeAverageRating, 1) . '/5' : 'New' }}</p>
                                    </div>
                                    <div class="rounded-2xl bg-black/10 px-4 py-4">
                                        <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-slate-300">Reviews</p>
                                        <p class="mt-2 text-lg font-semibold text-white">{{ number_format($storeReviewCount) }}</p>
                                    </div>
                                    <div class="rounded-2xl bg-black/10 px-4 py-4">
                                        <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-slate-300">Socials</p>
                                        <p class="mt-2 text-lg font-semibold text-white">{{ $socialLinksCount }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="grid gap-4 border-t border-gray-200 bg-gray-50 px-6 py-6 sm:px-8 lg:grid-cols-4 lg:px-10">
                        @foreach($trustSignals as $signal)
                            <div class="rounded-3xl border border-gray-200 bg-white p-5 shadow-sm">
                                <div class="flex items-center justify-between gap-3">
                                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-gray-400">{{ $signal['label'] }}</p>
                                    <span @class([
                                        'rounded-full px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.18em]',
                                        'bg-emerald-50 text-emerald-700' => $signal['tone'] === 'emerald',
                                        'bg-sky-50 text-sky-700' => $signal['tone'] === 'sky',
                                        'bg-violet-50 text-violet-700' => $signal['tone'] === 'violet',
                                        'bg-amber-50 text-amber-700' => $signal['tone'] === 'amber',
                                        'bg-rose-50 text-rose-700' => $signal['tone'] === 'rose',
                                        'bg-slate-100 text-slate-600' => $signal['tone'] === 'slate',
                                    ])>{{ $signal['value'] }}</span>
                                </div>
                                <p class="mt-3 text-sm leading-6 text-gray-600">{{ $signal['description'] }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </section>

        <section class="py-8">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="grid gap-6 lg:grid-cols-[minmax(0,1fr)_320px]">
                    <div class="space-y-6">
                        <form method="GET" action="{{ route('stores.show', $vendor->store_slug) }}" class="rounded-3xl border border-gray-200 bg-white p-6 shadow-sm">
                            <div class="flex flex-col gap-2 border-b border-gray-100 pb-4 sm:flex-row sm:items-end sm:justify-between">
                                <div>
                                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-red-500">Browse this store</p>
                                    <h2 class="mt-2 text-2xl font-semibold text-gray-900">Search and filter products</h2>
                                </div>
                                <p class="text-sm text-gray-500">Quick ways to narrow the catalog without leaving the storefront.</p>
                            </div>

                            <div class="mt-5 grid gap-4 md:grid-cols-2 xl:grid-cols-4">
                                <div class="md:col-span-2 xl:col-span-1">
                                    <label for="search" class="mb-2 block text-sm font-medium text-gray-700">Search this store</label>
                                    <input
                                        id="search"
                                        name="search"
                                        value="{{ $search }}"
                                        placeholder="Search products from {{ $vendor->publicStoreName() }}"
                                        class="w-full rounded-2xl border-gray-300 px-4 py-3 text-sm focus:border-red-500 focus:ring-red-500"
                                    >
                                </div>

                                <div>
                                    <label for="sort" class="mb-2 block text-sm font-medium text-gray-700">Sort by</label>
                                    <select id="sort" name="sort" class="w-full rounded-2xl border-gray-300 px-4 py-3 text-sm focus:border-red-500 focus:ring-red-500">
                                        <option value="latest" @selected($sortBy === 'latest')>Newest</option>
                                        <option value="popular" @selected($sortBy === 'popular')>Popular</option>
                                        <option value="price_low" @selected($sortBy === 'price_low')>Price: Low to High</option>
                                        <option value="price_high" @selected($sortBy === 'price_high')>Price: High to Low</option>
                                        <option value="name" @selected($sortBy === 'name')>Name: A to Z</option>
                                    </select>
                                </div>

                                <div>
                                    <label for="category_id" class="mb-2 block text-sm font-medium text-gray-700">Category</label>
                                    <select id="category_id" name="category_id" class="w-full rounded-2xl border-gray-300 px-4 py-3 text-sm focus:border-red-500 focus:ring-red-500">
                                        <option value="">All categories</option>
                                        @foreach($categoryBreakdown as $category)
                                            <option value="{{ $category->id }}" @selected($selectedCategoryId === (int) $category->id)>
                                                {{ $category->name }} ({{ $category->products_count }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div>
                                    <label for="type" class="mb-2 block text-sm font-medium text-gray-700">Product type</label>
                                    <select id="type" name="type" class="w-full rounded-2xl border-gray-300 px-4 py-3 text-sm focus:border-red-500 focus:ring-red-500">
                                        <option value="">All types</option>
                                        @foreach($availableTypes as $type)
                                            <option value="{{ $type }}" @selected($selectedType === $type)>{{ ucfirst($type) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="mt-5 flex flex-wrap gap-3">
                                <button type="submit" class="inline-flex items-center justify-center rounded-2xl bg-gray-900 px-5 py-3 text-sm font-semibold text-white transition hover:bg-black">
                                    Apply Filters
                                </button>
                                <a href="{{ route('stores.show', $vendor->store_slug) }}" class="inline-flex items-center justify-center rounded-2xl border border-gray-300 bg-white px-5 py-3 text-sm font-semibold text-gray-700 transition hover:bg-gray-50">
                                    Reset
                                </a>
                            </div>
                        </form>

                        <div class="rounded-3xl border border-gray-200 bg-white p-6 shadow-sm">
                            <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                                <div>
                                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-red-500">About the store</p>
                                    <h2 class="mt-2 text-2xl font-semibold text-gray-900">{{ $vendor->publicStoreName() }}</h2>
                                </div>
                                <a href="{{ route('shop') }}" class="inline-flex items-center rounded-2xl border border-gray-300 px-4 py-3 text-sm font-semibold text-gray-700 transition hover:bg-gray-50">
                                    Back to Marketplace
                                </a>
                            </div>

                            <p class="mt-4 text-sm leading-7 text-gray-600">
                                {{ $vendor->store_description ?: ($vendor->publicStoreName() . ' is a verified vendor on ' . $siteName . '. Explore the full catalog, compare offers, and shop in one focused storefront.') }}
                            </p>

                            @if($categoryBreakdown->count())
                                <div class="mt-6">
                                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-gray-400">Store categories</p>
                                    <div class="mt-3 flex flex-wrap gap-3">
                                        @foreach($categoryBreakdown as $category)
                                            <a href="{{ route('category.show', $category->slug) }}" class="inline-flex items-center gap-2 rounded-full border border-gray-200 bg-gray-50 px-4 py-2 text-sm font-medium text-gray-700 transition hover:border-red-200 hover:bg-red-50 hover:text-red-700">
                                                <span>{{ $category->name }}</span>
                                                <span class="rounded-full bg-white px-2 py-0.5 text-xs text-gray-500">{{ $category->products_count }}</span>
                                            </a>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>

                        @if($recentReviews->count())
                            <div class="rounded-3xl border border-gray-200 bg-white p-6 shadow-sm">
                                <div class="flex flex-col gap-2 border-b border-gray-100 pb-4 sm:flex-row sm:items-end sm:justify-between">
                                    <div>
                                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-red-500">Shopper reviews</p>
                                        <h2 class="mt-2 text-2xl font-semibold text-gray-900">Recent feedback</h2>
                                    </div>
                                    <p class="text-sm text-gray-500">Product reviews from buyers help shoppers trust the store faster.</p>
                                </div>

                                <div class="mt-5 grid gap-4 md:grid-cols-2">
                                    @foreach($recentReviews->take(4) as $review)
                                        <article class="rounded-2xl border border-gray-200 bg-gray-50 p-5">
                                            <div class="flex items-center justify-between gap-3">
                                                <p class="text-sm font-semibold text-gray-900">{{ $review->user?->name ?: ($review->guest_name ?: 'Verified shopper') }}</p>
                                                <span class="rounded-full bg-amber-50 px-3 py-1 text-xs font-semibold text-amber-700">{{ $review->rating }}/5</span>
                                            </div>
                                            <p class="mt-3 line-clamp-4 text-sm leading-6 text-gray-600">{{ $review->comment }}</p>
                                            @if($review->product)
                                                <a href="{{ route('product.show', $review->product->slug) }}" class="mt-4 inline-flex text-sm font-semibold text-red-600 transition hover:text-red-700">
                                                    Reviewed on {{ $review->product->name }}
                                                </a>
                                            @endif
                                        </article>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        @if($featuredProducts->count())
                            <div class="rounded-3xl border border-gray-200 bg-white p-6 shadow-sm">
                                <div class="flex flex-col gap-2 border-b border-gray-100 pb-4 sm:flex-row sm:items-end sm:justify-between">
                                    <div>
                                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-red-500">Store highlights</p>
                                        <h2 class="mt-2 text-2xl font-semibold text-gray-900">Featured products</h2>
                                    </div>
                                    <p class="text-sm text-gray-500">A few products this vendor may want shoppers to notice first.</p>
                                </div>

                                <div class="mt-5 grid grid-cols-2 gap-3 md:grid-cols-4 md:gap-4">
                                    @foreach($featuredProducts as $featuredProduct)
                                        <x-instant-product-card :product="$featuredProduct" />
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>

                    <div class="space-y-6">
                        <div class="rounded-3xl border border-gray-200 bg-white p-6 shadow-sm">
                            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-red-500">Store contact</p>
                            <div class="mt-4 space-y-4">
                                <div class="rounded-2xl border border-gray-200 bg-gray-50 p-4">
                                    <p class="text-xs uppercase tracking-[0.18em] text-gray-400">Business phone</p>
                                    <p class="mt-2 text-sm font-medium text-gray-900">{{ $vendor->phone ?: 'Not shared yet' }}</p>
                                </div>

                                <div class="rounded-2xl border border-gray-200 bg-gray-50 p-4">
                                    <p class="text-xs uppercase tracking-[0.18em] text-gray-400">Business address</p>
                                    <p class="mt-2 text-sm leading-6 text-gray-700">{{ $vendor->address ?: 'Not shared yet' }}</p>
                                </div>

                                <div class="rounded-2xl border border-gray-200 bg-gray-50 p-4">
                                    <p class="text-xs uppercase tracking-[0.18em] text-gray-400">Store rating</p>
                                    <div class="mt-2 flex items-center gap-3">
                                        <div class="flex text-amber-400">
                                            @for($i = 1; $i <= 5; $i++)
                                                <svg class="h-4 w-4 {{ $storeAverageRating >= $i ? 'fill-current' : 'fill-slate-200 text-slate-200' }}" viewBox="0 0 20 20">
                                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.02 3.137a1 1 0 00.95.69h3.299c.969 0 1.371 1.24.588 1.81l-2.67 1.94a1 1 0 00-.364 1.118l1.02 3.138c.3.92-.755 1.688-1.54 1.118l-2.67-1.94a1 1 0 00-1.176 0l-2.67 1.94c-.784.57-1.838-.197-1.539-1.118l1.02-3.138a1 1 0 00-.364-1.118l-2.67-1.94c-.783-.57-.38-1.81.588-1.81h3.3a1 1 0 00.95-.69l1.017-3.137z"/>
                                                </svg>
                                            @endfor
                                        </div>
                                        <p class="text-sm font-semibold text-gray-900">{{ $storeReviewCount > 0 ? number_format($storeAverageRating, 1) . '/5' : 'No rating yet' }}</p>
                                    </div>
                                    <p class="mt-2 text-xs text-gray-500">
                                        {{ $storeReviewCount > 0 ? number_format($storeReviewCount) . ' shopper reviews across products.' : 'Ratings will appear here as buyers review products from this store.' }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="rounded-3xl border border-gray-200 bg-white p-6 shadow-sm">
                            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-red-500">Follow this store</p>
                            <h3 class="mt-2 text-xl font-semibold text-gray-900">Save it for later</h3>
                            <p class="mt-3 text-sm leading-6 text-gray-600">
                                Follow this storefront in your browser so you can come back quickly without searching again.
                            </p>

                            <div class="mt-5 flex flex-wrap gap-3">
                                <button type="button" @click="toggleFollow()" class="inline-flex items-center justify-center rounded-2xl bg-gray-900 px-5 py-3 text-sm font-semibold text-white transition hover:bg-black">
                                    <span x-text="isFollowing ? 'Following Store' : 'Follow This Store'"></span>
                                </button>
                                <button type="button" @click="shareStore()" class="inline-flex items-center justify-center rounded-2xl border border-gray-300 px-5 py-3 text-sm font-semibold text-gray-700 transition hover:bg-gray-50">
                                    Share with friends
                                </button>
                            </div>
                        </div>

                        <div class="rounded-3xl border border-gray-200 bg-gradient-to-br from-slate-950 via-slate-900 to-red-950 p-6 text-white shadow-sm">
                            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-red-200">Store identity</p>
                            <h3 class="mt-2 text-2xl font-semibold text-white">Clean, trusted, and easy to browse</h3>
                            <p class="mt-3 text-sm leading-7 text-slate-200">
                                This storefront groups trust, contact, and product discovery in one consistent experience for shoppers.
                            </p>

                            <div class="mt-5 grid grid-cols-1 gap-3 min-[430px]:grid-cols-2">
                                <div class="rounded-2xl bg-white/10 px-4 py-4">
                                    <p class="text-xs uppercase tracking-[0.18em] text-slate-300">Social links</p>
                                    <p class="mt-2 text-xl font-semibold text-white">{{ $socialLinksCount }}</p>
                                </div>
                                <div class="rounded-2xl bg-white/10 px-4 py-4">
                                    <p class="text-xs uppercase tracking-[0.18em] text-slate-300">Positive reviews</p>
                                    <p class="mt-2 text-xl font-semibold text-white">{{ $reviewSummary['positive_rate'] ?? 0 }}%</p>
                                </div>
                            </div>

                            <div class="mt-5 flex flex-wrap gap-3">
                                @if($vendor->storefrontWhatsAppUrl())
                                    <a href="{{ $vendor->storefrontWhatsAppUrl() }}" target="_blank" rel="noopener" class="inline-flex items-center justify-center rounded-2xl bg-emerald-500 px-4 py-3 text-sm font-semibold text-white transition hover:bg-emerald-600">
                                        WhatsApp
                                    </a>
                                @endif
                                @if($vendor->storefrontInstagramUrl())
                                    <a href="{{ $vendor->storefrontInstagramUrl() }}" target="_blank" rel="noopener" class="inline-flex items-center justify-center rounded-2xl border border-white/15 bg-white/10 px-4 py-3 text-sm font-semibold text-white transition hover:bg-white/15">
                                        Instagram
                                    </a>
                                @endif
                                @if($vendor->storefrontFacebookUrl())
                                    <a href="{{ $vendor->storefrontFacebookUrl() }}" target="_blank" rel="noopener" class="inline-flex items-center justify-center rounded-2xl border border-white/15 bg-white/10 px-4 py-3 text-sm font-semibold text-white transition hover:bg-white/15">
                                        Facebook
                                    </a>
                                @endif
                                @if($vendor->storefrontWebsiteUrl())
                                    <a href="{{ $vendor->storefrontWebsiteUrl() }}" target="_blank" rel="noopener" class="inline-flex items-center justify-center rounded-2xl border border-white/15 bg-white/10 px-4 py-3 text-sm font-semibold text-white transition hover:bg-white/15">
                                        Website
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section id="store-products" class="pb-14">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                @if($products->count())
                    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-red-500">Store catalog</p>
                            <h2 class="mt-2 text-3xl font-semibold text-gray-900">Products from {{ $vendor->publicStoreName() }}</h2>
                            <p class="mt-2 text-sm text-gray-500">
                                Showing {{ $products->firstItem() }} to {{ $products->lastItem() }} of {{ $products->total() }} products.
                            </p>
                            @if($search !== '' || $selectedCategoryId || $selectedType !== '')
                                <p class="mt-2 text-xs font-medium uppercase tracking-[0.18em] text-gray-400">
                                    Filtered storefront view
                                    @if($search !== '')
                                        | Search: "{{ $search }}"
                                    @endif
                                    @if($selectedType !== '')
                                        | Type: {{ ucfirst($selectedType) }}
                                    @endif
                                </p>
                            @endif
                        </div>

                        <span class="inline-flex items-center rounded-full bg-gray-100 px-4 py-2 text-sm font-medium text-gray-700">
                            {{ number_format($storeMetrics['active_products'] ?? 0) }} items available
                        </span>
                    </div>

                    <div class="grid grid-cols-2 gap-3 md:grid-cols-3 md:gap-4 xl:grid-cols-4">
                        @foreach($products as $product)
                            <x-instant-product-card :product="$product" />
                        @endforeach
                    </div>

                    <div class="mt-8">
                        {{ $products->links() }}
                    </div>
                @else
                    <div class="rounded-3xl border border-gray-200 bg-white px-6 py-14 text-center shadow-sm">
                        <p class="text-sm font-semibold uppercase tracking-[0.2em] text-red-500">No products found</p>
                        <h2 class="mt-3 text-3xl font-semibold text-gray-900">This store does not have matching items right now.</h2>
                        <p class="mx-auto mt-3 max-w-2xl text-sm leading-6 text-gray-500">
                            Try clearing your filters, browsing the full marketplace, or come back later as this vendor updates the catalog.
                        </p>
                        <div class="mt-6 flex flex-wrap items-center justify-center gap-3">
                            <a href="{{ route('stores.show', $vendor->store_slug) }}" class="inline-flex items-center justify-center rounded-2xl bg-gray-900 px-5 py-3 text-sm font-semibold text-white transition hover:bg-black">
                                View Full Store Again
                            </a>
                            <a href="{{ route('shop') }}" class="inline-flex items-center justify-center rounded-2xl border border-gray-300 px-5 py-3 text-sm font-semibold text-gray-700 transition hover:bg-gray-50">
                                Browse Marketplace
                            </a>
                        </div>
                    </div>
                @endif
            </div>
        </section>
    </div>
@endsection

@push('scripts')
<script>
function storefrontExperience(storeSlug, storeName, storeUrl) {
    return {
        storageKey: 'keffi-followed-stores',
        isFollowing: false,
        init() {
            this.isFollowing = this.followedStores().includes(storeSlug);
        },
        followedStores() {
            try {
                return JSON.parse(localStorage.getItem(this.storageKey) || '[]');
            } catch (error) {
                return [];
            }
        },
        saveFollowedStores(stores) {
            localStorage.setItem(this.storageKey, JSON.stringify(stores));
        },
        toggleFollow() {
            const stores = this.followedStores();

            if (stores.includes(storeSlug)) {
                this.saveFollowedStores(stores.filter((item) => item !== storeSlug));
                this.isFollowing = false;
                window.KeffiCart?.notify(storeName + ' removed from followed stores.', 'success');
                return;
            }

            stores.push(storeSlug);
            this.saveFollowedStores([...new Set(stores)]);
            this.isFollowing = true;
            window.KeffiCart?.notify(storeName + ' saved in followed stores.', 'success');
        },
        shareStore() {
            window.KeffiCart?.share(storeUrl, storeName + ' Store');
        }
    };
}
</script>
@endpush
