<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkerShift extends Model
{
    use HasFactory;

    protected $fillable = [
        'worker_id',
        'work_day_id',
        'check_in',
        'check_out',
        'snapshot_daily_wage',
        'snapshot_currency_id',
        'snapshot_exchange_rate',
        'notes'
    ];

    protected $casts = [
        'check_in' => 'datetime',
        'check_out' => 'datetime',
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
        return $this->belongsTo(Currency::class, 'snapshot_currency_id');
    }
}
