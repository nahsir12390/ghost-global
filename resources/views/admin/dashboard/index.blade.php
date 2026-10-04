@extends('layouts.admin')

@section('title', 'Dashboard')
@section('breadcrumb', 'Dashboard')

@php
    $user = Auth::user();
@endphp

@section('content')
<div x-data="dashboard()" x-init="init()" class="space-y-6">
    <section class="overflow-hidden rounded-[2rem] border border-slate-200 bg-gradient-to-br from-slate-950 via-slate-900 to-red-900 text-white shadow-xl shadow-slate-900/10">
        <div class="grid gap-6 px-6 py-7 lg:grid-cols-[1.35fr_.65fr] lg:px-8">
            <div class="space-y-5">
                <div class="inline-flex items-center gap-2 rounded-full border border-white/15 bg-white/10 px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.26em] text-red-100">
                    <span class="inline-block h-2 w-2 rounded-full bg-red-400"></span>
                    {{ 'Admin Control Hub' }}
                </div>

                <div>
                    <h1 class="text-3xl font-semibold tracking-tight sm:text-4xl">
                        {{ 'Dashboard overview for your store team' }}
                    </h1>
                    <p class="mt-3 max-w-3xl text-sm leading-6 text-slate-200/85 sm:text-base">
                        {{ 'Keep an eye on orders, payments and stock levels without jumping between multiple screens.' }}
                    </p>
                </div>

                <div class="grid gap-3 sm:grid-cols-3">

                        <div class="rounded-2xl border border-white/10 bg-white/10 px-4 py-4 backdrop-blur">
                            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-300">Pending Orders</p>
                            <p class="mt-2 text-lg font-semibold">{{ $pendingOrders ?? 0 }}</p>
                            <p class="mt-1 text-xs text-slate-300">Orders waiting for action</p>
                        </div>



                </div>
            </div>

            <div class="grid gap-4 rounded-[1.75rem] border border-white/10 bg-white/10 p-5 backdrop-blur">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-300">Timeframe</p>
                    <div class="mt-3 relative">
                        <select x-model="selectedPeriod" @change="updatePeriod()"
                                class="w-full appearance-none rounded-2xl border border-white/15 bg-white/95 px-4 py-3 pr-10 text-sm font-medium text-slate-800 focus:border-red-300 focus:outline-none focus:ring-2 focus:ring-red-200">
                            <option value="today">Today</option>
                            <option value="week">This Week</option>
                            <option value="month">This Month</option>
                            <option value="year">This Year</option>
                        </select>
                        <span class="pointer-events-none absolute inset-y-0 right-4 flex items-center text-slate-400">
                            <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                            </svg>
                        </span>
                    </div>
                </div>

                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-300">Quick actions</p>
                    <div class="mt-3 grid gap-3">
                        <button @click="refreshData()" :disabled="refreshing"
                                class="inline-flex items-center justify-center gap-2 rounded-2xl bg-white px-4 py-3 text-sm font-semibold text-slate-900 transition hover:bg-red-50 disabled:cursor-not-allowed disabled:opacity-60">
                            <svg :class="{'animate-spin': refreshing}" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h5M20 20v-5h-5M5.64 18.36A9 9 0 1020 12M18.36 5.64A9 9 0 004 12" />
                            </svg>
                            <span x-text="refreshing ? 'Refreshing...' : 'Refresh Dashboard'"></span>
                        </button>


                            <div class="grid gap-3 sm:grid-cols-2">
                                <a href="{{ route('admin.orders.index') }}" class="rounded-2xl border border-white/15 bg-white/10 px-4 py-3 text-sm font-semibold text-white transition hover:bg-white/15">
                                    Review Orders
                                </a>
                                <a href="{{ route('admin.dashboard.database-backup') }}" class="rounded-2xl border border-white/15 bg-white/10 px-4 py-3 text-sm font-semibold text-white transition hover:bg-white/15">
                                    Backup Database
                                </a>
                            </div>

                    </div>
                </div>
            </div>
        </div>
    </section>



    @if (session('success'))
        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-medium text-red-800">
            {{ session('error') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="rounded-2xl border border-red-200 bg-red-50 px-4 py-4 text-sm text-red-800">
            <p class="font-semibold">Please fix the highlighted form issues.</p>
            <ul class="mt-2 list-disc space-y-1 pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif


        <section class="grid gap-6 xl:grid-cols-2">
            <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.22em] text-emerald-700">Wallet Oversight</p>
                        <h2 class="mt-2 text-xl font-semibold text-slate-900">Customer wallet activity</h2>
                    </div>
                    <span class="rounded-full bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700">
                        {{ $walletSummary['wallet_users'] ?? 0 }} wallets
                    </span>
                </div>

                <div class="mt-6 grid gap-4 sm:grid-cols-2">
                    <div class="rounded-2xl bg-slate-50 p-4">
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Total Wallet Balance</p>
                        <p class="mt-2 text-2xl font-semibold text-slate-900">{{ \App\Helpers\SettingsHelper::currency($walletSummary['total_balance'] ?? 0) }}</p>
                    </div>
                    <div class="rounded-2xl bg-slate-50 p-4">
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Top-ups Today</p>
                        <p class="mt-2 text-2xl font-semibold text-slate-900">{{ \App\Helpers\SettingsHelper::currency($walletSummary['topups_today'] ?? 0) }}</p>
                    </div>
                    <div class="rounded-2xl bg-slate-50 p-4">
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Transactions Today</p>
                        <p class="mt-2 text-2xl font-semibold text-slate-900">{{ number_format($walletSummary['transactions_today'] ?? 0) }}</p>
                    </div>
                    <div class="rounded-2xl bg-slate-50 p-4">
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Wallet-enabled Users</p>
                        <p class="mt-2 text-2xl font-semibold text-slate-900">{{ number_format($walletSummary['wallet_users'] ?? 0) }}</p>
                    </div>
                </div>
            </div>

            <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.22em] text-blue-700">Referral Program</p>
                        <h2 class="mt-2 text-xl font-semibold text-slate-900">Referral performance overview</h2>
                    </div>
                    <span class="rounded-full bg-blue-50 px-3 py-1 text-xs font-semibold text-blue-700">
                        {{ \App\Helpers\SettingsHelper::currency(\App\Helpers\SettingsHelper::referralRewardAmount()) }} reward
                    </span>
                </div>

                <div class="mt-6 grid gap-4 sm:grid-cols-2">
                    <div class="rounded-2xl bg-slate-50 p-4">
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Users Referred</p>
                        <p class="mt-2 text-2xl font-semibold text-slate-900">{{ number_format($referralSummary['referred_users'] ?? 0) }}</p>
                    </div>
                    <div class="rounded-2xl bg-slate-50 p-4">
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Active Referrers</p>
                        <p class="mt-2 text-2xl font-semibold text-slate-900">{{ number_format($referralSummary['total_referrers'] ?? 0) }}</p>
                    </div>
                    <div class="rounded-2xl bg-slate-50 p-4">
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Rewarded Referrals</p>
                        <p class="mt-2 text-2xl font-semibold text-slate-900">{{ number_format($referralSummary['rewarded_referrals'] ?? 0) }}</p>
                    </div>
                    <div class="rounded-2xl bg-slate-50 p-4">
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Bonus Paid Out</p>
                        <p class="mt-2 text-2xl font-semibold text-slate-900">{{ \App\Helpers\SettingsHelper::currency($referralSummary['referral_payouts'] ?? 0) }}</p>
                    </div>
                </div>
            </div>
        </section>

        <section class="grid gap-6 xl:grid-cols-[1.1fr_.9fr]">
            <div class="rounded-3xl border border-slate-200 bg-white shadow-sm">
                <div class="flex items-center justify-between border-b border-slate-200 px-6 py-5">
                    <div>
                        <h2 class="text-lg font-semibold text-slate-900">Recent wallet activity</h2>
                        <p class="mt-1 text-sm text-slate-500">Latest wallet top-ups, debits, refunds, and referral bonuses.</p>
                    </div>
                </div>

                <div class="divide-y divide-slate-200">
                    @forelse($recentWalletTransactions as $transaction)
                        <div class="flex flex-col gap-3 px-6 py-4 sm:flex-row sm:items-center sm:justify-between">
                            <div>
                                <p class="text-sm font-semibold text-slate-900">{{ $transaction->user?->name ?: 'Deleted user' }}</p>
                                <p class="mt-1 text-sm text-slate-500">{{ ucfirst(str_replace('_', ' ', $transaction->type)) }} • {{ $transaction->reference }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-semibold {{ $transaction->direction === 'credit' ? 'text-emerald-600' : 'text-slate-900' }}">
                                    {{ $transaction->direction === 'credit' ? '+' : '-' }}{{ \App\Helpers\SettingsHelper::currency($transaction->amount) }}
                                </p>
                                <p class="mt-1 text-xs text-slate-400">{{ $transaction->created_at->diffForHumans() }}</p>
                            </div>
                        </div>
                    @empty
                        <div class="px-6 py-10 text-center text-sm text-slate-500">No wallet transactions yet.</div>
                    @endforelse
                </div>
            </div>

            <div class="rounded-3xl border border-slate-200 bg-white shadow-sm">
                <div class="flex items-center justify-between border-b border-slate-200 px-6 py-5">
                    <div>
                        <h2 class="text-lg font-semibold text-slate-900">Top referrers</h2>
                        <p class="mt-1 text-sm text-slate-500">Users bringing in the most rewarded signups.</p>
                    </div>
                </div>

                <div class="divide-y divide-slate-200">
                    @forelse($topReferrers as $referrer)
                        <div class="flex items-center justify-between gap-4 px-6 py-4">
                            <div>
                                <p class="text-sm font-semibold text-slate-900">{{ $referrer->name }}</p>
                                <p class="mt-1 text-xs text-slate-400">{{ $referrer->referral_code }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-semibold text-slate-900">{{ $referrer->rewarded_referrals_count }} rewarded</p>
                                <p class="mt-1 text-xs text-slate-500">{{ $referrer->referrals_count }} total signups</p>
                            </div>
                        </div>
                    @empty
                        <div class="px-6 py-10 text-center text-sm text-slate-500">No referral activity yet.</div>
                    @endforelse
                </div>
            </div>
        </section>




    <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">

            <article class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
                <p class="text-sm font-medium text-slate-500">Pending Orders</p>
                <p class="mt-3 text-3xl font-semibold text-slate-900">{{ $pendingOrders ?? 0 }}</p>
                <p class="mt-2 text-xs text-slate-500">Orders waiting for review or fulfillment</p>
            </article>
            <article class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
                <p class="text-sm font-medium text-slate-500">Processing Orders</p>
                <p class="mt-3 text-3xl font-semibold text-slate-900">{{ $processingOrders ?? 0 }}</p>
                <p class="mt-2 text-xs text-slate-500">Orders currently in progress</p>
            </article>



    </section>

    <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <template x-for="stat in stats" :key="stat.id">
            <article class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <p class="text-sm font-medium text-slate-500" x-text="stat.label"></p>
                        <p class="mt-3 text-3xl font-semibold tracking-tight text-slate-900" x-text="stat.value"></p>
                        <p class="mt-2 text-xs text-slate-500" x-text="stat.change"></p>
                    </div>
                    <div :class="stat.iconBg" class="flex h-12 w-12 items-center justify-center rounded-2xl">
                        <svg :class="stat.iconColor" class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="stat.icon"></path>
                        </svg>
                    </div>
                </div>
            </article>
        </template>
    </section>





    <section class="grid gap-6 xl:grid-cols-[1.5fr_.9fr]">
        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="flex flex-col gap-4 border-b border-slate-200 pb-5 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-lg font-semibold text-slate-900">Sales overview</h2>
                    <p class="mt-1 text-sm text-slate-500">{{ 'Watch sales performance across the store.' }}</p>
                </div>
                <div class="inline-flex rounded-2xl bg-slate-100 p-1">
                    <button @click="changeChartPeriod('7days')" :class="chartPeriod === '7days' ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-500'" class="rounded-xl px-3 py-2 text-sm font-medium transition">7 Days</button>
                    <button @click="changeChartPeriod('30days')" :class="chartPeriod === '30days' ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-500'" class="rounded-xl px-3 py-2 text-sm font-medium transition">30 Days</button>
                    <button @click="changeChartPeriod('90days')" :class="chartPeriod === '90days' ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-500'" class="rounded-xl px-3 py-2 text-sm font-medium transition">90 Days</button>
                </div>
            </div>
            <div class="mt-6 h-80">
                <canvas id="salesChart" x-ref="salesChart"></canvas>
            </div>
        </div>

        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="border-b border-slate-200 pb-5">
                <h2 class="text-lg font-semibold text-slate-900">Order status</h2>
                <p class="mt-1 text-sm text-slate-500">{{ 'See how current orders are distributed.' }}</p>
            </div>
            <div class="mt-6 h-80">
                <canvas id="orderStatusChart" x-ref="orderStatusChart"></canvas>
            </div>
        </div>
    </section>

    <section class="grid gap-6 xl:grid-cols-2">
        <div class="rounded-3xl border border-slate-200 bg-white shadow-sm">
            <div class="flex items-center justify-between border-b border-slate-200 px-6 py-5">
                <div>
                    <h2 class="text-lg font-semibold text-slate-900">Recent orders</h2>
                    <p class="mt-1 text-sm text-slate-500">{{ 'Latest order activity across the store.' }}</p>
                </div>
                <a href="{{ route('admin.orders.index') }}" class="text-sm font-semibold text-red-600 transition hover:text-red-700">View all</a>
            </div>
            <div class="divide-y divide-slate-200">
                <template x-if="recentOrders.length === 0">
                    <div class="px-6 py-10 text-center text-sm text-slate-500">No recent orders found.</div>
                </template>
                <template x-for="order in recentOrders" :key="order.id">
                    <div class="flex items-center justify-between gap-4 px-6 py-4">
                        <div class="min-w-0">
                            <a :href="`/admin/orders/${order.id}`" class="text-sm font-semibold text-slate-900 transition hover:text-red-600" x-text="order.order_number"></a>
                            <p class="mt-1 truncate text-sm text-slate-500" x-text="order.customer_name"></p>
                            <p class="mt-1 text-xs text-slate-400" x-text="order.created_at_human"></p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-semibold text-slate-900" x-text="`NGN ${formatCurrency(order.total)}`"></p>
                            <span class="mt-2 inline-flex rounded-full px-2.5 py-1 text-xs font-medium" :class="getStatusBadgeClass(order.status)" x-text="formatStatusLabel(order.status)"></span>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        <div class="rounded-3xl border border-slate-200 bg-white shadow-sm">
            <div class="flex items-center justify-between border-b border-slate-200 px-6 py-5">
                <div>
                    <h2 class="text-lg font-semibold text-slate-900">Top products</h2>
                    <p class="mt-1 text-sm text-slate-500">Best performing products over the last 30 days.</p>
                </div>
                <a href="{{ route('admin.products.index') }}" class="text-sm font-semibold text-red-600 transition hover:text-red-700">View all</a>
            </div>
            <div class="divide-y divide-slate-200">
                <template x-if="topProducts.length === 0">
                    <div class="px-6 py-10 text-center text-sm text-slate-500">No product performance data yet.</div>
                </template>
                <template x-for="product in topProducts" :key="product.id">
                    <div class="flex items-center gap-4 px-6 py-4">
                        <div class="flex h-12 w-12 items-center justify-center overflow-hidden rounded-2xl bg-slate-100">
                            <img :src="product.image_url ? `/storage/${product.image_url}` : 'https://via.placeholder.com/80x80?text=Item'" alt="Product" class="h-full w-full object-cover">
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="truncate text-sm font-semibold text-slate-900" x-text="product.name"></p>
                            <p class="mt-1 text-xs text-slate-500" x-text="`Sold ${product.total_quantity} units`"></p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-semibold text-slate-900" x-text="`NGN ${formatCurrency(product.total_sales)}`"></p>
                            <p class="mt-1 text-xs text-slate-500" x-text="`NGN ${formatCurrency(product.price)} each`"></p>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </section>

    <section class="grid gap-6 xl:grid-cols-[1.05fr_.95fr]">
        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="flex items-center justify-between border-b border-slate-200 pb-4">
                <div>
                    <h2 class="text-lg font-semibold text-slate-900">{{ 'Low stock products' }}</h2>
                    <p class="mt-1 text-sm text-slate-500">{{ 'Products that need restocking soon.' }}</p>
                </div>
                <span class="rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold uppercase tracking-wide text-amber-800">
                    {{ is_iterable($lowStockProducts) ? $lowStockProducts->count() : 0 }} items
                </span>
            </div>

            <div class="mt-5 space-y-3">
                @forelse($lowStockProducts as $product)
                    <div class="rounded-2xl border border-slate-200 px-4 py-4">
                        <div class="flex items-center justify-between gap-3">
                            <div>
                                <p class="text-sm font-semibold text-slate-900">{{ $product->name }}</p>
                                <p class="mt-1 text-xs text-slate-500">{{ $product->category?->name ?? 'Uncategorized' }}</p>
                            </div>
                            <span class="rounded-full {{ $product->quantity <= 3 ? 'bg-red-100 text-red-700' : 'bg-amber-100 text-amber-700' }} px-3 py-1 text-xs font-semibold uppercase tracking-wide">
                                {{ $product->quantity }} left
                            </span>
                        </div>
                    </div>
                @empty
                    <div class="rounded-2xl border border-dashed border-slate-200 px-4 py-8 text-center text-sm text-slate-500">
                        No low-stock products right now.
                    </div>
                @endforelse
            </div>
        </div>


            <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                <div class="flex items-center justify-between border-b border-slate-200 pb-4">
                    <div>
                        <h2 class="text-lg font-semibold text-slate-900">Quick admin actions</h2>
                        <p class="mt-1 text-sm text-slate-500">Jump into common management tasks quickly.</p>
                    </div>
                </div>

                <div class="mt-5 grid gap-3 sm:grid-cols-2">
                    <a href="{{ route('admin.products.create') }}" class="rounded-2xl border border-slate-200 px-4 py-5 text-sm font-semibold text-slate-900 transition hover:border-red-200 hover:bg-red-50">
                        Add product
                        <p class="mt-1 text-xs font-normal text-slate-500">Create a new product listing.</p>
                    </a>
                    <a href="{{ route('admin.orders.index') }}" class="rounded-2xl border border-slate-200 px-4 py-5 text-sm font-semibold text-slate-900 transition hover:border-red-200 hover:bg-red-50">
                        Process orders
                        <p class="mt-1 text-xs font-normal text-slate-500">Review and update incoming orders.</p>
                    </a>
                    <a href="{{ route('admin.users.index') }}" class="rounded-2xl border border-slate-200 px-4 py-5 text-sm font-semibold text-slate-900 transition hover:border-red-200 hover:bg-red-50">
                        Manage customers
                        <p class="mt-1 text-xs font-normal text-slate-500">Open customer accounts.</p>
                    </a>
                    <a href="{{ route('admin.reports.sales') }}" class="rounded-2xl border border-slate-200 px-4 py-5 text-sm font-semibold text-slate-900 transition hover:border-red-200 hover:bg-red-50">
                        View reports
                        <p class="mt-1 text-xs font-normal text-slate-500">See sales and product reports.</p>
                    </a>
                </div>
            </div>

    </section>




</div>

@push('scripts')
<script>
function dashboard() {
    return {
        selectedPeriod: 'today',
        chartPeriod: '30days',
        refreshing: false,
        stats: [],
        recentOrders: [],
        topProducts: [],
        orderStatusSummary: {},
        dashboardData: {},
        salesChart: null,
        orderStatusChart: null,
        refreshIntervalId: null,


        async init() {
            await this.loadData();
            this.initCharts();
            await this.loadChartData();
            this.updateOrderStatusChart();

            if (window.dashboardRefreshInterval) {
                clearInterval(window.dashboardRefreshInterval);
            }

            window.dashboardRefreshInterval = setInterval(() => {
                if (!document.hidden) {
                    this.refreshData(false);
                }
            }, 300000);

            this.refreshIntervalId = window.dashboardRefreshInterval;
        },

        async loadData() {
            const timestamp = Date.now();
            const requests = [
                {
                    key: 'stats',
                    critical: true,
                    url: `/admin/dashboard/stats?period=${this.selectedPeriod}&t=${timestamp}`,
                },
                {
                    key: 'orders',
                    critical: false,
                    url: `/admin/dashboard/recent-orders?t=${timestamp}`,
                },
                {
                    key: 'products',
                    critical: false,
                    url: `/admin/dashboard/top-products?t=${timestamp}`,
                },
                {
                    key: 'status',
                    critical: false,
                    url: `/admin/dashboard/order-status-summary?t=${timestamp}`,
                }
            ];

            const results = await Promise.allSettled(
                requests.map((request) => this.fetchDashboardJson(request.url))
            );

            let criticalFailure = false;

            results.forEach((result, index) => {
                const request = requests[index];

                if (result.status !== 'fulfilled') {
                    console.error(`Dashboard request failed for ${request.key}:`, result.reason);

                    if (request.critical) {
                        criticalFailure = true;
                    }

                    return;
                }

                const payload = result.value;

                if (!payload?.success) {
                    console.error(`Dashboard request returned unsuccessful payload for ${request.key}:`, payload);

                    if (request.critical) {
                        criticalFailure = true;
                    }

                    return;
                }

                if (request.key === 'stats') {
                    this.dashboardData = payload.data;
                    this.stats = this.processStats(payload.data);
                }

                if (request.key === 'orders') {
                    this.recentOrders = payload.data;
                }

                if (request.key === 'products') {
                    this.topProducts = payload.data;
                }

                if (request.key === 'status') {
                    this.orderStatusSummary = payload.data || {};
                    this.updateOrderStatusChart();
                }
            });

            if (criticalFailure) {
                this.notify('Unable to load dashboard data right now.', 'error');
            }
        },

        processStats(data) {
            const periodLabel = this.getPeriodLabel(this.selectedPeriod);



            return [
                {
                    id: 'sales_total',
                    label: 'Sales in Period',
                    value: `NGN ${this.formatCurrency(data.sales_total)}`,
                    change: `${data.orders_count} paid orders in ${periodLabel.toLowerCase()}`,
                    icon: 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
                    iconBg: 'bg-red-100',
                    iconColor: 'text-red-600'
                },
                {
                    id: 'orders_count',
                    label: 'Orders in Period',
                    value: data.orders_count,
                    change: `Average order NGN ${this.formatCurrency(data.avg_order_value)}`,
                    icon: 'M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z',
                    iconBg: 'bg-blue-100',
                    iconColor: 'text-blue-600'
                },
                {
                    id: 'customers',
                    label: 'Customers',
                    value: data.total_customers,
                    change: `+${data.new_customers} new in ${periodLabel.toLowerCase()}`,
                    icon: 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z',
                    iconBg: 'bg-emerald-100',
                    iconColor: 'text-emerald-600'
                },
                {
                    id: 'inventory_value',
                    label: 'Inventory Value',
                    value: `NGN ${this.formatCurrency(data.inventory_value)}`,
                    change: `Store growth ${Number(data.customer_growth || 0).toFixed(1)}%`,
                    icon: 'M20 13V7a2 2 0 00-2-2h-3V4a2 2 0 00-2-2H7a2 2 0 00-2 2v1H2a2 2 0 00-2 2v6m20 0v5a2 2 0 01-2 2H2a2 2 0 01-2-2v-5m20 0H0m8 0v1a2 2 0 104 0v-1',
                    iconBg: 'bg-amber-100',
                    iconColor: 'text-amber-600'
                }
            ];
        },

        getPeriodLabel(period) {
            const labels = {
                today: 'Today',
                week: 'This Week',
                month: 'This Month',
                year: 'This Year'
            };

            return labels[period] || 'This Period';
        },

        async updatePeriod() {
            await this.loadData();
            await this.loadChartData();
        },

        async changeChartPeriod(period) {
            this.chartPeriod = period;
            await this.loadChartData();
        },

        async loadChartData() {
            try {
                const data = await this.fetchDashboardJson(`/admin/dashboard/chart-data?period=${this.chartPeriod}`);

                if (data.success && this.salesChart) {
                    this.salesChart.data.labels = data.data.labels;
                    this.salesChart.data.datasets[0].data = data.data.sales;
                    this.salesChart.data.datasets[1].data = data.data.orders;
                    this.salesChart.update();
                }
            } catch (error) {
                console.error('Error loading chart data:', error);
            }
        },

        async refreshData(showToast = true) {
            this.refreshing = true;

            try {
                await fetch('/admin/dashboard/refresh-cache', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });

                await this.loadData();
                await this.loadChartData();

                if (this.salesChart) {
                    this.salesChart.update();
                }

                if (this.orderStatusChart) {
                    this.orderStatusChart.update();
                }

                if (showToast) {
                    this.notify('Dashboard data refreshed successfully.', 'success');
                }
            } catch (error) {
                console.error('Error refreshing dashboard:', error);
                this.notify('Failed to refresh dashboard data.', 'error');
            } finally {
                this.refreshing = false;
            }
        },

        async fetchDashboardJson(url) {
            const response = await fetch(url, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            if (!response.ok) {
                throw new Error(`Request failed with status ${response.status}`);
            }

            return response.json();
        },

        initCharts() {
            if (this.salesChart) {
                this.salesChart.destroy();
                this.salesChart = null;
            }

            if (this.orderStatusChart) {
                this.orderStatusChart.destroy();
                this.orderStatusChart = null;
            }

            const salesCtx = this.$refs.salesChart.getContext('2d');
            this.salesChart = new Chart(salesCtx, {
                type: 'line',
                data: {
                    labels: [],
                    datasets: [
                        {
                            label: 'Sales (NGN)',
                            data: [],
                            borderColor: 'rgb(220, 38, 38)',
                            backgroundColor: 'rgba(220, 38, 38, 0.12)',
                            tension: 0.35,
                            fill: true,
                            borderWidth: 2
                        },
                        {
                            label: 'Orders',
                            data: [],
                            borderColor: 'rgb(15, 23, 42)',
                            backgroundColor: 'rgba(15, 23, 42, 0.08)',
                            tension: 0.35,
                            fill: true,
                            borderWidth: 2
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: {
                        intersect: false,
                        mode: 'index'
                    },
                    plugins: {
                        legend: {
                            position: 'top',
                            labels: {
                                usePointStyle: true,
                                boxWidth: 8,
                                color: '#334155'
                            }
                        }
                    },
                    scales: {
                        x: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                color: '#64748b'
                            }
                        },
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: 'rgba(148, 163, 184, 0.18)'
                            },
                            ticks: {
                                color: '#64748b'
                            }
                        }
                    }
                }
            });

            const statusCtx = this.$refs.orderStatusChart.getContext('2d');
            this.orderStatusChart = new Chart(statusCtx, {
                type: 'doughnut',
                data: {
                    labels: [],
                    datasets: [{
                        data: [],
                        backgroundColor: [
                            '#dc2626',
                            '#2563eb',
                            '#059669',
                            '#d97706',
                            '#7c3aed',
                            '#475569',
                            '#0f766e',
                            '#be123c'
                        ],
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '68%',
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                usePointStyle: true,
                                boxWidth: 8,
                                padding: 16,
                                color: '#334155'
                            }
                        }
                    }
                }
            });

            this.updateOrderStatusChart();
        },

        updateOrderStatusChart() {
            if (!this.orderStatusChart) {
                return;
            }

            const entries = Array.isArray(this.orderStatusSummary)
                ? this.orderStatusSummary.map(item => [item.label || item.status || 'Unknown', item.count || 0])
                : Object.entries(this.orderStatusSummary || {});

            this.orderStatusChart.data.labels = entries.map(([label]) => this.formatStatusLabel(label));
            this.orderStatusChart.data.datasets[0].data = entries.map(([, count]) => count || 0);
            this.orderStatusChart.update();
        },

        formatCurrency(value) {
            const number = Number(value || 0);
            return number.toLocaleString('en-US', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
        },

        formatStatusLabel(value) {
            return String(value || '')
                .replace(/_/g, ' ')
                .replace(/\b\w/g, function(char) {
                    return char.toUpperCase();
                });
        },

        getStatusBadgeClass(status) {
            const classes = {
                pending: 'bg-amber-100 text-amber-800',
                processing: 'bg-blue-100 text-blue-800',
                completed: 'bg-emerald-100 text-emerald-800',
                shipped: 'bg-violet-100 text-violet-800',
                delivered: 'bg-indigo-100 text-indigo-800',
                cancelled: 'bg-slate-100 text-slate-800'
            };

            return classes[status] || 'bg-slate-100 text-slate-800';
        },

        notify(message, type = 'info') {
            if (window.KeffiCart?.notify) {
                window.KeffiCart.notify(message, type);
                return;
            }

            if (window.AppNotify) {
                window.AppNotify(message, type);
            }
        }
    };
}
</script>
@endpush
@endsection
