<?php

namespace App\Support;

use App\Models\Deposit;
use App\Models\Withdrawal;

final class TransactionDetailPayload
{
    /**
     * @return array<string, mixed>
     */
    public static function forDeposit(Deposit $deposit): array
    {
        $customer = $deposit->account->customer;
        $createdAt = $deposit->created_at;

        return [
            'kind' => 'deposit',
            'reference' => $deposit->deposit_number,
            'occurred_at' => $createdAt->format('M d, Y \a\t g:i A'),
            'account_name' => $customer->full_name,
            'account_number' => $deposit->account->account_number,
            'customer_type' => $customer->customer_type,
            'metal' => $deposit->metalType->name,
            'storage_type' => $deposit->storage_type,
            'quantity_kg' => (float) $deposit->quantity_kg,
            'bars' => self::barsFromDeposit($deposit),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function forWithdrawal(Withdrawal $withdrawal): array
    {
        $customer = $withdrawal->account->customer;
        $createdAt = $withdrawal->created_at;
        // Post-withdrawal, bars carry current status (often "unallocated") and serial/weight for the modal table.
        $bars = $withdrawal->allocatedBars->map(static function ($bar): array {
            return [
                'serial_number' => $bar->serial_number,
                'weight_kg' => (float) $bar->weight_kg,
                'status' => $bar->status,
            ];
        })->values()->all();

        return [
            'kind' => 'withdrawal',
            'reference' => $withdrawal->withdrawal_number,
            'occurred_at' => $createdAt->format('M d, Y \a\t g:i A'),
            'account_name' => $customer->full_name,
            'account_number' => $withdrawal->account->account_number,
            'customer_type' => $customer->customer_type,
            'metal' => $withdrawal->metalType->name,
            'storage_type' => $withdrawal->storage_type,
            'quantity_kg' => (float) $withdrawal->quantity_kg,
            'bars' => $bars,
        ];
    }

    /**
     * @return list<array{serial_number: string, weight_kg: float, status: string}>
     */
    private static function barsFromDeposit(Deposit $deposit): array
    {
        // Method name vs return: hasMany returns a collection despite singular `allocatedBar` on the model.
        return $deposit->allocatedBar->map(static function ($bar): array {
            return [
                'serial_number' => $bar->serial_number,
                'weight_kg' => (float) $bar->weight_kg,
                'status' => $bar->status,
            ];
        })->values()->all();
    }
}
