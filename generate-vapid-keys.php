<?php
/**
 * Generate VAPID Keys for Web Push Notifications
 * Run: php generate-vapid-keys.php
 */

// Generate random bytes for public and private keys
function generateVapidKeys() {
    // Generate 65 bytes for the public key (uncompressed)
    $publicKeyBytes = openssl_random_pseudo_bytes(65);
    
    // Generate 32 bytes for the private key
    $privateKeyBytes = openssl_random_pseudo_bytes(32);
    
    // Encode as URL-safe base64
    $publicKey = rtrim(strtr(base64_encode($publicKeyBytes), '+/', '-_'), '=');
    $privateKey = rtrim(strtr(base64_encode($privateKeyBytes), '+/', '-_'), '=');
    
    return [
        'public' => $publicKey,
        'private' => $privateKey,
    ];
}

// Generate keys
$keys = generateVapidKeys();

// Display results
echo "✅ VAPID Keys Generated Successfully!\n";
echo "\n";
echo "Add these to your .env file:\n";
echo "═══════════════════════════════════════════════════════════\n";
echo "PUSH_PUBLIC_KEY=" . $keys['public'] . "\n";
echo "PUSH_PRIVATE_KEY=" . $keys['private'] . "\n";
echo "═══════════════════════════════════════════════════════════\n";
echo "\n";
echo "Then run: php artisan config:cache\n";
?>
