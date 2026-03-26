<?php

namespace Database\Seeders;

use App\Models\MetalTypes;
use Illuminate\Database\Seeder;

class MetalTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        MetalTypes::factory()->count(3)->create();
    }
}