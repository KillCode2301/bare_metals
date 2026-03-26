<?php

namespace Database\Factories;

use App\Models\AccountHolding;
use App\Models\Account;
use App\Models\MetalType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AccountHolding>
 */
class AccountHoldingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'account_id' => Account::query()->inRandomOrder()->value('id') ?? Account::factory(),
            'metal_type_id' => MetalType::query()->inRandomOrder()->value('id') ?? MetalType::factory(),
            'storage_type' => fake()->randomElement(['allocated', 'unallocated']),
            'balance_kg' => fake()->randomFloat(2, 0, 100000),
        ];
    }
}
