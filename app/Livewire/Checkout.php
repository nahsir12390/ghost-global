<?php

namespace App\Livewire;

use Livewire\Component;
use App\Helpers\SettingsHelper;
use Illuminate\Support\Facades\Auth;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\WalletTransaction;
use App\Services\ReferralService;
use App\Services\WalletService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class Checkout extends Component
{
    public $cart = [];
    public $subtotal = 0;
    public $tax = 0;
    public $taxRate = 0;
    public $serviceFeeRate = 0;
    public $shipping = 0;
    public $total = 0;
    public $freeShippingThreshold = 0;
    public $loading = false;
    public $checkoutError = '';
    public $hasFreeShipping = false;
    public $freeShippingProgress = 0;
    public $remainingForFreeShipping = 0;
    public $requiresShipping = true;
    
    // Formatted values
    public $formattedSubtotal = '₦0.00';
    public $formattedTax = '₦0.00';
    public $formattedShipping = '₦0.00';
    public $formattedTotal = '₦0.00';
    
    // Shipping address
    public $shipping_first_name = '';
    public $shipping_last_name = '';
    public $shipping_email = '';
    public $shipping_phone = '';
    public $shipping_address = '';
    public $shipping_city = '';
    public $shipping_state = '';
    public $shipping_country = '';
    public $shipping_postal_code = '';
    
    public $same_as_shipping = true;
    
    // Billing address
    public $billing_first_name = '';
    public $billing_last_name = '';
    public $billing_email = '';
    public $billing_phone = '';
    public $billing_address = '';
    public $billing_city = '';
    public $billing_state = '';
    public $billing_country = '';
    public $billing_postal_code = '';
    
    // Notes
    public $notes = '';
    
    // Settings
    public $currencySymbol = '₦';
    public $availablePaymentMethods = [];
    public $payment_method = '';
    public $walletBalance = 0;
    public $formattedWalletBalance = '₦0.00';
    
    protected $rules = [
        'shipping_first_name' => 'required|string|max:255',
        'shipping_last_name' => 'required|string|max:255',
        'shipping_email' => 'required|email',
        'shipping_phone' => 'required|string|max:20',
        'shipping_address' => 'required|string',
        'shipping_city' => 'required|string|max:100',
        'shipping_state' => 'required|string|max:100',
        'shipping_country' => 'required|string|max:100',
        'shipping_postal_code' => 'required|string|max:20',
        'notes' => 'nullable|string|max:500',
        'payment_method' => 'required|in:paystack,cash_on_delivery,wallet',
        
        // Billing validation
        'billing_first_name' => 'required_if:same_as_shipping,false|nullable|string|max:255',
        'billing_last_name' => 'required_if:same_as_shipping,false|nullable|string|max:255',
        'billing_email' => 'required_if:same_as_shipping,false|nullable|email',
        'billing_phone' => 'required_if:same_as_shipping,false|nullable|string|max:20',
        'billing_address' => 'required_if:same_as_shipping,false|nullable|string',
        'billing_city' => 'required_if:same_as_shipping,false|nullable|string|max:100',
        'billing_state' => 'required_if:same_as_shipping,false|nullable|string|max:100',
        'billing_country' => 'required_if:same_as_shipping,false|nullable|string|max:100',
        'billing_postal_code' => 'required_if:same_as_shipping,false|nullable|string|max:20',
    ];

    protected $messages = [
        'payment_method.required' => 'Please select a payment method.',
        'shipping_first_name.required' => 'First name is required.',
        'shipping_last_name.required' => 'Last name is required.',
        'shipping_email.required' => 'Email address is required.',
        'shipping_email.email' => 'Please enter a valid email address.',
        'shipping_phone.required' => 'Phone number is required.',
        'shipping_address.required' => 'Street address is required.',
        'shipping_city.required' => 'City is required.',
        'shipping_state.required' => 'State is required.',
        'shipping_country.required' => 'Country is required.',
        'shipping_postal_code.required' => 'Postal code is required.',
    ];

    public function mount()
    {
        // Load user data if authenticated
        if (Auth::check()) {
            $user = Auth::user();
            [$firstName, $lastName] = $this->splitName($user->name ?? '');
            $this->shipping_first_name = $firstName;
            $this->shipping_last_name = $lastName;
            $this->shipping_email = $user->email;
        }
        
        // Load cart
        $this->loadCart();
        
        // Load settings
        $this->loadSettings();
        
        // Load payment methods
        $this->loadPaymentMethods();
        
        // Set default payment method
        if (!empty($this->availablePaymentMethods)) {
            $this->payment_method = $this->availablePaymentMethods[0];
        }
        
        // Calculate totals
        $this->calculateTotals();
    }

    private function splitName(string $name): array
    {
        $parts = preg_split('/\s+/', trim($name), 2);

        return [
            $parts[0] ?? '',
            $parts[1] ?? '',
        ];
    }

    public function loadCart()
    {
        $this->cart = session('cart', []);
        $this->requiresShipping = collect($this->cart)->contains(fn ($item) => (bool) ($item['requires_shipping'] ?? true));
        $this->calculateTotals();
    }

    public function loadSettings()
    {
        $this->freeShippingThreshold = SettingsHelper::freeShippingThreshold();
        $this->shipping = SettingsHelper::shippingFee();
        $this->currencySymbol = SettingsHelper::get('site_currency_symbol', '₦');
        $this->loadWalletBalance();
        $this->applyCheckoutDefaults();
    }

    private function loadWalletBalance(): void
    {
        if (! Auth::check()) {
            $this->walletBalance = 0;
            $this->formattedWalletBalance = $this->currencySymbol . number_format(0, 2);
            return;
        }

        $wallet = app(WalletService::class)->getOrCreateWallet(Auth::user());
        $this->walletBalance = (float) $wallet->balance;
        $this->formattedWalletBalance = $this->currencySymbol . number_format($this->walletBalance, 2);
    }

    private function applyCheckoutDefaults(): void
    {
        $defaults = SettingsHelper::checkoutDefaults();

        $this->shipping_address = $this->shipping_address ?: $defaults['address'];
        $this->shipping_city = $this->shipping_city ?: $defaults['city'];
        $this->shipping_state = $this->shipping_state ?: $defaults['state'];
        $this->shipping_country = $this->shipping_country ?: $defaults['country'];
        $this->shipping_postal_code = $this->shipping_postal_code ?: $defaults['postal_code'];

        $this->billing_address = $this->billing_address ?: $defaults['address'];
        $this->billing_city = $this->billing_city ?: $defaults['city'];
        $this->billing_state = $this->billing_state ?: $defaults['state'];
        $this->billing_country = $this->billing_country ?: $defaults['country'];
        $this->billing_postal_code = $this->billing_postal_code ?: $defaults['postal_code'];
    }

    public function loadPaymentMethods()
    {
        $this->availablePaymentMethods = [];
        
        $testMode = SettingsHelper::get('test_mode', true);
        
        // Check for Paystack keys based on test mode
        if ($testMode) {
            // Test mode - check for test keys
            $testPublicKey = SettingsHelper::get('paystack_test_public_key', '');
            $testSecretKey = SettingsHelper::get('paystack_test_secret_key', '');
            
            if (!empty($testPublicKey) && !empty($testSecretKey)) {
                $this->availablePaymentMethods[] = 'paystack';
                Log::info('Paystack enabled in test mode');
            } else {
                Log::info('Paystack test keys missing');
            }
        } else {
            // Live mode - check for live keys
            $livePublicKey = SettingsHelper::get('paystack_public_key', '');
            $liveSecretKey = SettingsHelper::get('paystack_secret_key', '');
            
            if (!empty($livePublicKey) && !empty($liveSecretKey)) {
                $this->availablePaymentMethods[] = 'paystack';
                Log::info('Paystack enabled in live mode');
            } else {
                Log::info('Paystack live keys missing');
            }
        }
        
        // Check if Cash on Delivery is enabled
        $cashOnDeliveryEnabled = SettingsHelper::get('enable_cash_on_delivery', true);
        if ($cashOnDeliveryEnabled) {
            $this->availablePaymentMethods[] = 'cash_on_delivery';
            Log::info('Cash on Delivery enabled');
        }

        if (SettingsHelper::isWalletEnabled()) {
            $this->availablePaymentMethods[] = 'wallet';
            Log::info('Wallet enabled');
        }
        
        Log::info('Available payment methods: ' . json_encode($this->availablePaymentMethods));
    }

    public function calculateTotals()
    {
        $this->subtotal = 0;
        foreach ($this->cart as $item) {
            $this->subtotal += $item['price'] * $item['quantity'];
        }

        if ($this->requiresShipping) {
            $breakdown = SettingsHelper::calculateOrderBreakdown($this->subtotal);
        } else {
            $serviceFeeRate = (float) SettingsHelper::platformServiceFeeRate($this->subtotal);
            $serviceFee = round(($this->subtotal * $serviceFeeRate) / 100, 2);

            $breakdown = [
                'subtotal' => round($this->subtotal, 2),
                'service_fee_rate' => $serviceFeeRate,
                'service_fee' => $serviceFee,
                'shipping' => 0,
                'total' => round($this->subtotal + $serviceFee, 2),
            ];
        }

        $this->taxRate = $breakdown['service_fee_rate'] / 100;
        $this->serviceFeeRate = $breakdown['service_fee_rate'];
        $this->tax = $breakdown['service_fee'];
        $this->shipping = $breakdown['shipping'];
        
        // Free delivery threshold
        if ($this->shipping == 0) {
            $this->hasFreeShipping = true;
        } else {
            $this->hasFreeShipping = false;
        }
        
        // Calculate free delivery progress
        if ($this->freeShippingThreshold > 0 && !$this->hasFreeShipping) {
            $this->freeShippingProgress = min(100, ($this->subtotal / $this->freeShippingThreshold) * 100);
            $this->remainingForFreeShipping = max(0, $this->freeShippingThreshold - $this->subtotal);
        } else {
            $this->freeShippingProgress = 100;
            $this->remainingForFreeShipping = 0;
        }
        
        $this->total = $breakdown['total'];
        
        // Format currency values
        $this->formattedSubtotal = $this->currencySymbol . number_format($this->subtotal, 2);
        $this->formattedTax = $this->currencySymbol . number_format($this->tax, 2);
        $this->formattedShipping = $this->currencySymbol . number_format($this->shipping, 2);
        $this->formattedTotal = $this->currencySymbol . number_format($this->total, 2);
    }

    public function updatedSameAsShipping($value)
    {
        if ($value) {
            // Clear billing address
            $this->billing_first_name = '';
            $this->billing_last_name = '';
            $this->billing_email = '';
            $this->billing_phone = '';
            $this->billing_address = '';
            $defaults = SettingsHelper::checkoutDefaults();
            $this->billing_city = $defaults['city'];
            $this->billing_state = $defaults['state'];
            $this->billing_country = $defaults['country'];
            $this->billing_postal_code = $defaults['postal_code'];
        }
    }

    public function submitOrder()
    {
        $this->validate($this->validationRules());
        
        // Validate payment method
        if (empty($this->payment_method)) {
            $this->checkoutError = 'Please select a payment method.';
            return;
        }

        try {
            Log::info('=== CHECKOUT SUBMISSION START ===');
            Log::info('User ID: ' . Auth::id());
            Log::info('Cart items: ' . count($this->cart));
            Log::info('Total: ' . $this->total);
            Log::info('Payment Method: ' . $this->payment_method);
            
            if (empty($this->cart)) {
                $this->checkoutError = 'Your cart is empty. Please add items before checking out.';
                Log::error('Cart is empty');
                return;
            }

            $availabilityError = $this->validateCartAvailability();
            if ($availabilityError) {
                $this->checkoutError = $availabilityError;
                Log::warning('Checkout blocked by product/vendor availability', ['error' => $availabilityError]);
                return;
            }

            if ($this->payment_method === 'wallet' && $this->walletBalance < $this->total) {
                $this->checkoutError = 'Your wallet balance is not enough for this order. Please fund your wallet and try again.';
                return;
            }
            
            Log::info('Creating order...');
            $order = DB::transaction(function () {
                $order = Order::create([
                    'user_id' => Auth::id(),
                    'order_number' => 'ORD-' . time() . '-' . rand(100, 999),
                    'subtotal' => round($this->subtotal, 2),
                    'tax' => round($this->tax, 2),
                    'shipping' => round($this->shipping, 2),
                    'total' => round($this->total, 2),
                    'payment_method' => $this->payment_method,
                    'payment_status' => 'pending',
                    'status' => 'ordered',
                    'notes' => $this->notes,
                    'tracking_number' => null,

                    // Shipping address
                    'shipping_first_name' => $this->shipping_first_name,
                    'shipping_last_name' => $this->shipping_last_name,
                    'shipping_email' => $this->shipping_email,
                    'shipping_phone' => $this->shipping_phone,
                    'shipping_address' => $this->requiresShipping ? $this->shipping_address : null,
                    'shipping_city' => $this->requiresShipping ? $this->shipping_city : null,
                    'shipping_state' => $this->requiresShipping ? $this->shipping_state : null,
                    'shipping_country' => $this->requiresShipping ? $this->shipping_country : null,
                    'shipping_postal_code' => $this->requiresShipping ? $this->shipping_postal_code : null,

                    // Billing address
                    'billing_first_name' => $this->same_as_shipping ? $this->shipping_first_name : $this->billing_first_name,
                    'billing_last_name' => $this->same_as_shipping ? $this->shipping_last_name : $this->billing_last_name,
                    'billing_email' => $this->same_as_shipping ? $this->shipping_email : $this->billing_email,
                    'billing_phone' => $this->same_as_shipping ? $this->shipping_phone : $this->billing_phone,
                    'billing_address' => $this->same_as_shipping ? $this->shipping_address : $this->billing_address,
                    'billing_city' => $this->same_as_shipping ? $this->shipping_city : $this->billing_city,
                    'billing_state' => $this->same_as_shipping ? $this->shipping_state : $this->billing_state,
                    'billing_country' => $this->same_as_shipping ? $this->shipping_country : $this->billing_country,
                    'billing_postal_code' => $this->same_as_shipping ? $this->shipping_postal_code : $this->billing_postal_code,
                ]);

                foreach ($this->cart as $productId => $item) {
                    $product = Product::with('vendor')->lockForUpdate()->find($productId);

                    if (! $product || ! $product->isPurchasable()) {
                        throw new \Exception($product?->unavailableReason() ?: 'A product in your cart is no longer available.');
                    }

                    if ($product->tracksInventory() && $product->quantity < $item['quantity']) {
                        throw new \Exception($product->name . ' is out of stock! Only ' . $product->quantity . ' available.');
                    }

                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $productId,
                        'vendor_id' => $product->vendor_id,
                        'product_name' => $item['name'],
                        'quantity' => $item['quantity'],
                        'price' => $item['price'],
                        'total' => $item['price'] * $item['quantity'],
                        'options' => [
                            'product_type' => $product->product_type,
                        ],
                    ]);

                    if ($product->tracksInventory()) {
                        $product->decrement('quantity', $item['quantity']);
                    }

                    Log::info('Created order item: product=' . $productId . ', qty=' . $item['quantity']);
                }

                if ($this->payment_method === 'wallet') {
                    $transaction = app(WalletService::class)->debit(
                        Auth::user(),
                        (float) $order->total,
                        WalletTransaction::TYPE_ORDER_PAYMENT,
                        'Wallet payment for order ' . $order->order_number,
                        'WLT-ORDER-' . $order->id,
                        ['order_id' => $order->id]
                    );

                    $order->update([
                        'payment_status' => 'paid',
                        'status' => 'ordered',
                        'payment_reference' => $transaction->reference,
                        'tracking_number' => $order->tracking_number ?: $order->order_number,
                    ]);

                    app(ReferralService::class)->rewardReferrerForFirstPaidOrder($order);
                }

                return $order;
            });

            Log::info('Order created: ' . $order->id);
            
            // Clear cart
            session()->forget('cart');
            
            Log::info('Cart cleared, redirecting to payment');
            
            // For Cash on Delivery, go to success page directly
            if ($this->payment_method === 'cash_on_delivery') {
                $order->update([
                    'payment_status' => 'pending',
                    'status' => 'ordered',
                    'tracking_number' => $order->tracking_number ?: $order->order_number,
                ]);

                $this->sendOrderEmails($order->fresh(['user', 'items.product', 'items.vendor']));

                return redirect()->route('checkout.success', $order);
            }

            if ($this->payment_method === 'wallet') {
                $this->sendOrderEmails($order->fresh(['user', 'items.product', 'items.vendor']));
                $this->loadWalletBalance();
                return redirect()->route('checkout.success', $order);
            }
            
            // For Paystack, redirect to payment page
            return redirect()->route('payment.index', $order);
            
        } catch (\Exception $e) {
            Log::error('Checkout error: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            $this->checkoutError = 'An error occurred while creating your order: ' . $e->getMessage();
        }
    }

    private function validateCartAvailability(): ?string
    {
        foreach ($this->cart as $productId => $item) {
            $product = Product::with('vendor')->find($item['product_id'] ?? $productId);

            if (! $product || ! $product->is_active) {
                return 'A product in your cart is no longer available.';
            }

            if ($product->tracksInventory() && $product->quantity < ($item['quantity'] ?? 1)) {
                return $product->name . ' is out of stock! Only ' . $product->quantity . ' available.';
            }

            if (! $product->vendorIsAvailable()) {
                return $product->name . ': ' . $product->unavailableReason();
            }
        }

        return null;
    }

    public function render()
    {
        return view('livewire.checkout');
    }

    private function validationRules(): array
    {
        $rules = $this->rules;

        if (! $this->requiresShipping) {
            unset(
                $rules['shipping_address'],
                $rules['shipping_city'],
                $rules['shipping_state'],
                $rules['shipping_country'],
                $rules['shipping_postal_code']
            );
        }

        return $rules;
    }

    private function sendOrderEmails(Order $order): void
    {
        try {
            if (config('mail.default') === 'log') {
                Log::info('Order emails logged for order: ' . $order->order_number);
                return;
            }

            if ($order->shipping_email) {
                Mail::to($order->shipping_email)->send(new \App\Mail\OrderConfirmationMail($order));
            }

            $adminEmail = SettingsHelper::get('site_email', config('mail.from.address'));

            if ($adminEmail) {
                Mail::to($adminEmail)->send(new \App\Mail\AdminOrderNotificationMail($order));
            }
        } catch (\Throwable $e) {
            Log::error('Failed to send checkout order emails: ' . $e->getMessage(), [
                'order_id' => $order->id,
                'exception' => $e,
            ]);
        }
    }
}
