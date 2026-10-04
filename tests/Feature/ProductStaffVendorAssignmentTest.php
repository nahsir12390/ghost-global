<?php

use App\Livewire\Admin\Products\Create;
use App\Livewire\Admin\Products\Edit;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Livewire\Livewire;

function productStaffUser(array $attributes = []): User
{
    return User::factory()->create(array_merge([
        'role' => 'customer',
        'is_admin' => false,
        'is_staff' => true,
        'staff_role' => 'product_manager',
        'staff_assigned_at' => now(),
        'staff_deactivated_at' => null,
    ], $attributes));
}

it('lets product staff create store products without selecting a vendor', function () {
    $staff = productStaffUser();
    $category = Category::create(['name' => 'Electronics', 'is_active' => true]);

    Livewire::actingAs($staff)->test(Create::class)
        ->assertDontSee('Vendor Store')
        ->set('name', 'Store Product')
        ->set('category_id', $category->id)
        ->set('price', 25000)
        ->set('quantity', 5)
        ->call('save')
        ->assertHasNoErrors()
        ->assertRedirect(route('admin.products.index'));

    $product = Product::where('name', 'Store Product')->firstOrFail();
    expect($product->vendor_id)->toBeNull();
});

it('lets product staff edit store products without a vendor assignment', function () {
    $staff = productStaffUser();
    $category = Category::create(['name' => 'Books', 'is_active' => true]);
    $product = Product::create([
        'category_id' => $category->id, 'name' => 'Original',
        'product_type' => Product::TYPE_PHYSICAL, 'price' => 1000,
        'quantity' => 3, 'is_active' => true,
    ]);

    Livewire::actingAs($staff)->test(Edit::class, ['product' => $product])
        ->assertDontSee('Vendor Store')
        ->set('name', 'Updated')
        ->set('price', 1500)
        ->call('update')
        ->assertHasNoErrors();

    expect($product->fresh()->name)->toBe('Updated')
        ->and($product->fresh()->vendor_id)->toBeNull();
});
