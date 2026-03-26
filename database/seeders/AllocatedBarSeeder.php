<?php

namespace Database\Seeders;

use App\Models\AllocatedBar;
use App\Models\Deposit;
use Illuminate\Database\Seeder;

class AllocatedBarSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Deposit::query()->each(function (Deposit $deposit): void {
            AllocatedBar::factory()->create([
                'deposit_id' => $deposit->id,
                'account_id' => $deposit->account_id,
                'metal_type_id' => $deposit->metal_type_id,
                'status' => $deposit->storage_type === 'allocated' ? 'allocated' : 'unallocated',
            ]);
        });
    }
}
