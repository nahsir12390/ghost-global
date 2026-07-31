<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\SettingsHelper;
use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    /**
     * Display settings index page.
     */
    public function index()
    {
        $this->createDefaultSettings();

        return view('admin.settings.index');
    }

    /**
     * Metadata for general settings so new keys can be created safely.
     */
    private function generalSettingDefinitions(): array
    {
        return [
            'site_name' => ['value' => config('app.name'), 'type' => 'string', 'label' => 'Site Name', 'order' => 1, 'is_public' => true],
            'site_email' => ['value' => config('mail.from.address'), 'type' => 'email', 'label' => 'Site Email', 'order' => 2, 'is_public' => true],
            'site_phone' => ['value' => '', 'type' => 'string', 'label' => 'Site Phone', 'order' => 3, 'is_public' => true],
            'site_address' => ['value' => '', 'type' => 'textarea', 'label' => 'Site Address', 'order' => 4, 'is_public' => true],
            'support_whatsapp_number' => ['value' => '', 'type' => 'string', 'label' => 'Support WhatsApp Number', 'order' => 5, 'is_public' => true],
            'site_currency' => ['value' => 'NGN', 'type' => 'string', 'label' => 'Currency Code', 'order' => 6, 'is_public' => true],
            'site_currency_symbol' => ['value' => '₦', 'type' => 'string', 'label' => 'Currency Symbol', 'order' => 7, 'is_public' => true],
            'site_description' => ['value' => '', 'type' => 'textarea', 'label' => 'Site Description', 'order' => 8, 'is_public' => true],
            'site_logo' => ['value' => '', 'type' => 'string', 'label' => 'Site Logo URL', 'order' => 9, 'is_public' => true],
            'site_favicon' => ['value' => '', 'type' => 'string', 'label' => 'Favicon URL', 'order' => 10, 'is_public' => true],
            'maintenance_mode' => ['value' => '0', 'type' => 'boolean', 'label' => 'Maintenance Mode', 'order' => 11, 'is_public' => false],
            'checkout_default_country' => ['value' => 'Nigeria', 'type' => 'string', 'label' => 'Checkout Default Country', 'order' => 12, 'is_public' => true],
            'checkout_default_state' => ['value' => 'Nasarawa', 'type' => 'string', 'label' => 'Checkout Default State', 'order' => 13, 'is_public' => true],
            'checkout_default_city' => ['value' => '', 'type' => 'string', 'label' => 'Checkout Default City', 'order' => 14, 'is_public' => true],
            'checkout_default_postal_code' => ['value' => '961101', 'type' => 'string', 'label' => 'Checkout Default Postal Code', 'order' => 15, 'is_public' => true],
            'checkout_default_address' => ['value' => '', 'type' => 'string', 'label' => 'Checkout Default Address', 'order' => 16, 'is_public' => true],
            'platform_fee_tier_1_max' => ['value' => '10000', 'type' => 'number', 'label' => 'Tier 1 Maximum', 'order' => 17, 'is_public' => true],
            'platform_fee_tier_1_rate' => ['value' => '7.5', 'type' => 'number', 'label' => 'Tier 1 Service Fee (%)', 'order' => 18, 'is_public' => true],
            'platform_fee_tier_2_max' => ['value' => '50000', 'type' => 'number', 'label' => 'Tier 2 Maximum', 'order' => 19, 'is_public' => true],
            'platform_fee_tier_2_rate' => ['value' => '5', 'type' => 'number', 'label' => 'Tier 2 Service Fee (%)', 'order' => 20, 'is_public' => true],
            'platform_fee_tier_3_max' => ['value' => '200000', 'type' => 'number', 'label' => 'Tier 3 Maximum', 'order' => 21, 'is_public' => true],
            'platform_fee_tier_3_rate' => ['value' => '3', 'type' => 'number', 'label' => 'Tier 3 Service Fee (%)', 'order' => 22, 'is_public' => true],
            'platform_fee_tier_4_rate' => ['value' => '2', 'type' => 'number', 'label' => 'Final Tier Service Fee (%)', 'order' => 23, 'is_public' => true],
        ];
    }

    /**
     * Create default settings for the application.
     */
    private function createDefaultSettings()
    {
        $defaultSettings = [];

        foreach ($this->generalSettingDefinitions() as $key => $definition) {
            $defaultSettings[] = array_merge($definition, [
                'key' => $key,
                'group' => 'general',
            ]);
        }

        $defaultSettings[] = [
            'key' => 'about_team_members',
            'value' => json_encode(SettingsHelper::aboutTeamMembers()),
            'type' => 'json',
            'group' => 'general',
            'label' => 'About Page Team Members',
            'order' => 24,
            'is_public' => true,
        ];

        $defaultSettings = array_merge($defaultSettings, [
            [
                'key' => 'default_payment_gateway',
                'value' => 'paystack',
                'type' => 'string',
                'group' => 'payment',
                'label' => 'Default Payment Gateway',
                'order' => 1,
                'is_public' => true,
            ],
            [
                'key' => 'enable_cash_on_delivery',
                'value' => '1',
                'type' => 'boolean',
                'group' => 'payment',
                'label' => 'Enable Cash on Delivery',
                'order' => 2,
                'is_public' => true,
            ],
            [
                'key' => 'enable_wallet',
                'value' => '1',
                'type' => 'boolean',
                'group' => 'payment',
                'label' => 'Enable Wallet',
                'order' => 3,
                'is_public' => true,
            ],
            [
                'key' => 'wallet_minimum_topup',
                'value' => '500',
                'type' => 'number',
                'group' => 'payment',
                'label' => 'Wallet Minimum Top-up',
                'order' => 4,
                'is_public' => true,
            ],
            [
                'key' => 'test_mode',
                'value' => '1',
                'type' => 'boolean',
                'group' => 'payment',
                'label' => 'Test Mode',
                'order' => 5,
                'is_public' => false,
            ],
            [
                'key' => 'paystack_test_public_key',
                'value' => '',
                'type' => 'string',
                'group' => 'payment',
                'label' => 'Paystack Test Public Key',
                'order' => 6,
                'is_public' => false,
            ],
            [
                'key' => 'paystack_test_secret_key',
                'value' => '',
                'type' => 'string',
                'group' => 'payment',
                'label' => 'Paystack Test Secret Key',
                'order' => 7,
                'is_public' => false,
            ],
            [
                'key' => 'paystack_public_key',
                'value' => '',
                'type' => 'string',
                'group' => 'payment',
                'label' => 'Paystack Public Key',
                'order' => 8,
                'is_public' => false,
            ],
            [
                'key' => 'paystack_secret_key',
                'value' => '',
                'type' => 'string',
                'group' => 'payment',
                'label' => 'Paystack Secret Key',
                'order' => 9,
                'is_public' => false,
            ],
            [
                'key' => 'flutterwave_public_key',
                'value' => '',
                'type' => 'string',
                'group' => 'payment',
                'label' => 'Flutterwave Public Key',
                'order' => 10,
                'is_public' => false,
            ],
            [
                'key' => 'flutterwave_secret_key',
                'value' => '',
                'type' => 'string',
                'group' => 'payment',
                'label' => 'Flutterwave Secret Key',
                'order' => 11,
                'is_public' => false,
            ],
            [
                'key' => 'flutterwave_test_public_key',
                'value' => '',
                'type' => 'string',
                'group' => 'payment',
                'label' => 'Flutterwave Test Public Key',
                'order' => 12,
                'is_public' => false,
            ],
            [
                'key' => 'flutterwave_test_secret_key',
                'value' => '',
                'type' => 'string',
                'group' => 'payment',
                'label' => 'Flutterwave Test Secret Key',
                'order' => 13,
                'is_public' => false,
            ],
            [
                'key' => 'shipping_enabled',
                'value' => '1',
                'type' => 'boolean',
                'group' => 'shipping',
                'label' => 'Enable Shipping',
                'order' => 1,
                'is_public' => true,
            ],
            [
                'key' => 'shipping_method',
                'value' => 'flat_rate',
                'type' => 'string',
                'group' => 'shipping',
                'label' => 'Shipping Method',
                'order' => 2,
                'is_public' => true,
            ],
            [
                'key' => 'shipping_fee',
                'value' => '1000',
                'type' => 'number',
                'group' => 'shipping',
                'label' => 'Delivery Fee',
                'order' => 3,
                'is_public' => true,
            ],
            [
                'key' => 'free_shipping_threshold',
                'value' => '10000',
                'type' => 'number',
                'group' => 'shipping',
                'label' => 'Free Delivery Threshold',
                'order' => 4,
                'is_public' => true,
            ],
            [
                'key' => 'estimated_delivery_days',
                'value' => '3',
                'type' => 'number',
                'group' => 'shipping',
                'label' => 'Estimated Delivery Days',
                'order' => 5,
                'is_public' => true,
            ],
        ]);

        foreach ($defaultSettings as $setting) {
            Setting::firstOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }
    }

    /**
     * Get all settings grouped by category.
     */
    public function getAllSettings()
    {
        $settings = Setting::orderBy('group')->orderBy('order')->get();

        return response()->json([
            'success' => true,
            'data' => $settings->groupBy('group'),
        ]);
    }

    /**
     * Update GENERAL settings only.
     */
    public function updateGeneral(Request $request)
    {
        $validated = $request->validate([
            'site_name' => 'nullable|string|max:255',
            'site_email' => 'nullable|email|max:255',
            'site_phone' => 'nullable|string|max:255',
            'site_address' => 'nullable|string',
            'support_whatsapp_number' => 'nullable|string|max:30',
            'site_currency' => 'nullable|string|size:3',
            'site_currency_symbol' => 'nullable|string|max:5',
            'site_description' => 'nullable|string',
            'site_logo' => 'nullable|string|max:255',
            'site_favicon' => 'nullable|string|max:255',
            'checkout_default_country' => 'nullable|string|max:100',
            'checkout_default_state' => 'nullable|string|max:100',
            'checkout_default_city' => 'nullable|string|max:100',
            'checkout_default_postal_code' => 'nullable|string|max:20',
            'checkout_default_address' => 'nullable|string|max:1000',
            'platform_fee_tier_1_max' => 'required|numeric|min:0',
            'platform_fee_tier_1_rate' => 'required|numeric|min:0|max:100',
            'platform_fee_tier_2_max' => 'required|numeric|min:0|gt:platform_fee_tier_1_max',
            'platform_fee_tier_2_rate' => 'required|numeric|min:0|max:100',
            'platform_fee_tier_3_max' => 'required|numeric|min:0|gt:platform_fee_tier_2_max',
            'platform_fee_tier_3_rate' => 'required|numeric|min:0|max:100',
            'platform_fee_tier_4_rate' => 'required|numeric|min:0|max:100',
            'maintenance_mode' => 'nullable|boolean',
            'team_members' => 'nullable|array',
            'team_members.*.name' => 'nullable|string|max:255',
            'team_members.*.role' => 'nullable|string|max:255',
            'team_members.*.image' => 'nullable|string|max:255',
            'team_members.*.photo' => 'nullable|image|mimes:jpg,jpeg,png,gif,webp|max:4096',
            'team_members.*.bio' => 'nullable|string',
        ]);

        $teamMembers = collect($request->input('team_members', []))
            ->filter(fn ($member) => filled($member['name'] ?? null))
            ->map(function ($member, $index) use ($request) {
                $image = trim($member['image'] ?? '');
                $photo = $request->file("team_members.{$index}.photo");

                if ($photo && $photo->isValid()) {
                    $image = 'storage/' . $photo->store('about-team', 'public');
                }

                return [
                    'name' => trim($member['name'] ?? ''),
                    'role' => trim($member['role'] ?? ''),
                    'image' => $image,
                    'bio' => trim($member['bio'] ?? ''),
                ];
            })
            ->values()
            ->all();

        unset($validated['team_members']);

        $definitions = $this->generalSettingDefinitions();

        foreach ($validated as $key => $value) {
            $definition = $definitions[$key] ?? [
                'type' => 'string',
                'label' => ucwords(str_replace('_', ' ', $key)),
                'order' => 999,
                'is_public' => false,
            ];

            Setting::updateOrCreate(
                ['key' => $key],
                [
                    'value' => $value,
                    'type' => $definition['type'],
                    'group' => 'general',
                    'label' => $definition['label'],
                    'order' => $definition['order'],
                    'is_public' => $definition['is_public'],
                ]
            );
        }

        Setting::updateOrCreate(
            ['key' => 'about_team_members'],
            [
                'value' => json_encode($teamMembers),
                'type' => 'json',
                'group' => 'general',
                'label' => 'About Page Team Members',
                'order' => 24,
                'is_public' => true,
            ]
        );

        SettingsHelper::clearCache();

        return redirect()->back()->with('success', 'General settings updated successfully!');
    }

    /**
     * Update SHIPPING settings only.
     */
    public function updateShipping(Request $request)
    {
        $validated = $request->validate([
            'shipping_enabled' => 'nullable|boolean',
            'shipping_method' => 'nullable|string|in:flat_rate,free,local_pickup',
            'shipping_fee' => 'nullable|numeric|min:0',
            'free_shipping_threshold' => 'nullable|numeric|min:0',
            'estimated_delivery_days' => 'nullable|integer|min:1',
        ]);

        foreach ($validated as $key => $value) {
            Setting::updateOrCreate(
                ['key' => $key, 'group' => 'shipping'],
                [
                    'value' => $value,
                    'type' => in_array($key, ['shipping_enabled']) ? 'boolean' : 'string',
                ]
            );
        }

        SettingsHelper::clearCache();

        return redirect()->back()->with('success', 'Shipping settings updated successfully!');
    }
}
