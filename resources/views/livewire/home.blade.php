@php
    $siteName = \App\Helpers\SettingsHelper::get('site_name', config('app.name', 'E-Commerce'));

    $sections = [
        [
            'id' => 'featured',
            'show' => $featuredProducts->count() > 0,
            'badge' => 'Featured Picks',
            'title' => 'Featured Products',
            'subtitle' => 'Popular items handpicked for fast browsing and easier shopping.',
            'accent' => 'from-red-500 to-rose-600',
            'products' => $featuredProducts,
            'tag' => null,
            'background' => 'bg-gradient-to-b from-gray-50 to-white',
        ],
        [
            'id' => 'new-arrivals',
            'show' => $newArrivals->count() > 0,
            'badge' => 'Just In',
            'title' => 'New Arrivals',
            'subtitle' => 'Fresh products added recently from verified sellers.',
            'accent' => 'from-blue-500 to-cyan-500',
            'products' => $newArrivals,
            'tag' => 'New',
            'background' => 'bg-white',
        ],
        [
            'id' => 'on-sale',
            'show' => $onSaleProducts->count() > 0,
            'badge' => 'Hot Deals',
            'title' => 'On Sale Now',
            'subtitle' => 'Grab these limited offers before they are gone.',
            'accent' => 'from-red-500 to-rose-600',
            'products' => $onSaleProducts,
            'tag' => 'Sale',
            'background' => 'bg-gradient-to-b from-white to-red-50/40',
        ],
        [
            'id' => 'best-sellers',
            'show' => $bestSellingProducts->count() > 0,
            'badge' => 'Customer Favorites',
            'title' => 'Best Sellers',
            'subtitle' => 'Shop what everyone is loving right now.',
            'accent' => 'from-green-500 to-emerald-600',
            'products' => $bestSellingProducts,
            'tag' => 'Best Seller',
            'background' => 'bg-gradient-to-b from-gray-50 to-white',
        ],
    ];
@endphp

