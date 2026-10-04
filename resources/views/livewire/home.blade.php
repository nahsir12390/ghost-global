@php
    $siteName = \App\Helpers\SettingsHelper::siteName();
    $siteTagline = \App\Helpers\SettingsHelper::siteTagline();
    $productSections = [
        ['id' => 'featured', 'eyebrow' => 'Curated for you', 'title' => 'The edit everyone is talking about.', 'description' => 'Standout finds selected to make your next great purchase effortless.', 'products' => $featuredProducts, 'tag' => null],
        ['id' => 'new-arrivals', 'eyebrow' => 'Freshly landed', 'title' => 'New energy. New essentials.', 'description' => 'The latest products to arrive across the store.', 'products' => $newArrivals, 'tag' => 'New'],
        ['id' => 'on-sale', 'eyebrow' => 'Limited drop', 'title' => 'Big finds. Better prices.', 'description' => 'Special offers worth moving quickly for.', 'products' => $onSaleProducts, 'tag' => 'Sale'],
        ['id' => 'best-sellers', 'eyebrow' => 'Most wanted', 'title' => 'Loved across our store.', 'description' => 'The products customers keep coming back for.', 'products' => $bestSellingProducts, 'tag' => 'Popular'],
    ];
@endphp

<main class="storefront-home bg-[#f5f3ee] text-slate-950" data-storefront-home>
    <section class="storefront-hero relative isolate min-h-[calc(100svh-4rem)] overflow-hidden bg-[#090909] text-white">
        <div class="absolute inset-0 z-0" data-hero-canvas aria-hidden="true"></div>
        <div class="storefront-noise absolute inset-0 z-[1] opacity-40" aria-hidden="true"></div>
        <div class="absolute inset-x-0 bottom-0 z-[2] h-48 bg-gradient-to-t from-[#090909] to-transparent" aria-hidden="true"></div>
        <div class="relative z-10 mx-auto flex min-h-[calc(100svh-4rem)] max-w-[90rem] flex-col justify-between px-5 pb-8 pt-16 sm:px-8 lg:px-12 lg:pb-12 lg:pt-24">
            <div class="grid items-center gap-12 lg:grid-cols-[1.05fr_.95fr]">
                <div class="max-w-4xl">
                    <div class="storefront-eyebrow storefront-eyebrow--dark" data-hero-reveal><span class="storefront-eyebrow__dot"></span>The store, reimagined</div>
                    <h1 class="mt-7 text-[clamp(3.3rem,9vw,8.5rem)] font-semibold leading-[.82] tracking-[-0.075em]" data-hero-reveal>Find your<br><span class="storefront-outline-text">next</span> thing.</h1>
                    <p class="mt-8 max-w-xl text-base leading-7 text-white/60 sm:text-lg" data-hero-reveal>{{ $siteTagline }} {{ $siteName }} brings great products, secure checkout and clear fulfilment together in one beautifully simple experience.</p>
                    <div class="mt-9 flex flex-col gap-3 sm:flex-row" data-hero-reveal>
                        <a href="{{ route('shop') }}" class="storefront-button storefront-button--primary">Explore the store <span aria-hidden="true">↗</span></a>
                        <a href="#categories" class="storefront-button storefront-button--ghost">See what’s trending</a>
                    </div>
                </div>
                <div class="relative min-h-[20rem] lg:min-h-[36rem]" aria-hidden="true">
                    <div class="hero-orbit-card hero-orbit-card--one" data-float-card><span class="hero-orbit-card__icon">✦</span><span><strong>Curated</strong><small>Fresh finds</small></span></div>
                    <div class="hero-orbit-card hero-orbit-card--two" data-float-card><span class="hero-orbit-card__icon">✓</span><span><strong>Secure</strong><small>Confident checkout</small></span></div>
                    <div class="hero-orbit-card hero-orbit-card--three" data-float-card><span class="hero-orbit-card__icon">→</span><span><strong>Delivered</strong><small>Track every step</small></span></div>
                </div>
            </div>
            <div class="mt-12 flex flex-col gap-6 border-t border-white/10 pt-6 sm:flex-row sm:items-end sm:justify-between" data-hero-reveal>
                <div class="grid grid-cols-3 gap-4 sm:gap-8"><x-storefront.metric value="100%" label="Secure" dark /><x-storefront.metric value="Global" label="Reach" dark /><x-storefront.metric value="Tracked" label="Delivery" dark /></div>
                <a href="#categories" class="hidden items-center gap-3 text-xs font-semibold uppercase tracking-[0.2em] text-white/45 transition hover:text-white sm:flex">Scroll to discover <span class="storefront-scroll-dot">↓</span></a>
            </div>
        </div>
    </section>

    <section id="categories" class="relative px-5 py-20 sm:px-8 lg:px-12 lg:py-32">
        <div class="mx-auto max-w-[90rem]">
            <div class="grid gap-8 lg:grid-cols-[.72fr_1.28fr] lg:items-end">
                <x-storefront.section-heading eyebrow="Shop your world" title="Everything you want. One place." description="Explore the categories shaping everyday life, from practical essentials to the pieces that make it personal." />
                <div class="flex lg:justify-end"><a href="{{ route('shop') }}" class="storefront-text-link">View every category <span>↗</span></a></div>
            </div>
            <div class="mt-12 grid auto-rows-[11rem] grid-cols-2 gap-3 sm:auto-rows-[15rem] lg:grid-cols-4 lg:gap-5">
                @forelse($categories as $category)
                    <a href="{{ route('category.show', $category->slug) }}" class="storefront-category group {{ $loop->first ? 'col-span-2 row-span-2' : '' }} {{ $loop->iteration === 4 ? 'col-span-2' : '' }}" data-reveal>
                        @if($category->image)<img src="{{ asset('storage/' . $category->image) }}" alt="{{ $category->name }}" class="storefront-category__image" loading="lazy">
                        @else<div class="storefront-category__fallback" aria-hidden="true"><span>{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span></div>@endif
                        <div class="storefront-category__scrim"></div>
                        <div class="storefront-category__content"><span class="text-[10px] font-semibold uppercase tracking-[.2em] text-white/55">{{ $category->products_count }} products</span><h3 class="mt-1 text-xl font-semibold tracking-tight text-white sm:text-3xl">{{ $category->name }}</h3></div>
                        <span class="storefront-category__arrow">↗</span>
                    </a>
                @empty
                    @foreach(['Style', 'Technology', 'Home', 'Beauty'] as $emptyCategory)
                        <a href="{{ route('shop') }}" class="storefront-category group {{ $loop->first ? 'col-span-2 row-span-2' : '' }}" data-reveal><div class="storefront-category__fallback"><span>{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span></div><div class="storefront-category__scrim"></div><div class="storefront-category__content"><h3 class="text-xl font-semibold text-white sm:text-3xl">{{ $emptyCategory }}</h3></div><span class="storefront-category__arrow">↗</span></a>
                    @endforeach
                @endforelse
            </div>
        </div>
    </section>

    <section class="storefront-manifesto relative overflow-hidden bg-[#d9272e] px-5 py-24 text-white sm:px-8 lg:px-12 lg:py-36">
        <div class="storefront-marquee" aria-hidden="true"><div>DISCOVER · DESIRE · DELIVER · DISCOVER · DESIRE · DELIVER ·&nbsp;</div></div>
        <div class="relative z-10 mx-auto grid max-w-[90rem] gap-16 lg:grid-cols-[.8fr_1.2fr] lg:items-center">
            <div data-reveal><span class="text-xs font-semibold uppercase tracking-[.25em] text-white/60">The {{ $siteName }} way</span><p class="mt-6 max-w-md text-2xl font-medium leading-tight tracking-[-.035em] sm:text-4xl">Shopping should feel less like searching—and more like finding.</p></div>
            <div class="grid gap-px overflow-hidden rounded-[2rem] bg-white/15 sm:grid-cols-3" data-reveal>
                @foreach([['01', 'Discover', 'Browse a store designed around clarity, quality and surprise.'], ['02', 'Choose', 'Buy confidently with transparent product information and clear prices.'], ['03', 'Receive', 'Check out securely and follow your order from cart to doorstep.']] as [$number, $title, $copy])
                    <article class="bg-[#c91f27] p-7 sm:min-h-[20rem] sm:p-8"><span class="text-xs font-bold tracking-[.2em] text-white/45">{{ $number }}</span><h3 class="mt-16 text-3xl font-semibold tracking-tight">{{ $title }}</h3><p class="mt-4 text-sm leading-6 text-white/65">{{ $copy }}</p></article>
                @endforeach
            </div>
        </div>
    </section>

    @foreach($productSections as $section)
        @if($section['products']->count() > 0)
            <section id="{{ $section['id'] }}" class="px-5 py-20 sm:px-8 lg:px-12 lg:py-32 {{ $loop->even ? 'bg-white' : 'bg-[#f5f3ee]' }}">
                <div class="mx-auto max-w-[90rem]">
                    <div class="grid gap-6 lg:grid-cols-[.8fr_1.2fr] lg:items-end">
                        <x-storefront.section-heading :eyebrow="$section['eyebrow']" :title="$section['title']" :description="$section['description']" />
                        <div class="flex lg:justify-end"><a href="{{ route('shop') }}" class="storefront-text-link">Shop all products <span>↗</span></a></div>
                    </div>
                    <div class="mt-12 grid grid-cols-2 gap-x-3 gap-y-10 sm:gap-x-5 lg:grid-cols-4">
                        @foreach($section['products'] as $product)
                            <x-storefront.product-card :product="$product" :tag="$section['tag']" />
                        @endforeach
                    </div>
                </div>
            </section>
        @endif
    @endforeach

    <section class="px-5 py-20 sm:px-8 lg:px-12 lg:py-32">
        <div class="mx-auto max-w-[90rem] overflow-hidden rounded-[2.5rem] bg-[#101010] px-6 py-14 text-white sm:px-10 lg:grid lg:grid-cols-[1.15fr_.85fr] lg:items-center lg:px-14 lg:py-20">
            <div data-reveal><span class="text-xs font-bold uppercase tracking-[.22em] text-red-400">Shop with confidence</span><h2 class="mt-5 max-w-3xl text-4xl font-semibold leading-[.98] tracking-[-.055em] sm:text-6xl">From discovery to delivery, keep every step clear.</h2><p class="mt-5 max-w-xl text-base leading-7 text-white/55">Browse products, check availability for your destination, pay securely and track your order as it moves through fulfilment.</p><a href="{{ route('shop') }}" class="storefront-button storefront-button--light mt-8">Explore products</a></div>
            <div class="mt-12 grid grid-cols-2 gap-3 lg:mt-0" data-reveal><x-storefront.metric class="rounded-3xl bg-white/5 p-6" value="Secure" label="Checkout" dark /><x-storefront.metric class="rounded-3xl bg-white/5 p-6" value="Global" label="Destinations" dark /><x-storefront.metric class="rounded-3xl bg-white/5 p-6" value="Clear" label="Order updates" dark /><x-storefront.metric class="rounded-3xl bg-white/5 p-6" value="Tracked" label="Shipments" dark /></div>
        </div>
    </section>
</main>
