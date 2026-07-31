<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class WalletTransaction extends Model
{
    use HasFactory;

    public const TYPE_TOPUP = 'topup';
    public const TYPE_ORDER_PAYMENT = 'order_payment';
    public const TYPE_REFUND = 'refund';
    public const TYPE_ADMIN_ADJUSTMENT = 'admin_adjustment';
    public const TYPE_REFERRAL_BONUS = 'referral_bonus';

    protected $fillable = [
        'user_id',
        'wallet_id',
        'reference',
        'type',
        'direction',
        'amount',
        'balance_before',
        'balance_after',
        'status',
        'payment_provider',
        'external_reference',
        'description',
        'meta',
        'completed_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'balance_before' => 'decimal:2',
        'balance_after' => 'decimal:2',
        'meta' => 'array',
        'completed_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function wallet(): BelongsTo
    {
        return $this->belongsTo(Wallet::class);
    }
}
