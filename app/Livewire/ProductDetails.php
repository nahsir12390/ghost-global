<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Product;
use App\Models\Wishlist;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class ProductDetails extends Component
{
    private const PRODUCT_PAGE_CACHE_TTL_MINUTES = 15;

    public Product $product;
    public $quantity = 1;
    public $selectedImage = 0;
    public $cart = [];
    public $wishlistStatus = false;
    public $loading = false;
    public $addingToCart = false;
    public $updatingWishlist = false;
    
    protected $listeners = [
        'cartUpdated' => 'loadCart',
        'wishlist-updated' => 'loadWishlistStatus',
        'cart-add' => 'handleCartAdd',
        'cart-increment' => 'handleCartIncrement',
        'cart-decrement' => 'handleCartDecrement',
        'cart-remove' => 'handleCartRemove',
        'toggleWishlist' => 'toggleWishlist',
    ];
    

    public function mount(Product $product)
    {
        $this->product = $product->load([
            'category:id,name,slug',
            'vendor:id,name,role,store_name,store_slug,vendor_is_active,verified_at,verification_status',
        ]);
        $this->loadCart();
        $this->loadWishlistStatus();
        
        // Track recently viewed
        $this->trackRecentlyViewed();
    }
    
    public function trackRecentlyViewed()
    {
        $recentlyViewed = session()->get('recently_viewed', []);
        
        // Remove if already exists
        if (($key = array_search($this->product->id, $recentlyViewed)) !== false) {
            unset($recentlyViewed[$key]);
        }
        
        // Add to beginning
        array_unshift($recentlyViewed, $this->product->id);
        
        // Keep only last 5
        $recentlyViewed = array_slice($recentlyViewed, 0, 5);
        
        session()->put('recently_viewed', $recentlyViewed);
    }
    
    public function loadCart()
    {
        $this->cart = session()->get('cart', []);
    }
    
    public function loadWishlistStatus()
    {
        if (Auth::check()) {
            $this->wishlistStatus = Wishlist::where('user_id', Auth::id())
                ->where('product_id', $this->product->id)
                ->exists();
        } else {
            $this->wishlistStatus = false;
        }
    }
    
    public function getCartQuantity()
    {
        return $this->cart[$this->product->id]['quantity'] ?? 0;
    }
    
    public function isInCart()
    {
        return isset($this->cart[$this->product->id]);
    }
    
    public function incrementQuantity()
    {
        if (! $this->product->tracksInventory() || $this->quantity < $this->product->quantity) {
            $this->quantity++;
        }
    }
    
    public function decrementQuantity()
    {
        if ($this->quantity > 1) {
            $this->quantity--;
        }
    }
    
    public function incrementCartQuantity()
    {
        if ($this->isInCart()) {
            $cartQuantity = $this->getCartQuantity();
            
            // Check stock
            if ($this->product->tracksInventory() && $cartQuantity >= $this->product->quantity) {
                $this->dispatch('notify', 
                    message: 'Only ' . $this->product->quantity . ' items in stock!', 
                    type: 'error'
                );
                return;
            }
            
            $cart = session()->get('cart', []);
            $cart[$this->product->id]['quantity']++;
            session()->put('cart', $cart);
            $this->cart = $cart;
            
            $this->dispatch('cartUpdated');
            $this->dispatch('notify', 
                message: 'Quantity updated!', 
                type: 'success'
            );
        }
    }
    
    public function decrementCartQuantity()
    {
        if ($this->isInCart()) {
            $cartQuantity = $this->getCartQuantity();
            
            if ($cartQuantity <= 1) {
                $this->removeFromCart();
                return;
            }
            
            $cart = session()->get('cart', []);
            $cart[$this->product->id]['quantity']--;
            session()->put('cart', $cart);
            $this->cart = $cart;
            
            $this->dispatch('cartUpdated');
            $this->dispatch('notify', 
                message: 'Quantity updated!', 
                type: 'success'
            );
        }
    }
    
    public function addToCart()
    {
        $this->addingToCart = true;
        
        try {
            if (! $this->product->isPurchasable()) {
                $this->dispatch('notify',
                    message: $this->product->unavailableReason() ?: 'This product is currently unavailable.',
                    type: 'error'
                );
                return;
            }

            // Check stock
            if ($this->product->tracksInventory() && $this->product->quantity < $this->quantity) {
                $this->dispatch('notify', 
                    message: 'Only ' . $this->product->quantity . ' items in stock!', 
                    type: 'error'
                );
                return;
            }
            
            $cart = session()->get('cart', []);
            $productId = $this->product->id;
            
            if (isset($cart[$productId])) {
                $newQuantity = $cart[$productId]['quantity'] + $this->quantity;
                
                // Check total quantity doesn't exceed stock
                if ($this->product->tracksInventory() && $newQuantity > $this->product->quantity) {
                    $this->dispatch('notify', 
                        message: 'Cannot add more than ' . $this->product->quantity . ' items!', 
                        type: 'error'
                    );
                    return;
                }
                
                $cart[$productId]['quantity'] = $newQuantity;
            } else {
                $cart[$productId] = [
                    'product_id' => $this->product->id,
                    'name' => $this->product->name,
                    'price' => $this->product->price,
                    'quantity' => $this->quantity,
                    'image' => $this->product->images[0] ?? null,
                    'slug' => $this->product->slug,
                    'product_type' => $this->product->product_type,
                    'requires_shipping' => $this->product->requiresShipping(),
                ];
            }
            
            session()->put('cart', $cart);
            $this->cart = $cart;
            
            $this->dispatch('cartUpdated');
            $this->dispatch('notify', 
                message: 'Added ' . $this->quantity . ' item(s) to cart!', 
                type: 'success'
            );
            
            // Reset quantity
            $this->quantity = 1;
        } finally {
            $this->addingToCart = false;
        }
    }
    
    public function removeFromCart()
    {
        $cart = session()->get('cart', []);
        $productId = $this->product->id;
        
        if (isset($cart[$productId])) {
            unset($cart[$productId]);
            session()->put('cart', $cart);
            $this->cart = $cart;
            
            $this->dispatch('cartUpdated');
            $this->dispatch('notify', 
                message: 'Item removed from cart!', 
                type: 'success'
            );
        }
    }
    
    public function handleCartAdd($productId)
    {
        if ($productId == $this->product->id) {
            $this->addToCart();
            return;
        }
        
        // Handle related product add
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
            if ($product->tracksInventory() && $cart[$productId]['quantity'] >= $product->quantity) {
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
                'image' => $product->images[0] ?? null,
                'slug' => $product->slug,
                'product_type' => $product->product_type,
                'requires_shipping' => $product->requiresShipping(),
            ];
        }
        
        session()->put('cart', $cart);
        $this->cart = $cart;
        
        $this->dispatch('cartUpdated');
        $this->dispatch('notify', 
            message: 'Added to cart!', 
            type: 'success'
        );
    }
    
    public function handleCartIncrement($productId)
    {
        if ($productId == $this->product->id) {
            $this->incrementCartQuantity();
            return;
        }
        
        $product = Product::with('vendor')->find($productId);
        
        if (!$product) {
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
            if ($product->tracksInventory() && $cart[$productId]['quantity'] >= $product->quantity) {
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
            $this->dispatch('notify', 
                message: 'Quantity updated!', 
                type: 'success'
            );
        }
    }
    
    public function handleCartDecrement($productId)
    {
        if ($productId == $this->product->id) {
            $this->decrementCartQuantity();
            return;
        }
        
        $cart = session()->get('cart', []);
        
        if (isset($cart[$productId])) {
            if ($cart[$productId]['quantity'] <= 1) {
                $this->handleCartRemove($productId);
                return;
            }
            
            $cart[$productId]['quantity']--;
            session()->put('cart', $cart);
            $this->cart = $cart;
            
            $this->dispatch('cartUpdated');
            $this->dispatch('notify', 
                message: 'Quantity updated!', 
                type: 'success'
            );
        }
    }
    
    public function handleCartRemove($productId)
    {
        $cart = session()->get('cart', []);
        
        if (isset($cart[$productId])) {
            unset($cart[$productId]);
            session()->put('cart', $cart);
            $this->cart = $cart;
            
            $this->dispatch('cartUpdated');
            $this->dispatch('notify', 
                message: 'Item removed from cart!', 
                type: 'success'
            );
        }
    }
    
    public function toggleWishlist($productId = null)
    {
        // If productId is provided, check if it matches current product
        if ($productId && $productId != $this->product->id) {
            return;
        }
        
        $this->updatingWishlist = true;
        
        try {
            if (!Auth::check()) {
                $this->dispatch('notify', 
                    message: 'Please login to add items to your wishlist.', 
                    type: 'error'
                );
                return;
            }
            
            if ($this->wishlistStatus) {
                // Remove from wishlist
                Wishlist::where('user_id', Auth::id())
                    ->where('product_id', $this->product->id)
                    ->delete();
                    
                $this->wishlistStatus = false;
                $this->dispatch('notify', 
                    message: 'Removed from wishlist!', 
                    type: 'success'
                );
            } else {
                // Add to wishlist
                Wishlist::create([
                    'user_id' => Auth::id(),
                    'product_id' => $this->product->id
                ]);
                
                $this->wishlistStatus = true;
                $this->dispatch('notify', 
                    message: 'Added to wishlist!', 
                    type: 'success'
                );
            }
            
            // Dispatch event to update wishlist counter
            $this->dispatch('wishlistUpdated');
        } catch (\Exception $e) {
            $this->dispatch('notify', 
                message: 'Wishlist feature is not available right now. Please try again later.',
                type: 'error'
            );
        } finally {
            $this->updatingWishlist = false;
        }
    }
    
    public function selectImage($index)
    {
        $this->selectedImage = $index;
    }
    
    public function getRelatedProductsProperty()
    {
        return Cache::remember(
            'related_products_'.$this->product->id,
            now()->addMinutes(self::PRODUCT_PAGE_CACHE_TTL_MINUTES),
            fn () => Product::query()
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
                    'product_type',
                    'is_active',
                    'created_at',
                ])
                ->where('category_id', $this->product->category_id)
                ->whereKeyNot($this->product->id)
                ->where('is_active', true)
                ->with([
                    'category:id,name,slug',
                    'vendor:id,name,role,store_name,store_slug,vendor_is_active,verified_at,verification_status',
                ])
                ->latest()
                ->take(4)
                ->get()
        );
    }
    
    public function getRecentlyViewedProperty()
    {
        $recentlyViewedIds = session()->get('recently_viewed', []);
        
        // Remove current product
        $recentlyViewedIds = array_diff($recentlyViewedIds, [$this->product->id]);
        
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
                'product_type',
                'is_active',
                'created_at',
            ])
            ->whereIn('id', $recentlyViewedIds)
            ->where('is_active', true)
            ->with([
                'category:id,name,slug',
                'vendor:id,name,role,store_name,store_slug,vendor_is_active,verified_at,verification_status',
            ])
            ->take(4)
            ->get();
    }
    
    public function render()
    {
        return view('livewire.product-details');
    }
}
