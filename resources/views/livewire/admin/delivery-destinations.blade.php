<?php

use App\Models\DeliveryDestination;
use Illuminate\Validation\Rule;
use Livewire\Volt\Component;

new class extends Component {
    public string $country_code = '';
    public bool $enabled = false;
    public string $shipping_fee = '0';
    public int $min_days = 1;
    public int $max_days = 1;
    public bool $state_required = false;
    public bool $postal_required = false;
    public string $import_charges = '';

    public function mount(): void
    {
        abort_unless(auth()->user()?->isAdmin(), 403);
    }

    public function selectCountry(): void
    {
        abort_unless(auth()->user()?->isAdmin(), 403);
        $destination = DeliveryDestination::where('country_code', $this->country_code)->first();
        foreach (['enabled' => false, 'shipping_fee' => '0', 'min_days' => 1, 'max_days' => 1, 'state_required' => false, 'postal_required' => false, 'import_charges' => ''] as $field => $default) {
            $this->{$field} = $destination?->{$field} ?? $default;
        }
        $this->resetValidation();
    }

    public function save(): void
    {
        abort_unless(auth()->user()?->isAdmin(), 403);
        $data = $this->validate([
            'country_code' => ['required', Rule::in(array_keys(config('countries')))],
            'enabled' => 'boolean', 'shipping_fee' => 'required|numeric|min:0|max:999999999',
            'min_days' => 'required|integer|min:1|max:365',
            'max_days' => 'required|integer|gte:min_days|max:365',
            'state_required' => 'boolean', 'postal_required' => 'boolean',
            'import_charges' => 'required|string|max:255',
        ]);
        DeliveryDestination::updateOrCreate(['country_code' => $data['country_code']], $data);
        session()->flash('delivery_saved', 'Delivery settings saved.');
    }

    public function with(): array
    {
        return ['destinations' => DeliveryDestination::orderBy('country_code')->get()];
    }
}; ?>
<div class="space-y-6">
    <div><h1 class="text-2xl font-bold">Worldwide delivery</h1><p class="mt-2 text-sm text-slate-600">Enable destinations after checking supplier coverage. Set transit times in business days; each product’s preparation time is added at checkout.</p></div>
    @if(session('delivery_saved'))<p role="status" class="rounded-lg bg-green-50 p-4 text-green-800">{{ session('delivery_saved') }}</p>@endif
    @if($errors->any())<ul role="alert" class="rounded-lg bg-red-50 p-4 text-red-700">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>@endif
    <form wire:submit="save" class="grid gap-5 rounded-xl bg-white p-6 shadow-sm sm:grid-cols-2">
        <label class="text-sm sm:col-span-2">Country / Region<select wire:model="country_code" wire:change="selectCountry" required class="mt-2 w-full rounded-lg border-slate-300"><option value="">Select a country</option>@foreach(config('countries') as $code => $name)<option value="{{ $code }}">{{ $name }}</option>@endforeach</select></label>
        <label class="text-sm">Delivery fee (NGN)<input wire:model="shipping_fee" type="number" min="0" step="0.01" required class="mt-2 w-full rounded-lg border-slate-300"></label>
        <div class="grid grid-cols-2 gap-4"><label class="text-sm">Minimum transit days<input wire:model="min_days" type="number" min="1" max="365" required class="mt-2 w-full rounded-lg border-slate-300"></label><label class="text-sm">Maximum transit days<input wire:model="max_days" type="number" min="1" max="365" required class="mt-2 w-full rounded-lg border-slate-300"></label></div>
        <label class="text-sm sm:col-span-2">Import charges policy (shown before payment)<input wire:model="import_charges" maxlength="255" placeholder="Specify whether import charges are included or payable by the receiver" required class="mt-2 w-full rounded-lg border-slate-300"></label>
        <label class="flex items-center gap-3 text-sm"><input wire:model="state_required" type="checkbox" class="rounded">Require state / province</label>
        <label class="flex items-center gap-3 text-sm"><input wire:model="postal_required" type="checkbox" class="rounded">Require ZIP / postal code</label>
        <label class="flex items-center gap-3 text-sm"><input wire:model="enabled" type="checkbox" class="rounded">Accept orders for this destination</label>
        <button wire:loading.attr="disabled" class="rounded-lg bg-red-600 px-5 py-3 font-semibold text-white">Save destination</button>
    </form>
    <div class="overflow-x-auto rounded-xl bg-white p-6"><table class="w-full text-left text-sm"><thead><tr><th class="p-3">Destination</th><th>Availability</th><th>Fee</th><th>Transit estimate</th></tr></thead><tbody>@forelse($destinations as $destination)<tr wire:key="destination-{{ $destination->id }}" class="border-t"><td class="p-3">{{ $destination->country_name }}</td><td>{{ $destination->enabled ? 'Enabled' : 'Quote required' }}</td><td>₦{{ number_format($destination->shipping_fee, 2) }}</td><td>{{ $destination->min_days }}–{{ $destination->max_days }} business days</td></tr>@empty<tr><td colspan="4" class="p-4 text-slate-500">No destinations configured. Checkout will request a delivery quote until destinations are enabled.</td></tr>@endforelse</tbody></table></div>
</div>
