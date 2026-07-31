<div>
    <div class="max-w-7xl mx-auto px-2 sm:px-4 lg:px-8 py-6 sm:py-8">
        <div class="flex flex-col lg:flex-row gap-4 sm:gap-6 lg:gap-8">
            <!-- Cart Items -->
            <div class="lg:w-2/3">
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h1 class="text-2xl font-bold text-gray-900">Shopping Cart</h1>
                        @if(count($cart) > 0)
                            <span class="text-sm text-gray-500">
                                {{ count($cart) }} {{ count($cart) == 1 ? 'item' : 'items' }}
                            </span>
                        @endif
                    </div>

                    @if(count($cart) > 0)
                        <div wire:loading.remove class="space-y-4">
                            @foreach($cart as $productId => $item)
                                <div wire:key="cart-item-{{ $productId }}" 
                                     class="flex flex-col sm:flex-row items-start sm:items-center space-y-3 sm:space-y-0 sm:space-x-3 md:space-x-4 p-3 sm:p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                                    <!-- Product Image -->
                                    <div class="flex-shrink-0 w-16 h-16 sm:w-20 sm:h-20 bg-gray-100 rounded-md overflow-hidden">
                                        @if($item['image'])
                                            <img src="{{ asset('storage/' . $item['image']) }}"
                                                 alt="{{ $item['name'] }}"
                                                 class="w-full h-full object-center object-cover">
                                        @else
                                            <div class="w-full h-full flex items-center justify-center text-gray-400">
                                                <svg class="w-6 h-6 sm:w-8 sm:h-8" fill="currentColor" viewBox="0 0 24 24">
                                                    <path d="M4 5h16a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V6a1 1 0 0 1 1-1zM4 7v10h16V7H4zm8 2l5 4H7l5-4z"/>
                                                </svg>
                                            </div>
                                        @endif
                                    </div>

                                    <!-- Product Info -->
                                    <div class="flex-1 min-w-0">
                                        <h3 class="text-base sm:text-lg font-medium text-gray-900 truncate">
                                            <a href="{{ route('product.show', $item['slug']) }}" 
                                               class="hover:text-red-600 transition-colors">
                                                {{ $item['name'] }}
                                            </a>
                                        </h3>
                                        <p class="text-xs sm:text-sm text-gray-500">₦{{ number_format($item['price'], 2) }} each</p>
                                        <div class="mt-1 sm:mt-2">
                                            <p class="text-base sm:text-lg font-semibold text-red-600">
                                                ₦{{ number_format($item['price'] * $item['quantity'], 2) }}
                                            </p>
                                        </div>
                                    </div>

                                    <!-- Quantity Controls -->
                                    <div class="flex items-center space-x-2 sm:space-x-3 w-full sm:w-auto">
                                        <div class="flex items-center border border-gray-300 rounded-md flex-1 sm:flex-none">
                                            <button wire:click="decrementQuantity({{ $productId }})"
                                                    wire:loading.attr="disabled"
                                                    wire:target="decrementQuantity({{ $productId }})"
                                                    class="px-2 sm:px-3 py-1 sm:py-2 text-gray-600 hover:text-red-600 hover:bg-red-50 disabled:opacity-50 transition-colors">
                                                <svg class="w-3 h-3 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
                                                </svg>
                                            </button>
                                            <div class="w-10 sm:w-16 text-center flex-1">
                                                @if($updatingProductId == $productId)
                                                    <div class="py-1">
                                                        <div class="inline-block h-3 w-3 animate-spin rounded-full border-2 border-solid border-red-600 border-r-transparent"></div>
                                                    </div>
                                                @else
                                                    <span class="text-sm sm:text-lg font-medium py-1 block">
                                                        {{ $item['quantity'] }}
                                                    </span>
                                                @endif
                                            </div>
                                            <button wire:click="incrementQuantity({{ $productId }})"
                                                    wire:loading.attr="disabled"
                                                    wire:target="incrementQuantity({{ $productId }})"
                                                    class="px-2 sm:px-3 py-1 sm:py-2 text-gray-600 hover:text-red-600 hover:bg-red-50 disabled:opacity-50 transition-colors">
                                                <svg class="w-3 h-3 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                                </svg>
                                            </button>
                                        </div>

                                        <!-- Remove Button -->
                                        <button wire:click="removeItem({{ $productId }})"
                                                wire:loading.attr="disabled"
                                                class="text-red-600 hover:text-red-800 p-1 sm:p-2 disabled:opacity-50 transition-colors flex-shrink-0"
                                                title="Remove item">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Loading State -->
                        <div wire:loading class="space-y-4">
                            @foreach($cart as $productId => $item)
                                <div class="flex items-center space-x-4 p-4 border border-gray-200 rounded-lg animate-pulse">
                                    <div class="flex-shrink-0 w-20 h-20 bg-gray-300 rounded-md"></div>
                                    <div class="flex-1 space-y-2">
                                        <div class="h-4 bg-gray-300 rounded w-3/4"></div>
                                        <div class="h-3 bg-gray-300 rounded w-1/2"></div>
                                        <div class="h-4 bg-gray-300 rounded w-1/4"></div>
                                    </div>
                                    <div class="h-10 bg-gray-300 rounded w-32"></div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Cart Actions -->
                        <div class="mt-6 flex flex-col sm:flex-row justify-between items-center gap-3 sm:gap-4">
                            <a href="{{ route('shop') }}"
                               class="text-sm sm:text-base text-gray-600 hover:text-red-600 font-medium transition-colors w-full sm:w-auto text-center">
                                ← Continue Shopping
                            </a>
                            <button wire:click="clearCart"
                                    wire:loading.attr="disabled"
                                    class="text-sm sm:text-base text-red-600 hover:text-red-800 font-medium disabled:opacity-50 transition-colors w-full sm:w-auto">
                                Clear Cart
                            </button>
                        </div>
                    @else
                        <!-- Empty Cart State -->
                        <div class="text-center py-8 sm:py-12">
                            <svg class="mx-auto h-16 sm:h-24 w-16 sm:w-24 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                            <h3 class="mt-3 sm:mt-4 text-base sm:text-lg font-medium text-gray-900">Your cart is empty</h3>
                            <p class="mt-1 sm:mt-2 text-sm sm:text-base text-gray-500">Add some products to get started.</p>
                            <div class="mt-4 sm:mt-6">
                                <a href="{{ route('shop') }}"
                                   class="inline-flex items-center px-4 sm:px-6 py-2 sm:py-3 border border-transparent text-sm sm:text-base font-medium rounded-md text-white bg-red-600 hover:bg-red-700 transition-colors">
                                    Continue Shopping
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Order Summary -->
            @if(count($cart) > 0)
                <div class="lg:w-1/3">
                    <div class="bg-white rounded-lg shadow-sm p-4 sm:p-6 lg:sticky lg:top-6">
                        <h2 class="text-base sm:text-lg font-semibold text-gray-900 mb-4">Order Summary</h2>

                        <div class="space-y-3 mb-6">
                            <div class="flex justify-between text-xs sm:text-sm">
                                <span class="text-gray-600">Subtotal</span>
                                <span class="text-gray-900">₦{{ number_format($subtotal, 2) }}</span>
                            </div>
                            <div class="flex justify-between text-xs sm:text-sm">
                                <span class="text-gray-600">Platform Service Fee ({{ \App\Helpers\SettingsHelper::platformServiceFeeRate($subtotal) }}%)</span>
                                <span class="text-gray-900">₦{{ number_format($tax, 2) }}</span>
                            </div>
                            <div class="flex justify-between text-xs sm:text-sm">
                                <span class="text-gray-600">Delivery Fee</span>
                                <span class="text-gray-900">
                                    @if($shipping == 0)
                                        <span class="text-green-600 font-medium">Free</span>
                                    @else
                                        ₦{{ number_format($shipping, 2) }}
                                    @endif
                                </span>
                            </div>
                            <div class="border-t border-gray-200 pt-3 flex justify-between text-base sm:text-lg font-semibold">
                                <span class="text-gray-900">Total</span>
                                <span class="text-red-600">₦{{ number_format($total, 2) }}</span>
                            </div>
                        </div>

                        <!-- Shipping Info -->
                        <div class="mb-6 p-2 sm:p-3 bg-blue-50 rounded-md">
                            <div class="flex items-center text-xs sm:text-sm text-blue-700">
                                <svg class="w-4 h-4 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/>
                                </svg>
                                <span class="line-clamp-2">
                                    @if($shipping == 0)
                                        Free delivery applied!
                                    @else
                                        Add ₦{{ number_format(\App\Helpers\SettingsHelper::freeShippingThreshold() - $subtotal, 2) }} more for free delivery
                                    @endif
                                </span>
                            </div>
                        </div>

                        <button wire:click="checkout"
                                wire:loading.attr="disabled"
                                class="w-full bg-red-600 text-white py-2 sm:py-3 px-3 sm:px-4 rounded-md text-sm sm:text-base font-medium hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                            <span wire:loading.remove>Proceed to Checkout</span>
                            <span wire:loading>Processing...</span>
                        </button>

                        <!-- Payment Methods -->
                        <div class="mt-6 pt-6 border-t border-gray-200">
                            <h3 class="text-xs sm:text-sm font-medium text-gray-900 mb-3">We Accept</h3>
                            <div class="grid grid-cols-2 sm:flex sm:space-x-2 gap-2">
                                <div class="p-2 bg-gray-100 rounded">
                                    <span class="text-xs font-medium text-gray-600 block text-center">Visa</span>
                                </div>
                                <div class="p-2 bg-gray-100 rounded">
                                    <span class="text-xs font-medium text-gray-600 block text-center">Mastercard</span>
                                </div>
                                <div class="p-2 bg-gray-100 rounded">
                                    <span class="text-xs font-medium text-gray-600 block text-center">Paystack</span>
                                </div>
                                <div class="p-2 bg-gray-100 rounded">
                                    <span class="text-xs font-medium text-gray-600 block text-center">Bank Transfer</span>
                                </div>
                            </div>
                        </div>

                        <!-- Return Policy -->
                        <div class="mt-6 text-xs text-gray-500">
                            <p>30-day return policy • Secure checkout • SSL encrypted</p>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Recently Viewed Products -->
    @if(count($cart) > 0)
        @php
            $recentlyViewed = session()->get('recently_viewed', []);
            $recentProducts = \App\Models\Product::whereIn('id', $recentlyViewed)
                ->where('is_active', true)
                ->whereNotIn('id', array_keys($cart))
                ->take(4)
                ->get();
        @endphp
        
        @if($recentProducts->count() > 0)
            <div class="max-w-7xl mx-auto px-2 sm:px-4 lg:px-8 mt-8 sm:mt-12">
                <div class="bg-white rounded-lg shadow-sm p-4 sm:p-6">
                    <h2 class="text-lg sm:text-xl font-bold text-gray-900 mb-4 sm:mb-6">You Might Also Like</h2>
                    <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-6">
                        @foreach($recentProducts as $product)
                            <div class="group">
                                <a href="{{ route('product.show', $product->slug) }}" 
                                   class="block">
                                    <div class="aspect-w-1 aspect-h-1 w-full overflow-hidden rounded-lg bg-gray-200">
                                        @if($product->images)
                                            @php
                                                $image = $product->images[0] ?? null;
                                            @endphp
                                            @if($image)
                                                <img src="{{ asset('storage/' . $image) }}" 
                                                     alt="{{ $product->name }}" 
                                                     class="h-32 sm:h-48 w-full object-cover object-center group-hover:opacity-75">
                                            @else
                                                <div class="h-32 sm:h-48 w-full flex items-center justify-center">
                                                    <svg class="w-8 h-8 sm:w-12 sm:h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                    </svg>
                                                </div>
                                            @endif
                                        @endif
                                    </div>
                                    <div class="mt-2 sm:mt-4">
                                        <h3 class="text-xs sm:text-sm font-medium text-gray-900 group-hover:text-red-600 line-clamp-2">
                                            {{ $product->name }}
                                        </h3>
                                        <p class="mt-1 text-xs sm:text-sm text-gray-500">{{ $product->category->name ?? 'N/A' }}</p>
                                        <p class="mt-1 text-sm sm:text-lg font-medium text-gray-900">
                                            ₦{{ number_format($product->price, 2) }}
                                        </p>
                                    </div>
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif
    @endif
</div>
