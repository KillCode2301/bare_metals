<?php

namespace Database\Seeders;

use App\Models\Deposits;
use Illuminate\Database\Seeder;

class DepositsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Deposits::factory()->count(50)->create();
    }
}
