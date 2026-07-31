<div>
    <!-- Verification Status Notification -->
    @if(auth()->user()->role === 'vendor' && auth()->user()->verification_status === 'approved' && auth()->user()->bank_verification_status === 'verified')
        <div class="mb-8 bg-gradient-to-r from-green-50 to-blue-50 border border-green-200 rounded-lg p-4 shadow-sm">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div class="ml-3 flex-1">
                    <h3 class="text-sm font-medium text-green-900">Account Verified</h3>
                    <p class="mt-1 text-sm text-green-700">
                        Your account is fully verified! Order payouts will be credited to your bank account within <strong>24 hours</strong> after order completion, <strong>excluding weekends and public holidays</strong>.
                    </p>
                    <p class="mt-2 text-xs text-green-600 font-medium">
                        📅 Processing Schedule: Business days only (Monday - Friday)
                    </p>
                </div>
            </div>
        </div>
    @elseif(auth()->user()->role === 'vendor' && (auth()->user()->verification_status !== 'approved' || auth()->user()->bank_verification_status !== 'verified'))
        <div class="mb-8 bg-gradient-to-r from-yellow-50 to-orange-50 border border-yellow-200 rounded-lg p-4 shadow-sm">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4v.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div class="ml-3 flex-1">
                    <h3 class="text-sm font-medium text-yellow-900">Verification Pending</h3>
                    <p class="mt-1 text-sm text-yellow-700">
                        @if(auth()->user()->verification_status !== 'approved' && auth()->user()->bank_verification_status !== 'verified')
                            Your identity and bank verification are pending admin review. Once approved, order payouts will be credited within 24 hours (excluding weekends).
                        @elseif(auth()->user()->verification_status !== 'approved')
                            Your identity verification is pending admin review. Bank verification is required before receiving payouts.
                        @else
                            Your bank verification is pending admin review. Complete this to start receiving order payouts.
                        @endif
                    </p>
                    <p class="mt-2 text-xs text-yellow-600 font-medium">
                        ⏱️ Status updates typically sent within 24-48 hours
                    </p>
                </div>
            </div>
        </div>
    @endif

    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Dashboard</h1>
        <p class="mt-2 text-gray-600">Welcome back, {{ auth()->user()->name }}! Here's what's happening with your store.</p>
    </div>

    <!-- Period Selector -->
    <div class="mb-6">
        <div class="flex space-x-2">
            <button wire:click="updatePeriod('today')" 
                    class="px-4 py-2 text-sm rounded-md {{ $selectedPeriod === 'today' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                Today
            </button>
            <button wire:click="updatePeriod('week')" 
                    class="px-4 py-2 text-sm rounded-md {{ $selectedPeriod === 'week' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                This Week
            </button>
            <button wire:click="updatePeriod('month')" 
                    class="px-4 py-2 text-sm rounded-md {{ $selectedPeriod === 'month' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                This Month
            </button>
            <button wire:click="updatePeriod('year')" 
                    class="px-4 py-2 text-sm rounded-md {{ $selectedPeriod === 'year' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                This Year
            </button>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Total Orders -->
        <div class="bg-white rounded-lg shadow p-6 border border-gray-200">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="p-3 bg-blue-100 rounded-lg">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Total Orders</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['total_orders'] ?? 0) }}</p>
                    <p class="text-xs text-gray-500 mt-1">
                        {{ $selectedPeriod === 'today' ? 'Today: ' . ($stats['today_orders'] ?? 0) : 
                           $selectedPeriod === 'week' ? 'This Week: ' . ($stats['period_orders'] ?? 0) :
                           $selectedPeriod === 'month' ? 'This Month: ' . ($stats['period_orders'] ?? 0) :
                           'This Year: ' . ($stats['period_orders'] ?? 0) }}
                    </p>
                </div>
            </div>
        </div>

        <!-- Total Sales -->
        <div class="bg-white rounded-lg shadow p-6 border border-gray-200">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="p-3 bg-green-100 rounded-lg">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Total Sales</p>
                    <p class="text-2xl font-bold text-gray-900">₦{{ number_format($stats['total_sales'] ?? 0, 2) }}</p>
                    <p class="text-xs text-gray-500 mt-1">
                        {{ $selectedPeriod === 'today' ? 'Today: ₦' . number_format($stats['today_sales'] ?? 0, 2) : 
                           $selectedPeriod === 'week' ? 'This Week: ₦' . number_format($stats['period_sales'] ?? 0, 2) :
                           $selectedPeriod === 'month' ? 'This Month: ₦' . number_format($stats['period_sales'] ?? 0, 2) :
                           'This Year: ₦' . number_format($stats['period_sales'] ?? 0, 2) }}
                    </p>
                </div>
            </div>
        </div>

        <!-- Total Products -->
        <div class="bg-white rounded-lg shadow p-6 border border-gray-200">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="p-3 bg-purple-100 rounded-lg">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Total Products</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['total_products'] ?? 0) }}</p>
                    <p class="text-xs text-gray-500 mt-1">
                        <span class="{{ $stats['low_stock'] > 0 ? 'text-yellow-600' : 'text-gray-500' }}">
                            {{ $stats['low_stock'] ?? 0 }} low stock
                        </span>
                        <span class="mx-2">•</span>
                        <span class="{{ $stats['out_of_stock'] > 0 ? 'text-red-600' : 'text-gray-500' }}">
                            {{ $stats['out_of_stock'] ?? 0 }} out of stock
                        </span>
                    </p>
                </div>
            </div>
        </div>

        <!-- Total Customers -->
        <div class="bg-white rounded-lg shadow p-6 border border-gray-200">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="p-3 bg-yellow-100 rounded-lg">
                        <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Total Customers</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['total_customers'] ?? 0) }}</p>
                    <p class="text-xs text-gray-500 mt-1">{{ $recentCustomers->count() }} new this month</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Second Row Stats -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Pending Orders -->
        <div class="bg-white rounded-lg shadow p-6 border border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Pending Orders</p>
                    <p class="text-2xl font-bold text-yellow-600">{{ number_format($stats['pending_orders'] ?? 0) }}</p>
                </div>
                <div class="p-3 bg-yellow-100 rounded-lg">
                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Processing Orders -->
        <div class="bg-white rounded-lg shadow p-6 border border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Processing Orders</p>
                    <p class="text-2xl font-bold text-blue-600">{{ number_format($stats['processing_orders'] ?? 0) }}</p>
                </div>
                <div class="p-3 bg-blue-100 rounded-lg">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Featured Products -->
        <div class="bg-white rounded-lg shadow p-6 border border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Featured Products</p>
                    <p class="text-2xl font-bold text-purple-600">{{ number_format($stats['featured_products'] ?? 0) }}</p>
                </div>
                <div class="p-3 bg-purple-100 rounded-lg">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Categories -->
        <div class="bg-white rounded-lg shadow p-6 border border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Categories</p>
                    <p class="text-2xl font-bold text-green-600">{{ number_format($stats['total_categories'] ?? 0) }}</p>
                </div>
                <div class="p-3 bg-green-100 rounded-lg">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts and Recent Data -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
        <!-- Sales Chart -->
        <div class="bg-white rounded-lg shadow p-6 border border-gray-200">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-lg font-semibold text-gray-900">Sales Overview</h3>
                <div class="flex space-x-2">
                    <button wire:click="updateChartPeriod('7days')" 
                            class="px-3 py-1 text-xs rounded-md {{ $chartPeriod === '7days' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                        7 Days
                    </button>
                    <button wire:click="updateChartPeriod('30days')" 
                            class="px-3 py-1 text-xs rounded-md {{ $chartPeriod === '30days' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                        30 Days
                    </button>
                    <button wire:click="updateChartPeriod('90days')" 
                            class="px-3 py-1 text-xs rounded-md {{ $chartPeriod === '90days' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                        90 Days
                    </button>
                </div>
            </div>
            <div class="h-64">
                <canvas id="salesChart" class="w-full h-full"></canvas>
            </div>
        </div>

        <!-- Recent Orders -->
        <div class="bg-white rounded-lg shadow p-6 border border-gray-200">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-lg font-semibold text-gray-900">Recent Orders</h3>
                <a href="{{ route('admin.orders.index') }}" class="text-sm text-blue-600 hover:text-blue-800">
                    View All
                </a>
            </div>
            <div class="space-y-4">
                @forelse($recentOrders as $order)
                    <div class="flex items-center justify-between p-3 hover:bg-gray-50 rounded-lg transition-colors duration-150">
                        <div class="flex items-center space-x-3">
                            <div class="p-2 bg-gray-100 rounded">
                                <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                                </svg>
                            </div>
                            <div>
                                <p class="font-medium text-gray-900">{{ $order->order_number }}</p>
                                <p class="text-sm text-gray-500">
                                    {{ $order->user->name ?? 'Guest' }}
                                    <span class="mx-1">•</span>
                                    {{ $order->created_at->diffForHumans() }}
                                </p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="font-medium text-gray-900">₦{{ number_format($order->total, 2) }}</p>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $order->status === 'completed' ? 'bg-green-100 text-green-800' : ($order->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-blue-100 text-blue-800') }}">
                                {{ ucfirst($order->status) }}
                            </span>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-4 text-gray-500">
                        No orders yet
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Bottom Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Top Selling Products -->
        <div class="bg-white rounded-lg shadow p-6 border border-gray-200">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-lg font-semibold text-gray-900">Top Selling Products</h3>
                <a href="{{ route('admin.products.index') }}" class="text-sm text-blue-600 hover:text-blue-800">
                    View All
                </a>
            </div>
            <div class="space-y-4">
                @forelse($topProducts as $product)
                    <div class="flex items-center justify-between p-3 hover:bg-gray-50 rounded-lg transition-colors duration-150">
                        <div class="flex items-center space-x-3">
                            @if($product->images && count($product->images) > 0)
                                @php $image = $product->images[0]; @endphp
                                @if(str_starts_with($image, 'http'))
                                    <img src="{{ $image }}" alt="{{ $product->name }}" 
                                         class="w-10 h-10 object-cover rounded">
                                @else
                                    <img src="{{ Storage::url($image) }}" alt="{{ $product->name }}" 
                                         class="w-10 h-10 object-cover rounded">
                                @endif
                            @else
                                <div class="w-10 h-10 bg-gray-200 rounded flex items-center justify-center">
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </div>
                            @endif
                            <div>
                                <p class="font-medium text-gray-900 truncate max-w-xs">{{ $product->name }}</p>
                                <p class="text-sm text-gray-500">Sold: {{ $product->total_quantity }}</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="font-medium text-gray-900">₦{{ number_format($product->total_sales, 2) }}</p>
                            <p class="text-sm text-gray-500">₦{{ number_format($product->price, 2) }} each</p>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-4 text-gray-500">
                        No products sold yet
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Low Stock Products -->
        <div class="bg-white rounded-lg shadow p-6 border border-gray-200">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-lg font-semibold text-gray-900">Low Stock Products</h3>
                <a href="{{ route('admin.products.index') }}?stock=low" class="text-sm text-blue-600 hover:text-blue-800">
                    View All
                </a>
            </div>
            <div class="space-y-4">
                @forelse($lowStockProducts as $product)
                    <div class="flex items-center justify-between p-3 hover:bg-gray-50 rounded-lg transition-colors duration-150">
                        <div class="flex items-center space-x-3">
                            @if($product->images && count($product->images) > 0)
                                @php $image = $product->images[0]; @endphp
                                @if(str_starts_with($image, 'http'))
                                    <img src="{{ $image }}" alt="{{ $product->name }}" 
                                         class="w-10 h-10 object-cover rounded">
                                @else
                                    <img src="{{ Storage::url($image) }}" alt="{{ $product->name }}" 
                                         class="w-10 h-10 object-cover rounded">
                                @endif
                            @else
                                <div class="w-10 h-10 bg-gray-200 rounded flex items-center justify-center">
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </div>
                            @endif
                            <div>
                                <p class="font-medium text-gray-900 truncate max-w-xs">{{ $product->name }}</p>
                                <p class="text-sm {{ $product->quantity <= 5 ? 'text-red-600' : 'text-yellow-600' }}">
                                    Only {{ $product->quantity }} left
                                </p>
                            </div>
                        </div>
                        <a href="{{ route('admin.products.edit', $product) }}" 
                           class="px-3 py-1 text-sm bg-blue-600 text-white rounded hover:bg-blue-700">
                            Restock
                        </a>
                    </div>
                @empty
                    <div class="text-center py-4 text-gray-500">
                        No low stock products
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="mt-8 bg-white rounded-lg shadow p-6 border border-gray-200">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Quick Actions</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <a href="{{ route('admin.products.create') }}" 
               class="flex items-center justify-center p-4 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors duration-150">
                <div class="text-center">
                    <svg class="w-8 h-8 text-blue-600 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    <p class="font-medium text-gray-900">Add Product</p>
                    <p class="text-sm text-gray-500">Create new product listing</p>
                </div>
            </a>
            <a href="{{ route('admin.orders.index') }}" 
               class="flex items-center justify-center p-4 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors duration-150">
                <div class="text-center">
                    <svg class="w-8 h-8 text-green-600 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <p class="font-medium text-gray-900">Manage Orders</p>
                    <p class="text-sm text-gray-500">View and process orders</p>
                </div>
            </a>
            <a href="{{ route('admin.settings.index') }}" 
               class="flex items-center justify-center p-4 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors duration-150">
                <div class="text-center">
                    <svg class="w-8 h-8 text-purple-600 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    <p class="font-medium text-gray-900">Store Settings</p>
                    <p class="text-sm text-gray-500">Configure store options</p>
                </div>
            </a>
        </div>
    </div>

    @script
    <script>
        let salesChart = null;
        
        // Initialize chart
        function initChart() {
            const ctx = document.getElementById('salesChart').getContext('2d');
            
            if (salesChart) {
                salesChart.destroy();
            }
            
            salesChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: @json($chartLabels),
                    datasets: [{
                        label: 'Sales (₦)',
                        data: @json($salesData),
                        borderColor: 'rgb(59, 130, 246)',
                        backgroundColor: 'rgba(59, 130, 246, 0.1)',
                        tension: 0.4,
                        fill: true
                    }, {
                        label: 'Orders',
                        data: @json($ordersData),
                        borderColor: 'rgb(16, 185, 129)',
                        backgroundColor: 'rgba(16, 185, 129, 0.1)',
                        tension: 0.4,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top',
                        },
                        tooltip: {
                            mode: 'index',
                            intersect: false,
                            callbacks: {
                                label: function(context) {
                                    let label = context.dataset.label || '';
                                    if (label) {
                                        label += ': ';
                                    }
                                    if (context.datasetIndex === 0) {
                                        label += '₦' + context.parsed.y.toLocaleString();
                                    } else {
                                        label += context.parsed.y + ' orders';
                                    }
                                    return label;
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return '₦' + value.toLocaleString();
                                }
                            }
                        }
                    },
                    interaction: {
                        intersect: false,
                        mode: 'nearest'
                    }
                }
            });
        }
        
        // Listen for Livewire initialization
        document.addEventListener('livewire:init', () => {
            initChart();
            
            // Listen for chart updates from Livewire
            Livewire.on('updateChart', (data) => {
                if (salesChart) {
                    salesChart.data.labels = data.labels;
                    salesChart.data.datasets[0].data = data.sales;
                    salesChart.data.datasets[1].data = data.orders;
                    salesChart.update();
                }
            });
        });
        
        // Reinitialize chart when component updates
        document.addEventListener('livewire:update', () => {
            // Add a small delay to ensure DOM is ready
            setTimeout(initChart, 100);
        });
    </script>
    @endscript
</div>