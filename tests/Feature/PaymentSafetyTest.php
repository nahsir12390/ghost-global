<?php

use App\Helpers\SettingsHelper;
use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\Http;

function paymentOrder(): Order
{
    $user = User::factory()->create(['role' => 'customer', 'is_admin' => false]);
    auth()->login($user);

    return Order::factory()->create([
        'user_id' => $user->id,
        'buyer_email' => 'buyer@example.test',
        'total' => 12500,
        'payment_status' => 'pending',
        'payment_method' => 'paystack',
        'payment_reference' => 'ORD-SAFE-123',
    ]);
}

beforeEach(function () {
    SettingsHelper::set('test_mode', true);
    SettingsHelper::set('paystack_test_secret_key', 'sk_test_example_not_a_real_key');
});

it('does not mark a pending Paystack transaction as failed', function () {
    $order = paymentOrder();
    Http::fake(['api.paystack.co/*' => Http::response([
        'status' => true,
        'data' => ['status' => 'pending', 'reference' => 'ORD-SAFE-123'],
    ])]);

    $this->postJson(route('payment.verify'), ['reference' => 'ORD-SAFE-123', 'order_id' => $order->id])
        ->assertStatus(202)
        ->assertJson(['success' => false]);

    expect($order->fresh()->payment_status)->toBe('pending');
});

it('rejects a successful transaction with the wrong amount', function () {
    $order = paymentOrder();
    Http::fake(['api.paystack.co/*' => Http::response([
        'status' => true,
        'data' => ['id' => 1001, 'status' => 'success', 'reference' => 'ORD-SAFE-123', 'amount' => 100, 'currency' => 'NGN'],
    ])]);

    $this->postJson(route('payment.verify'), ['reference' => 'ORD-SAFE-123', 'order_id' => $order->id])
        ->assertStatus(202)
        ->assertJson(['success' => false]);

    expect($order->fresh()->payment_status)->toBe('pending');
});

it('rejects payment verification for another users order', function () {
    $order = paymentOrder();
    $other = User::factory()->create(['role' => 'customer', 'is_admin' => false]);

    $this->actingAs($other)->postJson(route('payment.verify'), [
        'reference' => 'ORD-SAFE-123', 'order_id' => $order->id,
    ])->assertForbidden();
});

it('keeps successful verification idempotent', function () {
    $order = paymentOrder();
    Http::fake(['api.paystack.co/*' => Http::response([
        'status' => true,
        'data' => ['id' => 1002, 'status' => 'success', 'reference' => 'ORD-SAFE-123', 'amount' => 1250000, 'currency' => 'NGN'],
    ])]);

    $this->postJson(route('payment.verify'), ['reference' => 'ORD-SAFE-123', 'order_id' => $order->id])->assertOk();
    $this->postJson(route('payment.verify'), ['reference' => 'ORD-SAFE-123', 'order_id' => $order->id])->assertOk();

    expect($order->fresh()->payment_status)->toBe('paid')
        ->and($order->fresh()->payment_id)->toBe('1002');
});
