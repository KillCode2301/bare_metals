<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Deposits extends Model
{
    /** @use HasFactory<\Database\Factories\DepositsFactory> */
    use HasFactory;

    protected $fillable = [
        'deposit_number',
        'account_id',
        'metal_type_id',
        'storage_type',
        'quantity_kg',
    ];
}
