<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\OrderTrackingController;
use App\Http\Controllers\StorefrontController;
use App\Http\Controllers\WalletController;
use App\Http\Controllers\NewsletterController;
use App\Http\Controllers\ReferralController;
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\PwaController;
use App\Http\Controllers\WebPushSubscriptionController;
use App\Http\Controllers\VendorUpgradeController;
use App\Http\Controllers\Admin\{
    DashboardController,
    CategoryController,
    ProductController as AdminProductController,
    OrderController as AdminOrderController,
    UserController as AdminUserController,
    StaffController,
    SettingsController,
    NewsletterSubscriberController,
    DatabaseBackupController
};

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Public Routes
// Public Routes
Route::get('/test-payment', function() {
    return response()->json([
        'message' => 'Payment route is accessible',
        'csrf_token' => csrf_token()
    ]);
});
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/r/{code}', [ReferralController::class, 'capture'])->name('referrals.capture');
Route::get('/shop', [ShopController::class, 'index'])->name('shop');
Route::get('/stores/{storeSlug}', [StorefrontController::class, 'show'])->name('stores.show');
Route::get('/category/{category:slug}', [ShopController::class, 'category'])->name('category.show');
Route::get('/product/{product:slug}', [ShopController::class, 'show'])->name('product.show');
Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');
Route::get('/manifest.webmanifest', [PwaController::class, 'manifest'])->name('pwa.manifest');
Route::get('/sw.js', [PwaController::class, 'serviceWorker'])->name('pwa.service-worker');
Route::get('/pwa/icon/{size}.png', [PwaController::class, 'icon'])
    ->whereNumber('size')
    ->name('pwa.icon');
Route::middleware('auth')->group(function () {
    Route::post('/push-subscriptions', [WebPushSubscriptionController::class, 'store'])->name('push-subscriptions.store');
    Route::delete('/push-subscriptions', [WebPushSubscriptionController::class, 'destroy'])->name('push-subscriptions.destroy');
});
// Order Tracking Routes (PUBLIC - No auth required)
Route::get('/track-order', [OrderTrackingController::class, 'index'])->name('tracking.index');
Route::post('/track-order', [OrderTrackingController::class, 'search'])->name('tracking.search');

// Newsletter Routes (PUBLIC - No auth required)
Route::post('/newsletter/subscribe', [NewsletterController::class, 'subscribe'])->name('newsletter.subscribe');

// Cart Routes
Route::get('/cart', [CartController::class, 'index'])->name('cart');
Route::get('/cart/summary', [CartController::class, 'summary'])->name('cart.summary');
Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
Route::post('/cart/update', [CartController::class, 'update'])->name('cart.update');
Route::post('/cart/remove', [CartController::class, 'remove'])->name('cart.remove');
Route::post('/cart/clear', [CartController::class, 'clear'])->name('cart.clear');

// Wishlist Routes
Route::view('/wishlist', 'wishlist')->name('wishlist')->middleware('auth');
Route::delete('/wishlist/clear', function () {
    \App\Models\Wishlist::where('user_id', Auth::id())->delete();
    session()->flash('success', 'Wishlist cleared successfully.');
    return redirect()->route('wishlist');
})->name('wishlist.clear')->middleware('auth');

// Checkout Routes
Route::middleware(['auth'])->group(function () {
    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout');
    Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');
    Route::get('/checkout/{order}/success', [CheckoutController::class, 'success'])->name('checkout.success');
    Route::get('/wallet', [WalletController::class, 'index'])->name('wallet.index');
    Route::post('/wallet/top-up', [WalletController::class, 'topUp'])->name('wallet.top-up');
    Route::get('/wallet/top-up/callback', [WalletController::class, 'callback'])->name('wallet.callback');
});

// Payment Routes
Route::middleware(['auth'])->group(function () {
    Route::get('/payment/{order}', [PaymentController::class, 'index'])->name('payment.index');
    Route::post('/payment/verify', [PaymentController::class, 'verify'])->name('payment.verify');
    Route::post('/payment/check-status', [PaymentController::class, 'checkStatus'])->name('payment.check-status');
    Route::post('/payment/{order}', [PaymentController::class, 'process'])->name('payment.process');
    Route::get('/payment/{order}/success', [PaymentController::class, 'success'])->name('payment.success');
    Route::get('/payment/failed', [PaymentController::class, 'failed'])->name('payment.failed');
});

