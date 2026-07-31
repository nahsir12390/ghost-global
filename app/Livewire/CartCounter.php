<?php

namespace App\Livewire;

use Livewire\Component;

class CartCounter extends Component
{
    public $cartCount = 0;
    public $loading = false;
    
    protected $listeners = [
        'cartUpdated' => 'updateCartCount',
        'update-cart-count' => 'updateCartCount'
    ];
    
    public function mount()
    {
        $this->updateCartCount();
    }
    
    public function updateCartCount()
    {
        $this->loading = true;
        $cart = session('cart', []);
        $this->cartCount = 0;
        
        foreach ($cart as $item) {
            $this->cartCount += $item['quantity'];
        }
        
        $this->loading = false;
    }
    
    public function render()
    {
        return view('livewire.cart-counter', [
            'cartCount' => $this->cartCount,
            'loading' => $this->loading
        ]);
    }
}