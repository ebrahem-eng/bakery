<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DistributorMobile extends Model
{
    use HasFactory;

    protected $fillable = ['distributor_id', 'number'];

    public function distributor()
    {
        return $this->belongsTo(Distributor::class);
    }
}