// Payment Callback (External) - No auth required for Paystack redirect
Route::get('/payment/callback', [PaymentController::class, 'callback'])->name('payment.callback');

// User Orders
Route::get('/my-orders', [OrderController::class, 'index'])->name('my.orders')->middleware('auth');
Route::get('/my-orders/{order}', [OrderController::class, 'show'])->name('my.orders.show')->middleware('auth');
Route::post('/my-orders/{order}/cancel', [OrderController::class, 'cancel'])->name('my.orders.cancel')->middleware('auth');
Route::get('/my-downloads', [OrderController::class, 'downloads'])->name('my.downloads')->middleware('auth');
Route::get('/my-courses', [OrderController::class, 'courses'])->name('my.courses')->middleware('auth');
Route::get('/my-downloads/{product}', [OrderController::class, 'downloadProduct'])->name('my.downloads.product')->middleware('auth');
Route::middleware('auth')->group(function () {
    Route::get('/become-a-vendor', [VendorUpgradeController::class, 'create'])->name('vendor-upgrade.create');
    Route::post('/become-a-vendor', [VendorUpgradeController::class, 'store'])->name('vendor-upgrade.store');
});

// About/Contact Pages
Route::view('/about', 'about')->name('about');
Route::get('/contact', [ContactController::class, 'show'])->name('contact');
Route::post('/contact', [ContactController::class, 'submit'])->name('contact.submit');
Route::view('/privacy', 'privacy')->name('privacy');
Route::view('/terms', 'terms')->name('terms');

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:admin,vendor,staff'])->group(function () {
    // Admin Dashboard (Controller + Livewire)
    Route::middleware('role:admin,vendor')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::post('/vendor/verification', [DashboardController::class, 'submitVendorVerification'])->name('vendor.verification.submit');
        Route::get('/dashboard/stats', [DashboardController::class, 'getStats'])->name('dashboard.stats');
        Route::get('/dashboard/chart-data', [DashboardController::class, 'getSalesChartData'])->name('dashboard.chart-data');
        Route::get('/dashboard/order-status-summary', [DashboardController::class, 'getOrderStatusSummary'])->name('dashboard.order-status-summary');
        Route::get('/dashboard/top-products', [DashboardController::class, 'getTopProducts'])->name('dashboard.top-products');
        Route::get('/dashboard/recent-orders', [DashboardController::class, 'getRecentOrders'])->name('dashboard.recent-orders');
        Route::post('/dashboard/refresh-cache', [DashboardController::class, 'refreshCache'])->name('dashboard.refresh-cache');
        
        // New Modern Dashboard
        Route::get('/dashboard/modern', [DashboardController::class, 'modern'])->name('dashboard.modern');
    });
    
    // Products Management
    Route::get('/products', function () {
        return view('admin.products.index');
    })->middleware('can.manage.products.or.admin')->name('products.index');

    // Product creation/edit routes - require product manager permission for staff
    Route::middleware(['verified.vendor', 'can.manage.products.or.admin'])->group(function () {
        Route::get('/products/create', function () {
            return view('admin.products.create');
        })->name('products.create');
        
        Route::get('/products/{product}/edit', function (\App\Models\Product $product) {
            return view('admin.products.edit', ['product' => $product]);
        })->name('products.edit');
        
        // Product API Routes
        Route::post('/products', [AdminProductController::class, 'store'])->name('products.store');
        Route::put('/products/{product}', [AdminProductController::class, 'update'])->name('products.update');
        Route::delete('/products/{product}', [AdminProductController::class, 'destroy'])->name('products.destroy');
        Route::post('/products/{product}/status', [AdminProductController::class, 'updateStatus'])->name('products.status');
        Route::post('/products/{product}/featured', [AdminProductController::class, 'updateFeatured'])->name('products.featured');
    });

    Route::get('/products/{product}', [AdminProductController::class, 'show'])->name('products.show');
    
    // Orders Management - Using Livewire Components
    Route::get('/orders', function () {
        $user = request()->user();
        abort_unless($user->isAdmin() || $user->isVendor() || ($user->isStaff() && $user->isOrderManager()), 403);

        return view('admin.orders.index');
    })->name('orders.index');
    
    Route::get('/orders/{order}', function (\App\Models\Order $order) {
        $user = request()->user();
        abort_unless($user->isAdmin() || $user->isVendor() || ($user->isStaff() && $user->isOrderManager()), 403);

        return view('admin.orders.show', ['order' => $order]);
    })->name('orders.show');
    
    Route::middleware('can.manage.orders.or.admin')->group(function () {
        Route::get('/orders/{order}/edit', [AdminOrderController::class, 'edit'])->name('orders.edit');
        
        // Order API Routes
        Route::put('/orders/{order}', [AdminOrderController::class, 'update'])->name('orders.update');
        Route::post('/orders/{order}/status', [AdminOrderController::class, 'updateStatus'])->name('orders.status.update');
        Route::post('/orders/{order}/payment-status', [AdminOrderController::class, 'updatePaymentStatus'])->name('orders.payment-status.update');
        Route::delete('/orders/{order}', [AdminOrderController::class, 'destroy'])->name('orders.destroy');
    });

    Route::get('/orders/{order}/invoice', [AdminOrderController::class, 'invoice'])->name('orders.invoice');
    Route::get('/orders/{order}/invoice/download', [AdminOrderController::class, 'downloadInvoice'])->name('orders.invoice.download');
    Route::get('/orders/filter', [AdminOrderController::class, 'filter'])->name('orders.filter');
    
    // Admin Profile
    Route::get('/profile', function () {
        return view('admin.profile');
    })->name('profile');

    Route::middleware('role:admin')->group(function () {
        // Categories Management
        Route::get('/categories', function () {
            return view('admin.categories.index');
        })->name('categories.index');
        
        Route::get('/categories/create', function () {
            return view('admin.categories.create');
        })->name('categories.create');
        
        Route::get('/categories/{category}/edit', function (\App\Models\Category $category) {
            return view('admin.categories.edit', ['category' => $category]);
        })->name('categories.edit');
        
        Route::get('/categories/{category}', [CategoryController::class, 'show'])->name('categories.show');
        
        // Category API Routes
        Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
        Route::put('/categories/{category}', [CategoryController::class, 'update'])->name('categories.update');
        Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');
        Route::post('/categories/{category}/status', [CategoryController::class, 'updateStatus'])->name('categories.status');

        // Customers Management
        Route::get('/customers', [AdminUserController::class, 'index'])->name('users.index');
        Route::get('/customers/export', [AdminUserController::class, 'export'])->name('users.export');
        Route::get('/customers/{user}', [AdminUserController::class, 'show'])->name('users.show');
        Route::get('/customers/{user}/edit', [AdminUserController::class, 'edit'])->name('users.edit');
        Route::get('/customers/{user}/orders', [AdminUserController::class, 'orders'])->name('users.orders');
        
        // User API Routes
        Route::put('/customers/{user}', [AdminUserController::class, 'update'])->name('users.update');
        Route::delete('/customers/{user}', [AdminUserController::class, 'destroy'])->name('users.destroy');
        Route::post('/customers/{user}/status', [AdminUserController::class, 'toggleStatus'])->name('users.status');
        Route::post('/customers/{user}/verification', [AdminUserController::class, 'updateVerification'])->name('users.verification');
        
        // Staff Management
        Route::get('/staff', [StaffController::class, 'index'])->name('staff.index');
        Route::get('/staff/assign', [StaffController::class, 'assign'])->name('staff.assign');
        Route::post('/staff/assign', [StaffController::class, 'storeAssignment'])->name('staff.store');
        Route::post('/staff/{user}/deactivate', [StaffController::class, 'deactivate'])->name('staff.deactivate');
        
        // Settings
        Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
        Route::get('/settings/general', function () {
            return view('admin.settings.general');
        })->name('settings.general');

        Route::get('/settings/payment', function () {
            return view('admin.settings.payment');
        })->name('settings.payment');

        Route::get('/settings/shipping', function () {
            return view('admin.settings.shipping');
        })->name('settings.shipping');

        // Update route for settings
        Route::put('/settings/update', [SettingsController::class, 'updateGeneral'])
            ->name('settings.update');
        
        // Reports
        Route::get('/reports/sales', function () {
            return view('admin.reports.sales');
        })->name('reports.sales');
        
        Route::get('/reports/products', function () {
            return view('admin.reports.products');
        })->name('reports.products');

        Route::get('/dashboard/database-backup', [DatabaseBackupController::class, 'download'])->name('dashboard.database-backup');

        // Newsletter Subscribers Management
        Route::get('/newsletter-subscribers', [NewsletterSubscriberController::class, 'index'])->name('newsletter-subscribers.index');
        Route::post('/newsletter-subscribers/send', [NewsletterSubscriberController::class, 'sendNewsletter'])->name('newsletter-subscribers.send');
        Route::patch('/newsletter-subscribers/{subscriber}/toggle', [NewsletterSubscriberController::class, 'toggleStatus'])->name('newsletter-subscribers.toggle');
        Route::delete('/newsletter-subscribers/{subscriber}', [NewsletterSubscriberController::class, 'destroy'])->name('newsletter-subscribers.destroy');
        Route::post('/newsletter-subscribers/destroy-multiple', [NewsletterSubscriberController::class, 'destroyMultiple'])->name('newsletter-subscribers.destroy-multiple');
        Route::get('/newsletter-subscribers/export', [NewsletterSubscriberController::class, 'export'])->name('newsletter-subscribers.export');
    });
});

