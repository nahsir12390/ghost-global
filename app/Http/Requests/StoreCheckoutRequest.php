<?php

namespace App\Http\Requests;

use App\Models\DeliveryDestination;
use App\Services\DeliveryService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class StoreCheckoutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return self::checkoutRules((string) $this->input('shipping_country'));
    }

    public static function checkoutRules(string $country): array
    {
        $lines = app(DeliveryService::class)->cartLines(session('cart', []));
        $physical = collect($lines)->contains(fn ($line) => $line['product']->requiresShipping());
        $destination = DeliveryDestination::where('country_code', $country)->where('enabled', true)->first();

        return [
            'shipping_first_name' => ['required', 'string', 'max:100'],
            'shipping_last_name' => ['required', 'string', 'max:100'],
            'buyer_email' => ['required', 'email', 'max:255', auth()->guest() ? Rule::unique('users', 'email') : 'string'],
            'buyer_whatsapp' => ['required', 'string', 'max:32', 'regex:/^\+?[0-9 ()\-]{7,32}$/'],
            'shipping_phone' => ['nullable', 'string', 'max:32', 'regex:/^\+?[0-9 ()\-]{7,32}$/'],
            'shipping_country' => [$physical ? 'required' : 'nullable', Rule::in(array_keys(config('countries')))],
            'shipping_state' => [$physical && $destination?->state_required ? 'required' : 'nullable', 'string', 'max:100'],
            'shipping_postal_code' => [$physical && $destination?->postal_required ? 'required' : 'nullable', 'string', 'max:32'],
            'shipping_city' => [$physical ? 'required' : 'nullable', 'string', 'max:100'],
            'shipping_address' => [$physical ? 'required' : 'nullable', 'string', 'max:1000'],
            'password' => auth()->guest() ? ['required', 'string', Password::min(8)] : ['nullable'],
            'marketing_consent' => ['boolean'],
            'terms_accepted' => ['accepted'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ];
    }

    public function messages(): array
    {
        return self::checkoutMessages();
    }

    public static function checkoutMessages(): array
    {
        return [
            'buyer_email.unique' => 'This email already has an account. Please sign in before checking out.',
            'buyer_whatsapp.regex' => 'Enter your WhatsApp number with its international calling code.',
            'shipping_phone.regex' => 'Enter the receiver’s phone number with its international calling code.',
            'terms_accepted.accepted' => 'Please accept the terms and conditions to place your order.',
            'shipping_country.in' => 'Please select a valid country or region.',
        ];
    }
}
