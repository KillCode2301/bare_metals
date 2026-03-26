<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MetalType extends Model
{
    /** @use HasFactory<\Database\Factories\MetalTypesFactory> */
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'current_price_per_kg',
    ];

    public function deposit()
    {
        return $this->hasMany(Deposit::class);
    }

    public function withdrawal()
    {
        return $this->hasMany(Withdrawal::class);
    }

    public function holding()
    {
        return $this->hasMany(AccountHolding::class);
    }

    public function allocatedBar()
    {
        return $this->hasMany(AllocatedBar::class);
    }
}
