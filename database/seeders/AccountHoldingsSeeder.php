<?php

namespace Database\Seeders;

use App\Models\AccountHoldings;
use Illuminate\Database\Seeder;

class AccountHoldingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        AccountHoldings::factory()->count(50)->create();
    }
}
