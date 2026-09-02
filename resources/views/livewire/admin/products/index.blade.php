<div class="space-y-6">
    <!-- Search and Filters -->
    <div class="bg-white p-6 rounded-lg border border-gray-200">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between space-y-4 md:space-y-0">
            <!-- Search -->
            <div class="w-full md:w-auto">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <input wire:model.live.debounce.850ms="search"
                           type="search" 
                           placeholder="Search products..."
                           class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg w-full md:w-80 focus:ring-2 focus:ring-red-500 focus:border-red-500">
                </div>
            </div>

            <!-- Filters and Actions -->
            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
                <!-- Category Filter -->
                <select wire:model.change="categoryFilter" 
                        class="border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-red-500 focus:border-red-500">
                    <option value="">All Categories</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                </select>

                <!-- Status Filter -->
                <select wire:model.change="statusFilter" 
                        class="border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-red-500 focus:border-red-500">
                    <option value="">All Status</option>
                    <option value="1">Active</option>
                    <option value="0">Inactive</option>
                </select>

                <!-- Bulk Actions -->
                @if(count($selectedProducts) > 0)
                    <div class="flex items-center gap-2 pl-3 border-l border-gray-300">
                        <span class="text-sm font-medium text-gray-700">
                            {{ count($selectedProducts) }} selected
                        </span>
                        <button wire:click="confirmDelete"
                                wire:confirm="Are you sure you want to delete {{ count($selectedProducts) }} selected product(s)? This action cannot be undone."
                                class="px-3 py-2 bg-red-600 text-white text-sm font-medium rounded-lg hover:bg-red-700 transition focus:outline-none focus:ring-2 focus:ring-red-500">
                            Delete Selected
                        </button>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Products Table -->
    <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <div class="flex items-center">
                                <input type="checkbox" 
                                       wire:model.change="selectAll"
                                       class="h-4 w-4 text-red-600 bg-white border-gray-300 rounded focus:ring-red-500 cursor-pointer">
                                <span class="ml-2">Product</span>
                            </div>
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Category
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <button wire:click="sortBy('price')" class="flex items-center space-x-1 hover:text-gray-700">
                                <span>Price</span>
                                @if($sortField === 'price')
                                    <svg class="w-4 h-4 {{ $sortDirection === 'asc' ? 'rotate-180' : '' }}" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                @endif
                            </button>
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Stock
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Status
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Featured
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <button wire:click="sortBy('created_at')" class="flex items-center space-x-1 hover:text-gray-700">
                                <span>Created</span>
                                @if($sortField === 'created_at')
                                    <svg class="w-4 h-4 {{ $sortDirection === 'asc' ? 'rotate-180' : '' }}" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                @endif
                            </button>
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($products as $product)
                        <tr class="hover:bg-gray-50 transition" wire:key="product-{{ $product->id }}">
                            <!-- Checkbox & Product -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <input type="checkbox" 
                                           wire:model.change="selectedProducts"
                                           value="{{ $product->id }}"
                                           class="h-4 w-4 text-red-600 bg-white border-gray-300 rounded focus:ring-red-500 cursor-pointer">
                                    <div class="ml-4 flex items-center">
                                        @php
                                            $mainImage = $product->main_image;
                                        @endphp
                                        @if($mainImage)
                                            @if(str_starts_with($mainImage, 'http'))
                                                <img class="h-10 w-10 rounded object-cover" 
                                                     src="{{ $mainImage }}" 
                                                     alt="{{ $product->name }}">
                                            @else
                                                <img class="h-10 w-10 rounded object-cover" 
                                                     src="{{ Storage::url($mainImage) }}" 
                                                     alt="{{ $product->name }}">
                                            @endif
                                        @else
                                            <div class="h-10 w-10 rounded bg-gray-200 flex items-center justify-center">
                                                <svg class="h-6 w-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                </svg>
                                            </div>
                                        @endif
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">
                                                <a href="{{ route('admin.products.show', $product) }}" 
                                                   class="hover:text-red-600 transition">
                                                    {{ Str::limit($product->name, 30) }}
                                                </a>
                                            </div>
                                            <div class="text-xs text-gray-500">
                                                SKU: {{ $product->sku }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <!-- Category -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                    {{ $product->category->name ?? 'N/A' }}
                                </span>
                            </td>

                            <!-- Price -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-bold text-gray-900">
                                    ₦{{ number_format($product->price, 2) }}
                                </div>
                                @if($product->compare_price && $product->compare_price > $product->price)
                                    <div class="text-xs text-gray-500 line-through">
                                        ₦{{ number_format($product->compare_price, 2) }}
                                    </div>
                                    <div class="text-xs font-semibold text-red-600">
                                        Save {{ $product->discount_percentage }}%
                                    </div>
                                @endif
                            </td>

                            <!-- Stock -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($product->quantity == 0)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-red-100 text-red-800">
                                        Out of Stock
                                    </span>
                                @elseif($product->quantity <= 10)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-800">
                                        Low ({{ $product->quantity }})
                                    </span>
                                @else
                                    <span class="text-sm text-gray-900 font-medium">
                                        {{ $product->quantity }}
                                    </span>
                                @endif
                            </td>

                            <!-- Status Toggle -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <button type="button"
                                        wire:click="toggleProductStatus({{ $product->id }})"
                                        class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 {{ $product->is_active ? 'bg-red-600' : 'bg-gray-300' }}">
                                    <span class="sr-only">Toggle product status</span>
                                    <span class="pointer-events-none relative inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out {{ $product->is_active ? 'translate-x-5' : 'translate-x-0' }}"></span>
                                </button>
                            </td>

                            <!-- Featured Toggle -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <button type="button"
                                        wire:click="toggleFeatured({{ $product->id }})"
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold transition {{ $product->is_featured ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800 hover:bg-gray-200' }}">
                                    @if($product->is_featured)
                                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                        </svg>
                                        Featured
                                    @else
                                        Not Featured
                                    @endif
                                </button>
                            </td>

                            <!-- Created Date -->
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                {{ $product->created_at->format('M d, Y') }}
                            </td>

                            <!-- Actions -->
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex items-center justify-end gap-1">
                                    <a href="{{ route('admin.products.show', $product) }}" 
                                       class="inline-flex items-center justify-center h-8 w-8 rounded-md text-gray-600 hover:bg-gray-100 hover:text-gray-900 transition" 
                                       title="View">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </a>
                                    <a href="{{ route('admin.products.edit', $product) }}" 
                                       class="inline-flex items-center justify-center h-8 w-8 rounded-md text-blue-600 hover:bg-blue-50 hover:text-blue-900 transition" 
                                       title="Edit">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </a>
                                    <button type="button"
                                            wire:click="confirmDelete({{ $product->id }})"
                                            wire:confirm="Are you sure you want to delete this product? This action cannot be undone."
                                            class="inline-flex items-center justify-center h-8 w-8 rounded-md text-red-600 hover:bg-red-50 hover:text-red-900 transition" 
                                            title="Delete">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                                </svg>
                                <h3 class="mt-2 text-sm font-semibold text-gray-900">No products found</h3>
                                <p class="mt-1 text-sm text-gray-500 mb-4">
                                    @if($search || $categoryFilter || $statusFilter !== '')
                                        Try adjusting your filters to find what you're looking for.
                                    @else
                                        Get started by creating your first product.
                                    @endif
                                </p>
                                @if(!$search && !$categoryFilter && $statusFilter === '')
                                    <a href="{{ route('admin.products.create') }}" 
                                       class="inline-flex items-center gap-2 px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-lg hover:bg-red-700 transition">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                        </svg>
                                        Add Product
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    @if($products->hasPages())
        <div class="bg-white border border-gray-200 rounded-lg px-6 py-4">
            {{ $products->links() }}
        </div>
    @endif
</div>
