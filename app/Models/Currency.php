<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class Currency extends Model
{
    use LogsActivity;

    protected $fillable = ['name', 'code', 'exchange_rate', 'is_default'];
    
    protected $casts = [
        'is_default' => 'boolean',
        'exchange_rate' => 'decimal:2',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontLogEmptyChanges()
            ->setDescriptionForEvent(fn(string $eventName) => "Currency was {$eventName}");
    }
}
