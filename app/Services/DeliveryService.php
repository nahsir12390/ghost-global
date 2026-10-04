<?php

namespace App\Services;

use App\Models\DeliveryDestination;
use App\Models\Product;
use Illuminate\Validation\ValidationException;

class DeliveryService
{
    public function cartLines(array $cart, bool $lock = false): array
    {
        if (empty($cart)) {
            throw ValidationException::withMessages(['cart' => 'Your cart is empty.']);
        }
        $lines = [];
        foreach ($cart as $key => $item) {
            $id = $item['product_id'] ?? $key;
            $product = Product::query()->when($lock, fn ($query) => $query->lockForUpdate())->find($id);
            $quantity = filter_var($item['quantity'] ?? null, FILTER_VALIDATE_INT);
            if (! $product || ! $product->isPurchasable() || ! $quantity || $quantity < 1 || ($product->tracksInventory() && $quantity > $product->quantity)) {
                throw ValidationException::withMessages(['cart' => 'An item is unavailable or its quantity exceeds available stock. Please review your cart.']);
            }
            if (isset($lines[$product->id])) {
                throw ValidationException::withMessages(['cart' => 'Please remove duplicate cart entries and try again.']);
            }
            $lines[$product->id] = ['product' => $product, 'quantity' => $quantity, 'total' => round((float) $product->price * $quantity, 2)];
        }

        return $lines;
    }

    public function quote(array $lines, string $country): array
    {
        $physical = array_filter($lines, fn ($line) => $line['product']->requiresShipping());
        if (! $physical) {
            return ['fee' => 0, 'from' => null, 'to' => null, 'import_charges' => null];
        }
        $destination = DeliveryDestination::query()->where('country_code', $country)->where('enabled', true)->first();
        if (! $destination) {
            throw ValidationException::withMessages(['shipping_country' => 'Delivery to this destination needs a quote. Please contact us before ordering.']);
        }
        $minProcessing = 0;
        $maxProcessing = 0;
        foreach ($physical as $line) {
            $product = $line['product'];
            if ($product->delivery_countries && ! in_array($country, $product->delivery_countries, true)) {
                throw ValidationException::withMessages(['shipping_country' => $product->name.' is not available for this destination. Contact us for alternatives.']);
            }
            $minProcessing = max($minProcessing, $product->processing_min_days);
            $maxProcessing = max($maxProcessing, $product->processing_max_days);
        }

        return [
            'fee' => (float) $destination->shipping_fee,
            'from' => today()->addWeekdays($destination->min_days + $minProcessing),
            'to' => today()->addWeekdays($destination->max_days + $maxProcessing),
            'import_charges' => $destination->import_charges,
        ];
    }
}
