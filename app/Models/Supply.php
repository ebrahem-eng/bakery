<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class Supply extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'admin_id',
        'work_day_id',
        'supplier_id',
        'category_id',
        'currency_id',
        'exchange_rate',
        'quantity',
        'unit_price',
        'total_cost',
        'paid_amount',
        'unloading_fee',
        'material_type_name',
        'boxes_count',
        'box_weight',
        'bags_count',
        'bag_weight',
        'molds_per_carton',
        'bag_type',
        'unloading_fee_payer',
        'unloading_fee_currency_id',
        'unloading_fee_exchange_rate',
        'notes',
        'paid_currency_id',
        'paid_exchange_rate',
        'due_date',
    ];

    protected $casts = [
        'due_date' => 'date',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontLogEmptyChanges()
            ->setDescriptionForEvent(fn (string $eventName) => "Supply was {$eventName}");
    }

    public function getUnpaidAmountAttribute(): float
    {
        $costInBase = Currency::convertAmount($this->total_cost ?? 0, $this->exchange_rate ?? 1);
        $totalPaidInBase = $this->getTotalPaidBase();

        $unpaidInBase = max(0, $costInBase - $totalPaidInBase);
        
        $baseToNativeRate = Currency::convertAmount(1, $this->exchange_rate ?? 1);
        if ($baseToNativeRate <= 0) return 0;
        
        $unpaidInNative = $unpaidInBase / $baseToNativeRate;

        return round($unpaidInNative, 2);
    }

    public function getTotalPaidBase(): float
    {
        $paidInBase = Currency::convertAmount($this->paid_amount ?? 0, $this->paid_exchange_rate ?? 1);
        
        $laterPaymentsBase = 0;
        foreach ($this->payments as $payment) {
            $laterPaymentsBase += Currency::convertAmount($payment->amount, $payment->exchange_rate);
        }

        return $paidInBase + $laterPaymentsBase;
    }

    public function getTotalPaidNativeAttribute(): float
    {
        $totalPaidInBase = $this->getTotalPaidBase();
        
        $baseToNativeRate = Currency::convertAmount(1, $this->exchange_rate ?? 1);
        if ($baseToNativeRate <= 0) return 0;
        
        $totalPaidInNative = $totalPaidInBase / $baseToNativeRate;

        return round($totalPaidInNative, 2);
    }

    public function getIsFullyPaidAttribute(): bool
    {
        return $this->unpaid_amount <= 0.05; // tolerance
    }

    public function paidCurrency()
    {
        return $this->belongsTo(Currency::class, 'paid_currency_id');
    }

    public function unloadingFeeCurrency()
    {
        return $this->belongsTo(Currency::class, 'unloading_fee_currency_id');
    }

    public function admin()
    {
        return $this->belongsTo(Admin::class);
    }

    public function workDay()
    {
        return $this->belongsTo(WorkDay::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }

    public function payments()
    {
        return $this->hasMany(SupplierPayment::class, 'supply_id');
    }
}
