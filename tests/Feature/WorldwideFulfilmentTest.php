<?php

use App\Http\Requests\StoreCheckoutRequest;
use App\Livewire\Checkout;
use App\Mail\ShipmentUpdated;
use App\Models\Category;
use App\Models\DeliveryDestination;
use App\Models\NewsletterSubscriber;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Services\CheckoutService;
use App\Services\ShipmentService;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Livewire\Livewire;
use Livewire\Volt\Volt;

function worldwideProduct(array $data = []): Product
{
    return Product::create(array_merge([
        'category_id' => Category::create(['name' => fake()->unique()->word(), 'is_active' => true])->id,
        'name' => 'Overseas gift', 'price' => 10000, 'quantity' => 10,
        'product_type' => 'physical', 'is_active' => true,
        'processing_min_days' => 2, 'processing_max_days' => 3,
    ], $data));
}

function worldwideData(array $data = []): array
{
    return array_merge([
        'shipping_first_name' => 'Receiver', 'shipping_last_name' => 'Example',
        'buyer_email' => 'buyer@example.test', 'buyer_whatsapp' => '+2348012345678',
        'shipping_phone' => '+15555555555', 'shipping_country' => 'US',
        'shipping_state' => 'New York', 'shipping_city' => 'New York',
        'shipping_address' => '123 Example Avenue', 'shipping_postal_code' => '10001',
        'password' => 'a-secure-password', 'terms_accepted' => true, 'marketing_consent' => false,
    ], $data);
}

function worldwideOrder(): Order
{
    Mail::fake();
    DeliveryDestination::factory()->create(['country_code' => 'US']);
    $buyer = User::factory()->create(['role' => 'customer', 'is_admin' => false]);
    auth()->login($buyer);
    $product = worldwideProduct();
    session(['cart' => [$product->id => ['product_id' => $product->id, 'quantity' => 2, 'price' => 1]]]);

    return app(CheckoutService::class)->place(worldwideData());
}

function worldwideAdmin(): User
{
    return User::factory()->create(['role' => 'admin', 'is_admin' => true]);
}

it('creates a buyer account and stores separate receiver details with a country quote', function () {
    Mail::fake();
    $this->travelTo(\Carbon\Carbon::parse('2026-10-05 10:00:00'));
    DeliveryDestination::factory()->create(['country_code' => 'US', 'min_days' => 7, 'max_days' => 14, 'shipping_fee' => 4000]);
    $product = worldwideProduct();
    $this->withSession(['cart' => [$product->id => ['quantity' => 2, 'price' => 1]]])
        ->get('/checkout')->assertOk()->assertSee('Your WhatsApp number')->assertSee('Create account password');
    $this->post('/checkout', worldwideData(['shipping_phone' => '']))->assertRedirect();
    $order = Order::firstOrFail();
    expect($order->buyer_email)->toBe('buyer@example.test')
        ->and($order->shipping_first_name)->toBe('Receiver')
        ->and($order->shipping_phone)->toBe('')
        ->and((float) $order->subtotal)->toBe(20000.0)
        ->and((float) $order->shipping)->toBe(4000.0)
        ->and($order->estimated_delivery_from->toDateString())->toBe('2026-10-16')
        ->and($order->estimated_delivery_to->toDateString())->toBe('2026-10-28')
        ->and($order->terms_accepted_at)->not->toBeNull()
        ->and($order->user->role)->toBe('customer')
        ->and(NewsletterSubscriber::count())->toBe(0);
    $this->assertAuthenticatedAs($order->user);
});

it('requires a configured destination and valid country before charging', function () {
    Mail::fake();
    $product = worldwideProduct();
    $this->withSession(['cart' => [$product->id => ['quantity' => 1]]])
        ->post('/checkout', worldwideData())->assertSessionHasErrors('shipping_country');
    expect(Order::count())->toBe(0)->and(User::count())->toBe(0)->and($product->fresh()->quantity)->toBe(10);
});

