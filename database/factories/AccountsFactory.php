<?php

namespace Database\Factories;

use App\Models\Accounts;
use App\Models\Customers;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Accounts>
 */
class AccountsFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'customer_id' => Customers::inRandomOrder()->first()->id,
            'account_number' => fake()->unique()->numerify('AC-######'),
        ];
    }
}
