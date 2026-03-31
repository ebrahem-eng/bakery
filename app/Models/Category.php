<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = ['name', 'is_active', 'unit', 'input_mode', 'track_in_daily_close'];

    protected $casts = [
        'is_active' => 'boolean',
        'track_in_daily_close' => 'boolean',
    ];

    public function suppliers()
    {
        return $this->belongsToMany(Supplier::class);
    }

    public function supplies()
    {
        return $this->hasMany(Supply::class);
    }

    public function consumptions()
    {
        return $this->hasMany(Consumption::class);
    }
}
