<?php

namespace Database\Factories;

use App\Models\Deposit;
use App\Models\Account;
use App\Models\MetalType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Deposit>
 */
class DepositFactory extends Factory
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
            'account_id' => Account::query()->inRandomOrder()->value('id') ?? Account::factory(),
            'metal_type_id' => MetalType::query()->inRandomOrder()->value('id') ?? MetalType::factory(),
            'storage_type' => fake()->randomElement(['allocated', 'unallocated']),
            'quantity_kg' => fake()->randomFloat(2, 0, 100000),
        ];
    }
}
