<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\DeliveryDestination>
 */
class DeliveryDestinationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'country_code' => fake()->unique()->countryCode(),
            'enabled' => true, 'shipping_fee' => 5000,
            'min_days' => 7, 'max_days' => 14,
            'state_required' => false, 'postal_required' => false,
            'import_charges' => 'Import charges are included.',
        ];
    }
}
