@extends('layouts.admin')

@section('title', 'Product Details')
@section('breadcrumb', 'Products / Details')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">{{ $product->name }}</h1>
            <div class="flex items-center space-x-2 mt-1">
                <span class="badge-{{ $product->is_active ? 'green' : 'red' }}">
                    {{ $product->is_active ? 'Active' : 'Inactive' }}
                </span>
                @if($product->is_featured)
                    <span class="badge-yellow">Featured</span>
                @endif
                <span class="text-sm text-gray-500">SKU: {{ $product->sku }}</span>
            </div>
        </div>
        <div class="flex space-x-2">
            <a href="{{ route('admin.products.edit', $product) }}" 
               class="btn-primary inline-flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
                Edit
            </a>
            <a href="{{ route('admin.products.index') }}" 
               class="btn-secondary inline-flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Back
            </a>
        </div>
    </div>

    <!-- Product Details -->
    <div class="bg-white shadow-sm rounded-lg overflow-hidden">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 p-6">
            <!-- Product Images -->
            <div>
                <h3 class="text-lg font-medium text-gray-900 mb-4">Product Images</h3>
                @if($product->images && count($product->images) > 0)
                    <div class="grid grid-cols-2 gap-4">
                        @foreach($product->images as $image)
                            <div class="relative">
                                <img src="{{ Storage::url($image) }}" 
                                     alt="{{ $product->name }}" 
                                     class="w-full h-48 object-cover rounded-lg">
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="bg-gray-100 rounded-lg p-12 text-center">
                        <svg class="w-12 h-12 text-gray-400 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        <p class="mt-2 text-gray-500">No images available</p>
                    </div>
                @endif
            </div>

            <!-- Product Information -->
            <div>
                <h3 class="text-lg font-medium text-gray-900 mb-4">Product Information</h3>
                <dl class="space-y-4">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Name</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $product->name }}</dd>
                    </div>
                    
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Category</dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            <a href="{{ route('admin.categories.show', $product->category) }}" 
                               class="text-red-600 hover:text-red-800">
                                {{ $product->category->name }}
                            </a>
                        </dd>
                    </div>
                    
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Description</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $product->description ?? 'No description' }}</dd>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Price</dt>
                            <dd class="mt-1 text-2xl font-bold text-red-600">
                                ₦{{ number_format($product->price, 2) }}
                            </dd>
                        </div>
                        
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Compare Price</dt>
                            <dd class="mt-1 text-lg text-gray-500">
                                @if($product->compare_price)
                                    <span class="line-through">₦{{ number_format($product->compare_price, 2) }}</span>
                                    <span class="ml-2 badge-green">{{ $product->discount_percentage }}% off</span>
                                @else
                                    -
                                @endif
                            </dd>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Quantity</dt>
                            <dd class="mt-1 text-lg font-medium {{ $product->quantity <= 10 ? 'text-red-600' : 'text-gray-900' }}">
                                {{ $product->quantity }}
                                @if($product->quantity == 0)
                                    <span class="badge-red ml-2">Out of Stock</span>
                                @elseif($product->quantity <= 10)
                                    <span class="badge-yellow ml-2">Low Stock</span>
                                @endif
                            </dd>
                        </div>
                        
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Status</dt>
                            <dd class="mt-1">
                                <span class="badge-{{ $product->is_active ? 'green' : 'red' }}">
                                    {{ $product->is_active ? 'Active' : 'Inactive' }}
                                </span>
                                @if($product->is_featured)
                                    <span class="badge-yellow ml-2">Featured</span>
                                @endif
                            </dd>
                        </div>
                    </div>
                    
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Created</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $product->created_at->format('M d, Y H:i') }}</dd>
                    </div>
                    
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Last Updated</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $product->updated_at->format('M d, Y H:i') }}</dd>
                    </div>
                </dl>
            </div>
        </div>

        <!-- Product Actions -->
        <div class="bg-gray-50 px-6 py-4 border-t border-gray-200">
            <div class="flex justify-between items-center">
                <div class="text-sm text-gray-500">
                    Product ID: {{ $product->id }}
                </div>
                <div class="flex space-x-2">
                    <button onclick="confirm('Are you sure you want to delete this product?') && Livewire.dispatch('deleteProduct', {id: {{ $product->id }}})"
                           class="btn-danger inline-flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                        Delete Product
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Orders with this product -->
    <div class="bg-white shadow-sm rounded-lg">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Recent Orders</h3>
        </div>
        <div class="p-6">
            @if($product->orderItems()->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead>
                            <tr>
                                <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Order #
                                </th>
                                <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Customer
                                </th>
                                <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Quantity
                                </th>
                                <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Price
                                </th>
                                <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Date
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($product->orderItems()->with('order.user')->latest()->take(10)->get() as $item)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <a href="{{ route('admin.orders.show', $item->order) }}" 
                                           class="text-red-600 hover:text-red-800">
                                            {{ $item->order->order_number }}
                                        </a>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        {{ $item->order->user->name }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        {{ $item->quantity }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        ₦{{ number_format($item->price, 2) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $item->created_at->format('M d, Y') }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @if($product->orderItems()->count() > 10)
                    <div class="mt-4 text-center">
                        <a href="#" class="text-red-600 hover:text-red-800 text-sm font-medium">
                            View all {{ $product->orderItems()->count() }} orders
                        </a>
                    </div>
                @endif
            @else
                <div class="text-center py-8">
                    <svg class="w-12 h-12 text-gray-400 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                    <p class="mt-2 text-gray-500">No orders for this product yet</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('livewire:initialized', () => {
        // Listen for delete events
        Livewire.on('productDeleted', (event) => {
            window.location.href = "{{ route('admin.products.index') }}";
        });
    });
</script>
@endpush