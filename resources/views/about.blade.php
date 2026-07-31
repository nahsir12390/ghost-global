@extends('layouts.app')

@php
    $siteName = \App\Helpers\SettingsHelper::get('site_name') ?: config('app.name', 'E-Commerce');
    $siteDescription = \App\Helpers\SettingsHelper::get('site_description') ?: 'Your trusted digital marketplace for quality products and seamless shopping experience';
    $teamMembers = \App\Helpers\SettingsHelper::aboutTeamMembers();
@endphp

@section('title', 'About Us - ' . $siteName)

@section('content')
<div class="bg-white text-gray-900">
    <!-- Hero -->
    <section class="relative min-h-[520px] overflow-hidden bg-gray-950">
        <img src="{{ asset('images/online shopping.jpg') }}"
             alt="{{ $siteName }} marketplace"
             class="absolute inset-0 h-full w-full object-cover opacity-55">
        <div class="absolute inset-0 bg-gradient-to-r from-gray-950 via-gray-950/80 to-red-950/45"></div>

        <div class="relative mx-auto flex min-h-[520px] max-w-7xl items-end px-4 pb-12 pt-24 sm:px-6 lg:px-8">
            <div class="max-w-3xl">
                <div class="mb-5 inline-flex items-center rounded-full border border-white/20 bg-white/10 px-4 py-2 text-sm font-semibold text-white backdrop-blur">
                    Built for local commerce
                </div>
                <h1 class="text-4xl font-bold leading-tight text-white md:text-6xl">
                    About {{ $siteName }}
                </h1>
                <p class="mt-6 max-w-2xl text-lg leading-8 text-gray-100 md:text-xl">
                    {{ $siteDescription }}
                </p>
                <div class="mt-8 flex flex-col gap-3 sm:flex-row">
                    <a href="{{ route('shop') }}" class="inline-flex items-center justify-center rounded-lg bg-red-600 px-6 py-3 text-sm font-semibold text-white shadow-lg shadow-red-950/30 transition hover:bg-red-700">
                        Shop Now
                        <svg class="ml-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                        </svg>
                    </a>
                    <a href="{{ route('register') }}" class="inline-flex items-center justify-center rounded-lg border border-white/40 px-6 py-3 text-sm font-semibold text-white transition hover:bg-white/10">
                        Become a Vendor
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Metrics -->
    <section class="border-b border-gray-200 bg-white">
        <div class="mx-auto grid max-w-7xl grid-cols-2 gap-px bg-gray-200 px-4 sm:px-6 lg:grid-cols-4 lg:px-8">
            @foreach([
                ['value' => '500+', 'label' => 'Happy Customers'],
                ['value' => '50+', 'label' => 'Trusted Vendors'],
                ['value' => '1,000+', 'label' => 'Products Sold'],
                ['value' => '24/7', 'label' => 'Customer Support'],
            ] as $metric)
                <div class="bg-white py-8 text-center">
                    <div class="text-3xl font-bold text-red-600">{{ $metric['value'] }}</div>
                    <div class="mt-2 text-sm font-medium text-gray-600">{{ $metric['label'] }}</div>
                </div>
            @endforeach
        </div>
    </section>

    <!-- Story -->
    <section class="mx-auto max-w-7xl px-4 py-20 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 gap-12 lg:grid-cols-[0.9fr_1.1fr] lg:items-center">
            <div class="relative overflow-hidden rounded-lg bg-gray-100">
                <img src="{{ asset('images/online shopping.jpg') }}"
                     alt="Online shopping experience"
                     class="h-[420px] w-full object-cover">
                <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-gray-950/85 to-transparent p-6">
                    <p class="max-w-md text-sm font-medium leading-6 text-white">
                        A marketplace designed to make local buying and selling simpler, faster, and more trustworthy.
                    </p>
                </div>
            </div>

            <div>
                <p class="text-sm font-bold uppercase tracking-wide text-red-600">Our Story</p>
                <h2 class="mt-3 text-3xl font-bold leading-tight text-gray-950 md:text-4xl">
                    Making everyday commerce easier for every community we serve.
                </h2>
                <div class="mt-6 space-y-5 text-base leading-8 text-gray-600">
                    <p>
                        {{ $siteName }} is a fast-growing digital marketplace built to transform how people buy and sell locally. The platform connects verified sellers to a wider network of buyers through a simple, dependable online experience.
                    </p>
                    <p>
                        From everyday essentials to gadgets, fashion, and services, {{ $siteName }} creates a central space where local businesses can grow and customers can shop with confidence.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Mission -->
    <section class="bg-gray-50 py-20">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 gap-8 lg:grid-cols-3">
                <div class="lg:col-span-1">
                    <p class="text-sm font-bold uppercase tracking-wide text-red-600">Why We Exist</p>
                    <h2 class="mt-3 text-3xl font-bold text-gray-950">Commerce should feel closer, safer, and easier.</h2>
                </div>
                <div class="grid grid-cols-1 gap-6 lg:col-span-2 md:grid-cols-2">
                    <div class="rounded-lg border border-gray-200 bg-white p-7 shadow-sm">
                        <div class="mb-5 flex h-12 w-12 items-center justify-center rounded-lg bg-red-50 text-red-600">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 21h18M5 21V7l8-4 6 3v15M9 9h1m4 0h1M9 13h1m4 0h1M9 17h1m4 0h1" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-950">Our Mission</h3>
                        <p class="mt-3 leading-7 text-gray-600">
                            To digitize local trade by creating a trusted environment where vendors can showcase products and customers can shop with confidence.
                        </p>
                    </div>
                    <div class="rounded-lg border border-gray-200 bg-white p-7 shadow-sm">
                        <div class="mb-5 flex h-12 w-12 items-center justify-center rounded-lg bg-emerald-50 text-emerald-600">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-950">Our Vision</h3>
                        <p class="mt-3 leading-7 text-gray-600">
                            To become a nationally recognized e-commerce brand that supports entrepreneurship, community growth, and economic activity across Nigeria.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- How It Works -->
    <section class="mx-auto max-w-7xl px-4 py-20 sm:px-6 lg:px-8">
        <div class="max-w-2xl">
            <p class="text-sm font-bold uppercase tracking-wide text-red-600">What We Do</p>
            <h2 class="mt-3 text-3xl font-bold text-gray-950 md:text-4xl">{{ $siteName }} connects sellers, buyers, and orders in one place.</h2>
        </div>

        <div class="mt-12 grid grid-cols-1 gap-6 md:grid-cols-3">
            @foreach([
                ['step' => '01', 'title' => 'Vendors list products', 'text' => 'Sellers can register, display products, and manage inventory with a clear digital storefront.'],
                ['step' => '02', 'title' => 'Customers shop easily', 'text' => 'Buyers can browse, compare, and order products without the stress of moving from shop to shop.'],
                ['step' => '03', 'title' => 'Orders stay organized', 'text' => 'The platform supports order coordination, secure payment options, and smoother customer support.'],
            ] as $item)
                <div class="rounded-lg border border-gray-200 bg-white p-7 shadow-sm transition hover:-translate-y-1 hover:shadow-lg">
                    <div class="text-sm font-bold text-red-600">{{ $item['step'] }}</div>
                    <h3 class="mt-5 text-xl font-bold text-gray-950">{{ $item['title'] }}</h3>
                    <p class="mt-3 leading-7 text-gray-600">{{ $item['text'] }}</p>
                </div>
            @endforeach
        </div>
    </section>

    <!-- Values -->
    <section class="bg-gray-950 py-20 text-white">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 gap-10 lg:grid-cols-[0.8fr_1.2fr] lg:items-start">
                <div>
                    <p class="text-sm font-bold uppercase tracking-wide text-red-400">Our Values</p>
                    <h2 class="mt-3 text-3xl font-bold md:text-4xl">The standards behind every transaction.</h2>
                </div>
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    @foreach([
                        ['title' => 'Trust', 'text' => 'Transparent experiences that help buyers and sellers feel confident.'],
                        ['title' => 'Innovation', 'text' => 'Practical technology that makes local commerce easier to use.'],
                        ['title' => 'Community Growth', 'text' => 'A platform that gives local businesses more visibility and reach.'],
                        ['title' => 'Accessibility', 'text' => 'Shopping and selling tools that remain simple for everyday users.'],
                    ] as $value)
                        <div class="rounded-lg border border-white/10 bg-white/5 p-6">
                            <h3 class="text-lg font-bold">{{ $value['title'] }}</h3>
                            <p class="mt-3 text-sm leading-6 text-gray-300">{{ $value['text'] }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    <!-- Team -->
    <section class="mx-auto max-w-7xl px-4 py-20 sm:px-6 lg:px-8">
        <div class="flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
            <div>
                <p class="text-sm font-bold uppercase tracking-wide text-red-600">Our Team</p>
                <h2 class="mt-3 text-3xl font-bold text-gray-950 md:text-4xl">Meet the people building {{ $siteName }}.</h2>
            </div>
            <p class="max-w-xl leading-7 text-gray-600">
                A focused team working to make local commerce more reliable, accessible, and useful for the community.
            </p>
        </div>

        <div class="mt-12 grid grid-cols-1 gap-8 md:grid-cols-2 lg:grid-cols-3">
            @foreach($teamMembers as $member)
                @php
                    $image = $member['image'] ?? '';
                    $imageUrl = $image
                        ? (preg_match('/^https?:\/\//i', $image) ? $image : asset(ltrim($image, '/')))
                        : asset('images/online shopping.jpg');
                @endphp
                <article class="overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm transition hover:-translate-y-1 hover:shadow-lg">
                    <div class="aspect-[4/3] bg-gray-100">
                        <img src="{{ $imageUrl }}" alt="{{ $member['name'] }}" class="h-full w-full object-cover">
                    </div>
                    <div class="p-6">
                        @if(!empty($member['role']))
                            <p class="text-sm font-bold text-red-600">{{ $member['role'] }}</p>
                        @endif
                        <h3 class="mt-2 text-xl font-bold text-gray-950">{{ $member['name'] }}</h3>
                        @if(!empty($member['bio']))
                            <p class="mt-4 text-sm leading-7 text-gray-600">{{ $member['bio'] }}</p>
                        @endif
                    </div>
                </article>
            @endforeach
        </div>
    </section>

    <!-- CTA -->
    <section class="border-t border-gray-200 bg-gray-50">
        <div class="mx-auto max-w-7xl px-4 py-16 text-center sm:px-6 lg:px-8">
            <h2 class="text-3xl font-bold text-gray-950">Ready to shop with {{ $siteName }}?</h2>
            <p class="mx-auto mt-4 max-w-2xl leading-7 text-gray-600">
                Discover products, support local sellers, and enjoy a smoother online shopping experience.
            </p>
            <div class="mt-8 flex flex-col justify-center gap-3 sm:flex-row">
                <a href="{{ route('shop') }}" class="inline-flex items-center justify-center rounded-lg bg-red-600 px-6 py-3 text-sm font-semibold text-white transition hover:bg-red-700">
                    Start Shopping
                </a>
                <a href="{{ route('register') }}" class="inline-flex items-center justify-center rounded-lg border border-gray-300 bg-white px-6 py-3 text-sm font-semibold text-gray-900 transition hover:border-red-200 hover:text-red-700">
                    Become a Vendor
                </a>
            </div>
        </div>
    </section>
</div>
@endsection
