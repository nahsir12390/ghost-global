<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get some users and products
        $users = User::all();
        $products = Product::all();

        if ($users->isEmpty() || $products->isEmpty()) {
            return; // Skip if no users or products
        }

        // Create sample orders
        $orders = [
            [
                'order_number' => 'ORD-' . strtoupper(Str::random(10)),
                'user_id' => $users->first()->id,
                'subtotal' => 1200000,
                'tax' => 120000,
                'shipping' => 10000,
                'total' => 1320000,
                'status' => 'completed',
                'payment_status' => 'paid',
                'payment_method' => 'paystack',
                'payment_reference' => 'PAY_' . strtoupper(uniqid()),
                'shipping_first_name' => 'John',
                'shipping_last_name' => 'Doe',
                'shipping_email' => 'john@example.com',
                'shipping_phone' => '+2348012345678',
                'shipping_address' => '123 Main Street',
                'shipping_city' => 'Lagos',
                'shipping_state' => 'Lagos',
                'shipping_country' => 'Nigeria',
                'shipping_postal_code' => '100001',
                'same_as_shipping' => true,
                'billing_first_name' => 'John',
                'billing_last_name' => 'Doe',
                'billing_email' => 'john@example.com',
                'billing_phone' => '+2348012345678',
                'billing_address' => '123 Main Street',
                'billing_city' => 'Lagos',
                'billing_state' => 'Lagos',
                'billing_country' => 'Nigeria',
                'billing_postal_code' => '100001',
            ],
            [
                'order_number' => 'ORD-' . strtoupper(Str::random(10)),
                'user_id' => $users->skip(1)->first()->id ?? $users->first()->id,
                'subtotal' => 70000,
                'tax' => 7000,
                'shipping' => 5000,
                'total' => 82000,
                'status' => 'processing',
                'payment_status' => 'paid',
                'payment_method' => 'cash_on_delivery',
                'shipping_first_name' => 'Jane',
                'shipping_last_name' => 'Smith',
                'shipping_email' => 'jane@example.com',
                'shipping_phone' => '+2348098765432',
                'shipping_address' => '456 Oak Avenue',
                'shipping_city' => 'Abuja',
                'shipping_state' => 'FCT',
                'shipping_country' => 'Nigeria',
                'shipping_postal_code' => '900001',
                'same_as_shipping' => true,
                'billing_first_name' => 'Jane',
                'billing_last_name' => 'Smith',
                'billing_email' => 'jane@example.com',
                'billing_phone' => '+2348098765432',
                'billing_address' => '456 Oak Avenue',
                'billing_city' => 'Abuja',
                'billing_state' => 'FCT',
                'billing_country' => 'Nigeria',
                'billing_postal_code' => '900001',
            ],
            [
                'order_number' => 'ORD-' . strtoupper(Str::random(10)),
                'user_id' => $users->skip(2)->first()->id ?? $users->first()->id,
                'subtotal' => 27000,
                'tax' => 2700,
                'shipping' => 2000,
                'total' => 31700,
                'status' => 'pending',
                'payment_status' => 'pending',
                'payment_method' => 'paystack',
                'shipping_first_name' => 'Bob',
                'shipping_last_name' => 'Johnson',
                'shipping_email' => 'bob@example.com',
                'shipping_phone' => '+2348076543210',
                'shipping_address' => '789 Pine Road',
                'shipping_city' => 'Port Harcourt',
                'shipping_state' => 'Rivers',
                'shipping_country' => 'Nigeria',
                'shipping_postal_code' => '500001',
                'same_as_shipping' => true,
                'billing_first_name' => 'Bob',
                'billing_last_name' => 'Johnson',
                'billing_email' => 'bob@example.com',
                'billing_phone' => '+2348076543210',
                'billing_address' => '789 Pine Road',
                'billing_city' => 'Port Harcourt',
                'billing_state' => 'Rivers',
                'billing_country' => 'Nigeria',
                'billing_postal_code' => '500001',
            ],
        ];

        foreach ($orders as $orderData) {
            $order = Order::create($orderData);

            // Add order items
            $selectedProducts = $products->random(min(3, $products->count()));

            foreach ($selectedProducts as $product) {
                $quantity = rand(1, 3);
                $price = $product->price;

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'price' => $price,
                    'quantity' => $quantity,
                    'total' => $price * $quantity,
                ]);

                // Reduce product quantity
                $product->decrement('quantity', $quantity);
            }
        }
    }
}
