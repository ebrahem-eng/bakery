<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class WorkDay extends Model
{
    use LogsActivity;

    protected $fillable = [
        'start_time', 'end_time', 'status', 'is_holiday', 'holiday_reason',
        'opened_by', 'closed_by', 'total_expenses_at_close', 'total_sales_at_close',
        'carried_over_bundles', 'carried_over_money',
        'carried_over_currency_id', 'carried_over_exchange_rate',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'is_holiday' => 'boolean',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontLogEmptyChanges()
            ->setDescriptionForEvent(fn (string $eventName) => "Work day was {$eventName}");
    }

    public function openedBy()
    {
        return $this->belongsTo(Admin::class, 'opened_by');
    }

    public function closedBy()
    {
        return $this->belongsTo(Admin::class, 'closed_by');
    }

    public function workerShifts()
    {
        return $this->hasMany(WorkerShift::class);
    }

    public function workerAttendances()
    {
        return $this->hasMany(WorkerAttendance::class);
    }

    public function workerTransactions()
    {
        return $this->hasMany(WorkerTransaction::class);
    }

    public function distributions()
    {
        return $this->hasMany(Distribution::class);
    }

    public function distributorReturns()
    {
        return $this->hasMany(DistributorReturn::class);
    }

    public function distributorTransactions()
    {
        return $this->hasMany(DistributorTransaction::class);
    }

    public function expenses()
    {
        return $this->hasMany(Expense::class);
    }

    public function supplies()
    {
        return $this->hasMany(Supply::class);
    }

    public function carriedOverCurrency()
    {
        return $this->belongsTo(Currency::class, 'carried_over_currency_id');
    }

    public function consumptions()
    {
        return $this->hasMany(Consumption::class);
    }
}
