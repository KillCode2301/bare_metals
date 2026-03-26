<?php

namespace Database\Factories;

use App\Models\AllocatedBar;
use App\Models\Deposit;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AllocatedBar>
 */
class AllocatedBarFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $deposit = Deposit::query()->inRandomOrder()->first() ?? Deposit::factory()->create();

        return [
            'deposit_id' => $deposit->id,
            'account_id' => $deposit->account_id,
            'metal_type_id' => $deposit->metal_type_id,
            'serial_number' => fake()->unique()->numerify('G-2026-####'),
            'weight_kg' => fake()->randomFloat(2, 0, 100000),
            'status' => $deposit->storage_type === 'allocated' ? 'allocated' : 'unallocated',
        ];
    }
}
