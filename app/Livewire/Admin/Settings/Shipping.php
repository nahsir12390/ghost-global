<?php

namespace App\Livewire\Admin\Settings;

use Livewire\Component;
use App\Models\Setting;
use App\Helpers\SettingsHelper;
use Illuminate\Support\Facades\Cache;

class Shipping extends Component
{
    public $settings = [];

    protected $rules = [
        'settings.shipping_enabled' => 'boolean',
        'settings.free_shipping_threshold' => 'nullable|numeric|min:0',
        'settings.shipping_fee' => 'nullable|numeric|min:0',
        'settings.shipping_method' => 'required|in:flat_rate,free,calculated',
        'settings.estimated_delivery_days' => 'nullable|integer|min:1',
    ];

    protected $messages = [
        'settings.shipping_method.required' => 'Please select a shipping method.',
        'settings.shipping_fee.numeric' => 'Shipping fee must be a valid number.',
        'settings.free_shipping_threshold.numeric' => 'Free delivery threshold must be a valid number.',
        'settings.estimated_delivery_days.integer' => 'Estimated delivery days must be a valid number.',
    ];

    public function mount()
    {
        $this->loadSettings();
    }

    public function loadSettings()
    {
        // Load all shipping settings
        $settings = Setting::where('group', 'shipping')->get();
        
        foreach ($settings as $setting) {
            $this->settings[$setting->key] = $setting->value;
        }

        // Set default values if not exist
        $defaults = [
            'shipping_enabled' => true,
            'shipping_method' => 'flat_rate',
            'shipping_fee' => 700,
            'free_shipping_threshold' => 10000,
            'estimated_delivery_days' => 3,
        ];

        foreach ($defaults as $key => $value) {
            if (!isset($this->settings[$key])) {
                $this->settings[$key] = $value;
            }
        }
        
        // Convert string booleans to actual booleans
        $this->settings['shipping_enabled'] = filter_var($this->settings['shipping_enabled'], FILTER_VALIDATE_BOOLEAN);
    }

    // Add explicit toggle method for shipping enabled
    public function toggleShippingEnabled()
    {
        $this->settings['shipping_enabled'] = !$this->settings['shipping_enabled'];
    }

    public function save()
    {
        $this->validate();

        try {
            foreach ($this->settings as $key => $value) {
                // Handle boolean values - convert to string for database
                if ($key === 'shipping_enabled') {
                    $value = $value ? '1' : '0';
                }
                
                Setting::updateOrCreate(
                    ['key' => $key],
                    [
                        'value' => $value,
                        'type' => $key === 'shipping_enabled' ? 'boolean' : 'string',
                        'group' => 'shipping',
                        'label' => $this->getLabel($key),
                        'order' => $this->getOrder($key),
                        'is_public' => true,
                    ]
                );
            }
            
            // Clear settings cache
            Cache::forget('settings_shipping');
            if (class_exists(SettingsHelper::class)) {
                SettingsHelper::clearCache();
            }
            
            session()->flash('success', 'Shipping settings saved successfully!');
            
            // Reload settings to ensure they're saved
            $this->loadSettings();
            
            // Dispatch event for notification
            $this->dispatch('settings-saved');
            
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to save settings: ' . $e->getMessage());
        }
    }

    private function getLabel($key)
    {
        $labels = [
            'shipping_enabled' => 'Enable Shipping',
            'free_shipping_threshold' => 'Free Delivery Threshold',
            'shipping_fee' => 'Delivery Fee',
            'shipping_method' => 'Shipping Method',
            'estimated_delivery_days' => 'Estimated Delivery Days',
        ];

        return $labels[$key] ?? ucwords(str_replace('_', ' ', $key));
    }

    private function getOrder($key)
    {
        $order = [
            'shipping_enabled' => 1,
            'shipping_method' => 2,
            'shipping_fee' => 3,
            'free_shipping_threshold' => 4,
            'estimated_delivery_days' => 5,
        ];

        return $order[$key] ?? 99;
    }

    public function render()
    {
        return view('livewire.admin.settings.shipping');
    }
}
