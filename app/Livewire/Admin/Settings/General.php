<?php

namespace App\Livewire\Admin\Settings;

use Livewire\Component;
use App\Models\Setting;
use App\Helpers\SettingsHelper;

class General extends Component
{
    public $settings = [];

    protected $rules = [
        'settings.site_name' => 'required|string|max:255',
        'settings.site_email' => 'required|email',
        'settings.site_phone' => 'nullable|string|max:20',
        'settings.site_address' => 'nullable|string',
        'settings.support_whatsapp_number' => 'nullable|string|max:30',
        'settings.site_currency' => 'required|string|size:3',
        'settings.site_currency_symbol' => 'required|string|max:5',
        'settings.site_description' => 'nullable|string',
        'settings.site_logo' => 'nullable|string',
        'settings.site_favicon' => 'nullable|string',
        'settings.checkout_default_country' => 'nullable|string|max:100',
        'settings.checkout_default_state' => 'nullable|string|max:100',
        'settings.checkout_default_city' => 'nullable|string|max:100',
        'settings.checkout_default_postal_code' => 'nullable|string|max:20',
        'settings.checkout_default_address' => 'nullable|string|max:1000',
        'settings.platform_fee_tier_1_max' => 'required|numeric|min:0',
        'settings.platform_fee_tier_1_rate' => 'required|numeric|min:0|max:100',
        'settings.platform_fee_tier_2_max' => 'required|numeric|min:0|gt:settings.platform_fee_tier_1_max',
        'settings.platform_fee_tier_2_rate' => 'required|numeric|min:0|max:100',
        'settings.platform_fee_tier_3_max' => 'required|numeric|min:0|gt:settings.platform_fee_tier_2_max',
        'settings.platform_fee_tier_3_rate' => 'required|numeric|min:0|max:100',
        'settings.platform_fee_tier_4_rate' => 'required|numeric|min:0|max:100',
        'settings.maintenance_mode' => 'boolean',
    ];

    public function mount()
    {
        // Load all general settings
        $settings = Setting::where('group', 'general')->get();
        
        foreach ($settings as $setting) {
            $this->settings[$setting->key] = $setting->value;
        }

        // Set default values if not exists
        $defaults = [
            'site_name' => config('app.name'),
            'site_email' => config('mail.from.address'),
            'support_whatsapp_number' => '',
            'site_currency' => 'NGN',
            'site_currency_symbol' => '₦',
            'checkout_default_country' => 'Nigeria',
            'checkout_default_state' => 'Nasarawa',
            'checkout_default_city' => '',
            'checkout_default_postal_code' => '961101',
            'checkout_default_address' => '',
            'platform_fee_tier_1_max' => '10000',
            'platform_fee_tier_1_rate' => '7.5',
            'platform_fee_tier_2_max' => '50000',
            'platform_fee_tier_2_rate' => '5',
            'platform_fee_tier_3_max' => '200000',
            'platform_fee_tier_3_rate' => '3',
            'platform_fee_tier_4_rate' => '2',
            'maintenance_mode' => false,
        ];

        foreach ($defaults as $key => $value) {
            if (!isset($this->settings[$key])) {
                $this->settings[$key] = $value;
            }
        }
    }

    public function save()
    {
        $this->validate();

        foreach ($this->settings as $key => $value) {
            Setting::updateOrCreate(
                ['key' => $key],
                [
                    'value' => $value,
                    'type' => 'string',
                    'group' => 'general',
                    'label' => $this->getLabel($key),
                    'order' => $this->getOrder($key),
                ]
            );
        }

        SettingsHelper::clearCache();

        $this->dispatch('notify', [
            'type' => 'success',
            'message' => 'Settings saved successfully!',
        ]);
    }

    private function getLabel($key)
    {
        $labels = [
            'site_name' => 'Site Name',
            'site_email' => 'Site Email',
            'site_phone' => 'Site Phone',
            'site_address' => 'Site Address',
            'support_whatsapp_number' => 'Support WhatsApp Number',
            'site_currency' => 'Currency Code',
            'site_currency_symbol' => 'Currency Symbol',
            'site_description' => 'Site Description',
            'site_logo' => 'Site Logo URL',
            'site_favicon' => 'Favicon URL',
            'checkout_default_country' => 'Checkout Default Country',
            'checkout_default_state' => 'Checkout Default State',
            'checkout_default_city' => 'Checkout Default City',
            'checkout_default_postal_code' => 'Checkout Default Postal Code',
            'checkout_default_address' => 'Checkout Default Address',
            'platform_fee_tier_1_max' => 'Tier 1 Maximum',
            'platform_fee_tier_1_rate' => 'Tier 1 Service Fee (%)',
            'platform_fee_tier_2_max' => 'Tier 2 Maximum',
            'platform_fee_tier_2_rate' => 'Tier 2 Service Fee (%)',
            'platform_fee_tier_3_max' => 'Tier 3 Maximum',
            'platform_fee_tier_3_rate' => 'Tier 3 Service Fee (%)',
            'platform_fee_tier_4_rate' => 'Final Tier Service Fee (%)',
            'maintenance_mode' => 'Maintenance Mode',
        ];

        return $labels[$key] ?? ucwords(str_replace('_', ' ', $key));
    }

    private function getOrder($key)
    {
        $order = [
            'site_name' => 1,
            'site_email' => 2,
            'site_phone' => 3,
            'site_address' => 4,
            'support_whatsapp_number' => 5,
            'site_currency' => 6,
            'site_currency_symbol' => 7,
            'site_description' => 8,
            'site_logo' => 9,
            'site_favicon' => 10,
            'checkout_default_country' => 11,
            'checkout_default_state' => 12,
            'checkout_default_city' => 13,
            'checkout_default_postal_code' => 14,
            'checkout_default_address' => 15,
            'platform_fee_tier_1_max' => 16,
            'platform_fee_tier_1_rate' => 17,
            'platform_fee_tier_2_max' => 18,
            'platform_fee_tier_2_rate' => 19,
            'platform_fee_tier_3_max' => 20,
            'platform_fee_tier_3_rate' => 21,
            'platform_fee_tier_4_rate' => 22,
            'maintenance_mode' => 23,
        ];

        return $order[$key] ?? 99;
    }

    public function render()
    {
        return view('livewire.admin.settings.general');
    }
}
