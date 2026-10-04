<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Shipment extends Model
{
    use HasFactory;

    public const STATUSES = ['preparing' => 'Preparing your order', 'shipped' => 'Shipped', 'in_transit' => 'In transit', 'out_for_delivery' => 'Out for delivery', 'delivered' => 'Delivered', 'delayed' => 'Delayed', 'delivery_attempted' => 'Delivery attempted', 'cancelled' => 'Cancelled'];

    protected $fillable = ['reference', 'supplier_name', 'supplier_order_reference', 'purchase_cost', 'purchase_currency', 'private_notes', 'status', 'carrier', 'tracking_number', 'tracking_url', 'estimated_from', 'estimated_to', 'customer_update'];

    protected $hidden = ['supplier_name', 'supplier_order_reference', 'purchase_cost', 'purchase_currency', 'private_notes'];

    protected function casts(): array
    {
        return ['purchase_cost' => 'decimal:2', 'estimated_from' => 'date', 'estimated_to' => 'date'];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function items(): BelongsToMany
    {
        return $this->belongsToMany(OrderItem::class)->withPivot('quantity');
    }
}
