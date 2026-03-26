<?php

namespace Database\Factories;

use App\Models\AllocatedBars;
use App\Models\Deposits;
use App\Models\Accounts;
use App\Models\MetalTypes;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AllocatedBars>
 */
class AllocatedBarsFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'deposit_id' => Deposits::inRandomOrder()->first()->id,
            'account_id' => Accounts::inRandomOrder()->first()->id,
            'metal_type_id' => MetalTypes::inRandomOrder()->first()->id,
            'serial_number' => fake()->unique()->numerify('G-2026-####'),
            'weight_kg' => fake()->randomFloat(2, 0, 100000),
            'status' => fake()->randomElement(['allocated', 'unallocated']),
        ];
    }
}
