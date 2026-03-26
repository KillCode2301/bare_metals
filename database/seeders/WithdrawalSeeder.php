<?php

namespace Database\Seeders;

use App\Models\Account;
use App\Models\MetalType;
use App\Models\Withdrawal;
use Illuminate\Database\Seeder;

class WithdrawalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Account::query()->each(function (Account $account): void {
            $metalTypeId = MetalType::query()->inRandomOrder()->value('id') ?? MetalType::factory()->create()->id;

            Withdrawal::factory()->for($account)->create([
                'metal_type_id' => $metalTypeId,
            ]);
        });
    }
}
