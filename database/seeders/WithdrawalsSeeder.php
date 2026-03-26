<?php

namespace Database\Seeders;

use App\Models\Withdrawals;
use Illuminate\Database\Seeder;

class WithdrawalsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Withdrawals::factory()->count(50)->create();
    }
}
