<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'vendor_id',
        'category_id',
        'name',
        'slug',
        'description',
        'product_type',
        'price',
        'compare_price',
        'quantity',
        'sku',
        'images',
        'download_file_path',
        'download_link',
        'course_access_url',
        'access_instructions',
        'is_featured',
        'is_active'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'compare_price' => 'decimal:2',
        'is_featured' => 'boolean',
        'is_active' => 'boolean'
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
        if (is_array($value)) {
            $this->attributes['images'] = json_encode($value);
        } else {
            $this->attributes['images'] = $value;
        }
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($product) {
            if (blank($product->slug)) {
                $product->slug = Str::slug($product->name);
            }
            
            // Generate SKU if not provided
            if (empty($product->sku)) {
                $product->sku = 'PROD-' . strtoupper(Str::random(8));
            }
        });

        static::updating(function ($product) {
            if (blank($product->slug)) {
                $product->slug = Str::slug($product->name);
            }
        });
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function vendor()
    {
        return $this->belongsTo(User::class, 'vendor_id');
    }

    public function vendorIsAvailable(): bool
    {
        return ! $this->vendor || $this->vendor->isVendorActive();
    }

    public function isPhysical(): bool
    {
        return $this->product_type === self::TYPE_PHYSICAL;
    }

    public function isDigital(): bool
    {
        return $this->product_type === self::TYPE_DIGITAL;
    }

    public function isCourse(): bool
    {
        return $this->product_type === self::TYPE_COURSE;
    }

    public function requiresShipping(): bool
    {
        return $this->isPhysical();
    }

    public function tracksInventory(): bool
    {
        return $this->isPhysical();
    }

    public function grantsDigitalAccess(): bool
    {
        return $this->isDigital() || $this->isCourse();
    }

    public function hasDigitalFulfillment(): bool
    {
        if ($this->isDigital()) {
            return filled($this->download_file_path) || filled($this->download_link);
        }

        if ($this->isCourse()) {
            return filled($this->course_access_url) || filled($this->access_instructions);
        }

        return false;
    }

    public function isPurchasable(): bool
    {
        if (! $this->is_active || ! $this->vendorIsAvailable()) {
            return false;
        }

        if ($this->tracksInventory()) {
            return $this->quantity > 0;
        }

        return $this->hasDigitalFulfillment();
    }

    public function unavailableReason(): ?string
    {
        if (! $this->is_active) {
            return 'This product is currently unavailable.';
        }

        if ($this->tracksInventory() && $this->quantity <= 0) {
            return 'This product is out of stock.';
        }

        if (! $this->vendorIsAvailable()) {
            return ($this->vendor?->store_name ?: 'This vendor').' is currently unavailable.';
        }

        if (! $this->tracksInventory() && ! $this->hasDigitalFulfillment()) {
            return 'This product is not fully configured yet.';
        }

        return null;
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }

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
        return !empty($images) ? $images[0] : null;
    }

    public function wishlists()
    {
        return $this->hasMany(Wishlist::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class)->where('is_approved', true)->latest();
    }

    public function scopeForManager($query, ?User $user)
    {
        if (!$user) {
            return $query->whereRaw('1 = 0');
        }

        if ($user->isAdmin()) {
            return $query;
        }

        if ($user->isVendor()) {
            return $query->where('vendor_id', $user->id);
        }

        // Staff with product manager permission sees all products
        if ($user->isStaff() && $user->isProductManager()) {
            return $query;
        }

        return $query->whereRaw('1 = 0');
    }

    public function canBeManagedBy(?User $user): bool
    {
        if (!$user) {
            return false;
        }

        if ($user->isAdmin()) {
            return true;
        }

        if ($user->isVendor()) {
            return (int) $this->vendor_id === (int) $user->id;
        }

        // Allow staff with product manager permission
        if ($user->isStaff() && $user->isProductManager()) {
            return true;
        }

        return false;
    }
}
