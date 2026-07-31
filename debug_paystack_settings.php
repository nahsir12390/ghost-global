<?php
/**
 * Debug script to check Paystack settings
 * Run this from your project root
 */

// Change to the application directory
chdir(__DIR__);

// Load Composer autoloader
require 'vendor/autoload.php';

// Create application instance
$app = new \Illuminate\Foundation\Application(
    getcwd()
);

// Bind interfaces
$app->singleton(
    \Illuminate\Contracts\Http\Kernel::class,
    \App\Http\Kernel::class
);

try {
    // Load environment
    $dotenv = \Dotenv\Dotenv::createImmutable(__DIR__);
    $dotenv->load();
    
    // Connect to database
    $config = require 'config/database.php';
    $connection = new \PDO(
        "mysql:host=" . $_ENV['DB_HOST'] . ";dbname=" . $_ENV['DB_DATABASE'],
        $_ENV['DB_USERNAME'],
        $_ENV['DB_PASSWORD']
    );
    
    echo "=== PAYSTACK SETTINGS CHECK ===\n";
    echo "Database: " . $_ENV['DB_DATABASE'] . "\n";
    echo "Status: Connected ✓\n\n";
    
    // Query settings
    $stmt = $connection->prepare("SELECT `key`, `value` FROM settings WHERE `key` LIKE '%paystack%' OR `key` = 'test_mode' ORDER BY `key`");
    $stmt->execute();
    $settings = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($settings)) {
        echo "⚠️  NO PAYSTACK SETTINGS FOUND IN DATABASE!\n";
        echo "Please go to Admin → Settings → Payment and add your Paystack keys\n";
    } else {
        echo "Found " . count($settings) . " setting(s):\n";
        foreach ($settings as $setting) {
            $value = $setting['value'];
            $key = $setting['key'];
            
            // Mask sensitive values
            if (strpos($key, 'key') !== false && strlen($value) > 10) {
                $value = substr($value, 0, 5) . '...***...' . substr($value, -5);
            } elseif (empty($value)) {
                $value = '[EMPTY]';
            }
            
            echo "  • $key: $value\n";
        }
    }
    
    // Check test mode specifically
    echo "\n=== TEST MODE CHECK ===\n";
    $stmt = $connection->prepare("SELECT `value` FROM settings WHERE `key` = 'test_mode' LIMIT 1");
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $testMode = $result ? (int)$result['value'] : 0;
    echo "Test Mode: " . ($testMode ? 'ENABLED ✓' : 'DISABLED ✗') . "\n";
    
    if ($testMode) {
        echo "Using: paystack_test_public_key & paystack_test_secret_key\n";
    } else {
        echo "Using: paystack_public_key & paystack_secret_key\n";
    }
    
    // Check which keys are configured
    echo "\n=== KEY CONFIGURATION STATUS ===\n";
    
    $keys = ['paystack_test_public_key', 'paystack_test_secret_key', 'paystack_public_key', 'paystack_secret_key'];
    foreach ($keys as $keyName) {
        $stmt = $connection->prepare("SELECT `value` FROM settings WHERE `key` = ? LIMIT 1");
        $stmt->execute([$keyName]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $value = $result ? trim($result['value']) : '';
        $status = !empty($value) ? '✓ SET' : '✗ EMPTY';
        echo "  $keyName: $status\n";
    }
    
} catch (\Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . " (Line: " . $e->getLine() . ")\n";
    exit(1);
}
