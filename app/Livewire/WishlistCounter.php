<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Wishlist;
use Illuminate\Support\Facades\Auth;

class WishlistCounter extends Component
{
    public $count = 0;

    protected $listeners = ['wishlistUpdated' => 'updateCount'];

    public function mount()
    {
        $this->updateCount();
    }

    public function updateCount()
    {
        if (Auth::check()) {
            $this->count = Wishlist::where('user_id', Auth::id())->count();
        } else {
            $this->count = 0;
        }
    }

    public function render()
    {
        return view('livewire.wishlist-counter');
    }
}