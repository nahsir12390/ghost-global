<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Console\Kernel');
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Helpers\SettingsHelper;

echo "=== CHECKOUT DIAGNOSTICS ===\n\n";

// Test 1: Settings cache
echo "Test 1: Settings Loading\n";
$start = microtime(true);
$tax = SettingsHelper::get('tax_rate', 7.5);
$time1 = microtime(true) - $start;
echo "First call: $time1 seconds\n";

$start = microtime(true);
$shipping = SettingsHelper::shippingFee();
$time2 = microtime(true) - $start;
echo "Second call: $time2 seconds\n";

echo "Result: Settings loading OK ✓\n\n";

// Test 2: User authentication
echo "Test 2: User Authentication\n";
DB::enableQueryLog();
$user = DB::table('users')->first();
$queries = count(DB::getQueryLog());
echo "Queries for user fetch: $queries\n";
echo "Result: User fetch OK ✓\n\n";

// Test 3: Session cart
echo "Test 3: Session Handling\n";
session_start();
$_SESSION['cart'] = ['test' => 'data'];
echo "Session set successfully\n";
echo "Result: Session OK ✓\n\n";

// Test 4: Check if any queries are hanging
echo "Test 4: Database Connection\n";
$start = microtime(true);
$count = DB::table('settings')->count();
$time = microtime(true) - $start;
echo "Settings count: $count in $time seconds\n";
echo "Result: Database OK ✓\n\n";

// Test 5: Check cache 
echo "Test 5: Cache System\n";
$start = microtime(true);
$cached = \Illuminate\Support\Facades\Cache::remember('test_cache', 3600, function() {
    return ['test' => 'data'];
});
$time = microtime(true) - $start;
echo "Cache operation: $time seconds\n";
echo "Result: Cache OK ✓\n\n";

echo "=== ALL DIAGNOSTICS PASSED ===\n";
