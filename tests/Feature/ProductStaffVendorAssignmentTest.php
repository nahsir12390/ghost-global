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
        'is_staff' => false,
        'staff_role' => null,
        'staff_assigned_at' => null,
        'staff_deactivated_at' => null,
    ], $attributes));
}

it('lets product staff create a product for an approved vendor', function () {
    $staff = productStaffUser([
        'is_staff' => true,
        'staff_role' => 'product_manager',
        'staff_assigned_at' => now(),
    ]);

    $vendor = productStaffUser([
        'role' => 'vendor',
        'store_name' => 'Assigned Store',
        'store_slug' => 'assigned-store',
        'vendor_is_active' => true,
        'verification_status' => 'approved',
        'verified_at' => now(),
    ]);

    $category = Category::create([
        'name' => 'Electronics',
        'description' => 'Electronics',
        'is_active' => true,
    ]);

    Livewire::actingAs($staff)
        ->test(Create::class)
        ->set('vendor_id', $vendor->id)
        ->set('name', 'Staff Created Product')
        ->set('category_id', $category->id)
        ->set('product_type', Product::TYPE_PHYSICAL)
        ->set('price', 25000)
        ->set('quantity', 5)
        ->call('save')
        ->assertHasNoErrors();

    $product = Product::query()->where('name', 'Staff Created Product')->first();

    expect($product)->not->toBeNull();
    expect($product->vendor_id)->toBe($vendor->id);
});

it('lets product staff reassign an existing product to another approved vendor', function () {
    $staff = productStaffUser([
        'is_staff' => true,
        'staff_role' => 'product_manager',
        'staff_assigned_at' => now(),
    ]);

    $originalVendor = productStaffUser([
        'role' => 'vendor',
        'store_name' => 'Original Store',
        'store_slug' => 'original-store',
        'vendor_is_active' => true,
        'verification_status' => 'approved',
        'verified_at' => now(),
    ]);

    $newVendor = productStaffUser([
        'role' => 'vendor',
        'store_name' => 'New Store',
        'store_slug' => 'new-store',
        'vendor_is_active' => true,
        'verification_status' => 'approved',
        'verified_at' => now(),
    ]);

    $category = Category::create([
        'name' => 'Books',
        'description' => 'Books',
        'is_active' => true,
    ]);

    $product = Product::create([
        'vendor_id' => $originalVendor->id,
        'category_id' => $category->id,
        'name' => 'Reassign Me',
        'slug' => 'reassign-me',
        'product_type' => Product::TYPE_PHYSICAL,
        'price' => 1000,
        'quantity' => 3,
        'is_active' => true,
    ]);

    Livewire::actingAs($staff)
        ->test(Edit::class, ['product' => $product])
        ->set('vendor_id', $newVendor->id)
        ->set('name', 'Reassign Me')
        ->set('category_id', $category->id)
        ->set('product_type', Product::TYPE_PHYSICAL)
        ->set('price', 1000)
        ->set('quantity', 3)
        ->call('update')
        ->assertHasNoErrors();

    expect($product->fresh()->vendor_id)->toBe($newVendor->id);
});
