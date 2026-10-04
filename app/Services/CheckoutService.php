<?php

namespace App\Services;

use App\Helpers\SettingsHelper;
use App\Models\NewsletterSubscriber;
use App\Models\Order;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class CheckoutService
{
    public function place(array $data): Order
    {
        $newUser = null;
        $order = Order::getConnectionResolver()->connection()->transaction(function () use ($data, &$newUser) {
            $delivery = app(DeliveryService::class);
            $lines = $delivery->cartLines(session('cart', []), true);
            $quote = $delivery->quote($lines, $data['shipping_country'] ?? '');
            $user = Auth::user();
            if (! $user) {
                $newUser = $user = User::create([
                    'name' => strstr($data['buyer_email'], '@', true),
                    'email' => $data['buyer_email'],
                    'password' => Hash::make($data['password']),
                    'role' => 'customer', 'is_admin' => false,
                ]);
            }
            $subtotal = array_sum(array_column($lines, 'total'));
            $fee = round($subtotal * (float) SettingsHelper::platformServiceFeeRate($subtotal) / 100, 2);
            $order = $user->orders()->create([
                'subtotal' => $subtotal, 'tax' => $fee, 'shipping' => $quote['fee'],
                'total' => round($subtotal + $fee + $quote['fee'], 2),
                'status' => 'ordered', 'payment_status' => 'pending',
                'buyer_email' => $data['buyer_email'], 'buyer_whatsapp' => $data['buyer_whatsapp'],
                'shipping_first_name' => $data['shipping_first_name'], 'shipping_last_name' => $data['shipping_last_name'],
                'shipping_email' => $data['buyer_email'], 'billing_email' => $data['buyer_email'],
                'shipping_phone' => $data['shipping_phone'] ?? '',
                'shipping_address' => $data['shipping_address'] ?? '',
                'shipping_city' => $data['shipping_city'] ?? '', 'shipping_state' => $data['shipping_state'] ?? '',
                'shipping_country' => $data['shipping_country'] ?? '', 'shipping_postal_code' => $data['shipping_postal_code'] ?? '',
                'notes' => $data['notes'] ?? null, 'terms_accepted_at' => now(),
                'marketing_consent' => (bool) ($data['marketing_consent'] ?? false),
                'estimated_delivery_from' => $quote['from'], 'estimated_delivery_to' => $quote['to'],
                'delivery_import_charges' => $quote['import_charges'],
            ]);
            foreach ($lines as $line) {
                $product = $line['product'];
                $order->items()->create([
                    'product_id' => $product->id, 'product_name' => $product->name,
                    'price' => $product->price, 'quantity' => $line['quantity'], 'total' => $line['total'],
                    'options' => ['product_type' => $product->product_type],
                ]);
                if ($product->tracksInventory()) {
                    $product->decrement('quantity', $line['quantity']);
                }
            }
            if ($data['marketing_consent'] ?? false) {
                NewsletterSubscriber::updateOrCreate(['email' => $data['buyer_email']], ['is_active' => true, 'subscribed_at' => now()]);
            }

            return $order;
        });
        if ($newUser) {
            Auth::login($newUser);
            session()->regenerate();
            event(new Registered($newUser));
        }
        session()->forget('cart');

        return $order;
    }
}
