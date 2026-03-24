<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Distributor extends Model
{
    use HasFactory;

    protected $fillable = ['first_name', 'last_name', 'title', 'preferred_currency_id', 'notes'];

    public function mobiles()
    {
        return $this->hasMany(DistributorMobile::class);
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class, 'preferred_currency_id');
    }
}
