@extends('layouts.app')

@section('title', 'About Us')

@section('content')
@php
    $siteName = \App\Helpers\SettingsHelper::siteName();
    $siteTagline = \App\Helpers\SettingsHelper::siteTagline();
    $siteDescription = \App\Helpers\SettingsHelper::siteDescription();
@endphp

<x-storefront.page-hero eyebrow="Built for global shopping" :title="$siteTagline" :description="$siteDescription" />

<div class="bg-[#f5f3ee]">
    <section class="px-5 py-16 sm:px-8 lg:px-12 lg:py-28">
        <div class="mx-auto grid max-w-[90rem] gap-12 lg:grid-cols-[.8fr_1.2fr] lg:items-start">
            <x-storefront.section-heading eyebrow="Why we exist" title="Global shopping should feel simple." :description="$siteName . ' brings product discovery, secure purchasing and reliable fulfilment into one clear experience.'" />
            <div class="grid gap-4 sm:grid-cols-2">
                @foreach([
                    ['01', 'Trust first', 'Clear product information, secure purchasing and transparent order updates are built into the experience.'],
                    ['02', 'Global reach', 'Discover products with delivery availability and fulfilment information designed for customers across supported destinations.'],
                    ['03', 'Reliable fulfilment', 'From checkout to shipment tracking, every order is designed to stay clear and easy to follow.'],
                    ['04', 'Simple by design', 'Technology should remove friction—not add more steps to everyday shopping.'],
                ] as [$number, $title, $copy])
                    <article class="rounded-[1.75rem] border border-slate-200 bg-white p-6 transition duration-300 hover:-translate-y-1 hover:shadow-xl hover:shadow-slate-900/5" data-reveal>
                        <span class="text-xs font-bold tracking-[.2em] text-red-500">{{ $number }}</span>
                        <h3 class="mt-10 text-2xl font-semibold tracking-tight text-slate-950">{{ $title }}</h3>
                        <p class="mt-3 text-sm leading-6 text-slate-500">{{ $copy }}</p>
                    </article>
                @endforeach
            </div>
        </div>
    </section>

    <section class="overflow-hidden bg-[#d9272e] px-5 py-20 text-white sm:px-8 lg:px-12 lg:py-28">
        <div class="mx-auto grid max-w-[90rem] gap-12 lg:grid-cols-2 lg:items-center">
            <div data-reveal>
                <span class="text-xs font-bold uppercase tracking-[.24em] text-white/55">Our vision</span>
                <blockquote class="mt-6 text-4xl font-semibold leading-[.98] tracking-[-.055em] sm:text-6xl">Great products. Wider reach. Clear delivery.</blockquote>
            </div>
            <div class="grid grid-cols-2 gap-px overflow-hidden rounded-[2rem] bg-white/15" data-reveal>
                <x-storefront.metric class="bg-red-700/35 p-7" value="Global" label="Product reach" dark />
                <x-storefront.metric class="bg-red-700/35 p-7" value="Secure" label="Every checkout" dark />
                <x-storefront.metric class="bg-red-700/35 p-7" value="Tracked" label="Order progress" dark />
                <x-storefront.metric class="bg-red-700/35 p-7" value="Simple" label="Shopping experience" dark />
            </div>
        </div>
    </section>

    <section class="px-5 py-16 sm:px-8 lg:px-12 lg:py-28">
        <div class="mx-auto max-w-[90rem] rounded-[2rem] bg-[#101010] px-6 py-12 text-white sm:px-10 lg:flex lg:items-end lg:justify-between lg:px-14 lg:py-16">
            <div class="max-w-2xl">
                <span class="text-xs font-bold uppercase tracking-[.22em] text-red-400">Your next find is waiting</span>
                <h2 class="mt-4 text-3xl font-semibold tracking-[-.04em] sm:text-5xl">Discover what {{ $siteName }} has for you.</h2>
                <p class="mt-4 text-white/50">Browse the store, order securely and follow your delivery from checkout to arrival.</p>
            </div>
            <div class="mt-8 flex flex-col gap-3 sm:flex-row lg:mt-0">
                <a href="{{ route('shop') }}" class="storefront-button storefront-button--light">Start shopping</a>
                @auth
                    <a href="{{ route('my.orders') }}" class="storefront-button storefront-button--ghost">My orders</a>
                @else
                    <a href="{{ route('register') }}" class="storefront-button storefront-button--ghost">Create account</a>
                @endauth
            </div>
        </div>
    </section>
</div>
@endsection
