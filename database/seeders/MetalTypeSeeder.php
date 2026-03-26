<?php

namespace Database\Seeders;

use App\Models\MetalType;
use Illuminate\Database\Seeder;

class MetalTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        MetalType::factory()->count(3)->create();
    }
}