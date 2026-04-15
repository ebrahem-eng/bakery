<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class DistributorTransaction extends Model
{
    use HasFactory, LogsActivity, \App\Traits\SyncsWithActiveWorkDay;

    protected $fillable = [
        'distributor_id', 'work_day_id', 'type',
        'amount', 'currency_id', 'exchange_rate', 'notes', 'created_by',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontLogEmptyChanges()
            ->setDescriptionForEvent(fn (string $eventName) => "Distributor transaction was {$eventName}");
    }

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

    public function createdBy()
    {
        return $this->belongsTo(Admin::class, 'created_by');
    }
}
