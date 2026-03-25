<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class Consumption extends Model
{
    use LogsActivity;

    protected $table = 'work_day_consumptions';

    protected $fillable = [
        'work_day_id',
        'category_id',
        'quantity',
        'notes',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontLogEmptyChanges()
            ->setDescriptionForEvent(fn(string $eventName) => "Consumption was {$eventName}");
    }

    public function workDay()
    {
        return $this->belongsTo(WorkDay::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
