<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Deposit extends Model
{
    /** @use HasFactory<\Database\Factories\DepositFactory> */
    use HasFactory;

    protected $fillable = [
        'deposit_number',
        'account_id',
        'metal_type_id',
        'storage_type',
        'quantity_kg',
    ];

    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    public function metalType()
    {
        return $this->belongsTo(MetalType::class);
    }

    // Relationship name is singular but returns hasMany: multiple bars can attach to one allocated deposit.
    public function allocatedBar()
    {
        return $this->hasMany(AllocatedBar::class);
    }
}
