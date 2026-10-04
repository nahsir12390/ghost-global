<?php

use App\Livewire\Admin\Products\Index;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Livewire\Livewire;

function catalogueManager(): User
{
    return User::factory()->create([
        'role' => 'customer',
        'is_admin' => false,
        'is_staff' => true,
        'staff_role' => 'product_manager',
        'staff_assigned_at' => now(),
        'staff_deactivated_at' => null,
    ]);
}

it('creates unique category slugs for duplicate names', function () {
    $first = Category::create(['name' => 'Phones', 'is_active' => true]);
    $second = Category::create(['name' => 'Phones', 'is_active' => true]);

    expect($first->slug)->toBe('phones')
        ->and($second->slug)->toBe('phones-2');
});

it('deactivates purchased products instead of deleting order history', function () {
    $manager = catalogueManager();
    $category = Category::create(['name' => 'Electronics', 'is_active' => true]);
    $product = Product::create([
        'category_id' => $category->id,
        'name' => 'Purchased Product',
        'product_type' => Product::TYPE_PHYSICAL,
        'price' => 5000,
        'quantity' => 5,
        'is_active' => true,
    ]);

    $order = Order::factory()->create();
    OrderItem::create([
        'order_id' => $order->id,
        'product_id' => $product->id,
        'product_name' => $product->name,
        'price' => 5000,
        'quantity' => 1,
        'total' => 5000,
    ]);

    Livewire::actingAs($manager)->test(Index::class)
        ->call('deleteProduct', $product->id);

    expect(Product::find($product->id))->not->toBeNull()
        ->and($product->fresh()->is_active)->toBeFalse()
        ->and(OrderItem::where('product_id', $product->id)->exists())->toBeTrue();
});

it('permanently deletes products that have never been ordered', function () {
    $manager = catalogueManager();
    $category = Category::create(['name' => 'Accessories', 'is_active' => true]);
    $product = Product::create([
        'category_id' => $category->id,
        'name' => 'Unused Product',
        'product_type' => Product::TYPE_PHYSICAL,
        'price' => 1000,
        'quantity' => 2,
        'is_active' => true,
    ]);

    Livewire::actingAs($manager)->test(Index::class)
        ->call('deleteProduct', $product->id);

    expect(Product::find($product->id))->toBeNull();
});
