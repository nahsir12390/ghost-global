<div>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="flex items-center justify-between mb-8">
            <h1 class="text-3xl font-bold text-gray-900">My Wishlist</h1>
            @if($wishlistItems->count() > 0)
                <span class="text-sm text-gray-500">{{ $wishlistItems->count() }} items</span>
            @endif
        </div>

        @if($wishlistItems->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                @foreach($wishlistItems as $item)
                    @if($item->product) {{-- Check if product exists --}}
                        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition-shadow duration-200">
                            <div class="relative">
                                <a href="{{ route('product.show', $item->product->slug) }}" class="block">
                                    @if($item->product->main_image)
                                        <img src="{{ asset('storage/' . $item->product->main_image) }}"
                                             alt="{{ $item->product->name }}"
                                             class="w-full h-48 object-cover">
                                    @else
                                        <div class="w-full h-48 bg-gray-200 flex items-center justify-center">
                                            <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 002 2z"></path>
                                            </svg>
                                        </div>
                                    @endif
                                </a>

                                <!-- Remove from wishlist button -->
                                <button wire:click="removeFromWishlist({{ $item->id }})"
                                        wire:loading.attr="disabled"
                                        class="absolute top-3 right-3 w-8 h-8 bg-white rounded-full shadow-md flex items-center justify-center hover:bg-red-50 hover:text-red-600 transition-colors duration-200"
                                        title="Remove from wishlist">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            </div>

                            <div class="p-4">
                                <a href="{{ route('product.show', $item->product->slug) }}" class="block">
                                    <h3 class="text-lg font-semibold text-gray-900 mb-2 line-clamp-2 hover:text-red-600 transition-colors">
                                        {{ $item->product->name }}
                                    </h3>
                                </a>

                                <div class="flex items-center justify-between mb-3">
                                    <div class="flex items-center space-x-2">
                                        <span class="text-xl font-bold text-red-600">
                                            {{ \App\Helpers\SettingsHelper::currency($item->product->price) }}
                                        </span>
                                        @if($item->product->compare_price && $item->product->compare_price > $item->product->price)
                                            <span class="text-sm text-gray-500 line-through">
                                                {{ \App\Helpers\SettingsHelper::currency($item->product->compare_price) }}
                                            </span>
                                            <span class="badge-red text-xs">
                                                -{{ round((($item->product->compare_price - $item->product->price) / $item->product->compare_price) * 100) }}%
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                <div class="flex items-center justify-between">
                                    <div class="text-xs text-gray-500">
                                        Added {{ $item->created_at->diffForHumans() }}
                                    </div>

                                    @if($item->product->quantity > 0)
                                        <button wire:click="addToCart({{ $item->product->id }})"
                                                wire:loading.attr="disabled"
                                                class="btn-primary text-sm px-3 py-1 disabled:opacity-50">
                                            <span wire:loading.remove wire:target="addToCart">Add to Cart</span>
                                            <span wire:loading wire:target="addToCart" class="flex items-center">
                                                <svg class="animate-spin h-4 w-4 mr-1 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                                </svg>
                                                Adding...
                                            </span>
                                        </button>
                                    @else
                                        <span class="text-xs text-red-600 font-medium">Out of Stock</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>

            <!-- Clear all button -->
            <div class="mt-8 flex justify-center">
                <form method="POST" action="{{ route('wishlist.clear') }}" onsubmit="return confirm('Are you sure you want to clear your entire wishlist?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="text-red-600 hover:text-red-800 font-medium text-sm">
                        Clear All Items
                    </button>
                </form>
            </div>
        @else
            <div class="text-center py-16">
                <div class="mx-auto w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                    <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Your wishlist is empty</h3>
                <p class="text-gray-500 mb-6">Start adding products you love to your wishlist!</p>
                <a href="{{ route('shop') }}" class="btn-primary">Browse Products</a>
            </div>
        @endif
    </div>
</div>