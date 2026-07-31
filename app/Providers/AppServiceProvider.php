<?php

namespace App\Providers;

use App\Models\Order;
use App\Models\User;
use App\Livewire\Cart;
use Livewire\Livewire;
use App\Livewire\CartCounter;
use App\Livewire\Notification;
use App\Livewire\Admin\Profile;
use App\Livewire\WishlistButton;
use App\Livewire\WishlistCounter;
use App\Livewire\Admin\Orders\Show;
use App\Livewire\WishlistComponent;
use App\Livewire\Admin\Orders\Index;
use App\Observers\OrderObserver;
use App\Observers\UserObserver;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use App\Livewire\Admin\Settings\General;
use App\Livewire\Admin\Settings\Payment;
use App\Livewire\Admin\Settings\Shipping;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register model observers
        Order::observe(OrderObserver::class);
        User::observe(UserObserver::class);
        
        View::composer('layouts.admin', function ($view) {
            $pendingOrdersCount = Order::where('status', 'pending')->count();
            $view->with('pendingOrdersCount', $pendingOrdersCount);
        });
        
        // Register Livewire components
        Livewire::component('cart-counter', CartCounter::class);
        Livewire::component('cart', Cart::class);
        Livewire::component('notification', Notification::class);
       Livewire::component('wishlist-counter', \App\Livewire\WishlistCounter::class);
Livewire::component('wishlist-component', \App\Livewire\WishlistComponent::class);
Livewire::component('wishlist-button', \App\Livewire\WishlistButton::class);
        Livewire::component('profile.update-profile-photo', \App\Livewire\Profile\UpdateProfilePhoto::class);
        Livewire::component('admin.settings.general', General::class);
        Livewire::component('admin.settings.payment', Payment::class);
        Livewire::component('admin.settings.shipping', Shipping::class);
        Livewire::component('admin.profile', Profile::class);
        Livewire::component('admin.orders.show', Show::class);
        Livewire::component('admin.orders.index', Index::class);
    }
}