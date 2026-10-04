<div>
    <form wire:submit="submitOrder" class="grid items-start gap-10 lg:grid-cols-3">
        <section class="space-y-6 rounded-2xl bg-white p-6 sm:p-8 lg:col-span-2">
            <h2 class="text-2xl font-semibold">Billing &amp; Shipping</h2>
            <p class="text-sm text-slate-600">Enter the receiver’s delivery details and your own contact information. Order updates go to you.</p>
            @if($errors->any())<div role="alert" class="rounded-xl bg-red-50 p-4 text-red-700"><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
            <div class="grid gap-5 sm:grid-cols-2">
                @foreach(['shipping_first_name' => 'Receiver’s first name', 'shipping_last_name' => 'Receiver’s other name'] as $field => $label)
                    <label class="block text-sm" wire:key="{{ $field }}">{{ $label }} *<input wire:model="{{ $field }}" required autocomplete="off" class="mt-2 w-full rounded-lg border-slate-200 bg-slate-50"></label>
                @endforeach
            </div>
            <label class="block text-sm">Your WhatsApp number *<input wire:model="buyer_whatsapp" type="tel" autocomplete="tel" placeholder="Include country code, e.g. +234…" required class="mt-2 w-full rounded-lg border-slate-200 bg-slate-50"></label>
            <label class="block text-sm">Your own email address *<input wire:model="buyer_email" type="email" autocomplete="email" required class="mt-2 w-full rounded-lg border-slate-200 bg-slate-50"></label>
            @if($requiresShipping)
                <label class="block text-sm">Country / Region *<select wire:model.live="shipping_country" required class="mt-2 w-full rounded-lg border-slate-200 bg-slate-50"><option value="">Select a country / region</option>@foreach(config('countries') as $code => $name)<option value="{{ $code }}">{{ $name }}</option>@endforeach</select></label>
                <label class="block text-sm">State / Province / Region {{ $destination?->state_required ? '*' : '(optional)' }}<input wire:model="shipping_state" @required($destination?->state_required) autocomplete="address-level1" class="mt-2 w-full rounded-lg border-slate-200 bg-slate-50"></label>
                <label class="block text-sm">Receiver’s street address *<input wire:model="shipping_address" required autocomplete="street-address" class="mt-2 w-full rounded-lg border-slate-200 bg-slate-50"></label>
                <label class="block text-sm">City / Town *<input wire:model="shipping_city" required autocomplete="address-level2" class="mt-2 w-full rounded-lg border-slate-200 bg-slate-50"></label>
                <label class="block text-sm">ZIP / Postal code {{ $destination?->postal_required ? '*' : '(optional)' }}<input wire:model="shipping_postal_code" @required($destination?->postal_required) autocomplete="postal-code" class="mt-2 w-full rounded-lg border-slate-200 bg-slate-50"></label>
                <label class="block text-sm">Receiver’s phone number (optional)<input wire:model="shipping_phone" type="tel" placeholder="Include their country code" class="mt-2 w-full rounded-lg border-slate-200 bg-slate-50"></label>
            @endif
            <label class="flex gap-3 text-sm"><input wire:model="marketing_consent" type="checkbox" class="mt-1 rounded">I would like to receive emails with discounts and product information.</label>
            @guest
                <label class="block text-sm">Create account password *<input wire:model="password" type="password" autocomplete="new-password" minlength="8" required class="mt-2 w-full rounded-lg border-slate-200 bg-slate-50"></label>
                <p class="text-sm text-slate-600">Already registered? <a href="{{ route('login') }}" class="underline">Sign in</a> to use your account.</p>
            @endguest
            <h3 class="text-xl font-semibold">Additional information</h3>
            <label class="block text-sm">Order notes (optional)<textarea wire:model="notes" rows="4" maxlength="2000" placeholder="Delivery instructions or details about your gift" class="mt-2 w-full rounded-lg border-slate-200 bg-slate-50"></textarea></label>
        </section>
        <aside class="space-y-5 rounded-2xl border border-blue-600 bg-white p-6 lg:sticky lg:top-24">
            <h2 class="text-2xl font-semibold">Your order</h2>
            @foreach($lines as $line)<div class="flex justify-between gap-4 border-b py-3 text-sm" wire:key="line-{{ $line['product']->id }}"><span>{{ $line['product']->name }} × {{ $line['quantity'] }}</span><span class="whitespace-nowrap">₦{{ number_format($line['total'], 2) }}</span></div>@endforeach
            <div class="flex justify-between text-sm"><span>Subtotal</span><span>₦{{ number_format($subtotal, 2) }}</span></div>
            @if($fee > 0)<div class="flex justify-between text-sm"><span>Service fee</span><span>₦{{ number_format($fee, 2) }}</span></div>@endif
            @if($quote)
                <div class="flex justify-between text-sm"><span>Delivery</span><span>₦{{ number_format($quote['fee'], 2) }}</span></div>
                <div class="flex justify-between border-t pt-4 font-bold"><span>Total (NGN)</span><span>₦{{ number_format($subtotal + $fee + $quote['fee'], 2) }}</span></div>
                @if($quote['from'])<div class="rounded-xl bg-blue-50 p-4 text-sm text-blue-950"><strong>Estimated arrival: {{ $quote['from']->format('j M') }}–{{ $quote['to']->format('j M Y') }}</strong><p class="mt-2">Includes preparation and transit in business days. Dates are estimates; customs and holidays may cause delays. Items may arrive in separate shipments.</p><p class="mt-2">{{ $quote['import_charges'] }}</p></div>@endif
            @else
                <div role="status" class="rounded-xl bg-amber-50 p-4 text-sm text-amber-900">{{ $shipping_country || !$requiresShipping ? $quoteError : 'Choose a destination to see delivery availability, fees, and estimated dates.' }} @if($shipping_country)<a href="{{ route('contact') }}" class="mt-2 block font-semibold underline">Request a delivery quote</a>@endif</div>
            @endif
            <p class="text-sm text-slate-600">You’ll choose an available payment method on the next screen. Payments are in naira.</p>
            <label class="flex gap-3 text-sm"><input wire:model="terms_accepted" type="checkbox" required class="mt-1 rounded"><span>I agree to the <a href="{{ route('terms') }}" class="text-blue-700 underline">terms and conditions</a> and have read the <a href="{{ route('privacy') }}" class="text-blue-700 underline">privacy policy</a>. *</span></label>
            <button type="submit" @disabled(!$quote || empty($lines)) wire:loading.attr="disabled" class="w-full rounded-lg bg-black px-5 py-4 font-semibold text-white disabled:opacity-40"><span wire:loading.remove wire:target="submitOrder">Place order</span><span wire:loading wire:target="submitOrder">Creating order…</span></button>
        </aside>
    </form>
</div>
