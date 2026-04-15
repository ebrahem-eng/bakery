<?php

namespace App\Models;
use App\Models\Category;
use App\Models\Currency;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class WorkDay extends Model
{
    use LogsActivity;

    protected $fillable = [
        'start_time', 'end_time', 'status', 'is_holiday', 'holiday_reason',
        'opened_by', 'closed_by', 'total_expenses_at_close', 'total_sales_at_close',
        'carried_over_bundles', 'carried_over_money',
        'carried_over_currency_id', 'carried_over_exchange_rate',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'is_holiday' => 'boolean',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontLogEmptyChanges()
            ->setDescriptionForEvent(fn (string $eventName) => "Work day was {$eventName}");
    }

    public function openedBy()
    {
        return $this->belongsTo(Admin::class, 'opened_by');
    }

    public function closedBy()
    {
        return $this->belongsTo(Admin::class, 'closed_by');
    }

    public function workerShifts()
    {
        return $this->hasMany(WorkerShift::class);
    }

    public function workerAttendances()
    {
        return $this->hasMany(WorkerAttendance::class);
    }

    public function workerTransactions()
    {
        return $this->hasMany(WorkerTransaction::class);
    }

    public function distributions()
    {
        return $this->hasMany(Distribution::class);
    }

    public function distributorReturns()
    {
        return $this->hasMany(DistributorReturn::class);
    }

    public function distributorTransactions()
    {
        return $this->hasMany(DistributorTransaction::class);
    }

    public function expenses()
    {
        return $this->hasMany(Expense::class);
    }

    public function supplies()
    {
        return $this->hasMany(Supply::class);
    }

    public function carriedOverCurrency()
    {
        return $this->belongsTo(Currency::class, 'carried_over_currency_id');
    }

    public function consumptions()
    {
        return $this->hasMany(Consumption::class);
    }

    public function supplierPayments()
    {
        return $this->hasMany(SupplierPayment::class);
    }

    /**
     * Get comprehensive statistics for the work day.
     * Synchronized between Dashboard Live Stats and Daily Close Settlement.
     */
    public function getStatistics()
    {
        $defaultCurrency = Currency::where('is_default', true)->first();
        $currencyCode = $defaultCurrency->code ?? 'SYP';

        // ── Sales Statistics ──────────────────────────────────────────
        // wholesaleSales (Distributors)
        $wholesaleSales = $this->distributions->reduce(fn($carry, $d) => $carry + Currency::convertAmount($d->total_price, $d->exchange_rate), 0);
        // retailSales (Worker Shifts)
        $retailSales = $this->workerShifts->reduce(fn($carry, $s) => $carry + Currency::convertAmount($s->cash_collected, $s->cash_exchange_rate), 0);
        
        $settlementCashBase = 0;
        if ($this->status === 'closed') {
            $settlementCashBase = Currency::convertAmount($this->carried_over_money, $this->carried_over_exchange_rate);
        }

        // Use settlement cash if closed, otherwise use reported retail sales as estimate
        $cashRevenue = ($this->status === 'closed') ? $settlementCashBase : $retailSales;

        $totalSales = $wholesaleSales + $cashRevenue;
        $totalRefunds = $this->distributorReturns->reduce(fn($carry, $r) => $carry + Currency::convertAmount($r->total_refund, $r->exchange_rate), 0);
        
        $explicitPayments = $this->distributorTransactions->where('type', 'payment')->reduce(fn($carry, $t) => $carry + Currency::convertAmount($t->amount, $t->exchange_rate), 0);
        $downPayments = $this->distributions->reduce(fn($carry, $d) => $carry + Currency::convertAmount($d->amount_paid, $d->exchange_rate), 0);
        $totalPaymentsReceived = $explicitPayments + $downPayments + $cashRevenue;
        
        $netSales = $totalSales - $totalRefunds;

        // ── Expense Breakdown ─────────────────────────────────────────
        $suppliesCost = $this->supplies->reduce(function ($carry, $s) {
            return $carry + Currency::convertAmount($s->total_cost, $s->exchange_rate);
        }, 0);

        $unloadingFees = $this->supplies->filter(fn($s) => $s->unloading_fee_payer === 'bakery')
            ->reduce(fn($carry, $s) => $carry + Currency::convertAmount($s->unloading_fee, $s->unloading_fee_exchange_rate), 0);
        
        // Manual worker payments (Cash-based reporting for expenses)
        $workerPayouts = $this->workerTransactions
            ->whereIn('type', ['salary', 'wage', 'advance', 'allowance', 'bonus'])
            ->reduce(fn($carry, $t) => $carry + Currency::convertAmount($t->amount, $t->exchange_rate), 0);
            
        $workerDeductions = $this->workerTransactions
            ->where('type', 'deduction')
            ->reduce(fn($carry, $t) => $carry + Currency::convertAmount($t->amount, $t->exchange_rate), 0);
            
        $operationalExpenses = $this->expenses->reduce(fn($carry, $e) => $carry + Currency::convertAmount($e->amount, $e->exchange_rate), 0);
        
        $supplierPayments = $this->supplierPayments->reduce(fn($carry, $sp) => $carry + Currency::convertAmount($sp->amount, $sp->exchange_rate), 0);

        $totalExpenses = $supplierPayments + $unloadingFees + $workerPayouts - $workerDeductions + $operationalExpenses;
        $netDayBalance = $netSales - $totalExpenses;

        // ── Bundle Flow ───────────────────────────────────────────────
        $bundlesDistributed = $this->distributions->sum('bundle_count');
        $bundlesReturnedByDistributors = $this->distributorReturns->sum('bundle_count');
        
        $bundlesFromOvenSum = $this->workerShifts->sum('bundles_from_oven');
        $bundlesFromBakerySum = $this->workerShifts->sum('bundles_from_bakery');
        
        // Split by shift status: closed = confirmed, open = in-transit
        $closedShifts = $this->workerShifts->whereNotNull('check_out');
        $openShifts = $this->workerShifts->whereNull('check_out');

        // Closed shifts — bundles confirmed sold & returned
        $bundlesReceivedByClosedShifts = $closedShifts->sum('bundles_received');
        $bundlesReturnedByClosedShifts = $closedShifts->sum('bundles_returned');
        $bundlesSoldFromShifts = $bundlesReceivedByClosedShifts - $bundlesReturnedByClosedShifts;

        // Open shifts — bundles delivered but NOT yet sold (in-transit with workers)
        $bundlesDeliveredToActiveShifts = $openShifts->sum('bundles_received');

        // Totals (all shifts, for reference)
        $bundlesReceivedByShifts = $this->workerShifts->sum('bundles_received');
        $bundlesReturnedByShiftsTotal = $this->workerShifts->sum('bundles_returned');

        $breadExpenses = $this->expenses->where('category', 'bread')->sum('quantity');

        // Previous day carry-over
        $previousDay = self::where('status', 'closed')->where('id', '<', $this->id)->orderBy('id', 'desc')->first();
        $previousCarryOverBundles = $previousDay ? $previousDay->carried_over_bundles : 0;

        // Remaining = what's physically in the bakery (excludes both sold AND in-transit)
        $calculatedRemainingBundles = max(0, $previousCarryOverBundles + $bundlesFromOvenSum - $bundlesSoldFromShifts - $bundlesDeliveredToActiveShifts - $bundlesDistributed + $bundlesReturnedByDistributors - $breadExpenses);

        // ── Cash collected from shifts ────────────────────────────────
        $totalCashFromShifts = $this->workerShifts->reduce(function ($carry, $s) {
            return $carry + Currency::convertAmount($s->cash_collected, $s->cash_exchange_rate);
        }, 0);

        // ── Raw Material Categories for Consumption ──────────────────
        $materialCategories = Category::where('track_in_daily_close', true)
            ->where('is_active', true)
            ->withSum(['supplies as total_in' => function ($query) {
                // Relevant for active day stock
            }], 'quantity')
            ->withSum('consumptions as total_out', 'quantity')
            ->get()
            ->map(function ($cat) {
                $cat->available = ($cat->total_in ?? 0) - ($cat->total_out ?? 0);
                return $cat;
            });

        return [
            'defaultCurrency' => $defaultCurrency,
            'currencyCode' => $currencyCode,
            'currencies' => Currency::all(),
            'materialCategories' => $materialCategories,
            'calculatedRemainingBundles' => $calculatedRemainingBundles,
            'totalCashFromShifts' => $totalCashFromShifts,
            // Sales Metrics
            'totalSales' => $totalSales,
            'wholesaleSales' => $wholesaleSales,
            'retailSales' => $retailSales,
            'totalRefunds' => $totalRefunds,
            'totalPaymentsReceived' => $totalPaymentsReceived,
            'netSales' => $netSales,
            // Expenses Metrics
            'suppliesCost' => $suppliesCost,
            'unloadingFees' => $unloadingFees,
            'workerPayments' => $workerPayouts,
            'workerDeductions' => $workerDeductions,
            'operationalExpenses' => $operationalExpenses,
            'supplierPayments' => $supplierPayments,
            'totalExpenses' => $totalExpenses,
            'netDayBalance' => $netDayBalance,
            // Bundle Counts
            'bundlesDistributed' => $bundlesDistributed,
            'bundlesReturnedByDistributors' => $bundlesReturnedByDistributors,
            'bundlesReceivedByShifts' => $bundlesReceivedByShifts,
            'bundlesReturnedByShifts' => $bundlesReturnedByShiftsTotal,
            'bundlesSoldFromShifts' => $bundlesSoldFromShifts,
            'bundlesSold' => ($bundlesDistributed - $bundlesReturnedByDistributors) + $bundlesSoldFromShifts,
            'bundlesDeliveredToActiveShifts' => $bundlesDeliveredToActiveShifts,
            'bundlesReturnedByClosedShifts' => $bundlesReturnedByClosedShifts,
            'bundlesFromOvenSum' => $bundlesFromOvenSum,
            'breadExpenses' => $breadExpenses,
            'previousCarryOverBundles' => $previousCarryOverBundles,
            'activeShifts' => $this->workerShifts->whereNull('check_out'),
            'settlementCashBase' => $settlementCashBase,
        ];
    }
}

