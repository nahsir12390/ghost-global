<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'buyer_email', 'buyer_whatsapp', 'terms_accepted_at', 'marketing_consent',
        'estimated_delivery_from', 'estimated_delivery_to', 'delivery_import_charges',
        'order_number',
        'user_id',
        'subtotal',
        'tax',
        'shipping',
        'total',
        'status',
        'payment_status',
        'payment_method',
        'payment_id',
        'payment_reference',
        'notes',
        'tracking_number',
        'shipping_carrier',
        'shipping_first_name',
        'shipping_last_name',
        'shipping_email',
        'shipping_phone',
        'shipping_address',
        'shipping_city',
        'shipping_state',
        'shipping_country',
        'shipping_postal_code',
        'same_as_shipping',
        'billing_first_name',
        'billing_last_name',
        'billing_email',
        'billing_phone',
        'billing_address',
        'billing_city',
        'billing_state',
        'billing_country',
        'billing_postal_code',
    ];

    protected $casts = [
        'terms_accepted_at' => 'datetime', 'marketing_consent' => 'boolean',
        'estimated_delivery_from' => 'date', 'estimated_delivery_to' => 'date',
        'user_id' => 'integer',
        'subtotal' => 'decimal:2',
        'tax' => 'decimal:2',
        'shipping' => 'decimal:2',
        'total' => 'decimal:2',
        'same_as_shipping' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($order) {
            // Generate unique order number
            $order->order_number = 'ORD-'.strtoupper(Str::random(10));
        });
    }

    public function shipments(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Shipment::class);
    }

    public function getContactEmailAttribute(): ?string
    {
        return $this->buyer_email ?: $this->shipping_email ?: $this->user?->email;
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function statusHistory()
    {
        return $this->hasMany(OrderStatusHistory::class);
    }

    public function getShippingFullNameAttribute()
    {
        return $this->shipping_first_name.' '.$this->shipping_last_name;
    }

    public function getBillingFullNameAttribute()
    {
        if ($this->same_as_shipping) {
            return $this->shipping_first_name.' '.$this->shipping_last_name;
        }

        return $this->billing_first_name.' '.$this->billing_last_name;
    }

    public function getFormattedTotalAttribute()
    {
        return '₦'.number_format($this->total, 2);
    }

    public function getStatusBadgeAttribute()
    {
        $badges = [
            'ordered' => 'bg-yellow-100 text-yellow-800',
            'confirmed' => 'bg-blue-100 text-blue-800',
            'picked_up' => 'bg-purple-100 text-purple-800',
            'on_the_way' => 'bg-indigo-100 text-indigo-800',
            'delivered' => 'bg-green-100 text-green-800',
            'cancelled' => 'bg-red-100 text-red-800',
            'failed' => 'bg-red-100 text-red-800',
        ];

        $paymentBadges = [
            'pending' => 'bg-yellow-100 text-yellow-800',
            'paid' => 'bg-green-100 text-green-800',
            'failed' => 'bg-red-100 text-red-800',
            'refunded' => 'bg-purple-100 text-purple-800',
        ];

        return [
            'status' => $badges[$this->status] ?? 'bg-gray-100 text-gray-800',
            'payment' => $paymentBadges[$this->payment_status] ?? 'bg-gray-100 text-gray-800',
        ];
    }

    public function scopeVisibleTo($query, ?User $user)
    {
        if (! $user) {
            return $query->whereRaw('1 = 0');
        }

        if ($user->isAdmin()) {
            return $query;
        }

        // Staff with order manager permission sees all orders
        if ($user->isStaff() && $user->isOrderManager()) {
            return $query;
        }

        return $query->where('user_id', $user->id);
    }

    public function canBeManagedBy(?User $user): bool
    {
        if (! $user) {
            return false;
        }

        if ($user->isAdmin()) {
            return true;
        }

        // Allow staff with order manager permission
        if ($user->isStaff() && $user->isOrderManager()) {
            return true;
        }

        return (int) $this->user_id === (int) $user->id;
    }

    public function canBeCancelledByCustomer(?User $user): bool
    {
        if (! $user || (int) $this->user_id !== (int) $user->id) {
            return false;
        }

        return in_array($this->status, ['pending', 'processing', 'packed', 'ordered', 'confirmed'], true);
    }
}
