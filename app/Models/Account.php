<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    /** @use HasFactory<\Database\Factories\AccountFactory> */
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'account_number',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

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
