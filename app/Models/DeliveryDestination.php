<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeliveryDestination extends Model
{
    use HasFactory;

    protected $fillable = ['country_code', 'enabled', 'shipping_fee', 'min_days', 'max_days', 'state_required', 'postal_required', 'import_charges'];

    protected function casts(): array
    {
        return ['enabled' => 'boolean', 'shipping_fee' => 'decimal:2', 'min_days' => 'integer', 'max_days' => 'integer', 'state_required' => 'boolean', 'postal_required' => 'boolean'];
    }

    public function getCountryNameAttribute(): string
    {
        return config('countries.'.$this->country_code, $this->country_code);
    }
}
