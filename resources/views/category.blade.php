@extends('layouts.app')

@section('title', $category->name)

@section('content')
    <div class="font-sans">
        <!-- Breadcrumb & Header Section -->
        <div class="bg-white border-b border-gray-200">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
                <!-- Breadcrumb -->
                <div class="flex items-center space-x-2 mb-4">
                    <a href="{{ route('home') }}" class="text-red-600 hover:text-red-700">Home</a>
                    <span class="text-gray-400">/</span>
                    <span class="text-gray-600">{{ $category->name }}</span>
                </div>

                <!-- Category Header -->
                <div class="flex flex-col md:flex-row md:items-start md:justify-between pt-4">
                    <div class="flex-1">
                        <h1 class="text-4xl font-bold text-gray-900 mb-2">{{ $category->name }}</h1>
                        @if($category->description)
                            <p class="text-gray-600 text-lg">{{ $category->description }}</p>
                        @endif
                        <p class="text-gray-500 mt-2">Showing <span class="font-semibold">{{ $products->total() }}</span> products</p>
                    </div>

                    <!-- Category Image -->
                    @if($category->image)
                        <div class="mt-6 md:mt-0 md:ml-8 flex-shrink-0">
                            <img src="{{ asset('storage/' . $category->image) }}"
                                 alt="{{ $category->name }}"
                                 class="h-48 w-48 object-cover rounded-lg shadow-lg">
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Products Section -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            @if($products->count() > 0)
                <!-- Products Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                    @foreach($products as $product)
                        <div class="group bg-white rounded-lg shadow-sm hover:shadow-lg transition-shadow duration-300 overflow-hidden border border-gray-200">
                            <!-- Product Image -->
                            <div class="relative overflow-hidden bg-gray-100 h-48">
                                @php
                                    $images = is_array($product->images) ? $product->images : [];
                                    $firstImage = !empty($images) ? $images[0] : 'https://via.placeholder.com/300x300?text=No+Image';
                                @endphp

                                <img src="{{ asset('storage/' . $firstImage) }}"
                                     alt="{{ $product->name }}"
                                     class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300"
                                     onerror="this.src='https://via.placeholder.com/300x300?text=No+Image'">

                                <!-- Wishlist Button -->
                                <div class="absolute top-3 right-3 opacity-0 group-hover:opacity-100 transition-opacity">
                                    <button @click="toggleWishlist({{ $product->id }})"
                                            class="bg-white rounded-full p-2 shadow-lg hover:bg-red-50 transition-colors">
                                        <svg class="w-5 h-5 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd" />
                                        </svg>
                                    </button>
                                </div>

                                <!-- Sale Badge -->
                                @if($product->compare_price && $product->compare_price > $product->price)
                                    @php
                                        $discount = round((($product->compare_price - $product->price) / $product->compare_price) * 100);
                                    @endphp
                                    <div class="absolute top-3 left-3 bg-red-600 text-white px-3 py-1 rounded-full text-sm font-semibold">
                                        -{{ $discount }}%
                                    </div>
                                @endif
                            </div>

                            <!-- Product Info -->
                            <div class="p-4">
                                <!-- Category -->
                                <p class="text-xs text-gray-500 uppercase tracking-wide mb-2">
                                    {{ $product->category->name ?? 'Uncategorized' }}
                                </p>

                                <!-- Product Name -->
                                <h3 class="text-gray-900 font-semibold text-sm mb-2 line-clamp-2">
                                    <a href="{{ route('product.show', $product->slug) }}" class="hover:text-red-600 transition-colors">
                                        {{ $product->name }}
                                    </a>
                                </h3>



                                <!-- Rating -->
                                <div class="flex items-center mb-3">
                                    <div class="flex text-yellow-400">
                                        @for($i = 0; $i < 5; $i++)
                                            <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20">
                                                <path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/>
                                            </svg>
                                        @endfor
                                    </div>
                                    <span class="ml-2 text-xs text-gray-500">(0)</span>
                                </div>

                                <!-- Price -->
                                <div class="flex items-center justify-between mb-4">
                                    <div>
                                        <p class="text-lg font-bold text-gray-900">
                                            {{ \App\Helpers\SettingsHelper::currency($product->price) }}
                                        </p>
                                        @if($product->compare_price)
                                            <p class="text-sm text-gray-500 line-through">
                                                {{ \App\Helpers\SettingsHelper::currency($product->compare_price) }}
                                            </p>
                                        @endif
                                    </div>
                                </div>

                                <!-- Add to Cart Button -->
                                <a href="{{ route('product.show', $product->slug) }}"
                                   class="block w-full py-2 px-4 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-lg text-center transition-colors">
                                    View Details
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="mt-12">
                    {{ $products->links() }}
                </div>
            @else
                <!-- No Products -->
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                    </svg>
                    <h3 class="mt-2 text-2xl font-medium text-gray-900">No products found</h3>
                    <p class="mt-1 text-gray-500">Sorry, we couldn't find any products in this category.</p>
                    <div class="mt-6">
                        <a href="{{ route('shop') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-base font-medium rounded-md shadow-sm text-white bg-red-600 hover:bg-red-700">
                            Browse All Products
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection
