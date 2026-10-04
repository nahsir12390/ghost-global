<?php

namespace App\Services;

use App\Mail\ShipmentUpdated;
use App\Models\Order;
use App\Models\Shipment;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class ShipmentService
{
    public function save(Order $order, ?int $shipmentId, array $data, array $quantities): Shipment
    {
        abort_unless(auth()->user()?->canManageOrders(), 403);
        $data = Validator::make($data, [
            'supplier_name' => 'nullable|string|max:255', 'supplier_order_reference' => 'nullable|string|max:255',
            'purchase_cost' => 'nullable|numeric|min:0|max:999999999', 'purchase_currency' => ['required', 'regex:/^[A-Z]{3}$/'],
            'private_notes' => 'nullable|string|max:5000', 'status' => ['required', Rule::in(array_keys(Shipment::STATUSES))],
            'carrier' => 'nullable|string|max:255', 'tracking_number' => 'nullable|string|max:255',
            'tracking_url' => ['nullable', 'url:http,https', 'max:2048'],
            'estimated_from' => 'nullable|required_with:estimated_to|date_format:Y-m-d',
            'estimated_to' => 'nullable|required_with:estimated_from|date_format:Y-m-d|after_or_equal:estimated_from',
            'customer_update' => 'nullable|string|max:2000',
        ])->validate();
        Validator::make(['quantities' => $quantities], ['quantities' => 'required|array', 'quantities.*' => 'nullable|integer|min:0'])->validate();
        $allocations = array_filter($quantities, fn ($quantity) => (int) $quantity > 0);
        if (! $allocations) {
            throw ValidationException::withMessages(['quantities' => 'Assign at least one item to this shipment.']);
        }

        return $order->getConnection()->transaction(function () use ($order, $shipmentId, $data, $allocations) {
            $order = Order::whereKey($order->id)->lockForUpdate()->firstOrFail();
            if ($order->status === 'cancelled' && $data['status'] !== 'cancelled') {
                throw ValidationException::withMessages(['status' => 'This order is cancelled. Only shipment cancellation is allowed.']);
            }
            $shipment = $shipmentId ? $order->shipments()->findOrFail($shipmentId) : $order->shipments()->make(['reference' => 'SHP-'.Str::upper(Str::random(10))]);
            $items = $order->items()->get()->keyBy('id');
            $otherShipments = $order->shipments()->where('status', '!=', 'cancelled')->when($shipmentId, fn ($query) => $query->whereKeyNot($shipmentId))->with('items')->get();
            foreach ($allocations as $itemId => $quantity) {
                $item = $items->get($itemId);
                $allocated = $otherShipments->sum(fn ($other) => (int) $other->items->firstWhere('id', $itemId)?->pivot->quantity);
                if (! $item || (int) $quantity > $item->quantity || ($data['status'] !== 'cancelled' && $allocated + (int) $quantity > $item->quantity)) {
                    throw ValidationException::withMessages(['quantities' => 'Shipment quantities must belong to this order and cannot exceed the unallocated quantity.']);
                }
            }
            $isNew = ! $shipment->exists;
            $oldAllocations = $isNew ? [] : $shipment->items()->pluck('order_item_shipment.quantity', 'order_items.id')->all();
            $shipment->fill($data);
            $notify = $isNew || $shipment->isDirty(['status', 'carrier', 'tracking_number', 'tracking_url', 'estimated_from', 'estimated_to', 'customer_update']) || $oldAllocations != $allocations;
            $shipment->save();
            $shipment->items()->sync(array_map(fn ($quantity) => ['quantity' => (int) $quantity], $allocations));
            $this->refreshOrderStatus($order);
            if ($notify && $order->contact_email) {
                Mail::to($order->contact_email)->queue((new ShipmentUpdated($shipment))->afterCommit());
            }

            return $shipment;
        });
    }

    private function refreshOrderStatus(Order $order): void
    {
        if ($order->status === 'cancelled') {
            return;
        }
        $shipments = $order->shipments()->where('status', '!=', 'cancelled')->with('items')->get();
        if ($shipments->isEmpty()) {
            $order->update(['status' => 'confirmed']);

            return;
        }
        $delivered = $shipments->where('status', 'delivered');
        $allDelivered = $order->items()->get()->every(fn ($item) => $delivered->sum(fn ($shipment) => (int) $shipment->items->firstWhere('id', $item->id)?->pivot->quantity) >= $item->quantity);
        $status = $allDelivered ? 'delivered' : ($shipments->contains(fn ($shipment) => in_array($shipment->status, ['in_transit', 'out_for_delivery', 'delivered', 'delayed', 'delivery_attempted'], true)) ? 'on_the_way' : ($shipments->contains('status', 'shipped') ? 'picked_up' : 'confirmed'));
        $order->update(['status' => $status]);
    }
}
