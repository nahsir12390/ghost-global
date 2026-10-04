@extends('layouts.app')

@section('title', 'Dashboard')

@section('header')
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        {{ __('Dashboard') }}
    </h2>
@endsection

@section('content')
    @php
        $currentUser = auth()->user();
        $referralCode = $currentUser->getOrCreateReferralCode();
        $referralLink = $currentUser->referralLink();
        $referralReward = \App\Helpers\SettingsHelper::referralRewardAmount();
        $directReferrals = $currentUser->referrals()->count();
        $rewardedReferrals = $currentUser->referrals()->whereNotNull('referral_rewarded_at')->count();
        $referralEarnings = $currentUser->walletTransactions()
            ->where('type', \App\Models\WalletTransaction::TYPE_REFERRAL_BONUS)
            ->where('status', 'completed')
            ->sum('amount');
        $recentOrders = $currentUser->orders()->latest()->take(5)->get();
        $orderSummary = $currentUser->orders()
            ->selectRaw("COUNT(*) as total, SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending, SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed")
            ->first();
    @endphp

    <div class="min-h-screen bg-[#f5f3ee]">
    <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 sm:py-12 lg:px-8">
        <div class="relative mb-8 overflow-hidden rounded-[2rem] bg-slate-950 px-6 py-8 text-white shadow-[0_28px_70px_-34px_rgba(15,23,42,.8)] sm:px-8 lg:px-10">
            <div class="absolute -right-16 -top-24 h-64 w-64 rounded-full bg-red-600/30 blur-3xl" aria-hidden="true"></div>
            <div class="relative flex flex-col gap-6 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <p class="text-xs font-bold uppercase tracking-[.24em] text-red-400">My account</p>
                    <h1 class="mt-3 text-3xl font-bold tracking-tight sm:text-4xl">Welcome back, {{ str($currentUser->name)->before(' ') }}.</h1>
                    <p class="mt-2 text-sm text-slate-300 sm:text-base">Orders, rewards and account tools in one calm workspace.</p>
                </div>
                <div class="flex flex-wrap gap-3">
                    <a href="{{ route('shop') }}" class="rounded-2xl bg-white px-5 py-3 text-sm font-bold text-slate-950 transition hover:-translate-y-0.5 hover:bg-red-50">Continue shopping</a>
                    <a href="{{ route('profile') }}" class="rounded-2xl border border-white/15 bg-white/5 px-5 py-3 text-sm font-bold text-white transition hover:bg-white/10">Profile settings</a>
                </div>
            </div>
        </div>

        <div class="mb-8 rounded-3xl border border-slate-200 bg-gradient-to-r from-slate-950 via-slate-900 to-red-800 p-6 text-white shadow-sm">
            <div class="grid gap-6 lg:grid-cols-[1.2fr_.8fr] lg:items-center">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.22em] text-red-100">Referral wallet rewards</p>
                    <h2 class="mt-3 text-2xl font-semibold">Invite friends and earn wallet credit</h2>
                    <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-200">
                        Share your referral link. When someone joins through your code and completes their first paid order, you can earn {{ \App\Helpers\SettingsHelper::currency($referralReward) }} in wallet credit.
                    </p>

                    <div class="mt-5 flex flex-col gap-3 sm:flex-row">
                        <div class="rounded-2xl bg-white/10 px-4 py-3 text-sm">
                            <span class="block text-xs uppercase tracking-wide text-slate-300">Referral Code</span>
                            <span class="mt-1 block font-semibold">{{ $referralCode }}</span>
                        </div>
                        <div class="rounded-2xl bg-white/10 px-4 py-3 text-sm">
                            <span class="block text-xs uppercase tracking-wide text-slate-300">Reward per success</span>
                            <span class="mt-1 block font-semibold">{{ \App\Helpers\SettingsHelper::currency($referralReward) }}</span>
                        </div>
                    </div>
                </div>

                <div class="rounded-3xl bg-white/10 p-5 backdrop-blur">
                    <label class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-300">Share Link</label>
                    <div class="mt-3 break-all rounded-2xl bg-white px-4 py-3 text-sm font-medium text-slate-700">
                        {{ $referralLink }}
                    </div>
                    <div class="mt-4 grid grid-cols-3 gap-3 text-center">
                        <div class="rounded-2xl bg-white/10 px-3 py-3">
                            <p class="text-xs uppercase tracking-wide text-slate-300">Signups</p>
                            <p class="mt-2 text-xl font-semibold">{{ $directReferrals }}</p>
                        </div>
                        <div class="rounded-2xl bg-white/10 px-3 py-3">
                            <p class="text-xs uppercase tracking-wide text-slate-300">Rewarded</p>
                            <p class="mt-2 text-xl font-semibold">{{ $rewardedReferrals }}</p>
                        </div>
                        <div class="rounded-2xl bg-white/10 px-3 py-3">
                            <p class="text-xs uppercase tracking-wide text-slate-300">Earned</p>
                            <p class="mt-2 text-xl font-semibold">{{ \App\Helpers\SettingsHelper::currency($referralEarnings) }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="mb-8 grid grid-cols-1 gap-6 md:grid-cols-2 xl:grid-cols-4">
            <div class="rounded-[1.5rem] border border-white bg-white/90 p-6 shadow-sm transition duration-300 hover:-translate-y-1 hover:shadow-xl">
                <div class="flex items-center">
                    <div class="rounded-md bg-red-100 p-3">
                        <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Total Orders</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ (int) $orderSummary->total }}</p>
                    </div>
                </div>
            </div>

            <div class="rounded-[1.5rem] border border-white bg-white/90 p-6 shadow-sm transition duration-300 hover:-translate-y-1 hover:shadow-xl">
                <div class="flex items-center">
                    <div class="rounded-md bg-yellow-100 p-3">
                        <svg class="h-6 w-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Pending Orders</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ (int) $orderSummary->pending }}</p>
                    </div>
                </div>
            </div>

            <div class="rounded-[1.5rem] border border-white bg-white/90 p-6 shadow-sm transition duration-300 hover:-translate-y-1 hover:shadow-xl">
                <div class="flex items-center">
                    <div class="rounded-md bg-green-100 p-3">
                        <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Completed Orders</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ (int) $orderSummary->completed }}</p>
                    </div>
                </div>
            </div>

            <div class="rounded-[1.5rem] border border-white bg-white/90 p-6 shadow-sm transition duration-300 hover:-translate-y-1 hover:shadow-xl">
                <div class="flex items-center">
                    <div class="rounded-md bg-emerald-100 p-3">
                        <svg class="h-6 w-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-2m0-6h2a2 2 0 012 2v2a2 2 0 01-2 2h-2m0-6h-4a2 2 0 100 4h4" />
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Wallet Balance</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ \App\Helpers\SettingsHelper::currency(optional($currentUser->wallet)->balance ?? 0) }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
            <div class="rounded-[2rem] border border-slate-200 bg-white p-6 shadow-sm sm:p-7">
                <div class="mb-6 flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-gray-900">Recent Orders</h2>
                    <a href="{{ route('my.orders') }}" class="text-sm font-medium text-red-600 hover:text-red-700">
                        View all ->
                    </a>
                </div>

                @if($recentOrders->count() > 0)
                    <div class="space-y-4">
                        @foreach($recentOrders as $order)
                            <a href="{{ route('my.orders.show', $order) }}" class="block rounded-2xl border border-slate-200 p-4 transition hover:border-red-200 hover:bg-red-50/40">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">Order #{{ $order->order_number }}</p>
                                        <p class="text-xs text-gray-500">{{ $order->created_at->format('M d, Y') }}</p>
                                    </div>
                                    <span class="rounded-full px-2 py-1 text-xs font-medium
                                        @if($order->status == 'completed') bg-green-100 text-green-800
                                        @elseif($order->status == 'processing') bg-blue-100 text-blue-800
                                        @elseif($order->status == 'pending') bg-yellow-100 text-yellow-800
                                        @elseif($order->status == 'cancelled') bg-red-100 text-red-800
                                        @else bg-gray-100 text-gray-800 @endif">
                                        {{ ucfirst($order->status) }}
                                    </span>
                                </div>
                                <p class="mt-2 text-sm text-gray-600">{{ \App\Helpers\SettingsHelper::currency($order->total) }}</p>
                            </a>
                        @endforeach
                    </div>
                @else
                    <div class="py-8 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                        <p class="mt-2 text-sm text-gray-600">No orders yet</p>
                        <a href="{{ route('shop') }}" class="btn-primary mt-4 inline-block">Start Shopping</a>
                    </div>
                @endif
            </div>

            <div class="rounded-[2rem] border border-slate-200 bg-white p-6 shadow-sm sm:p-7">
                <h2 class="mb-6 text-lg font-semibold text-gray-900">Account Management</h2>
                <div class="space-y-4">
                    <a href="{{ route('profile') }}" class="flex items-center rounded-lg border border-gray-200 p-4 transition-colors hover:border-red-200 hover:bg-red-50">
                        <div class="rounded-md bg-red-100 p-3">
                            <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-900">Update Profile</p>
                            <p class="text-xs text-gray-500">Edit your personal information</p>
                        </div>
                    </a>

                    <a href="{{ route('my.orders') }}" class="flex items-center rounded-lg border border-gray-200 p-4 transition-colors hover:border-red-200 hover:bg-red-50">
                        <div class="rounded-md bg-blue-100 p-3">
                            <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-900">My Orders</p>
                            <p class="text-xs text-gray-500">View and track your orders</p>
                        </div>
                    </a>

                    <a href="{{ route('wallet.index') }}" class="flex items-center rounded-lg border border-gray-200 p-4 transition-colors hover:border-red-200 hover:bg-red-50">
                        <div class="rounded-md bg-emerald-100 p-3">
                            <svg class="h-6 w-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-2m0-6h2a2 2 0 012 2v2a2 2 0 01-2 2h-2m0-6h-4a2 2 0 100 4h4" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-900">My Wallet</p>
                            <p class="text-xs text-gray-500">Fund your balance and pay faster at checkout</p>
                        </div>
                    </a>



                    <a href="{{ route('cart') }}" class="flex items-center rounded-lg border border-gray-200 p-4 transition-colors hover:border-red-200 hover:bg-red-50">
                        <div class="rounded-md bg-green-100 p-3">
                            <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-900">Shopping Cart</p>
                            <p class="text-xs text-gray-500">Review items in your cart</p>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>
    </div>
@endsection
