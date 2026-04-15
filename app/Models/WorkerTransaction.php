<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class WorkerTransaction extends Model
{
    use HasFactory, LogsActivity, \App\Traits\SyncsWithActiveWorkDay;

    const TYPE_ADVANCE = 'advance';
    const TYPE_ALLOWANCE = 'allowance';
    const TYPE_DEDUCTION = 'deduction';
    const TYPE_SALARY = 'salary';
    const TYPE_WAGE = 'wage';
    const TYPE_BONUS = 'bonus';

    protected $fillable = [
        'worker_id',
        'work_day_id',
        'type',
        'amount',
        'currency_id',
        'exchange_rate',
        'admin_id',
        'notes',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontLogEmptyChanges()
            ->setDescriptionForEvent(fn (string $eventName) => "Worker transaction was {$eventName}");
    }

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

    public function admin()
    {
        return $this->belongsTo(Admin::class);
    }
}
