<div class="bg-[#f5f3ee]">
    @php
        $images = is_array($product->images) ? $product->images : (is_string($product->images) ? json_decode($product->images, true) : []);
        $images = $images ?? [];
        $productShareUrl = route('product.show', $product->slug);
        $productShareText = 'Check out ' . $product->name;
        $encodedProductShareUrl = rawurlencode($productShareUrl);
        $encodedProductShareText = rawurlencode($productShareText);
    @endphp

    <style>
        .line-clamp-1 {
            overflow: hidden;
            display: -webkit-box;
            -webkit-box-orient: vertical;
            -webkit-line-clamp: 1;
        }
        
        .line-clamp-2 {
            overflow: hidden;
            display: -webkit-box;
            -webkit-box-orient: vertical;
            -webkit-line-clamp: 2;
        }
        
        .line-clamp-3 {
            overflow: hidden;
            display: -webkit-box;
            -webkit-box-orient: vertical;
            -webkit-line-clamp: 3;
        }

        /* Modern animations */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes scaleIn {
            from {
                opacity: 0;
                transform: scale(0.95);
            }
            to {
                opacity: 1;
                transform: scale(1);
            }
        }

        .animate-fade-in-up {
            animation: fadeInUp 0.6s ease-out forwards;
        }

        .animate-scale-in {
            animation: scaleIn 0.4s ease-out forwards;
        }

        /* Modern scrollbar */
        .modern-scrollbar::-webkit-scrollbar {
            height: 6px;
            width: 6px;
        }

        .modern-scrollbar::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }

        .modern-scrollbar::-webkit-scrollbar-thumb {
            background: #dc2626;
            border-radius: 10px;
        }

        .modern-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #b91c1c;
        }

        /* Glass morphism */
        .glass-effect {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        }

        /* Modern card hover */
        .modern-card {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .modern-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 20px 40px -12px rgba(0, 0, 0, 0.15);
        }

        /* Shimmer effect */
        @keyframes shimmer {
            0% {
                background-position: -1000px 0;
            }
            100% {
                background-position: 1000px 0;
            }
        }

        .shimmer {
            background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
            background-size: 1000px 100%;
            animation: shimmer 2s infinite;
        }
    </style>

    <!-- Modern Breadcrumb -->
    <div class="glass-effect sticky top-16 z-30 border-b border-slate-200/70">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <nav class="flex" aria-label="Breadcrumb">
                <ol class="flex items-center space-x-3 text-sm">
                    <li>
                        <a href="{{ route('home') }}" class="text-gray-500 hover:text-red-600 transition-colors flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                            </svg>
                            Home
                        </a>
                    </li>
                    <li class="text-gray-400">/</li>
                    <li>
                        <a href="{{ route('category.show', $product->category->slug) }}" 
                           class="text-gray-500 hover:text-red-600 transition-colors">
                            {{ $product->category->name }}
                        </a>
                    </li>
                    <li class="text-gray-400">/</li>
                    <li class="text-gray-900 font-medium truncate max-w-[200px]">
                        {{ $product->name }}
                    </li>
                </ol>
            </nav>
        </div>
    </div>

    <!-- Main Product Section -->
    <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8 lg:py-14">
        <div class="grid grid-cols-1 gap-8 lg:grid-cols-[1.05fr_.95fr] lg:gap-12">
            <!-- Product Images Section - Modern Gallery -->
            <div
                class="animate-fade-in-up"
                x-data="{ selectedImage: 0, images: @js(collect($images)->map(fn ($image) => asset('storage/' . $image))->values()) }"
            >
                <!-- Main Image Container -->
                <div class="group relative overflow-hidden rounded-[2rem] border border-white bg-gradient-to-br from-white to-slate-100 shadow-[0_28px_70px_-38px_rgba(15,23,42,.55)]">
                    <div class="aspect-w-1 aspect-h-1">
                        @if($images && isset($images[0]))
                            <img :src="images[selectedImage]" src="{{ asset('storage/' . $images[0]) }}"
                                 alt="{{ $product->name }}" 
                                 class="h-full w-full object-contain p-8 transition-transform duration-700 group-hover:scale-[1.035] sm:p-12">
                        @else
                            <div class="w-full h-[500px] flex items-center justify-center">
                                <svg class="w-32 h-32 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                        @endif
                    </div>

                    <!-- Badges -->
                    <div class="absolute top-4 left-4 flex flex-col gap-2">
                        @if($product->compare_price && $product->compare_price > $product->price)
                            <span class="px-3 py-1.5 bg-gradient-to-r from-red-500 to-pink-500 text-white text-xs font-bold rounded-lg shadow-lg">
                                -{{ round((($product->compare_price - $product->price) / $product->compare_price) * 100) }}%
                            </span>
                        @endif
                        
                        @if($product->created_at->gt(now()->subDays(7)))
                            <span class="px-3 py-1.5 bg-gradient-to-r from-green-500 to-emerald-500 text-white text-xs font-bold rounded-lg shadow-lg">
                                New Arrival
                            </span>
                        @endif

                        @if($product->is_featured)
                            <span class="px-3 py-1.5 bg-gradient-to-r from-yellow-500 to-amber-500 text-white text-xs font-bold rounded-lg shadow-lg">
                                Featured
                            </span>
                        @endif
                    </div>

                    <!-- Wishlist Button -->
                    <button wire:click="toggleWishlist"
                            wire:loading.attr="disabled"
                            class="absolute top-4 right-4 w-10 h-10 bg-white/90 backdrop-blur-sm rounded-full flex items-center justify-center hover:bg-red-50 transition-all duration-300 shadow-lg group">
                        @if($updatingWishlist)
                            <svg class="animate-spin h-5 w-5 text-red-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        @else
                            <svg class="w-5 h-5 {{ $wishlistStatus ? 'text-red-600 fill-current' : 'text-gray-600 group-hover:text-red-600' }} transition-colors" 
                                 stroke="currentColor" 
                                 stroke-width="{{ $wishlistStatus ? '0' : '1.5' }}" 
                                 viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" 
                                      d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                            </svg>
                        @endif
                    </button>
                </div>

                <!-- Thumbnail Gallery -->
                @if($images && count($images) > 1)
                    <div class="flex gap-3 mt-4 overflow-x-auto pb-2 modern-scrollbar">
                        @foreach($images as $index => $image)
                            <button type="button" @click="selectedImage = {{ $index }}"
                                    :aria-pressed="selectedImage === {{ $index }}"
                                    class="flex-shrink-0 group focus:outline-none">
                                <div class="relative">
                                    <img src="{{ asset('storage/' . $image) }}" 
                                         alt="Thumbnail {{ $index + 1 }}" 
                                         :class="selectedImage === {{ $index }} ? 'border-red-500 shadow-lg' : 'border-gray-200 group-hover:border-red-300'"
                                         class="w-20 h-20 object-cover rounded-xl border-2 transition-all duration-200">
                                    <div x-show="selectedImage === {{ $index }}" class="absolute inset-0 bg-red-500/10 rounded-xl"></div>
                                </div>
                            </button>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- Product Info Section -->
            <div class="animate-scale-in rounded-[2rem] border border-slate-200 bg-white p-5 shadow-sm sm:p-8 lg:sticky lg:top-36 lg:self-start">
                <!-- Product Header -->
                <div class="mb-6">
                    <div class="flex items-center gap-3 mb-3">
                        <a href="{{ route('category.show', $product->category->slug) }}" 
                           class="inline-flex items-center px-3 py-1 bg-red-50 text-red-600 text-xs font-semibold rounded-full hover:bg-red-100 transition-colors">
                            {{ $product->category->name }}
                        </a>
                        <div class="flex items-center gap-2">
                            <div class="flex text-amber-400" aria-label="{{ number_format((float) $product->comments_avg_rating, 1) }} out of 5 stars">★★★★★</div>
                            <a href="#customer-reviews" class="text-xs font-semibold text-slate-500 transition hover:text-red-600">{{ $product->comments_count ? number_format((float) $product->comments_avg_rating, 1).' · '.$product->comments_count.' reviews' : 'No reviews yet' }}</a>
                        </div>
                    </div>

                    <h1 class="text-3xl lg:text-4xl font-bold text-gray-900 leading-tight mb-3">
                        {{ $product->name }}
                    </h1>

                    <div class="flex flex-wrap items-center gap-3">
                        <div class="flex items-center gap-2">
                            <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span class="text-sm font-medium {{ $product->isPurchasable() ? 'text-green-600' : 'text-red-600' }}">
                                {{ $product->isPhysical() ? ($product->quantity > 0 ? 'In Stock' : 'Out of Stock') : ($product->isCourse() ? 'Instant Course Access' : 'Instant Digital Delivery') }}
                            </span>
                        </div>
                        <div class="w-px h-4 bg-gray-300"></div>
                        <span class="rounded-full bg-blue-100 px-3 py-1 text-xs font-semibold text-blue-700">
                            {{ ucfirst($product->product_type ?? 'physical') }}
                        </span>
                        <div class="w-px h-4 bg-gray-300"></div>
                        @if($product->vendor)
                            <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $product->vendorIsAvailable() ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                {{ $product->vendorIsAvailable() ? 'Vendor Active' : 'Vendor Inactive' }}
                            </span>
                            <div class="w-px h-4 bg-gray-300"></div>
                            @if($product->vendor->storefrontUrl())
                                <a href="{{ $product->vendor->storefrontUrl() }}" class="text-sm font-semibold text-red-600 transition hover:text-red-700 hover:underline">
                                    {{ $product->vendor->publicStoreName() }}
                                </a>
                                <div class="w-px h-4 bg-gray-300"></div>
                            @elseif($product->vendor->store_name)
                                <span class="text-sm font-medium text-gray-600">
                                    {{ $product->vendor->store_name }}
                                </span>
                                <div class="w-px h-4 bg-gray-300"></div>
                            @endif
                        @endif
                        <div class="flex items-center gap-2">
                            <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14" />
                            </svg>
                            <span class="text-sm text-gray-600">SKU: {{ $product->sku ?? substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, 8) }}</span>
                        </div>
                    </div>

                    <div class="mt-5 rounded-2xl border border-gray-200 bg-gray-50 p-4">
                        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                            <div>
                                <p class="text-sm font-semibold text-gray-900">Share this product</p>
                                <p class="mt-1 text-xs text-gray-500 break-all">{{ $productShareUrl }}</p>
                            </div>
                            <div class="flex flex-wrap gap-2">
                                <button type="button"
                                        onclick="if (navigator.share) { navigator.share({ title: @js($product->name), text: @js($productShareText), url: @js($productShareUrl) }); } else { navigator.clipboard.writeText(@js($productShareUrl)).then(() => window.KeffiCart?.notify('Product link copied.', 'success')); }"
                                        class="inline-flex items-center gap-2 rounded-xl bg-gray-900 px-3 py-2 text-xs font-semibold text-white transition hover:bg-black">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12s-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316" />
                                    </svg>
                                    Share
                                </button>
                                <a href="https://wa.me/?text={{ rawurlencode($productShareText . ' ' . $productShareUrl) }}" target="_blank" rel="noopener"
                                   class="inline-flex items-center rounded-xl bg-green-600 px-3 py-2 text-xs font-semibold text-white transition hover:bg-green-700">
                                    WhatsApp
                                </a>
                                <a href="https://www.facebook.com/sharer/sharer.php?u={{ $encodedProductShareUrl }}" target="_blank" rel="noopener"
                                   class="inline-flex items-center rounded-xl bg-blue-600 px-3 py-2 text-xs font-semibold text-white transition hover:bg-blue-700">
                                    Facebook
                                </a>
                                <a href="https://twitter.com/intent/tweet?text={{ $encodedProductShareText }}&url={{ $encodedProductShareUrl }}" target="_blank" rel="noopener"
                                   class="inline-flex items-center rounded-xl bg-slate-800 px-3 py-2 text-xs font-semibold text-white transition hover:bg-slate-900">
                                    X
                                </a>
                                <button type="button"
                                        onclick="navigator.clipboard.writeText(@js($productShareUrl)).then(() => window.KeffiCart?.notify('Product link copied.', 'success'))"
                                        class="inline-flex items-center rounded-xl border border-gray-300 bg-white px-3 py-2 text-xs font-semibold text-gray-700 transition hover:bg-gray-100">
                                    Copy Link
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Pricing Section -->
                <div class="mb-8 rounded-[1.5rem] border border-red-100 bg-gradient-to-br from-red-50 via-white to-rose-50 p-5 sm:p-6">
                    <div class="flex items-baseline gap-4">
                        <div>
                            <span class="text-4xl lg:text-5xl font-bold text-gray-900">
                                ₦{{ number_format($product->price, 2) }}
                            </span>
                            @if($product->compare_price && $product->compare_price > $product->price)
                                <span class="text-lg text-gray-500 line-through ml-3">
                                    ₦{{ number_format($product->compare_price, 2) }}
                                </span>
                            @endif
                        </div>
                        @if($product->compare_price && $product->compare_price > $product->price)
                            <div class="bg-red-600 text-white px-3 py-1.5 rounded-full text-sm font-bold">
                                Save ₦{{ number_format($product->compare_price - $product->price, 2) }}
                            </div>
                        @endif
                    </div>

                    <!-- Checkout fee info -->
                    <div class="mt-2 text-sm text-gray-600">
                        Platform service fee is calculated at checkout
                    </div>
                </div>

                <!-- Description -->
                <div class="mb-8">
                    <h3 class="text-lg font-semibold text-gray-900 mb-3 flex items-center gap-2">
                        <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Product Description
                    </h3>
                    <div class="text-gray-600 leading-relaxed space-y-2">
                                <p>{{ $product->description }}</p>
                    </div>
                </div>

                <!-- Stock Indicator -->
                @if($product->isPhysical() && $product->quantity > 0 && $product->quantity <= 10)
                    <div class="mb-8 p-4 bg-amber-50 rounded-xl border border-amber-200">
                        <div class="flex items-center justify-between mb-2">
                            <div class="flex items-center gap-2">
                                <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                                <span class="text-sm font-semibold text-amber-800">Low Stock Alert</span>
                            </div>
                            <span class="text-sm font-bold text-amber-900">{{ $product->quantity }} units left</span>
                        </div>
                        <div class="w-full bg-amber-200 rounded-full h-2">
                            <div class="bg-amber-600 h-2 rounded-full" style="width: {{ ($product->quantity / 10) * 100 }}%"></div>
                        </div>
                        <p class="text-xs text-amber-700 mt-2">Hurry! Only {{ $product->quantity }} items left in stock.</p>
                    </div>
                @endif

                <!-- Cart Controls -->
                <div x-data="cartCard({ productId: {{ $product->id }}, stock: {{ $product->tracksInventory() ? $product->quantity : 9999 }}, initialQuantity: {{ $this->getCartQuantity() }}, initialSelectedQuantity: 1 })" class="mb-8">
                    @if($product->isPurchasable())
                        <div x-show="quantity > 0" class="space-y-4">
                            <div class="flex items-center justify-between p-4 bg-green-50 rounded-xl border border-green-200">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-green-500 rounded-full flex items-center justify-center">
                                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="font-semibold text-green-800">Added to Cart</p>
                                        <p class="text-sm text-green-600">Quantity: <span class="font-bold" x-text="quantity"></span></p>
                                    </div>
                                </div>
                                <button @click="remove()" :disabled="busy" class="text-sm font-medium text-red-600 hover:text-red-700 transition-colors disabled:opacity-50">
                                    Remove
                                </button>
                            </div>

                            <div class="flex items-center gap-4">
                                <div class="flex items-center bg-white border border-gray-200 rounded-xl overflow-hidden shadow-sm">
                                    <button @click="decrement()" :disabled="busy || quantity <= 1" class="px-5 py-3 text-gray-600 hover:bg-gray-50 transition-colors disabled:opacity-50">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" />
                                        </svg>
                                    </button>
                                    <span class="px-4 text-xl font-bold text-gray-900 min-w-[60px] text-center" x-text="quantity"></span>
                                    <button @click="increment()" :disabled="busy || quantity >= stock" class="px-5 py-3 text-gray-600 hover:bg-gray-50 transition-colors disabled:opacity-50">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                        </svg>
                                    </button>
                                </div>

                                <a href="{{ route('cart') }}" 
                                   class="flex-1 bg-gradient-to-r from-red-600 to-red-700 text-white py-3 px-6 rounded-xl font-semibold hover:from-red-700 hover:to-red-800 transition-all duration-300 text-center shadow-lg hover:shadow-xl">
                                    View Cart →
                                </a>
                            </div>
                        </div>

                        <div x-show="quantity === 0" class="space-y-4">
                            <div class="flex items-center gap-6">
                                <div class="flex items-center gap-3">
                                    <span class="text-sm font-medium text-gray-700">Quantity:</span>
                                    <div class="flex items-center bg-white border border-gray-200 rounded-xl overflow-hidden shadow-sm">
                                        <button @click="decreaseSelected()" :disabled="busy || selectedQuantity <= 1" class="px-4 py-2 text-gray-600 hover:bg-gray-50 transition-colors disabled:opacity-50">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" />
                                            </svg>
                                        </button>
                                        <span class="px-4 text-lg font-semibold text-gray-900 min-w-[50px] text-center" x-text="selectedQuantity"></span>
                                        <button @click="increaseSelected()" :disabled="busy || selectedQuantity >= stock" class="px-4 py-2 text-gray-600 hover:bg-gray-50 transition-colors disabled:opacity-50">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                            </svg>
                                        </button>
                                    </div>
                                </div>

                                <div class="text-sm text-gray-500">
                                    {{ $product->isCourse() ? 'Course enrollment' : ($product->isDigital() ? 'Digital access' : '₦' . number_format($product->price, 2) . ' each') }}
                                </div>
                            </div>

                            <button @click="add()"
                                    :disabled="busy"
                                    class="w-full bg-gradient-to-r from-red-600 to-red-700 text-white py-4 px-6 rounded-xl font-semibold text-lg hover:from-red-700 hover:to-red-800 transition-all duration-300 transform hover:-translate-y-0.5 shadow-lg hover:shadow-xl disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                                <span x-text="busy ? 'Adding to Cart...' : '{{ $product->isCourse() ? 'Enroll Now' : ($product->isDigital() ? 'Buy Digital Product' : 'Add to Cart') }}'"></span>
                            </button>

                            <!-- Quick Actions -->
                            <div class="grid grid-cols-2 gap-3">
                                    <a href="{{ route('shop', ['category' => $product->category->slug]) }}" 
                                       class="flex items-center justify-center gap-2 border-2 border-red-600 text-red-600 py-3 px-4 rounded-xl font-semibold hover:bg-red-50 transition-all duration-300">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                                        </svg>
                                        View Similar
                                    </a>
                                    <a href="{{ route('contact') }}" 
                                       class="flex items-center justify-center gap-2 border-2 border-gray-300 text-gray-700 py-3 px-4 rounded-xl font-semibold hover:bg-gray-50 transition-all duration-300">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                        </svg>
                                        Contact Us
                                    </a>
                                </div>
                            </div>
                    @else
                        <!-- Out of Stock -->
                        <div class="space-y-4">
                            <button disabled
                                    class="w-full bg-gray-200 text-gray-500 py-4 px-6 rounded-xl font-semibold cursor-not-allowed flex items-center justify-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                                </svg>
                                {{ $product->vendorIsAvailable() ? 'Out of Stock' : 'Vendor Unavailable' }}
                            </button>

                            <!-- Notify Button -->
                            <button class="w-full bg-blue-600 text-white py-4 px-6 rounded-xl font-semibold hover:bg-blue-700 transition-all duration-300 flex items-center justify-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                </svg>
                                {{ $product->unavailableReason() ?: 'Notify Me When Available' }}
                            </button>
                        </div>
                    @endif
                </div>

                <!-- Product Features -->
                <div class="grid grid-cols-2 gap-4 pt-6 border-t border-gray-100">
                    <div class="flex items-start gap-3">
                        <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-gray-900">Quality Guarantee</p>
                            <p class="text-xs text-gray-500">30-day return policy</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3">
                        <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-gray-900">Secure Payment</p>
                            <p class="text-xs text-gray-500">100% secure transactions</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3">
                        <div class="w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-gray-900">Free Shipping</p>
                            <p class="text-xs text-gray-500">On orders over ₦{{ number_format(\App\Helpers\SettingsHelper::freeShippingThreshold(), 0) }}</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3">
                        <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-gray-900">24/7 Support</p>
                            <p class="text-xs text-gray-500">Dedicated customer service</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Related Products Section -->
    @if($this->relatedProducts->count() > 0)
        <div class="mt-16 bg-white py-16">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-12">
                    <span class="inline-block px-4 py-1.5 bg-red-100 text-red-700 rounded-full text-sm font-semibold mb-3">
                        You May Also Like
                    </span>
                    <h2 class="text-3xl lg:text-4xl font-bold text-gray-900">Related Products</h2>
                    <p class="text-gray-600 mt-2">Customers who bought this also loved these items</p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    @foreach($this->relatedProducts as $relatedProduct)
                        @php
                            $cartQuantity = isset($cart[$relatedProduct->id]) 
                                ? $cart[$relatedProduct->id]['quantity'] 
                                : 0;
                            $isInCart = $cartQuantity > 0;
                        @endphp
                        
                        <div class="group bg-white rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-500 overflow-hidden modern-card">
                            <a href="{{ route('product.show', $relatedProduct->slug) }}" class="block">
                                <div class="relative overflow-hidden bg-gradient-to-br from-gray-50 to-gray-100">
                                    @if($relatedProduct->images && isset($relatedProduct->images[0]))
                                        <img src="{{ asset('storage/' . $relatedProduct->images[0]) }}" 
                                             alt="{{ $relatedProduct->name }}" 
                                             class="w-full h-64 object-cover group-hover:scale-105 transition-transform duration-700">
                                    @else
                                        <div class="w-full h-64 flex items-center justify-center">
                                            <svg class="w-16 h-16 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                        </div>
                                    @endif

                                    @if($relatedProduct->compare_price && $relatedProduct->compare_price > $relatedProduct->price)
                                        <div class="absolute top-3 left-3">
                                            <span class="px-2 py-1 bg-red-500 text-white text-xs font-bold rounded-lg">
                                                -{{ round((($relatedProduct->compare_price - $relatedProduct->price) / $relatedProduct->compare_price) * 100) }}%
                                            </span>
                                        </div>
                                    @endif
                                </div>
                            </a>

                            <div class="p-5">
                                <a href="{{ route('product.show', $relatedProduct->slug) }}" class="block">
                                    <h3 class="font-bold text-gray-900 text-lg mb-1 group-hover:text-red-600 transition-colors line-clamp-2">
                                        {{ $relatedProduct->name }}
                                    </h3>
                                    <p class="text-sm text-gray-500 mb-3">{{ $relatedProduct->category->name }}</p>
                                </a>

                                <div class="mb-4">
                                    <p class="text-xl font-bold text-gray-900">
                                        ₦{{ number_format($relatedProduct->price, 2) }}
                                    </p>
                                    @if($relatedProduct->compare_price && $relatedProduct->compare_price > $relatedProduct->price)
                                        <p class="text-sm text-gray-500 line-through">
                                            ₦{{ number_format($relatedProduct->compare_price, 2) }}
                                        </p>
                                    @endif
                                </div>

                                <div x-data="cartCard({ productId: {{ $relatedProduct->id }}, stock: {{ $relatedProduct->tracksInventory() ? $relatedProduct->quantity : 9999 }}, initialQuantity: {{ $cartQuantity }} })">
                                    <div x-show="quantity > 0" class="flex items-center justify-between bg-gray-50 rounded-xl p-2">
                                        <div class="flex items-center gap-2">
                                            <button @click="decrement()" :disabled="busy" class="w-8 h-8 flex items-center justify-center bg-white text-red-600 rounded-lg shadow-sm hover:shadow transition-all disabled:opacity-50">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" />
                                                </svg>
                                            </button>
                                            <span class="text-lg font-bold text-gray-900 w-6 text-center" x-text="quantity"></span>
                                            <button @click="increment()" :disabled="busy || quantity >= stock" class="w-8 h-8 flex items-center justify-center bg-red-600 text-white rounded-lg shadow-sm hover:bg-red-700 transition-all disabled:opacity-50">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                                </svg>
                                            </button>
                                        </div>
                                        <button @click="remove()" :disabled="busy" class="text-sm font-medium text-red-600 hover:text-red-700 disabled:opacity-50">
                                            Remove
                                        </button>
                                    </div>
                                    <button x-show="quantity === 0" @click="add(1)" :disabled="busy || {{ $relatedProduct->isPurchasable() ? 'false' : 'true' }}" class="w-full bg-gradient-to-r from-red-600 to-red-700 text-white py-2.5 px-4 rounded-xl font-semibold hover:from-red-700 hover:to-red-800 transition-all duration-300 disabled:opacity-50">
                                        <span x-text="busy ? 'Adding...' : '{{ $relatedProduct->isCourse() ? 'Enroll Now' : ($relatedProduct->isDigital() ? 'Buy Digital Product' : 'Add to Cart') }}'"></span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    <!-- Recently Viewed Section -->
    @if($this->recentlyViewed->count() > 0)
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
            <div class="text-center mb-12">
                <span class="inline-block px-4 py-1.5 bg-blue-100 text-blue-700 rounded-full text-sm font-semibold mb-3">
                    Keep Exploring
                </span>
                <h2 class="text-3xl lg:text-4xl font-bold text-gray-900">Recently Viewed</h2>
                <p class="text-gray-600 mt-2">Products you've been checking out</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach($this->recentlyViewed as $recentProduct)
                    @php
                        $cartQuantity = isset($cart[$recentProduct->id]) 
                            ? $cart[$recentProduct->id]['quantity'] 
                            : 0;
                        $isInCart = $cartQuantity > 0;
                    @endphp
                    
                    <div class="group bg-white rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-500 overflow-hidden modern-card">
                        <a href="{{ route('product.show', $recentProduct->slug) }}" class="block">
                            <div class="relative overflow-hidden bg-gradient-to-br from-gray-50 to-gray-100">
                                @if($recentProduct->images && isset($recentProduct->images[0]))
                                    <img src="{{ asset('storage/' . $recentProduct->images[0]) }}" 
                                         alt="{{ $recentProduct->name }}" 
                                         class="w-full h-64 object-cover group-hover:scale-105 transition-transform duration-700">
                                @else
                                    <div class="w-full h-64 flex items-center justify-center">
                                        <svg class="w-16 h-16 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                        </svg>
                                    </div>
                                @endif
                            </div>
                        </a>

                        <div class="p-5">
                            <a href="{{ route('product.show', $recentProduct->slug) }}" class="block">
                                <h3 class="font-bold text-gray-900 text-lg mb-1 group-hover:text-red-600 transition-colors line-clamp-2">
                                    {{ $recentProduct->name }}
                                </h3>
                                <p class="text-sm text-gray-500 mb-3">{{ $recentProduct->category->name }}</p>
                            </a>

                            <div class="mb-4">
                                <p class="text-xl font-bold text-gray-900">
                                    ₦{{ number_format($recentProduct->price, 2) }}
                                </p>
                            </div>

                            <div x-data="cartCard({ productId: {{ $recentProduct->id }}, stock: {{ $recentProduct->tracksInventory() ? $recentProduct->quantity : 9999 }}, initialQuantity: {{ $cartQuantity }} })">
                                <div x-show="quantity > 0" class="flex items-center justify-between bg-gray-50 rounded-xl p-2">
                                    <div class="flex items-center gap-2">
                                        <button @click="decrement()" :disabled="busy" class="w-8 h-8 flex items-center justify-center bg-white text-red-600 rounded-lg shadow-sm hover:shadow transition-all disabled:opacity-50">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" />
                                            </svg>
                                        </button>
                                        <span class="text-lg font-bold text-gray-900 w-6 text-center" x-text="quantity"></span>
                                        <button @click="increment()" :disabled="busy || quantity >= stock" class="w-8 h-8 flex items-center justify-center bg-red-600 text-white rounded-lg shadow-sm hover:bg-red-700 transition-all disabled:opacity-50">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                            </svg>
                                        </button>
                                    </div>
                                    <button @click="remove()" :disabled="busy" class="text-sm font-medium text-red-600 hover:text-red-700 disabled:opacity-50">
                                        Remove
                                    </button>
                                </div>
                                <button x-show="quantity === 0" @click="add(1)" :disabled="busy || {{ $recentProduct->isPurchasable() ? 'false' : 'true' }}" class="w-full bg-gradient-to-r from-red-600 to-red-700 text-white py-2.5 px-4 rounded-xl font-semibold hover:from-red-700 hover:to-red-800 transition-all duration-300 disabled:opacity-50">
                                    <span x-text="busy ? 'Adding...' : '{{ $recentProduct->isCourse() ? 'Enroll Now' : ($recentProduct->isDigital() ? 'Buy Digital Product' : 'Add to Cart') }}'"></span>
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Comments Section -->
    <div id="customer-reviews" class="scroll-mt-28 bg-slate-950 py-16 text-white sm:py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @livewire('product-comments', ['product' => $product])
        </div>
    </div>
</div>
