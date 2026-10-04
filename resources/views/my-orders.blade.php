@extends('layouts.app')

@section('title', 'My Orders')

@section('header')
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        {{ __('My Orders') }}
    </h2>
@endsection

@section('content')
<x-storefront.page-hero eyebrow="Your purchases" title="Order history." description="Every purchase, payment and delivery update—organized in one place." />
<div class="bg-[#f5f3ee] py-8 sm:py-12">
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="overflow-hidden rounded-[2rem] border border-slate-200 bg-white shadow-sm">

        <div class="p-6">
            @if(session('success'))
                <div class="mb-6 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="mb-6 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                    {{ session('error') }}
                </div>
            @endif

            @if($orders->count() > 0)
                <div class="space-y-6">
                    @foreach($orders as $order)
                        <div class="rounded-2xl border border-slate-200 p-5 transition-all duration-300 hover:-translate-y-0.5 hover:border-red-200 hover:shadow-xl hover:shadow-slate-900/5 sm:p-6">
                            <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                                <div class="flex-1">
                                    <div class="flex items-center space-x-4">
                                        <div class="flex-shrink-0">
                                            <div class="w-12 h-12 bg-red-50 rounded-lg flex items-center justify-center">
                                                <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                                </svg>
                                            </div>
                                        </div>
                                        <div>
                                            <h3 class="text-lg font-semibold text-gray-900">
                                                Order #{{ $order->order_number }}
                                            </h3>
                                            <p class="text-sm text-gray-600">
                                                Placed on {{ $order->created_at->format('M d, Y') }}
                                            </p>
                                        </div>
                                    </div>

                                    <div class="mt-4 grid grid-cols-1 md:grid-cols-4 gap-4">
                                        <div>
                                            <span class="text-sm font-medium text-gray-500">Status</span>
                                            <div class="mt-1">
                                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium
                                                    @if(in_array($order->status, ['pending', 'ordered'])) bg-yellow-100 text-yellow-800
                                                    @elseif(in_array($order->status, ['confirmed', 'processing'])) bg-blue-100 text-blue-800
                                                    @elseif(in_array($order->status, ['picked_up', 'shipped', 'on_the_way'])) bg-indigo-100 text-indigo-800
                                                    @elseif(in_array($order->status, ['delivered', 'completed'])) bg-green-100 text-green-800
                                                    @elseif(in_array($order->status, ['cancelled', 'failed'])) bg-red-100 text-red-800
                                                    @else bg-gray-100 text-gray-800 @endif">
                                                    {{ ucfirst(str_replace('_', ' ', $order->status)) }}
                                                </span>
                                            </div>
                                        </div>

                                        <div>
                                            <span class="text-sm font-medium text-gray-500">Payment</span>
                                            <div class="mt-1">
                                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium
                                                    @if($order->payment_status === 'paid') bg-green-100 text-green-800
                                                    @elseif($order->payment_status === 'pending') bg-yellow-100 text-yellow-800
                                                    @else bg-red-100 text-red-800 @endif">
                                                    {{ ucfirst($order->payment_status) }}
                                                </span>
                                            </div>
                                        </div>

                                        <div>
                                            <span class="text-sm font-medium text-gray-500">Items</span>
                                            <div class="mt-1">
                                                <span class="text-sm font-semibold text-gray-900">
                                                    {{ $order->items()->count() }} item(s)
                                                </span>
                                            </div>
                                        </div>

                                        <div>
                                            <span class="text-sm font-medium text-gray-500">Total</span>
                                            <div class="mt-1">
                                                <span class="text-lg font-bold text-gray-900">
                                                    {{ \App\Helpers\SettingsHelper::currency($order->total) }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-4 md:mt-0 md:ml-6 flex space-x-3">
                                    <a href="{{ route('my.orders.show', $order) }}"
                                       class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors duration-200">
                                        <svg class="-ml-1 mr-2 h-4 w-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                        View Details
                                    </a>

                                    @if($order->canBeCancelledByCustomer(auth()->user()))
                                        <form method="POST" action="{{ route('my.orders.cancel', $order) }}">
                                            @csrf
                                            <button type="submit"
                                                    onclick="return confirm('Cancel this order? This action cannot be undone from your account.')"
                                                    class="inline-flex items-center px-4 py-2 border border-red-200 rounded-md shadow-sm text-sm font-medium text-red-700 bg-red-50 hover:bg-red-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors duration-200">
                                                Cancel Order
                                            </button>
                                        </form>
                                    @endif
                                    
                                    @if($order->status === 'delivered' || $order->status === 'completed')
                                    <a href="{{ route('my.orders.show', $order) }}"
                                       class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors duration-200">
                                        Reorder
                                    </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-8">
                    {{ $orders->links() }}
                </div>
            @else
                <div class="text-center py-12">
                    <svg class="mx-auto h-16 w-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                    </svg>
                    <h3 class="mt-4 text-lg font-medium text-gray-900">No orders yet</h3>
                    <p class="mt-2 text-sm text-gray-600">
                        You haven't placed any orders yet. Start shopping to see your orders here.
                    </p>
                    <div class="mt-6">
                        <a href="{{ route('shop') }}" class="btn-primary inline-flex items-center">
                            <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                            </svg>
                            Start Shopping
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
</div>

@push('styles')
<style>
    .btn-primary {
        background-color: #dc2626;
        color: white;
        padding: 0.75rem 1.5rem;
        border-radius: 0.375rem;
        font-weight: 500;
        transition: background-color 0.2s ease-in-out;
    }
    
    .btn-primary:hover {
        background-color: #b91c1c;
    }
    
    /* Pagination styling */
    .pagination {
        display: flex;
        justify-content: center;
        list-style: none;
        padding: 0;
    }
    
    .page-item {
        margin: 0 0.25rem;
    }
    
    .page-link {
        display: inline-block;
        padding: 0.5rem 0.75rem;
        border: 1px solid #d1d5db;
        border-radius: 0.375rem;
        color: #374151;
        font-size: 0.875rem;
        font-weight: 500;
        text-decoration: none;
        transition: all 0.2s ease;
    }
    
    .page-link:hover {
        background-color: #f9fafb;
        border-color: #dc2626;
        color: #dc2626;
    }
    
    .page-item.active .page-link {
        background-color: #dc2626;
        border-color: #dc2626;
        color: white;
    }
    
    .page-item.disabled .page-link {
        color: #9ca3af;
        cursor: not-allowed;
        background-color: #f9fafb;
    }
</style>
@endpush
@endsection
