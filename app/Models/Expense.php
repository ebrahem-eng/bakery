<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class Expense extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'work_day_id', 'admin_id', 'category', 'title', 'amount', 
        'currency_id', 'exchange_rate', 'notes'
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontLogEmptyChanges()
            ->setDescriptionForEvent(fn(string $eventName) => "Expense was {$eventName}");
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
