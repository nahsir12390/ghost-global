@extends('layouts.app')

@section('title', 'Track Your Order')

@section('content')
<x-storefront.page-hero eyebrow="Live order intelligence" title="Where is my order?" description="Enter two details from your receipt to see the latest status, delivery progress and fulfilment updates." step="Private & secure" />

<div class="bg-[#f5f3ee] px-4 py-10 sm:px-6 sm:py-16 lg:px-8">
    <div class="mx-auto grid max-w-6xl gap-6 lg:grid-cols-[1.08fr_.92fr]">
        <section class="rounded-[2rem] border border-slate-200 bg-white p-5 shadow-sm sm:p-8 lg:p-10" data-reveal>
            <div class="flex items-start justify-between gap-5">
                <div><span class="text-[10px] font-bold uppercase tracking-[.22em] text-red-500">Find your delivery</span><h2 class="mt-3 text-3xl font-semibold tracking-[-.04em] text-slate-950">Track in seconds.</h2></div>
                <span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-red-50 text-red-600">⌁</span>
            </div>

            @if($errors->any() || session('error'))
                <div class="mt-6 rounded-2xl border border-red-200 bg-red-50 p-4 text-sm font-medium text-red-700" role="alert">
                    {{ session('error') ?: $errors->first() }}
                </div>
            @endif

            <form action="{{ route('tracking.search') }}" method="POST" class="mt-8 space-y-5">
                @csrf
                <label class="block">
                    <span class="mb-2 block text-sm font-semibold text-slate-800">Order number</span>
                    <input type="text" name="order_number" value="{{ old('order_number') }}" placeholder="ORD-ABC123XYZ" autocomplete="off" required class="w-full rounded-2xl border-slate-200 bg-slate-50 px-4 py-4 text-base uppercase tracking-wide transition focus:border-red-500 focus:bg-white focus:ring-red-500 @error('order_number') border-red-400 @enderror">
                    <span class="mt-2 block text-xs text-slate-400">Printed on your receipt and confirmation email.</span>
                </label>
                <label class="block">
                    <span class="mb-2 block text-sm font-semibold text-slate-800">Email or phone number</span>
                    <input type="text" name="email_or_phone" value="{{ old('email_or_phone') }}" placeholder="The contact used at checkout" autocomplete="email" required class="w-full rounded-2xl border-slate-200 bg-slate-50 px-4 py-4 text-base transition focus:border-red-500 focus:bg-white focus:ring-red-500 @error('email_or_phone') border-red-400 @enderror">
                    <span class="mt-2 block text-xs text-slate-400">Used only to securely match your order.</span>
                </label>
                <button type="submit" class="storefront-button storefront-button--primary w-full">Show tracking progress <span>→</span></button>
            </form>
        </section>

        <aside class="relative overflow-hidden rounded-[2rem] bg-[#101010] p-6 text-white sm:p-8 lg:p-10" data-reveal>
            <div class="absolute -right-20 -top-20 h-64 w-64 rounded-full bg-red-600/25 blur-3xl"></div>
            <div class="relative">
                <span class="text-[10px] font-bold uppercase tracking-[.22em] text-white/40">From order to doorstep</span>
                <h2 class="mt-4 text-3xl font-semibold leading-tight tracking-[-.04em]">Every movement.<br>One clear timeline.</h2>
                <div class="mt-10 space-y-3">
                    @foreach([['01', 'Order confirmed', 'Your payment and items are verified.'], ['02', 'Prepared by seller', 'The seller packs and releases your order.'], ['03', 'On the way', 'Delivery progress appears on your timeline.'], ['04', 'Delivered', 'Your order arrives at its destination.']] as [$number, $title, $copy])
                        <div class="group flex gap-4 rounded-2xl border border-white/10 bg-white/5 p-4 transition hover:border-red-400/30 hover:bg-white/10">
                            <span class="text-xs font-bold text-red-400">{{ $number }}</span><div><h3 class="text-sm font-semibold">{{ $title }}</h3><p class="mt-1 text-xs leading-5 text-white/45">{{ $copy }}</p></div>
                        </div>
                    @endforeach
                </div>
            </div>
        </aside>
    </div>

    <div class="mx-auto mt-8 grid max-w-6xl gap-3 sm:grid-cols-3">
        @foreach([['Live status', 'See the newest fulfilment update.'], ['Private lookup', 'Two details securely identify the order.'], ['Need help?', 'Our support team is one message away.']] as [$title, $copy])
            <div class="rounded-2xl border border-slate-200 bg-white p-5"><h3 class="font-semibold text-slate-900">{{ $title }}</h3><p class="mt-2 text-sm leading-6 text-slate-500">{{ $copy }}</p></div>
        @endforeach
    </div>
</div>
@endsection
