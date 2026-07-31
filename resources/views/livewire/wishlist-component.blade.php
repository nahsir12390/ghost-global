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
                        @php
                            $cartQuantity = $this->getCartQuantity($item->product->id);
                            $isInCart = $this->isInCart($item->product->id);
                        @endphp
                        
                        <div class="bg-white rounded-xl shadow-lg hover:shadow-2xl transition-all duration-300 overflow-hidden border border-gray-100">
                            <div class="relative">
                                <a href="{{ route('product.show', $item->product->slug) }}" class="block">
                                    @if($item->product->main_image)
                                        <img src="{{ asset('storage/' . $item->product->main_image) }}"
                                             alt="{{ $item->product->name }}"
                                             class="w-full h-56 object-cover hover:scale-105 transition-transform duration-300">
                                    @else
                                        <div class="w-full h-56 bg-gradient-to-br from-gray-100 to-gray-200 flex items-center justify-center">
                                            <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 002 2z"></path>
                                            </svg>
                                        </div>
                                    @endif
                                </a>

                                <!-- Remove from wishlist button -->
                                <button wire:click="removeFromWishlist({{ $item->id }})"
                                        wire:loading.attr="disabled"
                                        class="absolute top-3 right-3 w-10 h-10 bg-white/90 backdrop-blur-sm rounded-full shadow-lg flex items-center justify-center hover:bg-red-50 hover:text-red-600 hover:scale-110 transition-all duration-300"
                                        title="Remove from wishlist">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                                
                                <!-- Sale Badge -->
                                @if($item->product->compare_price && $item->product->compare_price > $item->product->price)
                                    <div class="absolute top-3 left-3">
                                        <span class="px-3 py-1 bg-red-600 text-white text-xs font-bold rounded-full shadow-lg">
                                            -{{ round((($item->product->compare_price - $item->product->price) / $item->product->compare_price) * 100) }}%
                                        </span>
                                    </div>
                                @endif
                            </div>

                            <div class="p-5">
                                <a href="{{ route('product.show', $item->product->slug) }}" class="block group">
                                    <h3 class="text-lg font-bold text-gray-900 mb-2 line-clamp-2 group-hover:text-red-600 transition-colors">
                                        {{ $item->product->name }}
                                    </h3>
                                </a>

                                <div class="flex items-center justify-between mb-4">
                                    <div class="flex items-center space-x-2">
                                        <span class="text-2xl font-bold text-red-600">
                                            {{ \App\Helpers\SettingsHelper::currency($item->product->price) }}
                                        </span>
                                        @if($item->product->compare_price && $item->product->compare_price > $item->product->price)
                                            <span class="text-sm text-gray-500 line-through">
                                                {{ \App\Helpers\SettingsHelper::currency($item->product->compare_price) }}
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                <div class="text-xs text-gray-500 mb-4">
                                    <span class="flex items-center">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        Added {{ $item->created_at->diffForHumans() }}
                                    </span>
                                </div>

                                @if($item->product->quantity > 0)
                                    @if($isInCart)
                                        <div class="flex items-center justify-between bg-gray-50 rounded-xl p-2">
                                            <div class="flex items-center space-x-3">
                                                <button wire:click="decrementQuantity({{ $item->product->id }})"
                                                        class="w-8 h-8 flex items-center justify-center bg-white text-red-600 rounded-lg shadow hover:shadow-md transition-shadow">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
                                                    </svg>
                                                </button>
                                                <span class="text-lg font-bold text-gray-900 w-6 text-center">
                                                    {{ $cartQuantity }}
                                                </span>
                                                <button wire:click="incrementQuantity({{ $item->product->id }})"
                                                        wire:loading.attr="disabled"
                                                        class="w-8 h-8 flex items-center justify-center bg-red-600 text-white rounded-lg shadow hover:shadow-md hover:bg-red-700 transition-all">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                                    </svg>
                                                </button>
                                            </div>
                                            <span class="text-sm font-medium text-green-600">
                                                In Cart
                                            </span>
                                        </div>
                                    @else
                                        <button wire:click="addToCart({{ $item->product->id }})"
                                                wire:loading.attr="disabled"
                                                class="w-full bg-gradient-to-r from-red-600 to-red-700 text-white py-3 px-4 rounded-xl font-bold hover:from-red-700 hover:to-red-800 transition-all duration-300 transform hover:-translate-y-0.5 hover:shadow-lg disabled:opacity-50 disabled:cursor-not-allowed">
                                            <span wire:loading.remove wire:target="addToCart">Add to Cart</span>
                                            <span wire:loading wire:target="addToCart" class="flex items-center justify-center">
                                                <svg class="animate-spin h-5 w-5 mr-2 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                                </svg>
                                                Adding...
                                            </span>
                                        </button>
                                    @endif
                                    
                                    <!-- Stock Indicator -->
                                    @if($item->product->quantity <= 10)
                                        <div class="mt-3">
                                            <div class="flex items-center justify-between text-xs mb-1">
                                                <span class="text-gray-600">Stock: {{ $item->product->quantity }} left</span>
                                                <span class="font-medium">{{ min(100, round(($item->product->quantity / 10) * 100)) }}%</span>
                                            </div>
                                            <div class="w-full bg-gray-200 rounded-full h-1.5">
                                                <div class="bg-red-600 h-1.5 rounded-full" style="width: {{ min(100, ($item->product->quantity / 10) * 100) }}%"></div>
                                            </div>
                                        </div>
                                    @endif
                                @else
                                    <button disabled class="w-full bg-gray-300 text-gray-600 py-3 px-4 rounded-xl font-bold cursor-not-allowed">
                                        Out of Stock
                                    </button>
                                @endif
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>

            <!-- Clear all button -->
            <div class="mt-8 flex justify-center">
                <button wire:click="clearWishlist"
                        wire:loading.attr="disabled"
                        wire:confirm="Are you sure you want to clear your entire wishlist?"
                        class="px-6 py-3 border-2 border-red-600 text-red-600 font-bold rounded-full hover:bg-red-600 hover:text-white transition-all duration-300 disabled:opacity-50">
                    <span wire:loading.remove>Clear All Items</span>
                    <span wire:loading>Clearing...</span>
                </button>
            </div>
        @else
            <div class="text-center py-20">
                <div class="mx-auto w-32 h-32 bg-gradient-to-br from-red-50 to-pink-50 rounded-full flex items-center justify-center mb-6">
                    <svg class="w-16 h-16 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                    </svg>
                </div>
                <h3 class="text-2xl font-bold text-gray-900 mb-3">Your wishlist is empty</h3>
                <p class="text-gray-500 text-lg mb-8 max-w-md mx-auto">
                    Start adding products you love to your wishlist! Save items for later or compare products.
                </p>
                <a href="{{ route('shop') }}" 
                   class="inline-block bg-gradient-to-r from-red-600 to-red-700 text-white px-8 py-4 rounded-full font-bold text-lg hover:from-red-700 hover:to-red-800 transition-all duration-300 hover:shadow-lg">
                    Browse Products
                </a>
            </div>
        @endif
    </div>
    
    <!-- Add Livewire events to update cart quantity -->
    <script>
        document.addEventListener('livewire:initialized', () => {
            // Listen for increment/decrement quantity events
            Livewire.on('increment-quantity', (event) => {
                const { productId } = event;
                Livewire.dispatch('cart-increment', { productId });
            });
            
            Livewire.on('decrement-quantity', (event) => {
                const { productId } = event;
                Livewire.dispatch('cart-decrement', { productId });
            });
        });
    </script>
</div>