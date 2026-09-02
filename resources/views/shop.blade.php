@extends('layouts.app')

@section('title', 'Shop')

@section('header')
    <!-- You can add custom header content here if needed -->
@endsection

@section('content')
    <section class="border-b border-slate-200 bg-slate-50">
        <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
            <div class="overflow-hidden rounded-3xl bg-gradient-to-br from-slate-950 via-slate-900 to-red-900 px-6 py-8 text-white shadow-xl sm:px-8 lg:px-10">
                <p class="text-xs font-semibold uppercase tracking-[0.22em] text-red-200">Marketplace catalog</p>
                <div class="mt-4 flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
                    <div class="max-w-2xl">
                        <h1 class="text-3xl font-bold tracking-tight sm:text-4xl">Find what you need, without the wait.</h1>
                        <p class="mt-3 text-sm leading-6 text-slate-200 sm:text-base">Browse active listings, visit verified stores, and add items to your cart instantly.</p>
                    </div>
                    <div class="rounded-2xl border border-white/15 bg-white/10 px-4 py-3 text-sm text-slate-100">
                        <span class="font-semibold text-white">{{ number_format($products->total()) }}</span> products available
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="shop-catalog" class="bg-slate-50 py-8 sm:py-10">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <form method="GET" action="{{ route('shop') }}" data-shop-filters class="rounded-3xl border border-slate-200 bg-white p-4 shadow-sm sm:p-6">
                <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-[minmax(0,1.5fr)_minmax(11rem,1fr)_minmax(10rem,1fr)_auto] lg:items-end">
                    <label class="block">
                        <span class="mb-2 block text-sm font-semibold text-slate-700">Search products</span>
                        <input name="search" value="{{ $search }}" type="search" placeholder="What are you looking for?" class="w-full rounded-2xl border-slate-200 px-4 py-3 text-sm focus:border-red-500 focus:ring-red-500">
                    </label>

                    <label class="block">
                        <span class="mb-2 block text-sm font-semibold text-slate-700">Category</span>
                        <select name="category" data-filter-change class="w-full rounded-2xl border-slate-200 px-4 py-3 text-sm focus:border-red-500 focus:ring-red-500">
                            <option value="">All categories</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->slug }}" @selected(request('category') === $category->slug)>{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </label>

                    <label class="block">
                        <span class="mb-2 block text-sm font-semibold text-slate-700">Sort by</span>
                        <select name="sort" data-filter-change class="w-full rounded-2xl border-slate-200 px-4 py-3 text-sm focus:border-red-500 focus:ring-red-500">
                            <option value="latest" @selected($sort === 'latest')>Latest</option>
                            <option value="popular" @selected($sort === 'popular')>Most popular</option>
                            <option value="price_low" @selected($sort === 'price_low')>Price: low to high</option>
                            <option value="price_high" @selected($sort === 'price_high')>Price: high to low</option>
                            <option value="name" @selected($sort === 'name')>Name: A to Z</option>
                        </select>
                    </label>

                    <div class="flex gap-2">
                        <button type="submit" class="inline-flex flex-1 items-center justify-center rounded-2xl bg-slate-900 px-5 py-3 text-sm font-semibold text-white transition hover:bg-red-600">Apply</button>
                        @if(request()->hasAny(['search', 'category', 'min_price', 'max_price', 'sort', 'per_page']))
                            <a href="{{ route('shop') }}" class="inline-flex items-center justify-center rounded-2xl border border-slate-200 px-4 py-3 text-sm font-semibold text-slate-600 transition hover:border-red-200 hover:text-red-600">Reset</a>
                        @endif
                    </div>
                </div>

                <input type="hidden" name="per_page" value="{{ $perPage }}">
            </form>

            <div class="mt-7 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-xl font-bold text-slate-900">All active products</h2>
                    <p class="mt-1 text-sm text-slate-500">Showing {{ $products->firstItem() ?? 0 }} to {{ $products->lastItem() ?? 0 }} of {{ number_format($products->total()) }} results.</p>
                </div>
                <div class="flex items-center gap-2 text-sm text-slate-600">
                    <span>Show</span>
                    @foreach([12, 24, 48] as $pageSize)
                        <a href="{{ request()->fullUrlWithQuery(['per_page' => $pageSize, 'page' => 1]) }}#shop-catalog" class="rounded-xl px-3 py-2 font-semibold transition {{ $perPage === $pageSize ? 'bg-red-600 text-white' : 'bg-white text-slate-600 shadow-sm hover:text-red-600' }}">{{ $pageSize }}</a>
                    @endforeach
                </div>
            </div>

            @if($products->isNotEmpty())
                <div class="mt-6 grid grid-cols-2 gap-3 md:grid-cols-3 md:gap-4 xl:grid-cols-4">
                    @foreach($products as $product)
                        <x-instant-product-card :product="$product" />
                    @endforeach
                </div>

                @if($products->hasPages())
                    <nav class="mt-8 flex flex-wrap items-center justify-center gap-2" aria-label="Product pagination">
                        @if($products->onFirstPage())
                            <span class="rounded-xl border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-300">Previous</span>
                        @else
                            <a href="{{ $products->previousPageUrl() }}#shop-catalog" class="rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 shadow-sm transition hover:border-red-200 hover:text-red-600">Previous</a>
                        @endif

                        @foreach($products->getUrlRange(max(1, $products->currentPage() - 2), min($products->lastPage(), $products->currentPage() + 2)) as $page => $url)
                            <a href="{{ $url }}#shop-catalog" aria-label="Go to page {{ $page }}" aria-current="{{ $page === $products->currentPage() ? 'page' : 'false' }}" class="inline-flex h-10 min-w-10 items-center justify-center rounded-xl px-3 text-sm font-bold transition {{ $page === $products->currentPage() ? 'bg-red-600 text-white shadow-lg shadow-red-600/20' : 'border border-slate-200 bg-white text-slate-700 shadow-sm hover:border-red-200 hover:text-red-600' }}">{{ $page }}</a>
                        @endforeach

                        @if($products->hasMorePages())
                            <a href="{{ $products->nextPageUrl() }}#shop-catalog" class="rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 shadow-sm transition hover:border-red-200 hover:text-red-600">Next</a>
                        @else
                            <span class="rounded-xl border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-300">Next</span>
                        @endif
                    </nav>
                @endif
            @else
                <div class="mt-6 rounded-3xl border border-dashed border-slate-300 bg-white px-6 py-16 text-center shadow-sm">
                    <h2 class="text-xl font-bold text-slate-900">No products match these filters.</h2>
                    <p class="mt-2 text-sm text-slate-500">Try another product name or clear the active filters.</p>
                    <a href="{{ route('shop') }}" class="mt-5 inline-flex rounded-xl bg-red-600 px-4 py-3 text-sm font-semibold text-white transition hover:bg-red-700">Browse all products</a>
                </div>
            @endif
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const form = document.querySelector('[data-shop-filters]');

            form?.querySelectorAll('[data-filter-change]').forEach((field) => {
                field.addEventListener('change', () => form.requestSubmit());
            });
        });
    </script>
@endpush
