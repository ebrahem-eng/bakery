<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class SupplierPayment extends Model
{
    use LogsActivity;

    protected $fillable = [
        'supply_id', 'work_day_id', 'admin_id',
        'amount', 'currency_id', 'exchange_rate',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontLogEmptyChanges()
            ->setDescriptionForEvent(fn (string $eventName) => "Supplier payment was {$eventName}");
    }

    public function supply()
    {
        return $this->belongsTo(Supply::class);
    }

    public function workDay()
    {
        return $this->belongsTo(WorkDay::class);
    }

    public function admin()
    {
        return $this->belongsTo(Admin::class);
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }
}
