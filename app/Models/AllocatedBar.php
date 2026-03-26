<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AllocatedBar extends Model
{
    /** @use HasFactory<\Database\Factories\AllocatedBarFactory> */
    use HasFactory;

    protected $fillable = [
        'deposit_id',
        'account_id',
        'metal_type_id',
        'serial_number',
        'weight_kg',
        'status',
    ];

    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    public function metalType()
    {
        return $this->belongsTo(MetalType::class);
    }

    public function deposit()
    {
        return $this->belongsTo(Deposit::class);
    }
}
