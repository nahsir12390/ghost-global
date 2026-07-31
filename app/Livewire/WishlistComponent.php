<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Wishlist;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;

class WishlistComponent extends Component
{
    public $wishlistItems = [];
    public $cart = [];

    public function mount()
    {
        $this->loadWishlist();
        $this->loadCart();
    }

    public function loadWishlist()
    {
        if (Auth::check()) {
            $this->wishlistItems = Wishlist::where('user_id', Auth::id())
                ->with('product')
                ->latest()
                ->get();
        } else {
            $this->wishlistItems = collect();
        }
    }

    public function loadCart()
    {
        $this->cart = session()->get('cart', []);
    }

    public function removeFromWishlist($wishlistId)
    {
        if (!Auth::check()) {
            session()->flash('error', 'Please login to manage your wishlist.');
            return;
        }

        $wishlist = Wishlist::where('user_id', Auth::id())
            ->where('id', $wishlistId)
            ->first();

        if ($wishlist) {
            $wishlist->delete();
            session()->flash('success', 'Product removed from wishlist.');
            $this->loadWishlist();
            
            // Dispatch event to update counter
            $this->dispatch('wishlistUpdated');
        }
    }

    public function addToCart($productId)
    {
        $product = Product::with('vendor')->find($productId);

        if (!$product) {
            session()->flash('error', 'Product not found.');
            return;
        }

        if (! $product->isPurchasable()) {
            session()->flash('error', $product->unavailableReason() ?: 'This product is currently unavailable.');
            return;
        }

        // Get current cart from session
        $cart = session()->get('cart', []);

        // Check if we can add more items
        if (isset($cart[$productId])) {
            // Get current quantity in cart
            $currentQuantity = $cart[$productId]['quantity'];
            
            // Check stock limit
            if ($currentQuantity >= $product->quantity) {
                session()->flash('error', 'Only ' . $product->quantity . ' items in stock!');
                return;
            }
            
            $cart[$productId]['quantity']++;
        } else {
            // Add new item to cart
            $cart[$productId] = [
                'product_id' => $product->id,
                'name' => $product->name,
                'price' => $product->price,
                'quantity' => 1,
                'image' => $product->main_image,
                'slug' => $product->slug
            ];
        }

        // Store cart in session
        session()->put('cart', $cart);
        
        // Update local cart data
        $this->cart = $cart;
        
        session()->flash('success', 'Product added to cart!');
        
        // Dispatch both events to ensure all components update
        $this->dispatch('cartUpdated');
        $this->dispatch('update-cart-count');
        $this->dispatch('refresh-cart');
        
        // Dispatch notification
        $this->dispatch('notify', 
            message: 'Product added to cart!',
            type: 'success'
        );
    }

    // Helper method to check if product is already in cart
    public function isInCart($productId)
    {
        return isset($this->cart[$productId]) && $this->cart[$productId]['quantity'] > 0;
    }
// Add these methods to your WishlistComponent class

public function clearWishlist()
{
    if (!Auth::check()) {
        session()->flash('error', 'Please login to manage your wishlist.');
        return;
    }

    Wishlist::where('user_id', Auth::id())->delete();
    session()->flash('success', 'Wishlist cleared successfully.');
    $this->loadWishlist();
    $this->dispatch('wishlistUpdated');
}

public function incrementQuantity($productId)
{
    $product = Product::with('vendor')->find($productId);

    if (!$product) {
        session()->flash('error', 'Product not found.');
        return;
    }

    if (! $product->isPurchasable()) {
        session()->flash('error', $product->unavailableReason() ?: 'This product is currently unavailable.');
        return;
    }

    $cart = session()->get('cart', []);
    
    if (isset($cart[$productId])) {
        // Check stock limit
        if ($cart[$productId]['quantity'] >= $product->quantity) {
            session()->flash('error', 'Only ' . $product->quantity . ' items in stock!');
            return;
        }
        
        $cart[$productId]['quantity']++;
        session()->put('cart', $cart);
        $this->cart = $cart;
        
        // Dispatch events
        $this->dispatch('cartUpdated');
        $this->dispatch('update-cart-count');
        $this->dispatch('refresh-cart');
        $this->dispatch('notify', 
            message: 'Quantity updated!',
            type: 'success'
        );
    }
}

public function decrementQuantity($productId)
{
    $cart = session()->get('cart', []);
    
    if (isset($cart[$productId])) {
        if ($cart[$productId]['quantity'] <= 1) {
            $this->removeFromCart($productId);
            return;
        }
        
        $cart[$productId]['quantity']--;
        session()->put('cart', $cart);
        $this->cart = $cart;
        
        // Dispatch events
        $this->dispatch('cartUpdated');
        $this->dispatch('update-cart-count');
        $this->dispatch('refresh-cart');
        $this->dispatch('notify', 
            message: 'Quantity updated!',
            type: 'success'
        );
    }
}

public function removeFromCart($productId)
{
    $cart = session()->get('cart', []);
    
    if (isset($cart[$productId])) {
        unset($cart[$productId]);
        session()->put('cart', $cart);
        $this->cart = $cart;
        
        // Dispatch events
        $this->dispatch('cartUpdated');
        $this->dispatch('update-cart-count');
        $this->dispatch('refresh-cart');
        $this->dispatch('notify', 
            message: 'Item removed from cart!',
            type: 'success'
        );
    }
}
    // Helper method to get cart quantity
    public function getCartQuantity($productId)
    {
        return $this->cart[$productId]['quantity'] ?? 0;
    }

    public function render()
    {
        return view('livewire.wishlist-component');
    }
}
