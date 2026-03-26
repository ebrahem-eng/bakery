<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class Distributor extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = ['first_name', 'last_name', 'title', 'preferred_currency_id', 'notes'];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontLogEmptyChanges()
            ->setDescriptionForEvent(fn (string $eventName) => "Distributor was {$eventName}");
    }

    public function mobiles()
    {
        return $this->hasMany(DistributorMobile::class);
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class, 'preferred_currency_id');
    }

    public function distributions()
    {
        return $this->hasMany(Distribution::class);
    }

    public function returns()
    {
        return $this->hasMany(DistributorReturn::class);
    }

    public function transactions()
    {
        return $this->hasMany(DistributorTransaction::class);
    }
}
