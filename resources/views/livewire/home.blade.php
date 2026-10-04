@php
    $siteName = \App\Helpers\SettingsHelper::siteName();
    $siteTagline = \App\Helpers\SettingsHelper::siteTagline();
    $siteDescription = \App\Helpers\SettingsHelper::siteDescription();
    $globalDestinations = [
        ['code' => 'US', 'flag' => '🇺🇸', 'name' => 'United States', 'city' => 'New York'],
        ['code' => 'GB', 'flag' => '🇬🇧', 'name' => 'United Kingdom', 'city' => 'London'],
        ['code' => 'CA', 'flag' => '🇨🇦', 'name' => 'Canada', 'city' => 'Toronto'],
        ['code' => 'NG', 'flag' => '🇳🇬', 'name' => 'Nigeria', 'city' => 'Lagos'],
        ['code' => 'AE', 'flag' => '🇦🇪', 'name' => 'UAE', 'city' => 'Dubai'],
        ['code' => 'DE', 'flag' => '🇩🇪', 'name' => 'Germany', 'city' => 'Berlin'],
        ['code' => 'FR', 'flag' => '🇫🇷', 'name' => 'France', 'city' => 'Paris'],
        ['code' => 'ZA', 'flag' => '🇿🇦', 'name' => 'South Africa', 'city' => 'Johannesburg'],
    ];
    $productSections = [
        ['id' => 'featured', 'eyebrow' => 'Curated globally', 'title' => 'Products worth crossing borders for.', 'description' => 'Standout finds selected to make your next great purchase effortless.', 'products' => $featuredProducts, 'tag' => null],
        ['id' => 'new-arrivals', 'eyebrow' => 'Freshly landed', 'title' => 'New arrivals. Global possibilities.', 'description' => 'The latest products to arrive across the store.', 'products' => $newArrivals, 'tag' => 'New'],
        ['id' => 'on-sale', 'eyebrow' => 'Limited drop', 'title' => 'Big finds. Better prices.', 'description' => 'Special offers worth moving quickly for.', 'products' => $onSaleProducts, 'tag' => 'Sale'],
        ['id' => 'best-sellers', 'eyebrow' => 'Most wanted', 'title' => 'Loved across our store.', 'description' => 'The products customers keep coming back for.', 'products' => $bestSellingProducts, 'tag' => 'Popular'],
    ];
@endphp

