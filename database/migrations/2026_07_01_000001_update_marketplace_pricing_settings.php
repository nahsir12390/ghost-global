<?php

use App\Models\Setting;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Setting::updateOrCreate(
            ['key' => 'shipping_fee'],
            [
                'value' => '700',
                'type' => 'number',
                'group' => 'shipping',
                'label' => 'Delivery Fee',
                'order' => 3,
                'is_public' => true,
            ]
        );

        Setting::updateOrCreate(
            ['key' => 'free_shipping_threshold'],
            [
                'value' => '10000',
                'type' => 'number',
                'group' => 'shipping',
                'label' => 'Free Delivery Threshold',
                'order' => 4,
                'is_public' => true,
            ]
        );

        Setting::where('key', 'tax_rate')->update([
            'label' => 'Platform Service Fee',
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Setting::where('key', 'shipping_fee')->update([
            'value' => '1000',
            'label' => 'Shipping Fee',
        ]);

        Setting::where('key', 'free_shipping_threshold')->update([
            'label' => 'Free Shipping Threshold',
        ]);

        Setting::where('key', 'tax_rate')->update([
            'label' => 'Tax Rate (%)',
        ]);
    }
};
