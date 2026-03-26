<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MetalTypes extends Model
{
    /** @use HasFactory<\Database\Factories\MetalTypesFactory> */
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'current_price_per_kg',
    ];
}
