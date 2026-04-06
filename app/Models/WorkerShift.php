<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class WorkerShift extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'worker_id',
        'work_day_id',
        'check_in',
        'check_out',
        'snapshot_daily_wage',
        'snapshot_currency_id',
        'snapshot_exchange_rate',
        'bundles_from_oven',
        'bundles_from_bakery',
        'bundles_received',
        'bundles_returned',
        'price_per_bundle',
        'cash_collected',
        'cash_currency_id',
        'cash_exchange_rate',
        'admin_id',
        'notes',
    ];

    protected $casts = [
        'check_in' => 'datetime',
        'check_out' => 'datetime',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontLogEmptyChanges()
            ->setDescriptionForEvent(fn (string $eventName) => "Worker shift was {$eventName}");
    }

    public function worker()
    {
        return $this->belongsTo(Worker::class);
    }

    public function workDay()
    {
        return $this->belongsTo(WorkDay::class);
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class, 'snapshot_currency_id');
    }

    public function admin()
    {
        return $this->belongsTo(Admin::class);
    }

    public function cashCurrency()
    {
        return $this->belongsTo(Currency::class, 'cash_currency_id');
    }

    // ── Computed Financial Attributes ──────────────────────────────

    /**
     * Expected cash based on bundles sold and price per bundle.
     */
    public function getExpectedCashAttribute()
    {
        $sold = $this->bundles_received - $this->bundles_returned;
        return max(0, $sold * ($this->price_per_bundle ?? 0));
    }

    /**
     * Cash collected converted to the system's base currency.
     */
    public function getCashCollectedBaseAttribute()
    {
        if (!$this->cash_collected) return 0;
        
        // Use snapshot rate and Currency helper
        return Currency::convertAmount($this->cash_collected, $this->cash_exchange_rate);
    }

    /**
     * The difference between actual collected (in base) and expected (in base).
     * Since price_per_bundle is usually in base currency, we compare directly.
     */
    public function getRemainingCashAttribute()
    {
        return $this->cash_collected_base - $this->expected_cash;
    }
}
