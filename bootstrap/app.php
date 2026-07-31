<?php

use Illuminate\Foundation\Application;
use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\EnsureVerifiedVendor;
use App\Http\Middleware\RoleMiddleware;
use App\Http\Middleware\StaffOnly;
use App\Http\Middleware\CanManageOrders;
use App\Http\Middleware\CanManageProducts;
use App\Http\Middleware\CanManageOrdersOrAdmin;
use App\Http\Middleware\CanManageProductsOrAdmin;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'admin' => AdminMiddleware::class,
            'role' => RoleMiddleware::class,
            'verified.vendor' => EnsureVerifiedVendor::class,
            'staff.only' => StaffOnly::class,
            'staff.orders' => CanManageOrders::class,
            'staff.products' => CanManageProducts::class,
            'can.manage.orders.or.admin' => CanManageOrdersOrAdmin::class,
            'can.manage.products.or.admin' => CanManageProductsOrAdmin::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
