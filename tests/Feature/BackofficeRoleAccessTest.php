<?php

use App\Models\User;

function backofficeUser(array $attributes): User
{
    return User::factory()->create(array_merge([
        'role' => 'customer',
        'is_admin' => false,
        'is_staff' => false,
        'staff_role' => null,
        'staff_deactivated_at' => null,
    ], $attributes));
}

it('allows admins to access admin sidebar routes', function () {
    $admin = backofficeUser([
        'role' => 'admin',
        'is_admin' => true,
    ]);

    $routes = [
        '/admin/dashboard',
        '/admin/products',
        '/admin/orders',
        '/admin/categories',
        '/admin/customers',
        '/admin/staff',
        '/admin/settings',
        '/admin/reports/sales',
        '/admin/newsletter-subscribers',
    ];

    foreach ($routes as $route) {
        $this->actingAs($admin)->get($route)->assertOk();
    }
});

it('keeps legacy vendors out of all backoffice routes', function () {
    $vendor = backofficeUser([
        'role' => 'vendor',
        'store_name' => 'Vendor Store',
        'verification_status' => 'approved',
        'verified_at' => now(),
    ]);

    foreach (['/admin/dashboard', '/admin/products', '/admin/products/create', '/admin/orders'] as $route) {
        $this->actingAs($vendor)->get($route)->assertForbidden();
    }

    foreach ([
        '/admin/categories',
        '/admin/customers',
        '/admin/staff',
        '/admin/settings',
        '/admin/reports/sales',
        '/admin/newsletter-subscribers',
    ] as $route) {
        $this->actingAs($vendor)->get($route)->assertForbidden();
    }
});

it('allows order staff to access only order staff routes', function () {
    $staff = backofficeUser([
        'is_staff' => true,
        'staff_role' => 'order_manager',
        'staff_assigned_at' => now(),
    ]);

    foreach (['/staff/dashboard', '/staff/orders', '/admin/orders'] as $route) {
        $this->actingAs($staff)->get($route)->assertOk();
    }

    foreach ([
        '/admin/dashboard',
        '/staff/products',
        '/admin/products/create',
        '/admin/products',
        '/admin/categories',
        '/admin/customers',
        '/admin/staff',
        '/admin/settings',
        '/admin/reports/sales',
    ] as $route) {
        $this->actingAs($staff)->get($route)->assertForbidden();
    }
});

it('allows product staff to access only product staff routes', function () {
    $staff = backofficeUser([
        'is_staff' => true,
        'staff_role' => 'product_manager',
        'staff_assigned_at' => now(),
    ]);

    foreach (['/staff/dashboard', '/staff/products', '/admin/products', '/admin/products/create'] as $route) {
        $this->actingAs($staff)->get($route)->assertOk();
    }

    foreach ([
        '/admin/dashboard',
        '/staff/orders',
        '/admin/orders',
        '/admin/categories',
        '/admin/customers',
        '/admin/staff',
        '/admin/settings',
        '/admin/reports/sales',
    ] as $route) {
        $this->actingAs($staff)->get($route)->assertForbidden();
    }
});

it('sends staff users to the staff dashboard route', function () {
    $staff = backofficeUser([
        'is_staff' => true,
        'staff_role' => 'order_manager',
        'staff_assigned_at' => now(),
    ]);

    expect($staff->dashboardRouteName())->toBe('admin.staff.dashboard');

    $this->actingAs($staff)
        ->get('/user-dashboard')
        ->assertRedirect(route('admin.staff.dashboard'));
});

it('shows staff dashboard link on forbidden pages for staff users', function () {
    $staff = backofficeUser([
        'is_staff' => true,
        'staff_role' => 'order_manager',
        'staff_assigned_at' => now(),
    ]);

    $this->actingAs($staff)
        ->get('/admin/dashboard')
        ->assertForbidden()
        ->assertSee(route('admin.staff.dashboard'), false);
});
