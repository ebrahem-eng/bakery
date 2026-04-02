<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class Supplier extends Model
{
    use LogsActivity;

    protected $fillable = ['first_name', 'last_name', 'title'];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontLogEmptyChanges()
            ->setDescriptionForEvent(fn (string $eventName) => "Supplier was {$eventName}");
    }

    public function mobiles()
    {
        return $this->hasMany(SupplierMobile::class);
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class);
    }

    public function supplies()
    {
        return $this->hasMany(Supply::class);
    }

    public function payments()
    {
        return $this->hasManyThrough(SupplierPayment::class, Supply::class);
    }
}
