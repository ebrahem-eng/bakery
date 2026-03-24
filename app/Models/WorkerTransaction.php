<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkerTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'worker_id',
        'work_day_id',
        'type',
        'amount',
        'currency_id',
        'exchange_rate',
        'notes'
    ];

    public function worker()
    {
        return $this->belongsTo(Worker::class);
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