it('enforces destination address rules and terms acceptance', function () {
    DeliveryDestination::factory()->create(['country_code' => 'US', 'state_required' => true, 'postal_required' => true]);
    $product = worldwideProduct();
    session(['cart' => [$product->id => ['quantity' => 1]]]);
    $this->post('/checkout', worldwideData(['shipping_state' => '', 'shipping_postal_code' => '', 'terms_accepted' => false]))
        ->assertSessionHasErrors(['shipping_state', 'shipping_postal_code', 'terms_accepted']);
    $destination = DeliveryDestination::first();
    $destination->update(['state_required' => false, 'postal_required' => false]);
    expect(Validator::make(worldwideData(['shipping_state' => '', 'shipping_postal_code' => '']), StoreCheckoutRequest::checkoutRules('US'))->passes())->toBeTrue();
});

it('blocks products outside their supported destinations', function () {
    DeliveryDestination::factory()->create(['country_code' => 'US']);
    $product = worldwideProduct(['delivery_countries' => ['GB']]);
    $this->withSession(['cart' => [$product->id => ['quantity' => 1]]])->post('/checkout', worldwideData())->assertSessionHasErrors('shipping_country');
    expect(Order::count())->toBe(0);
});

it('uses the same rules and totals for Livewire checkout and records opt-in only when selected', function () {
    Mail::fake();
    DeliveryDestination::factory()->create(['country_code' => 'US']);
    $product = worldwideProduct();
    session(['cart' => [$product->id => ['quantity' => 1, 'price' => 1]]]);
    $component = Livewire::test(Checkout::class);
    foreach (worldwideData(['marketing_consent' => true]) as $field => $value) {
        $component->set($field, $value);
    }
    $component->call('submitOrder')->assertHasNoErrors();
    expect(Order::firstOrFail()->subtotal)->toBe('10000.00')->and(NewsletterSubscriber::firstOrFail()->email)->toBe('buyer@example.test');
});

it('restricts destination management to admins', function () {
    $this->actingAs(User::factory()->create(['role' => 'customer', 'is_admin' => false]))
        ->get(route('admin.settings.delivery'))->assertForbidden();
    Volt::actingAs(worldwideAdmin())->test('admin.delivery-destinations')
        ->set('country_code', 'US')->set('min_days', 14)->set('max_days', 7)
        ->set('import_charges', 'Included')->call('save')->assertHasErrors('max_days')
        ->set('max_days', 20)->set('enabled', true)->call('save')->assertHasNoErrors();
    expect(DeliveryDestination::firstOrFail()->enabled)->toBeTrue();
});

it('supports split parcels and marks the order delivered only after every item arrives without changing payment', function () {
    $order = worldwideOrder();
    $this->actingAs(worldwideAdmin());
    $item = $order->items->first();
    $service = app(ShipmentService::class);
    $first = $service->save($order, null, ['status' => 'delivered', 'purchase_currency' => 'USD', 'supplier_name' => 'Private supplier', 'purchase_cost' => 23], [$item->id => 1]);
    expect($order->fresh()->status)->not->toBe('delivered')->and($order->fresh()->payment_status)->toBe('pending');
    $service->save($order, null, ['status' => 'delivered', 'purchase_currency' => 'USD'], [$item->id => 1]);
    expect($order->fresh()->status)->toBe('delivered')->and($order->fresh()->payment_status)->toBe('pending');
    Mail::assertQueued(ShipmentUpdated::class, fn ($mail) => $mail->hasTo('buyer@example.test'));
    expect($first->toArray())->not->toHaveKey('purchase_cost')->not->toHaveKey('supplier_name');
});

