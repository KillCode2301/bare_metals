<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccountHolding extends Model
{
    /** @use HasFactory<\Database\Factories\AccountHoldingsFactory> */
    use HasFactory;

    protected $fillable = [
        'account_id',
        'metal_type_id',
        'storage_type',
        'balance_kg',
    ];

    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    public function metalType()
    {
        return $this->belongsTo(MetalType::class);
    }
}
