<div>
    <!-- Filter -->
    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Filter Reports</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label for="timePeriod" class="block text-sm font-medium text-gray-700 mb-1">Time Period</label>
                <select id="timePeriod" 
                        wire:model.change="timePeriod"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-red-500 focus:border-red-500">
                    <option value="last_30_days">Last 30 Days</option>
                    <option value="last_90_days">Last 90 Days</option>
                    <option value="this_year">This Year</option>
                    <option value="last_year">Last Year</option>
                    <option value="all_time">All Time</option>
                </select>
            </div>
            <div>
                <label for="categoryId" class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                <select id="categoryId" 
                        wire:model.change="categoryId"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-red-500 focus:border-red-500">
                    <option value="">All Categories</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <div class="text-sm text-gray-500 mb-1">Date Range</div>
                <div class="text-sm font-medium text-gray-900">
                    {{ \Carbon\Carbon::parse($startDate)->format('M d, Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('M d, Y') }}
                </div>
            </div>
        </div>
    </div>

    <!-- Products Overview Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-red-100 p-3 rounded-lg">
                    <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Total Products</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['total_products'] }}</p>
                </div>
            </div>
            <div class="mt-4">
                <div class="flex items-center text-sm">
                    @if($stats['product_growth'] >= 0)
                        <svg class="h-4 w-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18" />
                        </svg>
                        <span class="text-green-600 font-medium ml-1">+{{ $stats['product_growth'] }}%</span>
                    @else
                        <svg class="h-4 w-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3" />
                        </svg>
                        <span class="text-red-600 font-medium ml-1">{{ $stats['product_growth'] }}%</span>
                    @endif
                    <span class="text-gray-500 ml-2">growth this month</span>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-green-100 p-3 rounded-lg">
                    <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Best Selling</p>
                    <p class="text-2xl font-bold text-gray-900 truncate">{{ Str::limit($stats['best_selling_product'], 15) }}</p>
                </div>
            </div>
            <div class="mt-4">
                <div class="text-sm text-gray-500">{{ $stats['best_selling_count'] }} units sold</div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-blue-100 p-3 rounded-lg">
                    <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Low Stock</p>
                    <p class="text-2xl font-bold text-yellow-600">{{ $stats['low_stock_count'] }}</p>
                </div>
            </div>
            <div class="mt-4">
                <div class="text-sm text-gray-500">{{ $stats['low_stock_percentage'] }}% of products</div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-purple-100 p-3 rounded-lg">
                    <svg class="h-6 w-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Out of Stock</p>
                    <p class="text-2xl font-bold text-red-600">{{ $stats['out_of_stock_count'] }}</p>
                </div>
            </div>
            <div class="mt-4">
                <div class="text-sm text-gray-500">{{ $stats['out_of_stock_percentage'] }}% of products</div>
            </div>
        </div>
    </div>

    <!-- Top Selling Products and Stock Status -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-medium text-gray-900">Top Selling Products</h3>
                    <span class="text-sm text-gray-500">Last {{ $timePeriod === 'all_time' ? 'all time' : $timePeriod }}</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead>
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sold</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Revenue</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stock</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse($topProducts as $product)
                                <tr>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <div class="flex items-center">
                                            @if($product->main_image)
                                                <img src="{{ Storage::url($product->main_image) }}" 
                                                     alt="{{ $product->name }}"
                                                     class="h-10 w-10 rounded-lg object-cover">
                                            @endif
                                            <div class="ml-3">
                                                <div class="text-sm font-medium text-gray-900 truncate max-w-xs">
                                                    <a href="{{ route('admin.products.show', $product) }}" 
                                                       class="text-gray-900 hover:text-red-600">
                                                        {{ $product->name }}
                                                    </a>
                                                </div>
                                                <div class="text-xs text-gray-500">SKU: {{ $product->sku }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">
                                        {{ $product->category->name ?? 'N/A' }}
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900">
                                        {{ $product->total_sold }}
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm font-bold text-gray-900">
                                        ₦{{ number_format($product->revenue, 2) }}
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm">
                                        @if($product->stock_quantity <= 0)
                                            <span class="text-red-600 font-medium">Out of Stock</span>
                                        @elseif($product->stock_quantity <= 10)
                                            <span class="text-yellow-600 font-medium">Low Stock</span>
                                        @else
                                            <span class="text-green-600 font-medium">In Stock</span>
                                        @endif
                                        <div class="text-xs text-gray-500">{{ $product->stock_quantity }} units</div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-4 py-8 text-center">
                                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                                        </svg>
                                        <p class="mt-2 text-sm text-gray-500">No product sales data available</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Stock Status Overview -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-6">Stock Status Overview</h3>
            <div class="space-y-6">
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm font-medium text-gray-700">In Stock</span>
                        <span class="text-sm font-bold text-gray-900">{{ $stats['in_stock_count'] }}</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-green-600 h-2 rounded-full" style="width: {{ $stats['in_stock_percentage'] }}%"></div>
                    </div>
                    <div class="text-xs text-gray-500 mt-1">{{ $stats['in_stock_percentage'] }}% of total products</div>
                </div>
                
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm font-medium text-gray-700">Low Stock</span>
                        <span class="text-sm font-bold text-gray-900">{{ $stats['low_stock_count'] }}</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-yellow-600 h-2 rounded-full" style="width: {{ $stats['low_stock_percentage'] }}%"></div>
                    </div>
                    <div class="text-xs text-gray-500 mt-1">{{ $stats['low_stock_percentage'] }}% of total products</div>
                </div>
                
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm font-medium text-gray-700">Out of Stock</span>
                        <span class="text-sm font-bold text-gray-900">{{ $stats['out_of_stock_count'] }}</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-red-600 h-2 rounded-full" style="width: {{ $stats['out_of_stock_percentage'] }}%"></div>
                    </div>
                    <div class="text-xs text-gray-500 mt-1">{{ $stats['out_of_stock_percentage'] }}% of total products</div>
                </div>
            </div>
            
            <!-- Restock Suggestions -->
            <div class="mt-8 pt-6 border-t border-gray-200">
                <h4 class="text-sm font-medium text-gray-900 mb-4">Restock Suggestions</h4>
                <ul class="space-y-3">
                    @forelse($restockSuggestions as $product)
                        <li class="flex items-center justify-between text-sm">
                            <div>
                                <span class="text-gray-700 truncate max-w-xs block">{{ $product->name }}</span>
                                <span class="text-xs text-gray-500">Only {{ $product->stock_quantity }} left</span>
                            </div>
                            <div class="text-right">
                                <span class="text-red-600 font-medium block">Order {{ $product->suggested_quantity }}</span>
                                @if($product->days_of_supply > 0)
                                    <span class="text-xs text-gray-500">{{ $product->days_of_supply }} days supply</span>
                                @endif
                            </div>
                        </li>
                    @empty
                        <li class="text-sm text-gray-500">No restock suggestions at this time</li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>

    <!-- Products by Category -->
    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-medium text-gray-900">Products by Category</h3>
            <span class="text-sm text-gray-500">{{ $categoriesData->count() }} categories</span>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead>
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Products</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Revenue</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Avg. Price</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($categoriesData as $category)
                        <tr>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <div class="flex items-center">
                                    @if($category->image)
                                        <img src="{{ Storage::url($category->image) }}" 
                                             alt="{{ $category->name }}"
                                             class="h-8 w-8 rounded-lg object-cover mr-2">
                                    @endif
                                    <span class="text-sm font-medium text-gray-900 truncate max-w-xs">{{ $category->name }}</span>
                                </div>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">
                                {{ $category->product_count ?? 0 }}
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm font-bold text-gray-900">
                                ₦{{ number_format($category->revenue, 2) }}
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">
                                ₦{{ number_format($category->avg_price, 2) }}
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                @if($category->is_active)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        Active
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                        Inactive
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-8 text-center">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                </svg>
                                <p class="mt-2 text-sm text-gray-500">No category data available</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
