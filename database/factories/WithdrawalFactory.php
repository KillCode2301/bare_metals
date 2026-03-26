<?php

namespace Database\Factories;

use App\Models\Withdrawal;
use App\Models\Account;
use App\Models\MetalType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Withdrawal>
 */
class WithdrawalFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'withdrawal_number' => fake()->unique()->numerify('WD-######'),
            'account_id' => Account::query()->inRandomOrder()->value('id') ?? Account::factory(),
            'metal_type_id' => MetalType::query()->inRandomOrder()->value('id') ?? MetalType::factory(),
            'storage_type' => fake()->randomElement(['allocated', 'unallocated']),
            'quantity_kg' => fake()->randomFloat(2, 0, 100000),
        ];
    }
}
