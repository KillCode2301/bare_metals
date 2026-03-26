<?php

namespace Database\Factories;

use App\Models\AccountHoldings;
use App\Models\Accounts;
use App\Models\MetalTypes;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AccountHoldings>
 */
class AccountHoldingsFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'account_id' => Accounts::inRandomOrder()->first()->id,
            'metal_type_id' => MetalTypes::inRandomOrder()->first()->id,
            'storage_type' => fake()->randomElement(['allocated', 'unallocated']),
            'balance_kg' => fake()->randomFloat(2, 0, 100000),
        ];
    }
}