<main class="storefront-home bg-[#f4f1ea] text-slate-950" data-storefront-home>
    <section class="global-hero relative isolate min-h-[calc(100svh-4rem)] overflow-hidden bg-[#07090d] text-white">
        <div class="global-hero__aurora" aria-hidden="true"></div>
        <div class="absolute inset-0 z-0" data-hero-canvas aria-hidden="true"></div>
        <div class="storefront-noise absolute inset-0 z-[1] opacity-30" aria-hidden="true"></div>
        <div class="relative z-10 mx-auto flex min-h-[calc(100svh-4rem)] max-w-[94rem] flex-col justify-between px-5 pb-8 pt-12 sm:px-8 lg:px-12 lg:pb-10 lg:pt-20">
            <div class="grid flex-1 items-center gap-8 lg:grid-cols-[1.02fr_.98fr]">
                <div class="max-w-4xl py-8">
                    <div class="global-pill" data-hero-reveal><span class="global-pill__pulse"></span>Worldwide shopping, one destination</div>
                    <h1 class="mt-7 max-w-4xl text-[clamp(3.5rem,8.2vw,8.2rem)] font-semibold leading-[.84] tracking-[-.075em]" data-hero-reveal>
                        Your world.<br><span class="global-gradient-text">Delivered.</span>
                    </h1>
                    <p class="mt-7 max-w-2xl text-base leading-7 text-white/62 sm:text-lg" data-hero-reveal>{{ $siteTagline }} {{ $siteDescription }}</p>
                    <div class="mt-9 flex flex-col gap-3 sm:flex-row" data-hero-reveal>
                        <a href="{{ route('shop') }}" class="global-cta global-cta--primary">Shop worldwide <span>↗</span></a>
                        <a href="{{ route('tracking.index') }}" class="global-cta global-cta--glass">Track an order <span>→</span></a>
                    </div>
                    <div class="mt-9 flex flex-wrap gap-2" data-hero-reveal>
                        @foreach(array_slice($globalDestinations, 0, 5) as $destination)
                            <span class="hero-country-chip"><span>{{ $destination['flag'] }}</span>{{ $destination['code'] }}</span>
                        @endforeach
                        <a href="#global-delivery" class="hero-country-chip hero-country-chip--more">+ more destinations</a>
                    </div>
                </div>
                <div class="relative min-h-[28rem] lg:min-h-[42rem]" aria-hidden="true">
                    <div class="global-orbit-card global-orbit-card--one" data-float-card><span class="text-2xl">🇺🇸</span><span><strong>New York</strong><small>Global destination</small></span></div>
                    <div class="global-orbit-card global-orbit-card--two" data-float-card><span class="text-2xl">🇬🇧</span><span><strong>London</strong><small>Worldwide delivery</small></span></div>
                    <div class="global-orbit-card global-orbit-card--three" data-float-card><span class="text-2xl">🇳🇬</span><span><strong>Lagos</strong><small>Connected commerce</small></span></div>
                    <div class="global-orbit-card global-orbit-card--four" data-float-card><span class="text-2xl">📦</span><span><strong>Trackable</strong><small>From checkout to arrival</small></span></div>
                </div>
            </div>
            <div class="grid gap-4 border-t border-white/10 pt-5 sm:grid-cols-4" data-hero-reveal>
                @foreach([['Global', 'delivery reach'], ['Secure', 'Paystack checkout'], ['Live', 'order tracking'], ['Clear', 'delivery estimates']] as [$value, $label])
                    <div><strong class="block text-lg tracking-tight">{{ $value }}</strong><span class="text-xs uppercase tracking-[.16em] text-white/35">{{ $label }}</span></div>
                @endforeach
            </div>
        </div>
    </section>

    <section id="global-delivery" class="global-delivery relative overflow-hidden bg-white px-5 py-20 sm:px-8 lg:px-12 lg:py-32">
        <div class="mx-auto max-w-[94rem]">
            <div class="grid gap-10 lg:grid-cols-[.8fr_1.2fr] lg:items-end">
                <div data-reveal><span class="global-kicker">Across borders. Without the confusion.</span><h2 class="mt-5 max-w-3xl text-4xl font-semibold leading-[.98] tracking-[-.055em] sm:text-6xl">From our store to your corner of the world.</h2></div>
                <p class="max-w-2xl text-base leading-7 text-slate-500 lg:justify-self-end" data-reveal>{{ $siteName }} makes international shopping easier to understand. See product availability, checkout securely, get delivery estimates and follow your shipment as it moves.</p>
            </div>
            <div class="mt-14 grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
                @foreach($globalDestinations as $destination)
                    <article class="country-card" data-reveal data-country-card>
                        <div class="country-card__flag">{{ $destination['flag'] }}</div>
                        <div><span class="country-card__code">{{ $destination['code'] }}</span><h3>{{ $destination['name'] }}</h3><p>{{ $destination['city'] }} · global route</p></div>
                        <span class="country-card__arrow">↗</span>
                    </article>
                @endforeach
            </div>
            <p class="mt-5 text-xs text-slate-400">Featured destinations are illustrative highlights. Product delivery availability is confirmed for the destination selected during shopping and checkout.</p>
        </div>
    </section>

    <section class="global-process bg-[#0b0d12] px-5 py-20 text-white sm:px-8 lg:px-12 lg:py-32">
        <div class="mx-auto max-w-[94rem]">
            <div class="grid gap-12 lg:grid-cols-[.7fr_1.3fr]">
                <div data-reveal class="lg:sticky lg:top-28 lg:self-start"><span class="global-kicker global-kicker--dark">How {{ $siteName }} works</span><h2 class="mt-5 text-4xl font-semibold leading-none tracking-[-.055em] sm:text-6xl">Four steps.<br>One clear journey.</h2><p class="mt-5 max-w-md text-white/45">The experience is designed to tell you what happens next, from finding a product to receiving it.</p></div>
                <div class="space-y-3">
                    @foreach([['01','Discover','Explore products and categories in a focused, easy-to-browse global storefront.','✦'],['02','Check your destination','See whether a physical product can be delivered to your selected country.','◎'],['03','Pay securely','Complete checkout through supported secure payment methods with clear order totals.','✓'],['04','Follow the journey','Track fulfilment and shipment progress until your order reaches its destination.','→']] as [$number,$title,$copy,$icon])
                        <article class="process-card" data-reveal><span class="process-card__number">{{ $number }}</span><span class="process-card__icon">{{ $icon }}</span><div><h3>{{ $title }}</h3><p>{{ $copy }}</p></div></article>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    <section id="categories" class="relative px-5 py-20 sm:px-8 lg:px-12 lg:py-32">
        <div class="mx-auto max-w-[94rem]">
            <div class="grid gap-8 lg:grid-cols-[.72fr_1.28fr] lg:items-end">
                <x-storefront.section-heading eyebrow="Shop your world" title="Find what moves you." description="Explore categories designed around discovery, clarity and a faster path to the products you want." />
                <div class="flex lg:justify-end"><a href="{{ route('shop') }}" class="storefront-text-link">View the full store <span>↗</span></a></div>
            </div>
            <div class="mt-12 grid auto-rows-[11rem] grid-cols-2 gap-3 sm:auto-rows-[15rem] lg:grid-cols-4 lg:gap-5">
                @forelse($categories as $category)
                    <a href="{{ route('category.show', $category->slug) }}" class="storefront-category group {{ $loop->first ? 'col-span-2 row-span-2' : '' }} {{ $loop->iteration === 4 ? 'col-span-2' : '' }}" data-reveal>
                        @if($category->image)<img src="{{ asset('storage/' . $category->image) }}" alt="{{ $category->name }}" class="storefront-category__image" loading="lazy">@else<div class="storefront-category__fallback"><span>{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span></div>@endif
                        <div class="storefront-category__scrim"></div><div class="storefront-category__content"><span class="text-[10px] font-semibold uppercase tracking-[.2em] text-white/55">{{ $category->products_count }} products</span><h3 class="mt-1 text-xl font-semibold tracking-tight text-white sm:text-3xl">{{ $category->name }}</h3></div><span class="storefront-category__arrow">↗</span>
                    </a>
                @empty
                    @foreach(['Style','Technology','Home','Beauty'] as $emptyCategory)<a href="{{ route('shop') }}" class="storefront-category group {{ $loop->first ? 'col-span-2 row-span-2' : '' }}" data-reveal><div class="storefront-category__fallback"><span>{{ str_pad($loop->iteration,2,'0',STR_PAD_LEFT) }}</span></div><div class="storefront-category__scrim"></div><div class="storefront-category__content"><h3 class="text-xl font-semibold text-white sm:text-3xl">{{ $emptyCategory }}</h3></div><span class="storefront-category__arrow">↗</span></a>@endforeach
                @endforelse
            </div>
        </div>
    </section>

    @foreach($productSections as $section)
        @if($section['products']->count() > 0)
            <section id="{{ $section['id'] }}" class="px-5 py-20 sm:px-8 lg:px-12 lg:py-28 {{ $loop->even ? 'bg-white' : 'bg-[#f4f1ea]' }}">
                <div class="mx-auto max-w-[94rem]"><div class="grid gap-6 lg:grid-cols-[.8fr_1.2fr] lg:items-end"><x-storefront.section-heading :eyebrow="$section['eyebrow']" :title="$section['title']" :description="$section['description']" /><div class="flex lg:justify-end"><a href="{{ route('shop') }}" class="storefront-text-link">Shop all products <span>↗</span></a></div></div><div class="mt-12 grid grid-cols-2 gap-x-3 gap-y-10 sm:gap-x-5 lg:grid-cols-4">@foreach($section['products'] as $product)<x-storefront.product-card :product="$product" :tag="$section['tag']" />@endforeach</div></div>
            </section>
        @endif
    @endforeach

    <section class="px-5 py-20 sm:px-8 lg:px-12 lg:py-32">
        <div class="global-final-cta mx-auto max-w-[94rem] overflow-hidden rounded-[2.5rem] px-6 py-14 text-white sm:px-10 lg:grid lg:grid-cols-[1.1fr_.9fr] lg:items-center lg:px-14 lg:py-20">
            <div data-reveal><span class="global-kicker global-kicker--dark">The world is closer than it looks</span><h2 class="mt-5 max-w-3xl text-4xl font-semibold leading-[.96] tracking-[-.055em] sm:text-6xl">Discover globally.<br>Order confidently.</h2><p class="mt-5 max-w-xl text-base leading-7 text-white/55">{{ $siteName }} combines product discovery, secure checkout, worldwide fulfilment and tracking in one experience.</p><a href="{{ route('shop') }}" class="global-cta global-cta--primary mt-8">Explore the store <span>↗</span></a></div>
            <div class="global-mini-globe mt-12 lg:mt-0" data-mini-globe aria-hidden="true"><div class="global-mini-globe__ring"></div><div class="global-mini-globe__core">🌍</div><span class="global-mini-globe__flag global-mini-globe__flag--1">🇺🇸</span><span class="global-mini-globe__flag global-mini-globe__flag--2">🇬🇧</span><span class="global-mini-globe__flag global-mini-globe__flag--3">🇳🇬</span></div>
        </div>
    </section>
</main>