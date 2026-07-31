<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Staff extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'staff_role',
        'staff_assigned_at',
        'staff_deactivated_at',
        'permissions',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'staff_assigned_at' => 'datetime',
            'staff_deactivated_at' => 'datetime',
            'permissions' => 'json',
        ];
    }

    /**
     * Get the staff member user
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if staff member can manage orders
     */
    public function canManageOrders(): bool
    {
        return $this->staff_role === 'order_manager' || $this->staff_role === 'all';
    }

    /**
     * Check if staff member can manage products
     */
    public function canManageProducts(): bool
    {
        return $this->staff_role === 'product_manager' || $this->staff_role === 'all';
    }

    /**
     * Check if staff member is active
     */
    public function isActive(): bool
    {
        return $this->staff_deactivated_at === null;
    }

    /**
     * Deactivate staff member
     */
    public function deactivate(): void
    {
        $this->update(['staff_deactivated_at' => now()]);
        $this->user->update(['is_staff' => false]);
    }

    /**
     * Reactivate staff member
     */
    public function reactivate(): void
    {
        $this->update(['staff_deactivated_at' => null]);
        $this->user->update(['is_staff' => true]);
    }
}
