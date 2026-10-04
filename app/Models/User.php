<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'email_verified_at',
        'password',
        'provider',
        'provider_id',
        'provider_avatar',
        'referral_code',
        'referred_by_id',
        'referral_rewarded_at',
        'referral_reward_order_id',
        'is_admin',
        'role',
        'store_name',
        'store_slug',
        'store_description',
        'store_banner_path',
        'store_whatsapp',
        'store_instagram',
        'store_facebook',
        'store_website',
        'phone',
        'address',
        'verification_status',
        'vendor_is_active',
        'verification_submitted_at',
        'verified_at',
        'verification_notes',
        'verification_nin',
        'verification_email',
        'verification_phone',
        'verification_id_front_path',
        'verification_id_back_path',
        'bank_name',
        'bank_account_name',
        'bank_account_number',
        'bank_verification_status',
        'bank_verified_at',
        'profile_photo_path',
        'staff_role',
        'staff_assigned_at',
        'staff_deactivated_at',
        'is_staff',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_admin' => 'boolean',
            'is_staff' => 'boolean',
            'vendor_is_active' => 'boolean',
            'referred_by_id' => 'integer',
            'verification_submitted_at' => 'datetime',
            'verified_at' => 'datetime',
            'bank_verified_at' => 'datetime',
            'referral_rewarded_at' => 'datetime',
            'staff_assigned_at' => 'datetime',
            'staff_deactivated_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (self $user): void {
            if ($user->is_admin) {
                $user->role = 'admin';
            }

            if (blank($user->role)) {
                $user->role = 'customer';
            }

            if ($user->role === 'admin') {
                $user->is_admin = true;
            }

            if ($user->role !== 'vendor') {
                $user->store_name = null;
                $user->store_slug = null;
                $user->store_description = null;
                $user->store_banner_path = null;
                $user->store_whatsapp = null;
                $user->store_instagram = null;
                $user->store_facebook = null;
                $user->store_website = null;
                $user->verification_status = null;
                $user->verification_submitted_at = null;
                $user->verified_at = null;
                $user->verification_notes = null;
                $user->verification_nin = null;
                $user->verification_email = null;
                $user->verification_phone = null;
                $user->verification_id_front_path = null;
                $user->verification_id_back_path = null;
            } elseif ($user->store_name && (blank($user->store_slug) || $user->isDirty('store_name'))) {
                $user->store_slug = static::generateUniqueStoreSlug($user->store_name, $user->id);
            }

            if ($user->role === 'vendor' && blank($user->verification_status)) {
                $user->verification_status = 'pending';
            }

            // Handle staff role clearing when deactivated
            if (! $user->is_staff && $user->isDirty('is_staff')) {
                $user->staff_role = null;
                $user->staff_assigned_at = null;
                $user->staff_deactivated_at = now();
            }
        });
    }

    public function isAdmin(): bool
    {
        return $this->is_admin === true || $this->role === 'admin';
    }

    public function isVendor(): bool
    {
        return $this->role === 'vendor';
    }

    public function isCustomer(): bool
    {
        return ! $this->isAdmin() && ! $this->isVendor();
    }

    public function canAccessBackoffice(): bool
    {
        return $this->isAdmin() || $this->isStaff();
    }

    public function dashboardRouteName(): string
    {
        if ($this->isStaff()) {
            return 'admin.staff.dashboard';
        }

        if ($this->isAdmin()) {
            return 'admin.dashboard';
        }

        return 'dashboard';
    }

    public function isVendorVerified(): bool
    {
        if (! $this->isVendor()) {
            return false;
        }

        $status = strtolower((string) $this->verification_status);

        return $status === 'approved' || $this->verified_at !== null;
    }

    public function isVendorPending(): bool
    {
        return $this->isVendor() && $this->verification_status === 'pending';
    }

    public function isVendorActive(): bool
    {
        return ! $this->isVendor() || $this->vendor_is_active !== false;
    }

    public function vendorAvailabilityLabel(): string
    {
        return $this->isVendorActive() ? 'Active' : 'Inactive';
    }

    public function publicStoreName(): string
    {
        return (string) ($this->store_name ?: $this->name);
    }

    public function hasPublicStorefront(): bool
    {
        $storeSlug = $this->ensureStoreSlug();

        return $this->isVendor()
            && filled($storeSlug)
            && $this->isVendorVerified()
            && $this->isVendorActive();
    }

    public function storefrontUrl(): ?string
    {
        return null;
    }

    public function ensureStoreSlug(): ?string
    {
        if (! $this->isVendor() || blank($this->store_name)) {
            return null;
        }

        if (filled($this->store_slug)) {
            return $this->store_slug;
        }

        $storeSlug = static::generateUniqueStoreSlug($this->store_name, $this->id);

        if ($this->exists) {
            $this->forceFill(['store_slug' => $storeSlug])->saveQuietly();
        } else {
            $this->store_slug = $storeSlug;
        }

        return $storeSlug;
    }

    public function storefrontLogoUrl(): ?string
    {
        return $this->profile_photo_path ? asset('storage/'.$this->profile_photo_path) : null;
    }

    public function storefrontBannerUrl(): ?string
    {
        return $this->store_banner_path ? asset('storage/'.$this->store_banner_path) : null;
    }

    public function storefrontWhatsAppUrl(): ?string
    {
        $number = preg_replace('/\D+/', '', (string) $this->store_whatsapp);

        return $number ? 'https://wa.me/'.$number : null;
    }

    public function storefrontInstagramUrl(): ?string
    {
        return $this->normalizeSocialUrl($this->store_instagram, 'https://instagram.com/');
    }

    public function storefrontFacebookUrl(): ?string
    {
        return $this->normalizeSocialUrl($this->store_facebook, 'https://facebook.com/');
    }

    public function storefrontWebsiteUrl(): ?string
    {
        $website = trim((string) $this->store_website);

        if ($website === '') {
            return null;
        }

        if (Str::startsWith($website, ['http://', 'https://'])) {
            return $website;
        }

        return 'https://'.ltrim($website, '/');
    }

    protected function normalizeSocialUrl(?string $value, string $baseUrl): ?string
    {
        $value = trim((string) $value);

        if ($value === '') {
            return null;
        }

        if (Str::startsWith($value, ['http://', 'https://'])) {
            return $value;
        }

        return $baseUrl.ltrim($value, '@/');
    }

    public function isStaff(): bool
    {
        return $this->is_staff === true && $this->staff_deactivated_at === null;
    }

    public function isOrderManager(): bool
    {
        return $this->isStaff() && ($this->staff_role === 'order_manager' || $this->staff_role === 'all');
    }

    public function isProductManager(): bool
    {
        return $this->isStaff() && ($this->staff_role === 'product_manager' || $this->staff_role === 'all');
    }

    public function canManageOrders(): bool
    {
        return $this->isAdmin() || $this->isOrderManager();
    }

    public function canManageProducts(): bool
    {
        return $this->isAdmin() || $this->isProductManager();
    }

    public function canManageBoth(): bool
    {
        return $this->isAdmin() || ($this->isStaff() && $this->staff_role === 'all');
    }

    public function assignAsStaff(string $role): void
    {
        if (! in_array($role, ['order_manager', 'product_manager', 'all'])) {
            throw new \InvalidArgumentException('Invalid staff role');
        }

        $this->update([
            'is_staff' => true,
            'staff_role' => $role,
            'staff_assigned_at' => now(),
            'staff_deactivated_at' => null,
        ]);
    }

    public function deactivateStaff(): void
    {
        $this->update([
            'is_staff' => false,
            'staff_deactivated_at' => now(),
        ]);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function wishlists()
    {
        return $this->hasMany(Wishlist::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class, 'vendor_id');
    }

    public function vendorOrderItems()
    {
        return $this->hasMany(OrderItem::class, 'vendor_id');
    }

    public function wallet()
    {
        return $this->hasOne(Wallet::class);
    }

    public function walletTransactions()
    {
        return $this->hasMany(WalletTransaction::class);
    }

    public function referredBy()
    {
        return $this->belongsTo(User::class, 'referred_by_id');
    }

    public function referrals()
    {
        return $this->hasMany(User::class, 'referred_by_id');
    }

    public function referralRewardOrder()
    {
        return $this->belongsTo(Order::class, 'referral_reward_order_id');
    }

    public static function generateUniqueReferralCode(): string
    {
        do {
            $code = 'REF'.strtoupper(Str::random(8));
        } while (static::query()->where('referral_code', $code)->exists());

        return $code;
    }

    public static function generateUniqueStoreSlug(string $storeName, ?int $ignoreUserId = null): string
    {
        $baseSlug = Str::slug($storeName);
        $slug = $baseSlug !== '' ? $baseSlug : 'store';
        $counter = 1;

        while (
            static::query()
                ->when($ignoreUserId, fn ($query) => $query->where('id', '!=', $ignoreUserId))
                ->where('store_slug', $slug)
                ->exists()
        ) {
            $slug = ($baseSlug !== '' ? $baseSlug : 'store').'-'.$counter;
            $counter++;
        }

        return $slug;
    }

    public function getOrCreateReferralCode(): string
    {
        if ($this->referral_code) {
            return $this->referral_code;
        }

        $this->forceFill([
            'referral_code' => static::generateUniqueReferralCode(),
        ])->save();

        return (string) $this->referral_code;
    }

    public function referralLink(): string
    {
        return route('referrals.capture', ['code' => $this->getOrCreateReferralCode()]);
    }
}
