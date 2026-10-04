<?php

namespace App\Helpers;

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class SettingsHelper
{
    protected static $cacheKey = 'app_settings';

    protected static $cacheTTL = 3600; // 1 hour

    protected static $defaultTeamMembers = [
        [
            'name' => 'Acholoh Emmanuel',
            'role' => 'Co-Founder & CEO',
            'image' => 'images/Erik picture.jpeg',
            'bio' => 'Acholoh Emmanuel is a Full Stack Developer and Co-Founder & CEO. He is passionate about building scalable and user-friendly web applications using modern technologies. As CEO, he leads the vision of the platform, driving innovation and ensuring the platform delivers value to users, vendors, and the wider community.',
        ],
        [
            'name' => 'Nasiru Zakari',
            'role' => 'Co-Founder & COO',
            'image' => 'images/nasiru picture.jpeg',
            'bio' => 'Nasiru Zakari is a Full Stack Web Developer and Co-Founder & COO. He specializes in building modern, scalable web applications using HTML, CSS, JavaScript, PHP, and Laravel. He oversees platform development, smooth system operations, and continuous user experience improvements.',
        ],
    ];

    public static function get($key, $default = null)
    {
        // Get all settings from cache
        $settings = Cache::remember(self::$cacheKey, self::$cacheTTL, function () {
            return Setting::all()->pluck('value', 'key')->toArray();
        });

        return $settings[$key] ?? $default;
    }

    public static function set($key, $value)
    {
        Setting::setValue($key, $value);
        // Clear cache so next request gets fresh data
        Cache::forget(self::$cacheKey);
    }

    public static function siteName(): string
    {
        return (string) self::get('site_name', config('app.name', 'Marketplace'));
    }

    public static function siteTagline(): string
    {
        return (string) self::get('site_tagline', 'Shop smarter. Discover more.');
    }

    public static function siteDescription(): string
    {
        return (string) self::get('site_description', 'A modern marketplace for products you will love.');
    }

    public static function currencyCode(): string
    {
        return strtoupper((string) self::get('site_currency', 'NGN'));
    }

    public static function currencySymbol(): string
    {
        return (string) self::get('site_currency_symbol', '₦');
    }

    public static function assetUrl(string $setting, string $fallback): string
    {
        $value = trim((string) self::get($setting, ''));

        if ($value === '') {
            return asset($fallback);
        }

        return Str::startsWith($value, ['http://', 'https://', '/']) ? $value : asset($value);
    }

    public static function logoUrl(): string
    {
        if (trim((string) self::get('site_logo', '')) === '' && file_exists(public_path('storage/logo.png'))) {
            return asset('storage/logo.png');
        }

        return self::assetUrl('site_logo', 'images/keffi-cart-logo.svg');
    }

    public static function faviconUrl(): string
    {
        return self::assetUrl('site_favicon', 'favicon.ico');
    }

    public static function browserStorageKey(string $feature): string
    {
        return Str::slug(self::siteName() ?: 'storefront').'-'.$feature;
    }

    public static function supportWhatsAppNumber()
    {
        return self::formatWhatsAppNumber(
            self::get('support_whatsapp_number') ?: self::get('site_phone') ?: ''
        );
    }

    public static function formatWhatsAppNumber($number)
    {
        return preg_replace('/\D+/', '', (string) $number);
    }

    public static function clearCache()
    {
        Cache::forget(self::$cacheKey);
    }

    public static function aboutTeamMembers()
    {
        $members = json_decode(self::get('about_team_members', '[]'), true);

        if (! is_array($members)) {
            return self::$defaultTeamMembers;
        }

        $members = array_values(array_filter(array_map(function ($member) {
            if (! is_array($member) || empty(trim($member['name'] ?? ''))) {
                return null;
            }

            return [
                'name' => trim($member['name'] ?? ''),
                'role' => trim($member['role'] ?? ''),
                'image' => trim($member['image'] ?? ''),
                'bio' => trim($member['bio'] ?? ''),
            ];
        }, $members)));

        return $members ?: self::$defaultTeamMembers;
    }

    public static function currency($amount)
    {
        return self::currencySymbol().number_format((float) $amount, 2);
    }

    public static function shippingFee()
    {
        return (float) self::get('shipping_fee', 700);
    }

    public static function freeShippingThreshold()
    {
        return (float) self::get('free_shipping_threshold', 10000);
    }

    public static function estimatedDeliveryDays()
    {
        return (int) self::get('estimated_delivery_days', 3);
    }

    public static function checkoutDefaultCountry(): string
    {
        return (string) self::get('checkout_default_country', 'Nigeria');
    }

    public static function checkoutDefaultState(): string
    {
        return (string) self::get('checkout_default_state', 'Nasarawa');
    }

    public static function checkoutDefaultCity(): string
    {
        return (string) self::get('checkout_default_city', '');
    }

    public static function checkoutDefaultPostalCode(): string
    {
        return (string) self::get('checkout_default_postal_code', '961101');
    }

    public static function checkoutDefaultAddress(): string
    {
        return (string) self::get('checkout_default_address', '');
    }

    public static function checkoutDefaults(): array
    {
        return [
            'country' => self::checkoutDefaultCountry(),
            'state' => self::checkoutDefaultState(),
            'city' => self::checkoutDefaultCity(),
            'postal_code' => self::checkoutDefaultPostalCode(),
            'address' => self::checkoutDefaultAddress(),
        ];
    }

    public static function isMaintenanceMode()
    {
        return (bool) self::get('maintenance_mode', false);
    }

    public static function getPaymentGateway()
    {
        return self::get('default_payment_gateway', 'paystack');
    }

    public static function isCashOnDeliveryEnabled()
    {
        return (bool) self::get('enable_cash_on_delivery', true);
    }

    public static function isWalletEnabled(): bool
    {
        return (bool) self::get('enable_wallet', true);
    }

    public static function walletMinimumTopup(): float
    {
        return (float) self::get('wallet_minimum_topup', 500);
    }

    public static function isReferralEnabled(): bool
    {
        return (bool) self::get('enable_referrals', true);
    }

    public static function referralRewardAmount(): float
    {
        return (float) self::get('referral_reward_amount', 500);
    }

    public static function isTestMode()
    {
        return (bool) self::get('test_mode', true);
    }

    public static function paystackPublicKey()
    {
        $testMode = self::isTestMode();

        return $testMode
            ? self::get('paystack_test_public_key')
            : self::get('paystack_public_key');
    }

    public static function paystackSecretKey()
    {
        $testMode = self::isTestMode();

        return $testMode
            ? self::get('paystack_test_secret_key')
            : self::get('paystack_secret_key');
    }

    public static function taxRate()
    {
        return self::platformServiceFeeRate(0);
    }

    public static function platformServiceFeeRate(float $subtotal): float
    {
        $firstTierMax = (float) self::get('platform_fee_tier_1_max', 10000);
        $secondTierMax = (float) self::get('platform_fee_tier_2_max', 50000);
        $thirdTierMax = (float) self::get('platform_fee_tier_3_max', 200000);

        if ($subtotal <= $firstTierMax) {
            return (float) self::get('platform_fee_tier_1_rate', 7.5);
        }

        if ($subtotal <= $secondTierMax) {
            return (float) self::get('platform_fee_tier_2_rate', 5);
        }

        if ($subtotal <= $thirdTierMax) {
            return (float) self::get('platform_fee_tier_3_rate', 3);
        }

        return (float) self::get('platform_fee_tier_4_rate', 2);
    }

    public static function platformServiceFee(float $subtotal): float
    {
        return round($subtotal * (self::platformServiceFeeRate($subtotal) / 100), 2);
    }

    public static function getShippingFee($subtotal = 0)
    {
        $shippingFee = self::shippingFee();
        $freeShippingThreshold = self::freeShippingThreshold();

        if ($freeShippingThreshold > 0 && $subtotal >= $freeShippingThreshold) {
            return 0;
        }

        return $shippingFee;
    }

    public static function calculateOrderBreakdown(float $subtotal): array
    {
        $serviceFeeRate = self::platformServiceFeeRate($subtotal);
        $serviceFee = round($subtotal * ($serviceFeeRate / 100), 2);
        $shipping = round(self::getShippingFee($subtotal), 2);

        return [
            'subtotal' => round($subtotal, 2),
            'service_fee_rate' => $serviceFeeRate,
            'service_fee' => $serviceFee,
            'shipping' => $shipping,
            'total' => round($subtotal + $serviceFee + $shipping, 2),
        ];
    }

    public static function calculateOrderTotal($subtotal)
    {
        return self::calculateOrderBreakdown((float) $subtotal)['total'];
    }
}
