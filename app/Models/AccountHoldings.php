<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccountHoldings extends Model
{
    /** @use HasFactory<\Database\Factories\AccountHoldingsFactory> */
    use HasFactory;

    protected $fillable = [
        'account_id',
        'metal_type_id',
        'storage_type',
        'balance_kg',
    ];
}
