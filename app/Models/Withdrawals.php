<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Withdrawals extends Model
{
    /** @use HasFactory<\Database\Factories\WithdrawalsFactory> */
    use HasFactory;

    protected $fillable = [
        'withdrawal_number',
        'account_id',
        'metal_type_id',
        'storage_type',
        'quantity_kg',
    ];
}
