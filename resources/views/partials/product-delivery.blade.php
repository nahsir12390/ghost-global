<section class="space-y-4 rounded-2xl border border-slate-200 bg-white p-6">
    <h3 class="text-lg font-semibold">Delivery availability &amp; preparation</h3>
    <p class="text-sm text-slate-600">For physical products. Leave destinations empty to use every enabled delivery country. Preparation time is added to the destination’s transit estimate.</p>
    <div class="grid gap-4 sm:grid-cols-2">
        <label class="text-sm">Minimum preparation days<input wire:model="processing_min_days" type="number" min="0" max="365" class="mt-2 w-full rounded-lg border-slate-300">@error('processing_min_days')<span class="text-red-700">{{ $message }}</span>@enderror</label>
        <label class="text-sm">Maximum preparation days<input wire:model="processing_max_days" type="number" min="0" max="365" class="mt-2 w-full rounded-lg border-slate-300">@error('processing_max_days')<span class="text-red-700">{{ $message }}</span>@enderror</label>
    </div>
    <label class="block text-sm">Limit to these destinations (optional)<select wire:model="delivery_countries" multiple size="6" class="mt-2 w-full rounded-lg border-slate-300">@foreach(config('countries') as $code => $name)<option value="{{ $code }}">{{ $name }}</option>@endforeach</select></label>
    <p class="text-xs text-slate-500">Hold Ctrl or Command to select or deselect multiple countries. A country must also be enabled in Worldwide delivery settings.</p>
    @error('delivery_countries.*')<p class="text-red-700">{{ $message }}</p>@enderror
</section>
