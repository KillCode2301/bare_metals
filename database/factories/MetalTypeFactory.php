<?php

namespace Database\Factories;

use App\Models\MetalType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MetalType>
 */
class MetalTypeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'code' => fake()->unique()->randomElement(['AU', 'AG', 'PT', 'PD']),
            'name' => fake()->unique()->randomElement(['Gold', 'Silver', 'Platinum', 'Palladium']),
            'current_price_per_kg' => fake()->randomFloat(2, 0, 100000),
        ];
    }
}
