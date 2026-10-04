<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'delivery_countries', 'processing_min_days', 'processing_max_days',
        'category_id', 'name', 'slug', 'description', 'product_type', 'price',
        'compare_price', 'quantity', 'sku', 'images', 'download_file_path',
        'download_link', 'course_access_url', 'access_instructions',
        'is_featured', 'is_active',
    ];

    protected $casts = [
        'delivery_countries' => 'array',
        'processing_min_days' => 'integer',
        'processing_max_days' => 'integer',
        'price' => 'decimal:2',
        'compare_price' => 'decimal:2',
        'quantity' => 'integer',
        'is_featured' => 'boolean',
        'is_active' => 'boolean',
    ];

    public const TYPE_PHYSICAL = 'physical';
    public const TYPE_DIGITAL = 'digital';
    public const TYPE_COURSE = 'course';

    public function getImagesAttribute($value)
    {
        if (is_string($value)) {
            return json_decode($value, true) ?? [];
        }
        return $value ?? [];
    }

    public function setImagesAttribute($value)
    {
        $this->attributes['images'] = is_array($value) ? json_encode($value) : $value;
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function (Product $product) {
            if (blank($product->slug)) {
                $product->slug = static::uniqueSlug($product->name);
            }
            if (blank($product->sku)) {
                $product->sku = static::uniqueSku();
            }
        });

        static::updating(function (Product $product) {
            if (blank($product->slug)) {
                $product->slug = static::uniqueSlug($product->name, $product->id);
            }
        });
    }

    public static function uniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $base = Str::slug($name) ?: 'product';
        $slug = $base;
        $counter = 2;
        while (static::query()->where('slug', $slug)->when($ignoreId, fn ($q) => $q->whereKeyNot($ignoreId))->exists()) {
            $slug = $base.'-'.$counter++;
        }
        return $slug;
    }

    public static function uniqueSku(): string
    {
        do {
            $sku = 'PROD-'.Str::upper(Str::random(8));
        } while (static::query()->where('sku', $sku)->exists());
        return $sku;
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function isPhysical(): bool { return $this->product_type === self::TYPE_PHYSICAL; }
    public function isDigital(): bool { return $this->product_type === self::TYPE_DIGITAL; }
    public function isCourse(): bool { return $this->product_type === self::TYPE_COURSE; }
    public function requiresShipping(): bool { return $this->isPhysical(); }
    public function tracksInventory(): bool { return $this->isPhysical(); }
    public function grantsDigitalAccess(): bool { return $this->isDigital() || $this->isCourse(); }

    public function hasDigitalFulfillment(): bool
    {
        if ($this->isDigital()) return filled($this->download_file_path) || filled($this->download_link);
        if ($this->isCourse()) return filled($this->course_access_url) || filled($this->access_instructions);
        return false;
    }

    public function isPurchasable(): bool
    {
        if (! $this->is_active) return false;
        if ($this->tracksInventory()) return $this->quantity > 0;
        return $this->hasDigitalFulfillment();
    }

    public function unavailableReason(): ?string
    {
        if (! $this->is_active) return 'This product is currently unavailable.';
        if ($this->tracksInventory() && $this->quantity <= 0) return 'This product is out of stock.';
        if (! $this->tracksInventory() && ! $this->hasDigitalFulfillment()) return 'This product is not fully configured yet.';
        return null;
    }

    public function orderItems() { return $this->hasMany(OrderItem::class); }
    public function getRouteKeyName() { return 'slug'; }

    public function getDiscountPercentageAttribute()
    {
        if ($this->compare_price && $this->compare_price > $this->price) {
            return round((($this->compare_price - $this->price) / $this->compare_price) * 100);
        }
        return 0;
    }

    public function getMainImageAttribute()
    {
        $images = $this->images ?? [];
        return ! empty($images) ? $images[0] : null;
    }

    public function wishlists() { return $this->hasMany(Wishlist::class); }
    public function comments() { return $this->hasMany(Comment::class)->where('is_approved', true)->latest(); }

    public function scopeForManager($query, ?User $user)
    {
        if (! $user) return $query->whereRaw('1 = 0');
        if ($user->isAdmin() || ($user->isStaff() && $user->isProductManager())) return $query;
        return $query->whereRaw('1 = 0');
    }

    public function canBeManagedBy(?User $user): bool
    {
        return (bool) ($user && ($user->isAdmin() || ($user->isStaff() && $user->isProductManager())));
    }
}
