<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    protected $fillable = ['name', 'code', 'exchange_rate', 'is_default'];
    
    protected $casts = [
        'is_default' => 'boolean',
        'exchange_rate' => 'decimal:2',
    ];
}
