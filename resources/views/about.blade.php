@extends('layouts.app')

@section('title', 'About Us')

@section('content')
@php($siteName = \App\Helpers\SettingsHelper::siteName())

<x-storefront.page-hero eyebrow="Built for better commerce" :title="'Local ambition. Bigger possibilities.'" :description="$siteName . ' connects remarkable products, trusted sellers and everyday shoppers through one thoughtful marketplace.'" />

<div class="bg-[#f5f3ee]">
    <section class="px-5 py-16 sm:px-8 lg:px-12 lg:py-28">
        <div class="mx-auto grid max-w-[90rem] gap-12 lg:grid-cols-[.8fr_1.2fr] lg:items-start">
            <x-storefront.section-heading eyebrow="Why we exist" title="Commerce should feel human." description="We are building a marketplace where discovery feels inspiring, selling feels possible and every transaction feels clear." />
            <div class="grid gap-4 sm:grid-cols-2">
                @foreach([
                    ['01', 'Trust first', 'Active sellers, transparent information and secure purchasing are built into the experience.'],
                    ['02', 'Designed locally', 'We understand the realities of buying, selling and delivering within our communities.'],
                    ['03', 'Growth together', 'Every order can create momentum for a customer, a seller and the wider marketplace.'],
                    ['04', 'Simple by design', 'Technology should remove friction—not add more steps to everyday shopping.'],
                ] as [$number, $title, $copy])
                    <article class="rounded-[1.75rem] border border-slate-200 bg-white p-6 transition duration-300 hover:-translate-y-1 hover:shadow-xl hover:shadow-slate-900/5" data-reveal>
                        <span class="text-xs font-bold tracking-[.2em] text-red-500">{{ $number }}</span><h3 class="mt-10 text-2xl font-semibold tracking-tight text-slate-950">{{ $title }}</h3><p class="mt-3 text-sm leading-6 text-slate-500">{{ $copy }}</p>
                    </article>
                @endforeach
            </div>
        </div>
    </section>

    <section class="overflow-hidden bg-[#d9272e] px-5 py-20 text-white sm:px-8 lg:px-12 lg:py-28">
        <div class="mx-auto grid max-w-[90rem] gap-12 lg:grid-cols-2 lg:items-center">
            <div data-reveal><span class="text-xs font-bold uppercase tracking-[.24em] text-white/55">Our vision</span><blockquote class="mt-6 text-4xl font-semibold leading-[.98] tracking-[-.055em] sm:text-6xl">A marketplace where local ideas travel further.</blockquote></div>
            <div class="grid grid-cols-2 gap-px overflow-hidden rounded-[2rem] bg-white/15" data-reveal>
                <x-storefront.metric class="bg-red-700/35 p-7" value="Local" label="Seller powered" dark />
                <x-storefront.metric class="bg-red-700/35 p-7" value="Secure" label="Every checkout" dark />
                <x-storefront.metric class="bg-red-700/35 p-7" value="Open" label="Always discovering" dark />
                <x-storefront.metric class="bg-red-700/35 p-7" value="Growing" label="Together" dark />
            </div>
        </div>
    </section>

    <section class="px-5 py-16 sm:px-8 lg:px-12 lg:py-28">
        <div class="mx-auto max-w-[90rem] rounded-[2rem] bg-[#101010] px-6 py-12 text-white sm:px-10 lg:flex lg:items-end lg:justify-between lg:px-14 lg:py-16">
            <div class="max-w-2xl"><span class="text-xs font-bold uppercase tracking-[.22em] text-red-400">Your next find is waiting</span><h2 class="mt-4 text-3xl font-semibold tracking-[-.04em] sm:text-5xl">Be part of what we’re building.</h2><p class="mt-4 text-white/50">Discover products or bring your own store into the marketplace.</p></div>
            <div class="mt-8 flex flex-col gap-3 sm:flex-row lg:mt-0"><a href="{{ route('shop') }}" class="storefront-button storefront-button--light">Start shopping</a>@auth<a href="{{ route('my.orders') }}" class="storefront-button storefront-button--ghost">My orders</a>@else<a href="{{ route('register') }}" class="storefront-button storefront-button--ghost">Join {{ $siteName }}</a>@endauth</div>
        </div>
    </section>
</div>
@endsection
