<?php
// Bootstrap Laravel
$app = require __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Check Paystack settings
$settings = \App\Models\Setting::where('key', 'like', 'paystack%')
    ->orWhere('key', 'test_mode')
    ->get();

echo "=== PAYSTACK SETTINGS ===\n";
foreach ($settings as $setting) {
    $value = $setting->value;
    // Mask the key for security (show only first 5 and last 5 chars)
    if (strlen($value) > 10 && strpos($setting->key, 'key') !== false) {
        $value = substr($value, 0, 5) . '...' . substr($value, -5);
    }
    echo $setting->key . ": " . ($value ?: '[EMPTY]') . "\n";
}

echo "\n=== KEY VERIFICATION ===\n";
$testSecret = \App\Helpers\SettingsHelper::paystackSecretKey();
echo "Test Secret Key via Helper: " . ($testSecret ? 'YES (configured)' : 'NO (empty/null)') . "\n";

$testPublic = \App\Helpers\SettingsHelper::paystackPublicKey();
echo "Test Public Key via Helper: " . ($testPublic ? 'YES (configured)' : 'NO (empty/null)') . "\n";

echo "Test Mode: " . (\App\Helpers\SettingsHelper::isTestMode() ? 'YES' : 'NO') . "\n";
