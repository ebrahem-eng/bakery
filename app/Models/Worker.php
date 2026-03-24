<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Worker extends Model
{
    use HasFactory;

    protected $fillable = ['first_name', 'last_name', 'title', 'daily_wage', 'currency_id'];

    public function mobiles() { return $this->hasMany(WorkerMobile::class); }
    public function currency() { return $this->belongsTo(Currency::class); }
}
