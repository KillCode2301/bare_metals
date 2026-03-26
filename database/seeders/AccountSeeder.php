<?php

namespace Database\Seeders;

use App\Models\Account;
use App\Models\Customer;
use Illuminate\Database\Seeder;

class AccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Customer::query()->each(function (Customer $customer): void {
            Account::factory()->for($customer)->create();
        });
    }
}
