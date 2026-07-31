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
        $settings = [
            ['platform_fee_tier_1_max', '10000', 'Tier 1 Maximum', 11],
            ['platform_fee_tier_1_rate', '7.5', 'Tier 1 Service Fee (%)', 12],
            ['platform_fee_tier_2_max', '50000', 'Tier 2 Maximum', 13],
            ['platform_fee_tier_2_rate', '5', 'Tier 2 Service Fee (%)', 14],
            ['platform_fee_tier_3_max', '200000', 'Tier 3 Maximum', 15],
            ['platform_fee_tier_3_rate', '3', 'Tier 3 Service Fee (%)', 16],
            ['platform_fee_tier_4_rate', '2', 'Final Tier Service Fee (%)', 17],
        ];

        foreach ($settings as [$key, $value, $label, $order]) {
            Setting::updateOrCreate(
                ['key' => $key],
                [
                    'value' => $value,
                    'type' => 'number',
                    'group' => 'general',
                    'label' => $label,
                    'order' => $order,
                    'is_public' => true,
                ]
            );
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Setting::whereIn('key', [
            'platform_fee_tier_1_max',
            'platform_fee_tier_1_rate',
            'platform_fee_tier_2_max',
            'platform_fee_tier_2_rate',
            'platform_fee_tier_3_max',
            'platform_fee_tier_3_rate',
            'platform_fee_tier_4_rate',
        ])->delete();
    }
};
