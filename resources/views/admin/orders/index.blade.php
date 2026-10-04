@extends('layouts.admin')

@section('title', 'Orders Management')
@section('breadcrumb', 'Orders')

@section('content')
<div class="space-y-6">
    <section class="overflow-hidden rounded-[2rem] border border-slate-200 bg-gradient-to-br from-white via-slate-50 to-red-50 p-6 shadow-sm">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <div class="inline-flex items-center gap-2 rounded-full border border-red-200 bg-white px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.24em] text-red-700">
                    <span class="inline-block h-2 w-2 rounded-full bg-red-500"></span>
                    {{ 'Order Operations' }}
                </div>
                <h1 class="mt-4 text-3xl font-semibold tracking-tight text-slate-900">
                    {{ 'Manage customer orders from one workspace' }}
                </h1>
                <p class="mt-2 max-w-3xl text-sm leading-6 text-slate-600">
                    {{ 'Filter, review, update, and manage order activity across the marketplace with a cleaner workflow.' }}
                </p>
            </div>

            <div class="flex flex-col gap-3 sm:flex-row">
                <a href="{{ route(auth()->user()->dashboardRouteName()) }}"
                   class="inline-flex items-center justify-center rounded-2xl border border-slate-300 bg-white px-5 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">
                    <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Back to Dashboard
                </a>

                    <a href="{{ route('admin.reports.sales') }}"
                       class="inline-flex items-center justify-center rounded-2xl bg-slate-900 px-5 py-3 text-sm font-semibold text-white transition hover:bg-slate-800">
                        View Sales Report
                    </a>

            </div>
        </div>
    </section>

    <div class="rounded-3xl border border-slate-200 bg-white p-4 shadow-sm sm:p-6">
        @livewire('admin.orders.index')
    </div>
</div>
@endsection
