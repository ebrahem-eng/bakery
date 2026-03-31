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
            ->setDescriptionForEvent(fn (string $eventName) => "Currency was {$eventName}");
    }

    /**
     * Get the default system currency exchange rate.
     */
    public static function getDefaultRate(): float
    {
        $rate = self::where('is_default', true)->value('exchange_rate');
        
        if (empty($rate) || (float)$rate <= 0) {
            return 1.0; // Fallback to 1 to prevent inflation or division by zero
        }

        return (float) $rate;
    }

    /**
     * Get a raw DB expression for a single column with bidirectional conversion.
     */
    public static function getSelectRaw(string $column = 'amount', string $rateColumn = 'exchange_rate')
    {
        $defaultRate = self::getDefaultRate();
        return \Illuminate\Support\Facades\DB::raw("({$column} * COALESCE({$rateColumn}, 1)) / {$defaultRate}");
    }

    /**
     * Convert an in-memory amount to the default currency dynamically.
     */
    public static function convertAmount(float $amount, ?float $rate = null): float
    {
        $defaultRate = self::getDefaultRate();
        return ($amount * ($rate ?? 1)) / $defaultRate;
    }

    /**
     * Revert a displayed value back to its absolute system base value for database storage.
     */
    public static function revertToBase(float $displayAmount): float
    {
        $defaultRate = self::getDefaultRate();
        return $displayAmount * $defaultRate;
    }
}
