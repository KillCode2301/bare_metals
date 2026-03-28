<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Withdrawal extends Model
{
    /** @use HasFactory<\Database\Factories\WithdrawalFactory> */
    use HasFactory;

    protected $fillable = [
        'withdrawal_number',
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

    public function allocatedBars()
    {
        return $this->hasMany(AllocatedBar::class);
    }
}
