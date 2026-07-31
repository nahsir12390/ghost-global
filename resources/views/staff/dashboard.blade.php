@extends('layouts.admin')

@section('title', 'Staff Dashboard')

@section('breadcrumb')
    <span class="text-gray-700">Staff Dashboard</span>
@endsection

@section('content')
<div class="space-y-6">
    <!-- Welcome Banner -->
    <div class="rounded-2xl bg-gradient-to-r from-red-600 to-red-700 p-8 text-white shadow-lg">
        <h1 class="text-3xl font-bold mb-2">Welcome, {{ Auth::user()->name }}!</h1>
        <p class="text-red-100">
            @if(Auth::user()->isOrderManager())
                You have access to manage orders
            @elseif(Auth::user()->isProductManager())
                You have access to manage products
            @else
                You have full access to manage orders and products
            @endif
        </p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Your Permissions -->
        <div class="card-modern lg:col-span-3">
            <div class="p-6">
                <h2 class="text-lg font-bold text-gray-900 mb-4">Your Permissions</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @if(Auth::user()->canManageOrders())
                        <div class="rounded-xl bg-blue-50 border border-blue-200 p-4">
                            <div class="flex items-start space-x-3">
                                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="font-semibold text-gray-900">Order Management</h3>
                                    <p class="text-sm text-gray-600 mt-1">View and manage customer orders</p>
                                    <a href="{{ route('admin.staff.orders.index') }}" class="text-blue-600 hover:text-blue-700 font-medium text-sm mt-2 inline-block">
                                        Go to Orders →
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endif

                    @if(Auth::user()->canManageProducts())
                        <div class="rounded-xl bg-amber-50 border border-amber-200 p-4">
                            <div class="flex items-start space-x-3">
                                <div class="w-10 h-10 bg-amber-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                    <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="font-semibold text-gray-900">Product Management</h3>
                                    <p class="text-sm text-gray-600 mt-1">Create, edit, and manage products</p>
                                    <a href="{{ route('admin.staff.products.index') }}" class="text-amber-600 hover:text-amber-700 font-medium text-sm mt-2 inline-block">
                                        Go to Products →
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Quick Stats -->
        @if(Auth::user()->canManageOrders())
            <div class="card-modern">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="font-semibold text-gray-900">Recent Orders</h3>
                        <a href="{{ route('admin.staff.orders.index') }}" class="text-red-600 hover:text-red-700 text-sm font-medium">
                            View all
                        </a>
                    </div>
                    @php
                        $recentOrders = \App\Models\Order::latest()->take(5)->get();
                    @endphp
                    @if($recentOrders->count() > 0)
                        <div class="space-y-3">
                            @foreach($recentOrders as $order)
                                <div class="flex items-center justify-between p-2 hover:bg-gray-50 rounded-lg transition-colors">
                                    <div class="text-sm">
                                        <p class="font-medium text-gray-900">Order #{{ $order->id }}</p>
                                        <p class="text-gray-500 text-xs">{{ $order->created_at->format('M d, Y') }}</p>
                                    </div>
                                    <span class="text-sm font-semibold text-gray-900">₦{{ number_format($order->total, 2) }}</span>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-500 text-sm text-center py-6">No orders yet</p>
                    @endif
                </div>
            </div>
        @endif

        @if(Auth::user()->canManageProducts())
            <div class="card-modern">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="font-semibold text-gray-900">My Products</h3>
                        <a href="{{ route('admin.staff.products.index') }}" class="text-red-600 hover:text-red-700 text-sm font-medium">
                            View all
                        </a>
                    </div>
                    @php
                        $productCount = \App\Models\Product::count();
                    @endphp
                    <div class="text-center py-6">
                        <p class="text-3xl font-bold text-gray-900">{{ $productCount }}</p>
                        <p class="text-gray-500 text-sm mt-1">Total Products</p>
                    </div>
                </div>
            </div>
        @endif

        <!-- Staff Info -->
        <div class="card-modern">
            <div class="p-6">
                <h3 class="font-semibold text-gray-900 mb-4">Your Information</h3>
                <div class="space-y-3">
                    <div>
                        <p class="text-xs text-gray-600 uppercase tracking-wide">Name</p>
                        <p class="font-medium text-gray-900">{{ Auth::user()->name }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-600 uppercase tracking-wide">Email</p>
                        <p class="font-medium text-gray-900">{{ Auth::user()->email }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-600 uppercase tracking-wide">Role</p>
                        <p class="font-medium text-gray-900">
                            @if(Auth::user()->staff_role === 'order_manager')
                                📋 Order Manager
                            @elseif(Auth::user()->staff_role === 'product_manager')
                                📦 Product Manager
                            @else
                                🔑 All (Orders & Products)
                            @endif
                        </p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-600 uppercase tracking-wide">Assigned Since</p>
                        <p class="font-medium text-gray-900">{{ Auth::user()->staff_assigned_at->format('M d, Y') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
