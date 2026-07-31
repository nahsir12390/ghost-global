<?php

namespace App\Livewire\Admin\Settings;

use Livewire\Component;
use App\Models\Setting;
use App\Helpers\SettingsHelper;
use Illuminate\Support\Facades\Cache;

class Payment extends Component
{
    public $settings = [];
    public $selectedGateway = 'paystack';
    
    protected $rules = [
        'settings.default_payment_gateway' => 'required|in:paystack,flutterwave,cash',
        'settings.enable_cash_on_delivery' => 'boolean',
        'settings.enable_wallet' => 'boolean',
        'settings.wallet_minimum_topup' => 'nullable|numeric|min:0',
        'settings.test_mode' => 'boolean',
        // Paystack keys
        'settings.paystack_public_key' => 'nullable|string',
        'settings.paystack_secret_key' => 'nullable|string',
        'settings.paystack_test_public_key' => 'nullable|string',
        'settings.paystack_test_secret_key' => 'nullable|string',
        // Flutterwave keys
        'settings.flutterwave_public_key' => 'nullable|string',
        'settings.flutterwave_secret_key' => 'nullable|string',
        'settings.flutterwave_test_public_key' => 'nullable|string',
        'settings.flutterwave_test_secret_key' => 'nullable|string',
    ];

    protected $messages = [
        'settings.default_payment_gateway.required' => 'Please select a default payment gateway.',
        'settings.default_payment_gateway.in' => 'Invalid payment gateway selected.',
    ];

    public function mount()
    {
        $this->loadSettings();
    }

    public function loadSettings()
    {
        // Load all payment settings
        $settings = Setting::where('group', 'payment')->get();
        
        foreach ($settings as $setting) {
            $this->settings[$setting->key] = $setting->value;
        }

        // Set default values if not exist
        $defaults = [
            'default_payment_gateway' => 'paystack',
            'enable_cash_on_delivery' => true,
            'enable_wallet' => true,
            'wallet_minimum_topup' => 500,
            'test_mode' => true,
            // Paystack
            'paystack_public_key' => '',
            'paystack_secret_key' => '',
            'paystack_test_public_key' => '',
            'paystack_test_secret_key' => '',
            // Flutterwave
            'flutterwave_public_key' => '',
            'flutterwave_secret_key' => '',
            'flutterwave_test_public_key' => '',
            'flutterwave_test_secret_key' => '',
        ];

        foreach ($defaults as $key => $value) {
            if (!isset($this->settings[$key])) {
                $this->settings[$key] = $value;
            }
        }
        
        // Convert string booleans to actual booleans
        $this->settings['enable_cash_on_delivery'] = filter_var($this->settings['enable_cash_on_delivery'], FILTER_VALIDATE_BOOLEAN);
        $this->settings['enable_wallet'] = filter_var($this->settings['enable_wallet'], FILTER_VALIDATE_BOOLEAN);
        $this->settings['test_mode'] = filter_var($this->settings['test_mode'], FILTER_VALIDATE_BOOLEAN);
        
        $this->selectedGateway = $this->settings['default_payment_gateway'];
    }

    public function updatedSelectedGateway($value)
    {
        $this->settings['default_payment_gateway'] = $value;
    }

    // Add explicit toggle methods
    public function toggleCashOnDelivery()
    {
        $this->settings['enable_cash_on_delivery'] = !$this->settings['enable_cash_on_delivery'];
    }

    public function toggleWallet()
    {
        $this->settings['enable_wallet'] = !$this->settings['enable_wallet'];
    }
    
    public function toggleTestMode()
    {
        $this->settings['test_mode'] = !$this->settings['test_mode'];
    }

    public function save()
    {
        $this->validate();

        try {
            foreach ($this->settings as $key => $value) {
                // Handle boolean values - convert to string for database
                if (in_array($key, ['enable_cash_on_delivery', 'enable_wallet', 'test_mode'])) {
                    $value = $value ? '1' : '0';
                }
                
                Setting::updateOrCreate(
                    ['key' => $key],
                    [
                        'value' => $value,
                        'type' => in_array($key, ['enable_cash_on_delivery', 'enable_wallet', 'test_mode']) ? 'boolean' : 'string',
                        'group' => 'payment',
                        'label' => $this->getLabel($key),
                        'order' => $this->getOrder($key),
                        'is_public' => false,
                    ]
                );
            }
            
            // Clear settings cache
            Cache::forget('settings_payment');
            if (class_exists(SettingsHelper::class)) {
                SettingsHelper::clearCache();
            }
            
            session()->flash('success', 'Payment settings saved successfully!');
            
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
            'default_payment_gateway' => 'Default Payment Gateway',
            'enable_cash_on_delivery' => 'Enable Cash on Delivery',
            'enable_wallet' => 'Enable Wallet',
            'wallet_minimum_topup' => 'Wallet Minimum Top-up',
            'test_mode' => 'Test Mode',
            'paystack_public_key' => 'Paystack Live Public Key',
            'paystack_secret_key' => 'Paystack Live Secret Key',
            'paystack_test_public_key' => 'Paystack Test Public Key',
            'paystack_test_secret_key' => 'Paystack Test Secret Key',
            'flutterwave_public_key' => 'Flutterwave Live Public Key',
            'flutterwave_secret_key' => 'Flutterwave Live Secret Key',
            'flutterwave_test_public_key' => 'Flutterwave Test Public Key',
            'flutterwave_test_secret_key' => 'Flutterwave Test Secret Key',
        ];

        return $labels[$key] ?? ucwords(str_replace('_', ' ', $key));
    }

    private function getOrder($key)
    {
        $order = [
            'default_payment_gateway' => 1,
            'enable_cash_on_delivery' => 2,
            'enable_wallet' => 3,
            'wallet_minimum_topup' => 4,
            'test_mode' => 5,
            'paystack_public_key' => 6,
            'paystack_secret_key' => 7,
            'paystack_test_public_key' => 8,
            'paystack_test_secret_key' => 9,
            'flutterwave_public_key' => 10,
            'flutterwave_secret_key' => 11,
            'flutterwave_test_public_key' => 12,
            'flutterwave_test_secret_key' => 13,
        ];

        return $order[$key] ?? 99;
    }

    public function render()
    {
        return view('livewire.admin.settings.payment');
    }
}
