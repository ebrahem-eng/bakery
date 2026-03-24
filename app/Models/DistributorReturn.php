<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DistributorReturn extends Model
{
    use HasFactory;

    protected $fillable = [
        'distributor_id', 'work_day_id', 'bundle_count',
        'refund_per_bundle', 'total_refund', 'currency_id',
        'exchange_rate', 'notes'
    ];

    public function distributor()
    {
        return $this->belongsTo(Distributor::class);
    }

    public function workDay()
    {
        return $this->belongsTo(WorkDay::class);
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }
}
