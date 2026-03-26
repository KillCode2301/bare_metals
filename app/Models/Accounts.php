<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Accounts extends Model
{
    /** @use HasFactory<\Database\Factories\AccountsFactory> */
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'account_number',
    ];
}
