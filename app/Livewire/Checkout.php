<?php

namespace App\Livewire;

use App\Helpers\SettingsHelper;
use App\Http\Requests\StoreCheckoutRequest;
use App\Models\DeliveryDestination;
use App\Services\CheckoutService;
use App\Services\DeliveryService;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

class Checkout extends Component
{
    public string $shipping_first_name = '';

    public string $shipping_last_name = '';

    public string $buyer_email = '';

    public string $buyer_whatsapp = '';

    public string $shipping_phone = '';

    public string $shipping_country = '';

    public string $shipping_state = '';

    public string $shipping_address = '';

    public string $shipping_city = '';

    public string $shipping_postal_code = '';

    public string $notes = '';

    public string $password = '';

    public bool $terms_accepted = false;

    public bool $marketing_consent = false;

    public function mount(): void
    {
        $this->buyer_email = auth()->user()?->email ?? '';
        $this->buyer_whatsapp = auth()->user()?->phone ?? '';
    }

    public function updatedShippingCountry(): void
    {
        $this->shipping_state = '';
        $this->shipping_postal_code = '';
        $this->resetValidation();
    }

    public function submitOrder(): mixed
    {
        $data = $this->validate(StoreCheckoutRequest::checkoutRules($this->shipping_country), StoreCheckoutRequest::checkoutMessages());
        $order = app(CheckoutService::class)->place($data);
        $this->password = '';

        return redirect()->route('payment.index', $order);
    }

    public function render(): \Illuminate\View\View
    {
        $lines = [];
        $quote = null;
        $quoteError = null;
        try {
            $lines = app(DeliveryService::class)->cartLines(session('cart', []));
            $quote = app(DeliveryService::class)->quote($lines, $this->shipping_country);
        } catch (ValidationException $e) {
            $quoteError = $e->validator->errors()->first();
        }
        $subtotal = array_sum(array_column($lines, 'total'));
        $fee = round($subtotal * (float) SettingsHelper::platformServiceFeeRate($subtotal) / 100, 2);

        return view('livewire.checkout', [
            'lines' => $lines, 'quote' => $quote, 'quoteError' => $quoteError,
            'subtotal' => $subtotal, 'fee' => $fee,
            'destination' => DeliveryDestination::where('country_code', $this->shipping_country)->where('enabled', true)->first(),
            'requiresShipping' => collect($lines)->contains(fn ($line) => $line['product']->requiresShipping()),
        ]);
    }
}
