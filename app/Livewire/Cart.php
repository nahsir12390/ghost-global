<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Product;
use App\Helpers\SettingsHelper;

class Cart extends Component
{
    public $cart = [];
    public $subtotal = 0;
    public $tax = 0;
    public $taxRate = 0;
    public $shipping = 0;
    public $total = 0;
    public $loading = false;
    public $updatingProductId = null;

    protected $listeners = [
        'cartUpdated' => 'loadCart',
        'cart-updated' => 'loadCart',
        'refresh-cart' => 'loadCart'
    ];

    public function mount()
    {
        $this->loadCart();
    }

    public function loadCart()
    {
        $this->cart = session('cart', []);
        $this->calculateTotals();
    }

    public function calculateTotals()
    {
        $this->subtotal = 0;
        foreach ($this->cart as $item) {
            $this->subtotal += $item['price'] * $item['quantity'];
        }

        $breakdown = SettingsHelper::calculateOrderBreakdown($this->subtotal);

        $this->taxRate = $breakdown['service_fee_rate'] / 100;
        $this->tax = $breakdown['service_fee'];
        $this->shipping = $breakdown['shipping'];
        $this->total = $breakdown['total'];
    }

    public function updateQuantity($productId, $quantity)
    {
        if ($quantity <= 0) {
            $this->removeItem($productId);
            return;
        }

        $cart = session('cart', []);
        if (isset($cart[$productId])) {
            // Get product to check stock
            $product = Product::with('vendor')->find($productId);

            if ($product && ! $product->isPurchasable()) {
                $this->dispatch('notify',
                    message: $product->unavailableReason() ?: 'This product is currently unavailable.',
                    type: 'error'
                );
                return;
            }
            
            if ($product && $quantity > $product->quantity) {
                $this->dispatch('notify', 
                    message: 'Only ' . $product->quantity . ' items in stock!', 
                    type: 'error'
                );
                return;
            }
            
            $this->updatingProductId = $productId;
            $cart[$productId]['quantity'] = $quantity;
            session(['cart' => $cart]);
            
            $this->dispatch('cartUpdated');
            $this->dispatch('notify', 
                message: 'Quantity updated!', 
                type: 'success'
            );
            
            $this->loadCart();
            $this->updatingProductId = null;
        }
    }

    public function incrementQuantity($productId)
    {
        $this->updateQuantity($productId, ($this->cart[$productId]['quantity'] ?? 0) + 1);
    }

    public function decrementQuantity($productId)
    {
        $this->updateQuantity($productId, ($this->cart[$productId]['quantity'] ?? 0) - 1);
    }

    public function removeItem($productId)
    {
        $this->loading = true;
        
        $cart = session('cart', []);
        if (isset($cart[$productId])) {
            unset($cart[$productId]);
            session(['cart' => $cart]);
            $this->loadCart();
            $this->dispatch('cartUpdated');
            $this->dispatch('notify', 
                message: 'Item removed from cart!', 
                type: 'success'
            );
        }
        
        $this->loading = false;
    }

    public function clearCart()
    {
        $this->loading = true;
        
        session(['cart' => []]);
        $this->loadCart();
        $this->dispatch('cartUpdated');
        $this->dispatch('notify', 
            message: 'Cart cleared!', 
            type: 'success'
        );
        
        $this->loading = false;
    }

    public function checkout()
    {
        if (empty($this->cart)) {
            $this->dispatch('notify', 
                message: 'Your cart is empty!', 
                type: 'error'
            );
            return;
        }

        if (!auth()->check()) {
            return redirect()->route('login');
        }

        return redirect()->route('checkout');
    }

    public function render()
    {
        return view('livewire.cart', [
            'loading' => $this->loading,
            'updatingProductId' => $this->updatingProductId
        ]);
    }
}
