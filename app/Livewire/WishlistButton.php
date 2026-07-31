<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Wishlist;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;

class WishlistButton extends Component
{
    public $productId;
    public $isInWishlist = false;
    public $product;

    public function mount($productId)
    {
        $this->productId = $productId;
        $this->product = Product::find($productId);
        $this->checkWishlistStatus();
    }

    public function checkWishlistStatus()
    {
        if (Auth::check()) {
            $this->isInWishlist = Wishlist::where('user_id', Auth::id())
                ->where('product_id', $this->productId)
                ->exists();
        }
    }

    public function toggleWishlist()
    {
        if (!Auth::check()) {
            session()->flash('error', 'Please login to add items to your wishlist.');
            return redirect()->route('login');
        }

        $existing = Wishlist::where('user_id', Auth::id())
            ->where('product_id', $this->productId)
            ->first();

        if ($existing) {
            $existing->delete();
            $this->isInWishlist = false;
            session()->flash('success', 'Removed from wishlist.');
        } else {
            Wishlist::create([
                'user_id' => Auth::id(),
                'product_id' => $this->productId
            ]);
            $this->isInWishlist = true;
            session()->flash('success', 'Added to wishlist!');
        }

        // Dispatch events
        $this->dispatch('wishlistUpdated');
    }

    public function render()
    {
        return view('livewire.wishlist-button');
    }
}