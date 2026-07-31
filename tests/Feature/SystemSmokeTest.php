<?php

use App\Models\User;

function systemSmokeUser(array $attributes = []): User
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

it('renders core customer pages for an authenticated customer', function () {
    $customer = systemSmokeUser();

    $this->actingAs($customer)->followingRedirects()->get('/dashboard')->assertOk();
    $this->actingAs($customer)->get('/user-dashboard')->assertRedirect(route('dashboard'));
    $this->actingAs($customer)->get('/wallet')->assertOk();
    $this->actingAs($customer)->get('/my-orders')->assertOk();
    $this->actingAs($customer)->get('/become-a-vendor')->assertOk();
});

it('renders core vendor backoffice pages for an approved vendor', function () {
    $vendor = systemSmokeUser([
        'role' => 'vendor',
        'store_name' => 'Smoke Vendor Store',
        'store_slug' => 'smoke-vendor-store',
        'vendor_is_active' => true,
        'verification_status' => 'approved',
        'verified_at' => now(),
    ]);

    foreach ([
        '/admin/dashboard',
        '/admin/products',
        '/admin/products/create',
        '/admin/orders',
        '/admin/profile',
    ] as $route) {
        $this->actingAs($vendor)->get($route)->assertOk();
    }
});

it('renders core admin pages for an admin user', function () {
    $admin = systemSmokeUser([
        'role' => 'admin',
        'is_admin' => true,
    ]);

    foreach ([
        '/admin/dashboard',
        '/admin/products',
        '/admin/orders',
        '/admin/categories',
        '/admin/customers',
        '/admin/staff',
        '/admin/settings',
        '/admin/reports/sales',
        '/admin/newsletter-subscribers',
    ] as $route) {
        $this->actingAs($admin)->get($route)->assertOk();
    }
});

it('renders staff dashboards for order and product managers', function () {
    $orderStaff = systemSmokeUser([
        'is_staff' => true,
        'staff_role' => 'order_manager',
        'staff_assigned_at' => now(),
    ]);

    $productStaff = systemSmokeUser([
        'is_staff' => true,
        'staff_role' => 'product_manager',
        'staff_assigned_at' => now(),
    ]);

    $this->actingAs($orderStaff)->get('/staff/dashboard')->assertOk();
    $this->actingAs($orderStaff)->get('/staff/orders')->assertOk();

    $this->actingAs($productStaff)->get('/staff/dashboard')->assertOk();
    $this->actingAs($productStaff)->get('/staff/products')->assertOk();
});
