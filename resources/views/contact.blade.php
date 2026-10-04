@extends('layouts.app')

@section('title', 'Contact Us')

@section('content')
@php
    $siteName = \App\Helpers\SettingsHelper::siteName();
    $siteEmail = \App\Helpers\SettingsHelper::get('site_email', config('mail.from.address'));
    $sitePhone = \App\Helpers\SettingsHelper::get('site_phone', '');
    $siteAddress = \App\Helpers\SettingsHelper::get('site_address', 'Available online');
    $whatsAppNumber = \App\Helpers\SettingsHelper::supportWhatsAppNumber();
@endphp

<x-storefront.page-hero eyebrow="Human support" title="Let’s talk." description="Questions about an order, a product or selling with us? Tell us what you need and we’ll point you in the right direction." step="We’re listening" />

<div class="bg-[#f5f3ee] px-4 py-10 sm:px-6 sm:py-16 lg:px-8">
    <div class="mx-auto max-w-6xl">
        @if(session('success'))<div class="mb-6 rounded-2xl border border-emerald-200 bg-emerald-50 p-4 text-sm font-medium text-emerald-700" role="status">{{ session('success') }}</div>@endif
        @if(session('error'))<div class="mb-6 rounded-2xl border border-red-200 bg-red-50 p-4 text-sm font-medium text-red-700" role="alert">{{ session('error') }}</div>@endif

        <div class="grid gap-6 lg:grid-cols-[1.2fr_.8fr]">
            <section class="rounded-[2rem] border border-slate-200 bg-white p-5 shadow-sm sm:p-8 lg:p-10" data-reveal>
                <div class="max-w-xl"><span class="text-[10px] font-bold uppercase tracking-[.22em] text-red-500">Send a message</span><h2 class="mt-3 text-3xl font-semibold tracking-[-.04em] text-slate-950">How can we help?</h2><p class="mt-3 text-sm leading-6 text-slate-500">Share the details below. We usually respond within one business day.</p></div>
                <form action="{{ route('contact.submit') }}" method="POST" class="mt-8 space-y-5">
                    @csrf
                    <div class="grid gap-5 sm:grid-cols-2">
                        <label class="block"><span class="mb-2 block text-sm font-semibold text-slate-800">Full name</span><input type="text" name="name" value="{{ old('name', auth()->user()?->name) }}" required autocomplete="name" placeholder="Your full name" class="w-full rounded-2xl border-slate-200 bg-slate-50 px-4 py-4 text-base focus:border-red-500 focus:bg-white focus:ring-red-500 @error('name') border-red-400 @enderror">@error('name')<span class="mt-2 block text-xs text-red-600">{{ $message }}</span>@enderror</label>
                        <label class="block"><span class="mb-2 block text-sm font-semibold text-slate-800">Email address</span><input type="email" name="email" value="{{ old('email', auth()->user()?->email) }}" required autocomplete="email" placeholder="you@example.com" class="w-full rounded-2xl border-slate-200 bg-slate-50 px-4 py-4 text-base focus:border-red-500 focus:bg-white focus:ring-red-500 @error('email') border-red-400 @enderror">@error('email')<span class="mt-2 block text-xs text-red-600">{{ $message }}</span>@enderror</label>
                    </div>
                    <label class="block"><span class="mb-2 block text-sm font-semibold text-slate-800">Subject</span><input type="text" name="subject" value="{{ old('subject') }}" required placeholder="What can we help with?" class="w-full rounded-2xl border-slate-200 bg-slate-50 px-4 py-4 text-base focus:border-red-500 focus:bg-white focus:ring-red-500 @error('subject') border-red-400 @enderror">@error('subject')<span class="mt-2 block text-xs text-red-600">{{ $message }}</span>@enderror</label>
                    <label class="block"><span class="mb-2 block text-sm font-semibold text-slate-800">Message</span><textarea name="message" rows="6" required minlength="10" maxlength="5000" placeholder="Tell us what happened and include an order number if relevant." class="w-full resize-none rounded-2xl border-slate-200 bg-slate-50 px-4 py-4 text-base focus:border-red-500 focus:bg-white focus:ring-red-500 @error('message') border-red-400 @enderror">{{ old('message') }}</textarea>@error('message')<span class="mt-2 block text-xs text-red-600">{{ $message }}</span>@enderror</label>
                    <button type="submit" class="storefront-button storefront-button--primary w-full shadow-lg shadow-red-600/20 sm:w-auto">Send message <span aria-hidden="true">↗</span></button>
                </form>
            </section>

            <aside class="space-y-4" data-reveal>
                <div class="relative overflow-hidden rounded-[2rem] bg-[#101010] p-7 text-white sm:p-8">
                    <div class="absolute -right-16 -top-16 h-44 w-44 rounded-full bg-red-600/30 blur-3xl"></div>
                    <div class="relative"><span class="text-[10px] font-bold uppercase tracking-[.22em] text-white/40">Direct lines</span><h2 class="mt-3 text-3xl font-semibold tracking-[-.04em]">Prefer a quicker hello?</h2>
                        <div class="mt-8 space-y-3">
                            <a href="mailto:{{ $siteEmail }}" class="block rounded-2xl border border-white/10 bg-white/5 p-4 transition hover:bg-white/10"><span class="block text-xs text-white/40">Email</span><strong class="mt-1 block break-all text-sm">{{ $siteEmail }}</strong></a>
                            <a href="tel:{{ preg_replace('/[^0-9+]/', '', $sitePhone) }}" class="block rounded-2xl border border-white/10 bg-white/5 p-4 transition hover:bg-white/10"><span class="block text-xs text-white/40">Call</span><strong class="mt-1 block text-sm">{{ $sitePhone }}</strong></a>
                            <a href="https://wa.me/{{ $whatsAppNumber }}?text={{ rawurlencode('Hi, I need help with ' . $siteName) }}" target="_blank" rel="noopener" class="block rounded-2xl bg-emerald-500 p-4 transition hover:bg-emerald-400"><span class="block text-xs text-white/70">WhatsApp</span><strong class="mt-1 block text-sm">Start a conversation →</strong></a>
                        </div>
                    </div>
                </div>
                <div class="rounded-[2rem] border border-slate-200 bg-white p-7"><span class="text-[10px] font-bold uppercase tracking-[.22em] text-red-500">Visit us</span><h3 class="mt-3 text-xl font-semibold text-slate-950">{{ $siteAddress }}</h3><p class="mt-2 text-sm leading-6 text-slate-500">For order help, including your order number helps us respond faster.</p></div>
                <a href="{{ route('tracking.index') }}" class="flex items-center justify-between rounded-[2rem] border border-slate-200 bg-white p-6 transition hover:border-red-200 hover:shadow-lg"><span><strong class="block text-slate-950">Track an order</strong><small class="mt-1 block text-slate-500">Get a live status update</small></span><span class="text-xl text-red-500">↗</span></a>
            </aside>
        </div>
    </div>
</div>
@endsection
