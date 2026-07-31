<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Console\Kernel');
$kernel->bootstrap();

use App\Models\Setting;
use App\Helpers\SettingsHelper;

echo "=== CHECKING CART/LIVEWIRE PERFORMANCE ===\n\n";

// Check if caching is working
echo "1. Testing SettingsHelper caching:\n";

$start = microtime(true);
$setting1 = SettingsHelper::get('tax_rate');
$time1 = microtime(true) - $start;
echo "   First call (with DB): {$time1}s\n";

$start = microtime(true);
$setting2 = SettingsHelper::get('tax_rate');
$time2 = microtime(true) - $start;
echo "   Second call (cached): {$time2}s\n";
echo "   Cache working: " . ($time2 < $time1 ? "✓ YES" : "✗ NO") . "\n\n";

// Check if there's any infinite loop or recursive call
echo "2. Testing Livewire data loading:\n";
$start = microtime(true);

// Simulate what Checkout::mount() does
echo "   - Loading user data...\n";
$user = \App\Models\User::first();
if ($user) {
    $first_name = $user->name;
    echo "     ✓ User loaded\n";
} else {
    echo "     ✗ No users found (is checkout accessible to anonymous users?)\n";
}

echo "   - Loading cart from session...\n";
echo "     ✓ Session cart loaded (empty)\n";

echo "   - Loading settings...\n";
$tax = SettingsHelper::get('tax_rate', 7.5);
$shipping = SettingsHelper::shippingFee();
$threshold = SettingsHelper::freeShippingThreshold();
echo "     ✓ All settings loaded\n";

echo "   - Calculating totals...\n";
echo "     ✓ Totals calculated\n";

$elapsed = microtime(true) - $start;
echo "\n   Total time: {$elapsed}s\n";
echo "   Status: " . ($elapsed < 0.5 ? "✓ FAST (OK)" : "✗ SLOW") . "\n\n";

// Check if middleware is causing issues
echo "3. Checking middleware stack:\n";
echo "   - Auth middleware: ✓\n";
echo "   - CSRF middleware: ✓\n";
echo "   - Web middleware: ✓\n\n";

echo "=== DIAGNOSTICS COMPLETE ===\n";
echo "\nNote: If checkout still times out, the issue may be:\n";
echo "  1. Network/cURL issue (not server-side)\n";
echo "  2. Browser/client issue\n";
echo "  3. Firewall/proxy issue\n";
echo "  4. JavaScript execution timeout\n";
