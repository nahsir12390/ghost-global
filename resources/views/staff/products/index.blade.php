@extends('layouts.admin')

@section('title', 'Manage Products')

@section('breadcrumb')
    <a href="{{ route('admin.staff.dashboard') }}" class="text-red-600 hover:text-red-700">Staff Dashboard</a>
    <span class="mx-2">/</span>
    <span class="text-gray-700">Products</span>
@endsection

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Manage Products</h2>
            <p class="text-gray-600 text-sm mt-1">Create, edit, and manage your products</p>
        </div>
    </div>

    <!-- Header Button -->
    <div class="text-right mb-6">
        @if(auth()->user()->isProductManager())
            <a href="{{ route('admin.products.create') }}" class="btn-primary">
                + Add Product
            </a>
        @endif
    </div>

    <!-- Products Grid -->
    <div>
        @php
            $products = \App\Models\Product::latest()->paginate(12);
        @endphp

        @if($products->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($products as $product)
                    <div class="card-modern overflow-hidden hover:shadow-lg transition-shadow duration-300">
                        <!-- Product Image -->
                        <div class="relative bg-gray-100 h-48 overflow-hidden">
                            @php
                                $images = $product->images ?? [];
                                $firstImage = is_array($images) && count($images) > 0 ? $images[0] : null;
                            @endphp
                            @if($firstImage)
                                <img src="{{ asset('storage/' . $firstImage) }}" alt="{{ $product->name }}" class="w-full h-full object-cover hover:scale-105 transition-transform duration-300">
                            @else
                                <div class="w-full h-full flex items-center justify-center bg-gray-200">
                                    <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </div>
                            @endif
                            <!-- Price Badge -->
                            <div class="absolute top-3 right-3 bg-red-600 text-white px-3 py-1 rounded-lg text-sm font-semibold">
                                ₦{{ number_format($product->price, 0) }}
                            </div>
                        </div>

                        <!-- Product Info -->
                        <div class="p-4">
                            <h3 class="text-lg font-semibold text-gray-900 mb-1 line-clamp-2">{{ $product->name }}</h3>
                            <p class="text-sm text-gray-600 mb-3 line-clamp-2">{{ $product->description }}</p>

                            <!-- Stock Status -->
                            <div class="flex items-center justify-between mb-4">
                                <div class="text-sm">
                                    <p class="text-gray-600">Stock: <span class="font-semibold {{ $product->quantity > 0 ? 'text-green-600' : 'text-red-600' }}">{{ $product->quantity }}</span></p>
                                </div>
                                @if($product->category)
                                    <span class="text-xs bg-gray-200 text-gray-800 px-2 py-1 rounded">{{ $product->category->name }}</span>
                                @endif
                            </div>

                            <!-- Actions -->
                            <div class="flex gap-2">
                                <a href="{{ route('admin.products.show', $product) }}" class="flex-1 btn-secondary text-center text-sm py-2">
                                    View
                                </a>
                                @if(auth()->user()->isProductManager())
                                    <a href="{{ route('admin.products.edit', $product) }}" class="flex-1 btn-secondary text-center text-sm py-2">
                                        Edit
                                    </a>
                                    <form action="{{ route('admin.products.destroy', $product) }}" method="POST" class="flex-1" onsubmit="return confirm('Are you sure?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="w-full bg-red-100 text-red-600 hover:bg-red-200 px-3 py-2 rounded font-medium transition-colors text-sm">
                                            Delete
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            @if($products->hasPages())
                <div class="mx-auto mt-8">
                    {{ $products->links() }}
                </div>
            @endif
        @else
            <!-- Empty State -->
            <div class="card-modern p-12 text-center">
                <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m0 0l8 4m-8-4v10l8 4m0-10l8 4m-8-4v10M8 5v10m8-10v10" />
                </svg>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">No Products Yet</h3>
                <p class="text-gray-600 mb-6">There are no products to manage at the moment.</p>
                @if(auth()->user()->isProductManager())
                    <a href="{{ route('admin.products.create') }}" class="btn-primary">
                        Create First Product
                    </a>
                @endif
            </div>
        @endif
    </div>
</div>
@endsection
