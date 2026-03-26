<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AllocatedBars extends Model
{
    /** @use HasFactory<\Database\Factories\AllocatedBarsFactory> */
    use HasFactory;

    protected $fillable = [
        'deposit_id',
        'account_id',
        'metal_type_id',
        'serial_number',
        'weight_kg',
        'status',
    ];
}
