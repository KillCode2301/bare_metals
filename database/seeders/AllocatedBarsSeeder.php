<?php

namespace Database\Seeders;

use App\Models\AllocatedBars;
use Illuminate\Database\Seeder;

class AllocatedBarsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        AllocatedBars::factory()->count(50)->create();
    }
}
