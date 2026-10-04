<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'value',
        'type',
        'group',
        'label',
        'order',
        'is_public',
    ];

    protected $casts = [
        'is_public' => 'boolean',
        'order' => 'integer',
    ];

    /**
     * Get a setting value by key.
     */
    public static function getValue($key, $default = null)
    {
        $setting = static::where('key', $key)->first();

        return $setting ? $setting->value : $default;
    }

    /**
     * Set a setting value by key.
     */
    public static function setValue($key, $value)
    {
        $setting = static::firstOrNew(['key' => $key]);
        $setting->value = $value;

        if (! $setting->exists) {
            $setting->fill([
                'type' => 'string',
                'group' => 'general',
                'label' => ucwords(str_replace('_', ' ', $key)),
                'order' => 999,
                'is_public' => false,
            ]);
        }

        $setting->save();

        return $setting;
    }

    /**
     * Get settings by group.
     */
    public static function getByGroup($group)
    {
        return static::where('group', $group)->orderBy('order')->get();
    }
}
