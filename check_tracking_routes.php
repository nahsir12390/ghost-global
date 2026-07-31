#!/usr/bin/env php
<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Route;

$routes = Route::getRoutes();
$found = false;

echo "Checking for tracking routes...\n\n";

foreach ($routes as $route) {
    if (strpos($route->getName() ?? '', 'tracking') !== false) {
        echo "✓ Found: {$route->getName()}\n";
        echo "  Methods: " . implode('|', $route->methods) . "\n";
        echo "  URI: {$route->uri()}\n\n";
        $found = true;
    }
}

if (!$found) {
    echo "✗ No tracking routes found\n";
    echo "\nAll registered routes:\n";
    foreach ($routes as $route) {
        if ($route->getName()) {
            echo "- {$route->getName()}\n";
        }
    }
} else {
    echo "✓ All tracking routes are registered!\n";
}