it('rejects over-allocation, foreign items, unsafe links and reversed dates', function () {
    $order = worldwideOrder();
    $this->actingAs(worldwideAdmin());
    $service = app(ShipmentService::class);
    $data = ['status' => 'preparing', 'purchase_currency' => 'USD'];
    $item = $order->items->first();
    $service->save($order, null, $data, [$item->id => 2]);
    expect(fn () => $service->save($order, null, $data, [$item->id => 1]))->toThrow(ValidationException::class);
    expect(fn () => $service->save($order, null, $data, [999999 => 1]))->toThrow(ValidationException::class);
    expect(fn () => $service->save($order, null, $data + ['tracking_url' => 'javascript:alert(1)'], [$item->id => 1]))->toThrow(ValidationException::class);
    expect(fn () => $service->save($order, null, $data + ['estimated_from' => '2026-11-20', 'estimated_to' => '2026-11-10'], [$item->id => 1]))->toThrow(ValidationException::class);
});

it('keeps supplier details private in customer tracking and emails', function () {
    $order = worldwideOrder();
    $this->actingAs(worldwideAdmin());
    $shipment = app(ShipmentService::class)->save($order, null, [
        'status' => 'delayed', 'purchase_currency' => 'USD', 'supplier_name' => 'SecretSupplier',
        'supplier_order_reference' => 'PRIVATE123', 'private_notes' => 'PrivateMargin',
        'customer_update' => 'Customs processing is taking longer.', 'tracking_url' => 'https://example.com/track/123',
    ], [$order->items->first()->id => 2]);
    $this->actingAs($order->user)->get(route('my.orders.show', $order))->assertOk()
        ->assertSee('Customs processing is taking longer.')->assertDontSee('SecretSupplier')->assertDontSee('PRIVATE123')->assertDontSee('PrivateMargin');
    $this->post(route('tracking.search'), ['order_number' => $order->order_number, 'email_or_phone' => $order->buyer_email])->assertOk()->assertDontSee('SecretSupplier');
    $this->post(route('tracking.search'), ['order_number' => $order->order_number, 'email_or_phone' => $order->shipping_phone])->assertRedirect(route('tracking.index'));
    $html = (new ShipmentUpdated($shipment))->render();
    expect($html)->toContain('Customs processing')->not->toContain('SecretSupplier')->not->toContain('PRIVATE123');
    Volt::test('admin.order-shipments', ['orderId' => $order->id])->assertForbidden();
});

it('keeps payment pending when staff manually record delivery', function () {
    $order = worldwideOrder();
    $admin = worldwideAdmin();
    Livewire::actingAs($admin)->test(\App\Livewire\Admin\Orders\Show::class, ['order' => $order])
        ->set('status', 'delivered')->call('update')->assertHasNoErrors();
    expect($order->fresh()->payment_status)->toBe('pending');
});

it('rejects cash on delivery for prepaid worldwide orders', function () {
    $order = worldwideOrder();
    \App\Helpers\SettingsHelper::set('enable_cash_on_delivery', true);
    $this->post(route('payment.process', $order), ['payment_method' => 'cash_on_delivery'])->assertUnprocessable();
    expect($order->fresh()->payment_status)->toBe('pending');
});

it('verifies the payment currency as well as the amount', function () {
    $order = worldwideOrder();
    $order->update(['payment_reference' => 'WORLD-PAY-123']);
    \App\Helpers\SettingsHelper::set('test_mode', true);
    \App\Helpers\SettingsHelper::set('paystack_test_secret_key', 'sk_test_example_not_a_real_key');
    $payload = ['data' => ['status' => 'success', 'reference' => 'WORLD-PAY-123', 'amount' => (int) round($order->total * 100), 'currency' => 'USD']];
    $sequence = \Illuminate\Support\Facades\Http::sequence()->push($payload);
    $payload['data']['currency'] = 'NGN';
    $sequence->push($payload);
    \Illuminate\Support\Facades\Http::fake(['api.paystack.co/*' => $sequence]);
    $this->postJson(route('payment.check-status'), ['reference' => 'WORLD-PAY-123', 'order_id' => $order->id])->assertOk()->assertJson(['success' => false]);
    $this->postJson(route('payment.check-status'), ['reference' => 'WORLD-PAY-123', 'order_id' => $order->id])->assertOk()->assertJson(['success' => true]);
});
