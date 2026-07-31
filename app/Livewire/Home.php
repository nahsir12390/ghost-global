<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Product;
use App\Models\Category;
use App\Models\Wishlist;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class Home extends Component
{
    private const STOREFRONT_CACHE_TTL_MINUTES = 10;

    public $featuredProducts = [];
    public $newArrivals = [];
    public $bestSellingProducts = [];
    public $categories = [];
    public $onSaleProducts = [];
    public $cart = [];
    public $wishlistStatus = [];
    public $updatingCart = []; // Track which products are being updated
    public $addingToCart = []; // Track which products are being added to cart
    public $updatingWishlist = []; // Track which products are being updated in wishlist
    
    protected $listeners = [
        'cartUpdated' => 'loadCart',
        'update-cart-count' => 'loadCart',
        'refresh-cart' => 'loadCart'
    ];
    
    public function mount()
    {
        $this->loadFeaturedProducts();
        $this->loadNewArrivals();
        $this->loadBestSellingProducts();
        $this->loadCategories();
        $this->loadOnSaleProducts();
        $this->loadCart();
        $this->loadWishlistStatus();
    }
    
    public function loadCart()
    {
        $this->cart = session()->get('cart', []);
    }
    
    public function loadWishlistStatus()
    {
        if (Auth::check()) {
            $userWishlist = Wishlist::where('user_id', Auth::id())
                ->pluck('product_id')
                ->flip();
            
            // Initialize wishlist status for all products
            $allProducts = collect([...$this->featuredProducts, ...$this->newArrivals, ...$this->bestSellingProducts, ...$this->onSaleProducts])
                ->unique('id');
            
            foreach ($allProducts as $product) {
                $this->wishlistStatus[$product->id] = $userWishlist->has($product->id);
            }
        }
    }
    
    public function loadFeaturedProducts()
    {
        $this->featuredProducts = Cache::remember(
            'storefront_featured_products',
            now()->addMinutes(self::STOREFRONT_CACHE_TTL_MINUTES),
            function () {
                $limit = 8;

                $featuredProducts = $this->baseStorefrontProductQuery()
                    ->where('is_featured', true)
                    ->take($limit)
                    ->get();

                if ($featuredProducts->count() >= $limit) {
                    return $featuredProducts;
                }

                $fallbackProducts = $this->baseStorefrontProductQuery()
                    ->whereNotIn('id', $featuredProducts->pluck('id'))
                    ->latest()
                    ->take($limit - $featuredProducts->count())
                    ->get();

                return $featuredProducts->concat($fallbackProducts);
            }
        );
    }
    
    public function loadNewArrivals()
    {
        $this->newArrivals = Cache::remember(
            'storefront_new_arrivals',
            now()->addMinutes(self::STOREFRONT_CACHE_TTL_MINUTES),
            fn () => $this->baseStorefrontProductQuery()
                ->latest()
                ->take(8)
                ->get()
        );
    }
    
    public function loadBestSellingProducts()
    {
        $this->bestSellingProducts = Cache::remember(
            'storefront_best_sellers',
            now()->addMinutes(self::STOREFRONT_CACHE_TTL_MINUTES),
            fn () => $this->baseStorefrontProductQuery()
                ->withCount(['orderItems as total_sold'])
                ->orderByDesc('total_sold')
                ->take(8)
                ->get()
        );
    }
    
    public function loadCategories()
    {
        $this->categories = Cache::remember(
            'storefront_top_categories',
            now()->addMinutes(self::STOREFRONT_CACHE_TTL_MINUTES),
            fn () => Category::query()
                ->select(['id', 'name', 'slug', 'image', 'is_active'])
                ->where('is_active', true)
                ->withCount([
                    'products as products_count' => fn ($query) => $query->where('is_active', true),
                ])
                ->orderByDesc('products_count')
                ->take(6)
                ->get()
        );
    }
    
    public function loadOnSaleProducts()
    {
        $this->onSaleProducts = Cache::remember(
            'storefront_on_sale_products',
            now()->addMinutes(self::STOREFRONT_CACHE_TTL_MINUTES),
            fn () => $this->baseStorefrontProductQuery()
                ->whereNotNull('compare_price')
                ->whereColumn('compare_price', '>', 'price')
                ->latest()
                ->take(6)
                ->get()
        );
    }

    private function baseStorefrontProductQuery()
    {
        return Product::query()
            ->select([
                'id',
                'vendor_id',
                'category_id',
                'name',
                'slug',
                'price',
                'compare_price',
                'quantity',
                'images',
                'is_active',
                'created_at',
            ])
            ->where('is_active', true)
            ->with([
                'category:id,name,slug',
                'vendor:id,name,role,store_name,store_slug,vendor_is_active,verified_at,verification_status',
            ]);
    }
    
    // Helper method to get quantity from cart
    public function getCartQuantity($productId)
    {
        return $this->cart[$productId]['quantity'] ?? 0;
    }
    
    // Check if product is in cart
    public function isInCart($productId)
    {
        return isset($this->cart[$productId]) && $this->cart[$productId]['quantity'] > 0;
    }
    
    // Wishlist methods
    public function toggleWishlist($productId)
    {
        if (!Auth::check()) {
            $this->dispatch('notify', 
                message: 'Please login to add items to your wishlist.', 
                type: 'error'
            );
            return;
        }

        $this->updatingWishlist[$productId] = true;
        
        try {
            $product = Product::with('vendor')->find($productId);
            
            if (!$product) {
                $this->dispatch('notify', 
                    message: 'Product not found!', 
                    type: 'error'
                );
                return;
            }
            
            // Check if already in wishlist
            $existingWishlist = Wishlist::where('user_id', Auth::id())
                ->where('product_id', $productId)
                ->first();
            
            if ($existingWishlist) {
                // Remove from wishlist
                $existingWishlist->delete();
                $this->wishlistStatus[$productId] = false;
                $this->dispatch('notify', 
                    message: 'Removed from wishlist!', 
                    type: 'success'
                );
            } else {
                // Add to wishlist
                Wishlist::create([
                    'user_id' => Auth::id(),
                    'product_id' => $productId
                ]);
                $this->wishlistStatus[$productId] = true;
                $this->dispatch('notify', 
                    message: 'Added to wishlist!', 
                    type: 'success'
                );
            }
            
            // Dispatch event to update wishlist counter
            $this->dispatch('wishlistUpdated');
        } finally {
            $this->updatingWishlist[$productId] = false;
        }
    }
    
    // Check if product is in wishlist
    public function isInWishlist($productId)
    {
        return $this->wishlistStatus[$productId] ?? false;
    }
    
    // Add to cart method
    public function addToCart($productId)
    {
        $this->addingToCart[$productId] = true;
        
        try {
            $product = Product::with('vendor')->find($productId);
            
            if (!$product) {
                $this->dispatch('notify', 
                    message: 'Product not found!', 
                    type: 'error'
                );
                return;
            }
            
            if (! $product->isPurchasable()) {
                $this->dispatch('notify', 
                    message: $product->unavailableReason() ?: 'This product is currently unavailable.',
                    type: 'error'
                );
                return;
            }
            
            $cart = session()->get('cart', []);
            
            if (isset($cart[$productId])) {
                // Check if we can add more items
                if ($cart[$productId]['quantity'] >= $product->quantity) {
                    $this->dispatch('notify', 
                        message: 'Only ' . $product->quantity . ' items in stock!', 
                        type: 'error'
                    );
                    return;
                }
                $cart[$productId]['quantity']++;
            } else {
                $cart[$productId] = [
                    'product_id' => $product->id,
                    'name' => $product->name,
                    'price' => $product->price,
                    'quantity' => 1,
                    'image' => $product->main_image,
                    'slug' => $product->slug,
                ];
            }
            
            session()->put('cart', $cart);
            $this->cart = $cart;
            
            // Dispatch events for real-time updates
            $this->dispatch('cartUpdated');
            $this->dispatch('update-cart-count');
            $this->dispatch('notify', 
                message: 'Added to cart!', 
                type: 'success'
            );
        } finally {
            $this->addingToCart[$productId] = false;
        }
    }
    
    // Increment quantity method
    public function incrementQuantity($productId)
    {
        $this->updatingCart[$productId] = true;
        
        try {
            $product = Product::with('vendor')->find($productId);
            
            if (!$product) {
                $this->dispatch('notify', 
                    message: 'Product not found!', 
                    type: 'error'
                );
                return;
            }

            if (! $product->isPurchasable()) {
                $this->dispatch('notify',
                    message: $product->unavailableReason() ?: 'This product is currently unavailable.',
                    type: 'error'
                );
                return;
            }
            
            $cart = session()->get('cart', []);
            
            if (isset($cart[$productId])) {
                // Check stock limit
                if ($cart[$productId]['quantity'] >= $product->quantity) {
                    $this->dispatch('notify', 
                        message: 'Only ' . $product->quantity . ' items in stock!', 
                        type: 'error'
                    );
                    return;
                }
                
                $cart[$productId]['quantity']++;
                session()->put('cart', $cart);
                $this->cart = $cart;
                
                $this->dispatch('cartUpdated');
                $this->dispatch('update-cart-count');
                $this->dispatch('notify', 
                    message: 'Quantity updated!', 
                    type: 'success'
                );
            }
        } finally {
            $this->updatingCart[$productId] = false;
        }
    }
    
    // Decrement quantity method
    public function decrementQuantity($productId)
    {
        $this->updatingCart[$productId] = true;
        
        try {
            $cart = session()->get('cart', []);
            
            if (isset($cart[$productId])) {
                if ($cart[$productId]['quantity'] <= 1) {
                    $this->removeFromCart($productId);
                    return;
                }
                
                $cart[$productId]['quantity']--;
                session()->put('cart', $cart);
                $this->cart = $cart;
                
                $this->dispatch('cartUpdated');
                $this->dispatch('update-cart-count');
                $this->dispatch('notify', 
                    message: 'Quantity updated!', 
                    type: 'success'
                );
            }
        } finally {
            $this->updatingCart[$productId] = false;
        }
    }
    
    // Remove from cart method
    public function removeFromCart($productId)
    {
        $cart = session()->get('cart', []);
        
        if (isset($cart[$productId])) {
            unset($cart[$productId]);
            session()->put('cart', $cart);
            $this->cart = $cart;
            
            $this->dispatch('cartUpdated');
            $this->dispatch('update-cart-count');
            $this->dispatch('notify', 
                message: 'Item removed from cart!', 
                type: 'success'
            );
        }
    }
    
    public function render()
    {
        return view('livewire.home');
    }
}
