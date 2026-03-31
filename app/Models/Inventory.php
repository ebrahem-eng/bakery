<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class Inventory extends Model
{
    use LogsActivity;

    protected $fillable = [
        'admin_id', 'work_day_id', 'status', 'notes', 'total_value'
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontLogEmptyChanges()
            ->setDescriptionForEvent(fn (string $eventName) => "Warehouse stocktake was {$eventName}");
    }

    public function items()
    {
        return $this->hasMany(InventoryItem::class);
    }

    public function admin()
    {
        return $this->belongsTo(Admin::class);
    }

    public function workDay()
    {
        return $this->belongsTo(WorkDay::class);
    }
}
