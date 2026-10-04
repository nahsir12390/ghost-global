<?php

use App\Livewire\Admin\Products\Create;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Livewire\Livewire;
use Livewire\Volt\Volt;

function singleStoreProduct(array $attributes = []): Product
{
    $category = Category::create(['name' => 'Gifts', 'is_active' => true]);

    return Product::create(array_merge([
        'category_id' => $category->id, 'name' => 'Gift Box',
        'product_type' => Product::TYPE_PHYSICAL, 'price' => 5000,
        'quantity' => 10, 'is_active' => true,
    ], $attributes));
}

it('removes vendor registration and public store routes', function () {
    $this->get('/become-a-vendor')->assertNotFound();
    $this->post('/become-a-vendor', [])->assertNotFound();
    $this->get('/stores/example')->assertNotFound();
    $this->get('/register')->assertOk()->assertDontSee('Vendor');

    $customer = User::factory()->create(['role' => 'customer', 'is_admin' => false]);
    $this->actingAs($customer)->get('/dashboard')->assertOk()->assertDontSee('Become a Vendor');
});

it('registers customers without a role selection', function () {
    Mail::fake();
    Volt::test('pages.auth.register')
        ->set('name', 'Customer')->set('email', 'customer@ghost.test')
        ->set('password', 'safe-password-123')->set('password_confirmation', 'safe-password-123')
        ->call('register')->assertHasNoErrors();

    $user = User::where('email', 'customer@ghost.test')->firstOrFail();
    expect($user->role)->toBe('customer')->and($user->canAccessBackoffice())->toBeFalse();
});

it('rejects product creation by legacy vendors and customers', function (string $role) {
    $user = User::factory()->create(['role' => $role, 'is_admin' => false]);
    Livewire::actingAs($user)->test(Create::class)->assertForbidden();
})->with(['vendor', 'customer']);

it('shows an admin dashboard without vendor management panels', function () {
    $admin = User::factory()->create(['role' => 'admin', 'is_admin' => true]);
    foreach (['/admin/dashboard', '/admin/dashboard/modern', '/admin/customers', '/admin/products/create', '/admin/orders'] as $url) {
        $this->actingAs($admin)->get($url)->assertOk()
            ->assertDontSee('Vendor Reviews')->assertDontSee('Pending vendor reviews')
            ->assertDontSee('Registered vendors')->assertDontSee('Users &amp; Vendors', false)
            ->assertDontSee('Vendor Store')->assertDontSee('Filter by Vendor');
    }
});

it('ignores former vendor availability when shopping', function () {
    $vendor = User::factory()->create(['role' => 'vendor', 'vendor_is_active' => false, 'is_admin' => false]);
    $product = singleStoreProduct();
    $product->forceFill(['vendor_id' => $vendor->id])->save();

    expect($product->fresh()->isPurchasable())->toBeTrue()
        ->and($product->canBeManagedBy($vendor))->toBeFalse();
    $this->get(route('product.show', $product))->assertOk()->assertDontSee('Vendor Inactive');
    $this->postJson('/cart/add', ['product_id' => $product->id, 'quantity' => 1])->assertOk();
});

it('still rejects unavailable stock', function () {
    $product = singleStoreProduct(['quantity' => 0]);
    $this->postJson('/cart/add', ['product_id' => $product->id, 'quantity' => 1])->assertUnprocessable();
});

it('creates an order and payment page without vendor ownership', function () {
    Http::preventStrayRequests();
    Mail::fake();
    $customer = User::factory()->create(['role' => 'customer', 'is_admin' => false]);
    $product = singleStoreProduct();
    $this->actingAs($customer)->postJson('/cart/add', ['product_id' => $product->id, 'quantity' => 2])->assertOk();
    \App\Models\DeliveryDestination::factory()->create(['country_code' => 'US']);
    $response = $this->post('/checkout', [
        'buyer_email' => 'buyer@ghost.test', 'buyer_whatsapp' => '+2348012345678', 'terms_accepted' => true,
        'shipping_first_name' => 'Ada', 'shipping_last_name' => 'Obi',
        'shipping_email' => 'ada@ghost.test', 'shipping_phone' => '08012345678',
        'shipping_address' => '12 Example Street', 'shipping_city' => 'Lagos',
        'shipping_state' => 'Lagos', 'shipping_country' => 'US',
        'shipping_postal_code' => '100001', 'same_as_shipping' => true,
    ]);
    $order = Order::firstOrFail();
    $response->assertRedirect(route('payment.index', $order->id));
    expect($order->items()->first()->vendor_id)->toBeNull()
        ->and($order->items()->first()->quantity)->toBe(2)
        ->and($product->fresh()->quantity)->toBe(8);
    $this->get(route('payment.index', $order->id))->assertOk();

    $admin = User::factory()->create(['role' => 'admin', 'is_admin' => true]);
    $this->actingAs($admin)->get('/admin/orders')->assertOk()
        ->assertSee($order->order_number)->assertDontSee('Vendor(s)');
    $this->get(route('admin.orders.show', $order))->assertOk()
        ->assertSee($product->name)->assertDontSee('Vendor Summary');
});

it('does not seed vendor accounts or require vendors for demo products', function () {
    Mail::fake();
    $this->seed(\Database\Seeders\UserSeeder::class);
    $this->seed(\Database\Seeders\ProductSeeder::class);

    expect(User::where('role', 'vendor')->exists())->toBeFalse()
        ->and(Product::count())->toBeGreaterThan(0)
        ->and(Product::whereNotNull('vendor_id')->exists())->toBeFalse();
});

it('rechecks order management permission on livewire mutations', function () {
    $staff = User::factory()->create([
        'role' => 'customer', 'is_admin' => false, 'is_staff' => true,
        'staff_role' => 'order_manager', 'staff_deactivated_at' => null,
    ]);
    $component = Livewire::actingAs($staff)->test(\App\Livewire\Admin\Orders\Index::class);
    $staff->update(['is_staff' => false]);
    $component->call('updateOrderStatus', 1, 'delivered')->assertForbidden();
});

it('renders customer pages without links to removed vendor routes', function () {
    $customer = User::factory()->create(['role' => 'customer', 'is_admin' => false]);
    foreach (['/', '/about', '/contact', '/shop', '/profile', '/cart'] as $url) {
        $this->actingAs($customer)->get($url)->assertOk()
            ->assertDontSee('become-a-vendor')->assertDontSee('Vendor Dashboard');
    }
});

it('exports customer information without vendor and payout fields', function () {
    $admin = User::factory()->create(['role' => 'admin', 'is_admin' => true]);
    $customer = User::factory()->create(['role' => 'customer', 'is_admin' => false]);
    $this->actingAs($admin)->get(route('admin.users.export'))->assertOk()
        ->assertSee($customer->email)->assertDontSee('Vendor')->assertDontSee('Account Number');
});
