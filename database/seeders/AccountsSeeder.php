<?php

namespace Database\Seeders;

use App\Models\Accounts;
use Illuminate\Database\Seeder;

class AccountsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Accounts::factory()->count(60)->create();
    }
}