<div class="font-sans">
    <section class="relative flex min-h-[500px] items-start overflow-hidden bg-gradient-to-br from-gray-50 via-white to-gray-100 md:min-h-[700px] md:items-center">
        <div class="absolute inset-0">
            <div class="absolute left-10 top-20 h-96 w-96 rounded-full bg-gradient-to-br from-red-200/30 to-red-100/20 blur-3xl animate-pulse"></div>
            <div class="absolute bottom-20 right-10 h-96 w-96 rounded-full bg-gradient-to-tl from-red-100/20 to-rose-100/30 blur-3xl animate-pulse delay-1000"></div>
            <div class="absolute left-1/2 top-1/2 h-[800px] w-[800px] -translate-x-1/2 -translate-y-1/2 rounded-full bg-gradient-to-r from-red-50/10 via-transparent to-red-50/10 blur-3xl"></div>
            <div class="absolute inset-0 opacity-[0.03]" style="background-image: radial-gradient(circle at 1px 1px, #dc2626 1px, transparent 1px); background-size: 32px 32px;"></div>
        </div>

        <div class="relative z-20 mx-auto max-w-7xl px-4 pb-12 pt-6 sm:px-6 md:py-20 lg:px-8">
            <div class="grid items-center gap-8 md:gap-16 lg:grid-cols-2">
                <div class="text-left">
                    <div class="mb-4 inline-flex items-center gap-2 rounded-full border border-red-100/50 bg-white/60 px-3 py-1.5 text-xs font-semibold text-red-600 shadow-sm backdrop-blur-sm md:mb-8 md:px-5 md:py-2.5 md:text-sm">
                        <span class="relative flex h-2 w-2">
                            <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-red-400 opacity-75"></span>
                            <span class="relative inline-flex h-2 w-2 rounded-full bg-red-500"></span>
                        </span>
                        <span>Updated shopping experience</span>
                    </div>

                    <div class="mb-4 flex flex-wrap gap-2 md:mb-6">
                        <span class="inline-flex items-center rounded-full border border-emerald-200 bg-emerald-50 px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.18em] text-emerald-700">
                            Verified Sellers
                        </span>
                        <span class="inline-flex items-center rounded-full border border-sky-200 bg-sky-50 px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.18em] text-sky-700">
                            Secure Payments
                        </span>
                        <span class="inline-flex items-center rounded-full border border-amber-200 bg-amber-50 px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.18em] text-amber-700">
                            Fast Delivery
                        </span>
                    </div>

                    <h1 class="mb-4 text-3xl font-bold leading-[1.1] tracking-tight text-gray-900 md:mb-8 md:text-5xl lg:text-7xl">
                        Your Gateway to
                        <span class="relative inline-block">
                            <span class="relative z-10 text-red-600">Better</span>
                            <svg class="absolute bottom-1 -z-0 h-2 w-full md:bottom-2 md:h-3" viewBox="0 0 200 10" preserveAspectRatio="none">
                                <path d="M0,5 Q50,0 100,5 T200,5" stroke="#dc2626" stroke-width="3" fill="none" stroke-linecap="round" opacity="0.3"/>
                            </svg>
                        </span>
                        Shopping
                    </h1>

                    <p class="mb-6 max-w-2xl text-base leading-relaxed text-gray-500 md:mb-12 md:text-xl">
                        {{ $siteName }} brings trusted sellers, safe checkout, and smooth delivery into one modern shopping experience from discovery to doorstep.
                    </p>

                    <div class="mb-6 grid grid-cols-1 gap-3 md:mb-12 md:gap-5 sm:grid-cols-2">
                        <div class="group flex items-start gap-3 rounded-xl border border-gray-100/50 bg-white/70 p-3 shadow-sm transition-all duration-300 hover:-translate-y-1 hover:border-red-100 hover:shadow-lg backdrop-blur-sm md:gap-4 md:rounded-2xl md:p-4">
                            <div class="flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-lg bg-gradient-to-br from-red-50 to-red-100 transition-transform group-hover:scale-110 md:h-12 md:w-12 md:rounded-xl">
                                <svg class="h-4 w-4 text-red-500 md:h-6 md:w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m-9 9a9 9 0 019-9"/>
                                </svg>
                            </div>
                            <div>
                                <h3 class="mb-0.5 text-sm font-semibold text-gray-900 md:mb-1 md:text-base">Shop Anywhere</h3>
                                <p class="text-xs text-gray-500 md:text-sm">Access from any device</p>
                            </div>
                        </div>

                        <div class="group flex items-start gap-3 rounded-xl border border-gray-100/50 bg-white/70 p-3 shadow-sm transition-all duration-300 hover:-translate-y-1 hover:border-red-100 hover:shadow-lg backdrop-blur-sm md:gap-4 md:rounded-2xl md:p-4">
                            <div class="flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-lg bg-gradient-to-br from-red-50 to-red-100 transition-transform group-hover:scale-110 md:h-12 md:w-12 md:rounded-xl">
                                <svg class="h-4 w-4 text-red-500 md:h-6 md:w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <div>
                                <h3 class="mb-0.5 text-sm font-semibold text-gray-900 md:mb-1 md:text-base">Fast Delivery</h3>
                                <p class="text-xs text-gray-500 md:text-sm">Quick shipping</p>
                            </div>
                        </div>
                    </div>

                    <div class="flex flex-col gap-3 sm:flex-row md:gap-4">
                        <a href="{{ route('shop') }}" class="group relative flex items-center justify-center gap-2 overflow-hidden rounded-lg bg-gradient-to-r from-red-600 to-rose-600 px-6 py-2.5 text-sm font-semibold text-white shadow-lg transition-all duration-300 hover:-translate-y-1 hover:shadow-2xl md:gap-3 md:rounded-xl md:px-8 md:py-4 md:text-lg">
                            <span class="relative z-10">Browse Products</span>
                            <svg class="relative z-10 h-4 w-4 transition-transform group-hover:translate-x-2 md:h-5 md:w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                            </svg>
                            <div class="absolute inset-0 bg-gradient-to-r from-red-700 to-rose-700 opacity-0 transition-opacity duration-300 group-hover:opacity-100"></div>
                        </a>

                        <a href="#featured" class="flex items-center justify-center gap-2 rounded-lg border-2 border-red-200 bg-white/80 px-6 py-2.5 text-sm font-semibold text-red-600 transition-all duration-300 hover:border-red-400 hover:bg-red-50 hover:shadow-lg backdrop-blur-sm md:gap-3 md:rounded-xl md:px-8 md:py-4 md:text-lg">
                            <svg class="h-4 w-4 md:h-5 md:w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span>Explore Featured</span>
                        </a>
                    </div>
                </div>

                <div class="relative hidden lg:block">
                    <div class="mx-auto w-full max-w-xl">
                        <div class="relative rounded-3xl border border-white/50 bg-gradient-to-br from-white/90 to-red-50/80 p-8 shadow-2xl backdrop-blur-sm">
                            <div class="absolute -inset-1 -z-10 rounded-3xl bg-gradient-to-r from-red-200 to-rose-200 opacity-30 blur-xl"></div>
                            <div class="mb-6 flex items-start justify-between gap-4">
                                <div>
                                    <p class="text-xs font-semibold uppercase tracking-[0.24em] text-red-500">How {{ $siteName }} Works</p>
                                    <h3 class="mt-2 text-2xl font-bold text-slate-900">Discover, pay, and receive with confidence</h3>
                                    <p class="mt-2 text-sm leading-6 text-slate-500">A simple shopping flow built around trusted sellers, secure checkout, and faster delivery updates.</p>
                                </div>
                                <div class="rounded-2xl bg-slate-900 px-4 py-3 text-right text-white shadow-lg">
                                    <p class="text-[11px] uppercase tracking-[0.18em] text-slate-300">Live Orders</p>
                                    <p class="mt-1 text-2xl font-bold">128+</p>
                                </div>
                            </div>
                            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 sm:gap-6">
                                <div class="col-span-2 rounded-2xl border border-gray-100/50 bg-white p-6 shadow-lg transition-all duration-300 hover:shadow-xl">
                                    <div class="mb-4 flex items-center justify-between">
                                        <div class="flex items-center gap-3">
                                            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-br from-red-100 to-red-200">
                                                <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                                                </svg>
                                            </div>
                                            <div>
                                                <div class="font-semibold text-gray-900">Discover and add</div>
                                                <div class="text-sm text-gray-500">Find the right products faster</div>
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            <div class="bg-gradient-to-r from-red-600 to-rose-600 bg-clip-text text-2xl font-bold text-transparent">LIVE</div>
                                            <div class="text-xs font-semibold uppercase tracking-[0.18em] text-gray-400">Storefront</div>
                                        </div>
                                    </div>
                                    <div class="h-2 overflow-hidden rounded-full bg-gray-100">
                                        <div class="relative h-full w-3/4 overflow-hidden rounded-full bg-gradient-to-r from-red-500 to-rose-500">
                                            <div class="absolute inset-0 animate-shimmer bg-white/30"></div>
                                        </div>
                                    </div>
                                </div>

                                <div class="group rounded-2xl border border-gray-100/50 bg-white p-5 shadow-lg transition-all duration-300 hover:shadow-xl">
                                    <div class="mb-4 flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-br from-red-100 to-red-200 transition-transform group-hover:scale-110">
                                        <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                        </svg>
                                    </div>
                                    <div class="mb-1 font-semibold text-gray-900">Secure Payments</div>
                                    <div class="text-sm text-gray-500">Safe, encrypted, and simple</div>
                                </div>

                                <div class="group rounded-2xl border border-gray-100/50 bg-white p-5 shadow-lg transition-all duration-300 hover:shadow-xl">
                                    <div class="mb-4 flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-br from-red-100 to-red-200 transition-transform group-hover:scale-110">
                                        <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/>
                                        </svg>
                                    </div>
                                    <div class="mb-1 font-semibold text-gray-900">Fast Delivery</div>
                                    <div class="text-sm text-gray-500">Quick tracking to your doorstep</div>
                                </div>
                            </div>

                            <div class="mt-6 grid grid-cols-3 gap-4">
                                <div class="rounded-2xl bg-slate-900 px-4 py-4 text-white shadow-lg">
                                    <p class="text-[11px] uppercase tracking-[0.18em] text-slate-300">Products</p>
                                    <p class="mt-2 text-2xl font-bold">500+</p>
                                </div>
                                <div class="rounded-2xl border border-gray-100 bg-white px-4 py-4 text-slate-900 shadow-lg">
                                    <p class="text-[11px] uppercase tracking-[0.18em] text-slate-400">Checkout</p>
                                    <p class="mt-2 text-2xl font-bold text-red-600">Safe</p>
                                </div>
                                <div class="rounded-2xl border border-gray-100 bg-white px-4 py-4 text-slate-900 shadow-lg">
                                    <p class="text-[11px] uppercase tracking-[0.18em] text-slate-400">Delivery</p>
                                    <p class="mt-2 text-2xl font-bold text-emerald-600">Fast</p>
                                </div>
                            </div>

                            <div class="absolute -bottom-4 -left-4 flex items-center gap-2 rounded-full border border-gray-100 bg-white px-4 py-2 shadow-lg">
                                <div class="flex text-sm text-yellow-400">
                                    *****
                                </div>
                                <span class="text-sm font-semibold text-gray-700">Trusted shopping flow</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <style>
        @keyframes shimmer {
            0% { transform: translateX(-100%); }
            100% { transform: translateX(100%); }
        }

        .animate-shimmer {
            animation: shimmer 2s infinite;
        }

        html {
            scroll-behavior: smooth;
        }
    </style>

    <section class="relative bg-white py-8 md:py-12">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="overflow-hidden rounded-[2rem] border border-slate-200 bg-gradient-to-br from-slate-900 via-slate-800 to-red-900 p-6 text-white shadow-xl md:p-8">
                <div class="grid gap-6 lg:grid-cols-[1.1fr_.9fr] lg:items-center">
                    <div>
                        <div class="inline-flex items-center rounded-full border border-white/15 bg-white/10 px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.22em] text-white/90">
                            Install {{ $siteName }}
                        </div>
                        <h2 class="mt-4 text-2xl font-bold tracking-tight md:text-4xl">Add {{ $siteName }} to your device for faster shopping access.</h2>
                        <p class="mt-3 max-w-2xl text-sm leading-6 text-white/75 md:text-base">
                            Install the web app to open {{ $siteName }} like an app, reach your cart faster, track orders more easily, and enjoy a smoother mobile shopping experience.
                        </p>
                    </div>

                    <div class="grid gap-4 lg:justify-self-end">
                        <div class="grid gap-3 sm:grid-cols-2">
                            <button type="button" onclick="window.triggerStoreInstallPrompt?.()" class="inline-flex items-center justify-center rounded-2xl bg-white px-5 py-4 text-sm font-semibold text-slate-900 transition hover:bg-slate-100">
                                Install Web App
                            </button>
                            <button type="button" onclick="window.showStoreIosInstallGuide?.()" class="inline-flex items-center justify-center rounded-2xl border border-white/15 bg-white/5 px-5 py-4 text-sm font-semibold text-white transition hover:bg-white/10">
                                iPhone Install Steps
                            </button>
                        </div>

                        <div class="grid gap-3 sm:grid-cols-3">
                            <div class="rounded-2xl border border-white/10 bg-white/10 p-4 backdrop-blur-sm">
                                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-white/60">Quick Access</p>
                                <p class="mt-2 text-sm font-semibold text-white">Open from your home screen anytime</p>
                            </div>
                            <div class="rounded-2xl border border-white/10 bg-white/10 p-4 backdrop-blur-sm">
                                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-white/60">App Feel</p>
                                <p class="mt-2 text-sm font-semibold text-white">A smoother mobile browsing experience</p>
                            </div>
                            <div class="rounded-2xl border border-white/10 bg-white/10 p-4 backdrop-blur-sm">
                                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-white/60">Order Tracking</p>
                                <p class="mt-2 text-sm font-semibold text-white">Reach updates and checkout faster</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="py-12 md:py-16 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-8 md:mb-12">
                <span class="inline-block px-3 md:px-4 py-1 md:py-1.5 bg-red-50 text-red-600 rounded-full text-xs md:text-sm font-semibold mb-2 md:mb-3">
                    Shop by Category
                </span>
                <h2 class="text-2xl md:text-4xl lg:text-5xl font-bold text-gray-900 mb-2 md:mb-3">Browse Categories</h2>
                <p class="text-sm md:text-lg text-gray-600 max-w-2xl mx-auto">
                    Discover our wide range of products organized by category
                </p>
            </div>

            <div class="mb-6 grid grid-cols-2 gap-3 md:mb-8 md:grid-cols-3 md:gap-4 lg:grid-cols-6 lg:gap-6">
                @foreach($categories as $category)
                    <a href="{{ route('category.show', $category->slug) }}" class="group relative overflow-hidden bg-white rounded-lg md:rounded-xl shadow-md hover:shadow-lg transition-all duration-300">
                        <div class="relative h-32 sm:h-40 md:h-48 lg:h-52 overflow-hidden bg-gray-100">
                            @if($category->image)
                                <img src="{{ asset('storage/' . $category->image) }}" alt="{{ $category->name }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                            @else
                                <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-gray-100 to-gray-200">
                                    <svg class="w-8 h-8 md:w-12 md:h-12 text-gray-300" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                    </svg>
                                </div>
                            @endif
                            <div class="absolute top-2 right-2 md:top-3 md:right-3">
                                <span class="px-1.5 md:px-2.5 py-0.5 md:py-1 bg-red-600 text-white text-[8px] md:text-xs font-bold rounded">
                                    {{ $category->products_count }}
                                </span>
                            </div>
                        </div>

                        <div class="p-2 md:p-4">
                            <h3 class="text-center text-sm font-semibold text-gray-900 transition-colors group-hover:text-red-600 md:text-base">
                                {{ $category->name }}
                            </h3>
                        </div>
                    </a>
                @endforeach
            </div>

            <div class="text-center pt-2 md:pt-4">
                <a href="{{ route('shop') }}" class="inline-flex items-center px-4 md:px-6 py-2 md:py-3 bg-red-600 text-white font-semibold rounded-lg text-sm md:text-base hover:bg-red-700 transition-colors duration-300 gap-2">
                    View All Categories
                    <svg class="w-4 h-4 md:w-5 md:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                    </svg>
                </a>
            </div>
        </div>
    </section>

    @foreach($sections as $section)
        @if($section['show'])
            <section id="{{ $section['id'] }}" class="py-12 md:py-24 {{ $section['background'] }}">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="text-center mb-8 md:mb-16">
                        <div class="inline-flex items-center gap-2 rounded-full bg-gradient-to-r {{ $section['accent'] }} px-4 py-2 text-xs font-semibold text-white shadow-sm md:text-sm">
                            {{ $section['badge'] }}
                        </div>
                        <h2 class="mt-4 text-2xl font-bold tracking-tight text-gray-900 md:text-4xl lg:text-5xl">{{ $section['title'] }}</h2>
                        <p class="mx-auto mt-3 max-w-2xl text-sm text-gray-500 md:text-lg">
                            {{ $section['subtitle'] }}
                        </p>
                    </div>

                    <div class="grid grid-cols-2 gap-3 md:grid-cols-3 md:gap-6 lg:grid-cols-4">
                        @foreach($section['products'] as $product)
                            <div class="relative">
                                @if($section['tag'])
                                    <div class="absolute left-2 top-2 z-10 rounded-lg bg-gradient-to-r {{ $section['accent'] }} px-2 py-1 text-[8px] font-bold text-white shadow-lg md:left-3 md:top-3 md:text-xs">
                                        {{ $section['tag'] }}
                                    </div>
                                @endif
                                <x-instant-product-card :product="$product" />
                            </div>
                        @endforeach
                    </div>
                </div>
            </section>
        @endif
    @endforeach

    <section class="py-12 md:py-24 bg-gradient-to-br from-gray-900 via-gray-950 to-black relative overflow-hidden">
        <div class="absolute inset-0 opacity-20 bg-[radial-gradient(circle_at_top,_rgba(255,255,255,0.15),_transparent_60%)]"></div>
        <div class="absolute -left-20 top-20 h-64 w-64 rounded-full bg-red-500/20 blur-3xl animate-pulse"></div>
        <div class="absolute -right-20 bottom-20 h-80 w-80 rounded-full bg-rose-500/10 blur-3xl animate-pulse delay-1000"></div>
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-full h-full bg-[url('data:image/svg+xml,%3Csvg width=%2260%22 height=%2260%22 viewBox=%220 0 60 60%22 xmlns=%22http://www.w3.org/2000/svg%22%3E%3Cpath d=%22M30 15 L35 25 L45 27 L38 34 L40 44 L30 39 L20 44 L22 34 L15 27 L25 25 Z%22 fill=%22%23ffffff%22 fill-opacity=%220.03%22 fill-rule=%22evenodd%22/%3E%3C/svg%3E')] opacity-30"></div>

        @livewire('newsletter-subscribe')
    </section>

    <section class="py-12 md:py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 gap-4 min-[430px]:grid-cols-2 md:grid-cols-4 md:gap-8">
                <div class="text-center group">
                    <div class="w-12 h-12 md:w-16 md:h-16 bg-gradient-to-br from-red-100 to-rose-100 rounded-xl md:rounded-2xl flex items-center justify-center mx-auto mb-3 md:mb-5 group-hover:scale-110 transition-transform duration-300 shadow-md">
                        <svg class="w-6 h-6 md:w-8 md:h-8 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>
                    <h3 class="font-bold text-gray-900 text-sm md:text-lg mb-1 md:mb-2">Quality Guaranteed</h3>
                    <p class="text-gray-500 text-xs md:text-sm">Premium quality products</p>
                </div>

                <div class="text-center group">
                    <div class="w-12 h-12 md:w-16 md:h-16 bg-gradient-to-br from-red-100 to-rose-100 rounded-xl md:rounded-2xl flex items-center justify-center mx-auto mb-3 md:mb-5 group-hover:scale-110 transition-transform duration-300 shadow-md">
                        <svg class="w-6 h-6 md:w-8 md:h-8 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <h3 class="font-bold text-gray-900 text-sm md:text-lg mb-1 md:mb-2">Fast Delivery</h3>
                    <p class="text-gray-500 text-xs md:text-sm">Free delivery on orders over NGN {{ number_format(\App\Helpers\SettingsHelper::freeShippingThreshold(), 0) }}</p>
                </div>

                <div class="text-center group">
                    <div class="w-12 h-12 md:w-16 md:h-16 bg-gradient-to-br from-red-100 to-rose-100 rounded-xl md:rounded-2xl flex items-center justify-center mx-auto mb-3 md:mb-5 group-hover:scale-110 transition-transform duration-300 shadow-md">
                        <svg class="w-6 h-6 md:w-8 md:h-8 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                        </svg>
                    </div>
                    <h3 class="font-bold text-gray-900 text-sm md:text-lg mb-1 md:mb-2">Secure Payment</h3>
                    <p class="text-gray-500 text-xs md:text-sm">100% secure with SSL encryption</p>
                </div>

                <div class="text-center group">
                    <div class="w-12 h-12 md:w-16 md:h-16 bg-gradient-to-br from-red-100 to-rose-100 rounded-xl md:rounded-2xl flex items-center justify-center mx-auto mb-3 md:mb-5 group-hover:scale-110 transition-transform duration-300 shadow-md">
                        <svg class="w-6 h-6 md:w-8 md:h-8 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <h3 class="font-bold text-gray-900 text-sm md:text-lg mb-1 md:mb-2">Easy Returns</h3>
                    <p class="text-gray-500 text-xs md:text-sm">30-day hassle-free return policy</p>
                </div>
            </div>
        </div>
    </section>
</div>
