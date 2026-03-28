<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class Worker extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = ['first_name', 'last_name', 'title', 'daily_wage', 'currency_id', 'exchange_rate'];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontLogEmptyChanges()
            ->setDescriptionForEvent(fn (string $eventName) => "Worker was {$eventName}");
    }

    public function mobiles()
    {
        return $this->hasMany(WorkerMobile::class);
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }

    public function shifts()
    {
        return $this->hasMany(WorkerShift::class);
    }

    public function transactions()
    {
        return $this->hasMany(WorkerTransaction::class);
    }

    public function attendances()
    {
        return $this->hasMany(WorkerAttendance::class);
    }
}
