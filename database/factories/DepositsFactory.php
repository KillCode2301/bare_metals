<?php

namespace Database\Factories;

use App\Models\Deposits;
use App\Models\Accounts;
use App\Models\MetalTypes;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Deposits>
 */
class DepositsFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'deposit_number' => fake()->unique()->numerify('DP-######'),
            'account_id' => Accounts::inRandomOrder()->first()->id,
            'metal_type_id' => MetalTypes::inRandomOrder()->first()->id,
            'storage_type' => fake()->randomElement(['allocated', 'unallocated']),
            'quantity_kg' => fake()->randomFloat(2, 0, 100000),
        ];
    }
}
