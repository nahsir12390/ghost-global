<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OrderStatusHistory extends Model
{
    use HasFactory;

    protected $table = 'order_status_histories';

    protected $fillable = [
        'order_id',
        'old_status',
        'new_status',
        'notes'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Get status label with formatting
     */
    public function getStatusLabelAttribute()
    {
        $labels = [
            'ordered' => 'Ordered',
            'confirmed' => 'Confirmed',
            'picked_up' => 'Picked Up',
            'on_the_way' => 'On The Way',
            'delivered' => 'Delivered',
            'cancelled' => 'Cancelled',
            'failed' => 'Failed',
            'paid' => 'Paid'
        ];

        return $labels[$this->new_status] ?? ucfirst(str_replace('_', ' ', $this->new_status));
    }

    /**
     * Get status color for UI display
     */
    public function getStatusColorAttribute()
    {
        $colors = [
            'ordered' => 'yellow',
            'confirmed' => 'blue',
            'picked_up' => 'purple',
            'on_the_way' => 'orange',
            'delivered' => 'green',
            'cancelled' => 'red',
            'failed' => 'red',
            'paid' => 'green'
        ];

        return $colors[$this->new_status] ?? 'gray';
    }
}
