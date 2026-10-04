<?php

use App\Models\Order;
use App\Models\Shipment;
use App\Services\ShipmentService;
use Livewire\Attributes\Locked;
use Livewire\Volt\Component;

new class extends Component {
    #[Locked] public int $orderId;
    #[Locked] public ?int $shipmentId = null;
    public array $form = [];
    public array $quantities = [];

    public function mount(int $orderId): void
    {
        abort_unless(auth()->user()?->canManageOrders(), 403);
        $this->orderId = $orderId;
        $this->newShipment();
    }

    public function newShipment(): void
    {
        abort_unless(auth()->user()?->canManageOrders(), 403);
        $this->shipmentId = null;
        $this->quantities = [];
        $this->form = array_fill_keys(['supplier_name', 'supplier_order_reference', 'purchase_cost', 'private_notes', 'carrier', 'tracking_number', 'tracking_url', 'estimated_from', 'estimated_to', 'customer_update'], '');
        $this->form['purchase_currency'] = 'NGN';
        $this->form['status'] = 'preparing';
        $this->resetValidation();
    }

    public function edit(int $id): void
    {
        abort_unless(auth()->user()?->canManageOrders(), 403);
        $shipment = Order::findOrFail($this->orderId)->shipments()->findOrFail($id);
        $this->newShipment();
        $this->shipmentId = $id;
        foreach (array_keys($this->form) as $field) {
            $value = $shipment->{$field};
            $this->form[$field] = $value instanceof \DateTimeInterface ? $value->format('Y-m-d') : ($value ?? '');
        }
        $this->quantities = $shipment->items()->pluck('order_item_shipment.quantity', 'order_items.id')->all();
    }

    public function save(): void
    {
        $data = array_map(fn ($value) => $value === '' ? null : $value, $this->form);
        app(ShipmentService::class)->save(Order::findOrFail($this->orderId), $this->shipmentId, $data, $this->quantities);
        $this->newShipment();
        session()->flash('shipment_saved', 'Shipment saved. Customer-visible changes are queued for email to the buyer.');
        $this->dispatch('shipment-saved');
    }

    public function with(): array
    {
        abort_unless(auth()->user()?->canManageOrders(), 403);
        return ['shipmentOrder' => Order::with(['items', 'shipments.items'])->findOrFail($this->orderId)];
    }
}; ?>
<section class="space-y-5 rounded-2xl border border-slate-200 bg-white p-6">
    <div class="flex flex-wrap items-center justify-between gap-3"><div><h2 class="text-xl font-bold">Supplier purchases &amp; shipments</h2><p class="mt-1 text-sm text-slate-600">Split items across suppliers. Purchasing details stay private; the buyer sees delivery updates only.</p></div><button wire:click="newShipment" class="rounded-lg border px-4 py-2">New shipment</button></div>
    <p class="text-sm">Buyer: {{ $shipmentOrder->contact_email }} · WhatsApp: {{ $shipmentOrder->buyer_whatsapp ?: 'Not recorded' }}</p>
    @if(session('shipment_saved'))<p role="status" class="rounded-lg bg-green-50 p-3 text-green-800">{{ session('shipment_saved') }}</p>@endif
    @foreach($shipmentOrder->shipments as $shipment)<div wire:key="shipment-{{ $shipment->id }}" class="flex flex-wrap justify-between gap-3 rounded-xl bg-slate-50 p-4"><div><strong>{{ $shipment->reference }}</strong> · {{ Shipment::STATUSES[$shipment->status] }}<p class="text-sm text-slate-600">{{ $shipment->supplier_name ?: 'Supplier not recorded' }} · {{ $shipment->supplier_order_reference }} @if($shipment->purchase_cost !== null)· {{ $shipment->purchase_currency }} {{ number_format($shipment->purchase_cost, 2) }}@endif</p><ul class="text-sm">@foreach($shipment->items as $item)<li>{{ $item->product_name }} × {{ $item->pivot->quantity }}</li>@endforeach</ul></div><button wire:click="edit({{ $shipment->id }})" class="font-semibold text-blue-700">Edit shipment</button></div>@endforeach
    @if($errors->any())<ul role="alert" class="rounded-lg bg-red-50 p-4 text-red-700">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>@endif
    <form wire:submit="save" class="space-y-5">
        <h3 class="font-semibold">{{ $shipmentId ? 'Edit shipment' : 'Create shipment' }}</h3>
        <fieldset class="space-y-3"><legend class="mb-2 text-sm font-semibold">Items in this parcel</legend>@foreach($shipmentOrder->items as $item)<label wire:key="allocation-{{ $item->id }}" class="flex items-center justify-between gap-3 text-sm"><span>{{ $item->product_name }} ({{ $item->quantity }} ordered)</span><input aria-label="Quantity of {{ $item->product_name }}" wire:model="quantities.{{ $item->id }}" type="number" min="0" max="{{ $item->quantity }}" placeholder="0" class="w-24 rounded-lg border-slate-300"></label>@endforeach</fieldset>
        <div class="grid gap-4 sm:grid-cols-2">
            @foreach(['supplier_name' => 'Supplier (private)', 'supplier_order_reference' => 'Supplier purchase reference (private)', 'purchase_cost' => 'Purchase cost (private)', 'purchase_currency' => 'Purchase currency, e.g. USD (private)', 'carrier' => 'Courier', 'tracking_number' => 'Tracking number', 'tracking_url' => 'Tracking link (https://…)'] as $field => $label)<label wire:key="shipment-field-{{ $field }}" class="text-sm">{{ $label }}<input wire:model="form.{{ $field }}" @if($field === 'purchase_cost') type="number" min="0" step="0.01" @elseif($field === 'tracking_url') type="url" @else type="text" @endif class="mt-2 w-full rounded-lg border-slate-300"></label>@endforeach
            <label class="text-sm">Delivery status<select wire:model="form.status" class="mt-2 w-full rounded-lg border-slate-300">@foreach(Shipment::STATUSES as $value => $label)<option value="{{ $value }}">{{ $label }}</option>@endforeach</select></label>
            <label class="text-sm">Estimated arrival from<input wire:model="form.estimated_from" type="date" class="mt-2 w-full rounded-lg border-slate-300"></label>
            <label class="text-sm">Estimated arrival to<input wire:model="form.estimated_to" type="date" class="mt-2 w-full rounded-lg border-slate-300"></label>
            <label class="text-sm">Update for buyer<textarea wire:model="form.customer_update" maxlength="2000" class="mt-2 w-full rounded-lg border-slate-300" placeholder="Explain a delay or delivery attempt"></textarea></label>
            <label class="text-sm">Internal notes (private)<textarea wire:model="form.private_notes" maxlength="5000" class="mt-2 w-full rounded-lg border-slate-300"></textarea></label>
        </div>
        <button wire:loading.attr="disabled" class="rounded-lg bg-red-600 px-5 py-3 font-semibold text-white">Save shipment</button>
    </form>
</section>