// Staff Dashboard Routes
Route::middleware(['auth', 'staff.only'])->prefix('staff')->name('admin.staff.')->group(function () {
    // Staff Dashboard
    Route::get('/dashboard', function () {
        return view('staff.dashboard');
    })->name('dashboard');
    
    // Staff Orders (if order manager or all role)
    Route::middleware('staff.orders')->group(function () {
        Route::get('/orders', function () {
            return view('staff.orders.index');
        })->name('orders.index');
        
        Route::get('/orders/{order}', function (\App\Models\Order $order) {
            return view('staff.orders.show', ['order' => $order]);
        })->name('orders.show');
    });
    
    // Staff Products (if product manager or all role)
    Route::middleware('staff.products')->group(function () {
        Route::get('/products', function () {
            return view('staff.products.index');
        })->name('products.index');
        
        Route::get('/products/{product}', function (\App\Models\Product $product) {
            return view('staff.products.show', ['product' => $product]);
        })->name('products.show');
    });
});

/*
|--------------------------------------------------------------------------
| General Auth Routes
|--------------------------------------------------------------------------
*/
Route::post('/logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();

    if (request()->ajax() || request()->wantsJson()) {
        return response()->json([
            'success' => true,
            'redirect' => '/',
        ]);
    }

    return redirect('/');
})->name('logout');

/*
|--------------------------------------------------------------------------
| Breeze Auth Routes
|--------------------------------------------------------------------------
*/
// Dashboard entry point for authenticated users
Route::get('/dashboard', function () {
    $user = auth()->user();
    
    if ($user) {
        // Redirect staff members to staff dashboard
        if ($user->isStaff()) {
            return redirect()->route('admin.staff.dashboard');
        }
        
        // Redirect admin and vendor to admin dashboard
        if ($user->canAccessBackoffice()) {
            return redirect()->route('admin.dashboard');
        }
    }
    
    return view('dashboard');
})->middleware(['auth'])->name('dashboard');

Route::get('/user-dashboard', function () {
    $user = auth()->user();

    if ($user?->isStaff()) {
        return redirect()->route('admin.staff.dashboard');
    }

    if ($user?->canAccessBackoffice()) {
        return redirect()->route('admin.dashboard');
    }

    return redirect()->route('dashboard');
})->middleware(['auth'])->name('user.dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

require __DIR__.'/auth.php';



