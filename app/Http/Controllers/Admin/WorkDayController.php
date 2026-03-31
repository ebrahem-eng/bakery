<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Consumption;
use App\Models\Currency;
use App\Models\WorkDay;
use Illuminate\Http\Request;

class WorkDayController extends Controller
{
    public function index()
    {
        $workDays = WorkDay::with('openedBy', 'closedBy')->orderBy('id', 'desc')->get();
        $activeWorkDay = WorkDay::where('status', 'active')->first();

        return view('Admin.WorkDays.index', compact('workDays', 'activeWorkDay'));
    }

    public function show($id)
    {
        $workDay = WorkDay::with([
            'supplies.currency',
            'workerShifts.worker',
            'workerShifts.currency',
            'workerTransactions.currency',
            'distributions.distributor',
            'distributions.currency',
            'distributorReturns.distributor',
            'distributorReturns.currency',
            'distributorTransactions.currency',
            'expenses.currency',
        ])->findOrFail($id);

        $stats = $this->getWorkDayStatistics($workDay);

        return view('Admin.WorkDays.close', array_merge(['workDay' => $workDay], $stats));
    }

    public function store(Request $request)
    {
        $active = WorkDay::where('status', 'active')->first();
        if ($active) {
            return redirect()->back()->with('error', __('You must close the currently active work day before starting a new one.'));
        }

        $workDay = WorkDay::create([
            'opened_by' => auth()->guard('admin')->id(),
            'status' => 'active',
            'is_holiday' => $request->has('is_holiday'),
            'holiday_reason' => $request->holiday_reason,
            'start_time' => now(),
        ]);

        return redirect()->route('admin.work_days.index')->with('success', __('New Work Day started successfully.'));
    }

    public function showCloseForm(WorkDay $workDay)
    {
        if ($workDay->status === 'closed') {
            return redirect()->route('admin.work_days.index')->with('error_message', __('Work day is already closed.'));
        }

        $workDay->load([
            'supplies.currency',
            'supplies.category',
            'workerShifts.worker',
            'workerShifts.currency',
            'workerTransactions.currency',
            'distributions.distributor',
            'distributions.currency',
            'distributorReturns.distributor',
            'distributorReturns.currency',
            'distributorTransactions.currency',
            'expenses.currency',
        ]);

        $stats = $this->getWorkDayStatistics($workDay);

        return view('Admin.WorkDays.close', array_merge(['workDay' => $workDay], $stats));
    }

    private function getWorkDayStatistics(WorkDay $workDay)
    {
        $defaultCurrency = Currency::where('is_default', true)->first();
        $currencyCode = $defaultCurrency->code ?? '';

        // ── Sales Statistics ──────────────────────────────────────────
        $totalSales = $workDay->distributions->sum(fn($d) => Currency::convertAmount($d->total_price, $d->exchange_rate));
        $totalRefunds = $workDay->distributorReturns->sum(fn($r) => Currency::convertAmount($r->total_refund, $r->exchange_rate));
        $totalPaymentsReceived = $workDay->distributorTransactions->sum(fn($t) => Currency::convertAmount($t->amount, $t->exchange_rate));
        $netSales = $totalSales - $totalRefunds;

        // ── Expense Breakdown ─────────────────────────────────────────
        $suppliesCost = $workDay->supplies
            ->sum(fn($s) => Currency::convertAmount($s->total_cost, $s->exchange_rate));
            
        $unloadingFees = $workDay->supplies
            ->filter(fn($s) => $s->unloading_fee_payer === 'bakery')
            ->reduce(function ($carry, $s) {
                return $carry + Currency::convertAmount($s->unloading_fee, $s->unloading_fee_exchange_rate);
            }, 0);
        
        // Use actual recorded transactions instead of theoretical shift wages
        $workerPayments = $workDay->workerTransactions
            ->whereIn('type', ['salary', 'wage', 'advance', 'allowance', 'bonus'])
            ->reduce(function($carry, $t) { 
                return $carry + Currency::convertAmount($t->amount, $t->exchange_rate); 
            }, 0);
            
        $workerDeductions = $workDay->workerTransactions
            ->where('type', 'deduction')
            ->reduce(function($carry, $t) { 
                return $carry + Currency::convertAmount($t->amount, $t->exchange_rate); 
            }, 0);
            
        $operationalExpenses = $workDay->expenses->sum(fn($e) => Currency::convertAmount($e->amount, $e->exchange_rate));

        $totalExpenses = $suppliesCost + $unloadingFees + $workerPayments - $workerDeductions + $operationalExpenses;
        $netDayBalance = $netSales - $totalExpenses;

        // ── Bundle Flow ───────────────────────────────────────────────
        $bundlesDistributed = $workDay->distributions->sum('bundle_count');
        $bundlesReturnedByDistributors = $workDay->distributorReturns->sum('bundle_count');
        $bundlesReceivedByShifts = $workDay->workerShifts->sum('bundles_received');
        $bundlesReturnedByShifts = $workDay->workerShifts->sum('bundles_returned');

        // Previous day carry-over
        $previousDay = WorkDay::where('status', 'closed')
            ->where('id', '<', $workDay->id)
            ->orderBy('id', 'desc')
            ->first();
        $previousCarryOverBundles = $previousDay ? $previousDay->carried_over_bundles : 0;

        // Calculated remaining = previous carry-over + returned by shifts - distributed + returned by distributors
        $calculatedRemainingBundles = $previousCarryOverBundles + $bundlesReturnedByShifts - $bundlesDistributed + $bundlesReturnedByDistributors;
        if ($calculatedRemainingBundles < 0) {
            $calculatedRemainingBundles = 0;
        }

        // ── Currencies for settlement form ────────────────────────────
        $currencies = Currency::all();

        // ── Cash collected from shifts ────────────────────────────────
        $totalCashFromShifts = $workDay->workerShifts->sum(function ($s) {
            return Currency::convertAmount($s->cash_collected, $s->cash_exchange_rate);
        });

        // ── Raw Material Categories for Consumption ──────────────────
        $materialCategories = Category::where('track_in_daily_close', true)
            ->where('is_active', true)
            ->withSum(['supplies as total_in' => function ($query) {
                // Global stock tracking
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
            'currencies' => $currencies,
            'materialCategories' => $materialCategories,
            'calculatedRemainingBundles' => $calculatedRemainingBundles,
            'totalCashFromShifts' => $totalCashFromShifts,
            // Sales
            'totalSales' => $totalSales,
            'totalRefunds' => $totalRefunds,
            'totalPaymentsReceived' => $totalPaymentsReceived,
            'netSales' => $netSales,
            // Expenses
            'suppliesCost' => $suppliesCost,
            'unloadingFees' => $unloadingFees,
            'workerPayments' => $workerPayments,
            'workerDeductions' => $workerDeductions,
            'operationalExpenses' => $operationalExpenses,
            'totalExpenses' => $totalExpenses,
            'netDayBalance' => $netDayBalance,
            // Bundles
            'bundlesDistributed' => $bundlesDistributed,
            'bundlesReturnedByDistributors' => $bundlesReturnedByDistributors,
            'bundlesReceivedByShifts' => $bundlesReceivedByShifts,
            'bundlesReturnedByShifts' => $bundlesReturnedByShifts,
            'previousCarryOverBundles' => $previousCarryOverBundles,
            'activeShifts' => $workDay->workerShifts->whereNull('check_out'),
        ];
    }

    public function close(Request $request, WorkDay $workDay)
    {
        $request->validate([
            'carried_over_money' => 'required|numeric|min:0',
            'carried_over_currency_id' => 'nullable|exists:currencies,id',
            'carried_over_exchange_rate' => 'nullable|numeric|min:0',
            'total_expenses_at_close' => 'required|numeric',
            'total_sales_at_close' => 'required|numeric',
            'consumptions' => 'nullable|array',
            'consumptions.*' => 'nullable|numeric|min:0',
        ]);

        // Validate consumptions against available stock
        if ($request->consumptions) {
            foreach ($request->consumptions as $categoryId => $quantity) {
                if ($quantity > 0) {
                    $cat = Category::withSum('supplies', 'quantity')
                        ->withSum('consumptions', 'quantity')
                        ->find($categoryId);
                        
                    if ($cat) {
                        $available = ($cat->supplies_sum_quantity ?? 0) - ($cat->consumptions_sum_quantity ?? 0);
                        // Using a small tolerance to handle floating point errors
                        if ($quantity > ($available + 0.05)) {
                            return back()->withInput()->with('error_message', __("Recorded consumption for category :name (:quantity) exceeds strictly available stock (:available).", [
                                'name' => __($cat->name),
                                'quantity' => round($quantity, 2),
                                'available' => round($available, 2)
                            ]));
                        }
                    }
                }
            }
        }

        // Auto-calculate bundles from shift data
        $workDay->load(['workerShifts', 'distributions', 'distributorReturns']);

        $bundlesReturnedByShifts = $workDay->workerShifts->sum('bundles_returned');
        $bundlesDistributed = $workDay->distributions->sum('bundle_count');
        $bundlesReturnedByDistributors = $workDay->distributorReturns->sum('bundle_count');

        $previousDay = WorkDay::where('status', 'closed')
            ->where('id', '<', $workDay->id)
            ->orderBy('id', 'desc')
            ->first();
        $previousCarryOverBundles = $previousDay ? $previousDay->carried_over_bundles : 0;

        $calculatedBundles = $previousCarryOverBundles + $bundlesReturnedByShifts - $bundlesDistributed + $bundlesReturnedByDistributors;
        if ($calculatedBundles < 0) {
            $calculatedBundles = 0;
        }

        // Resolve exchange rate
        $exchangeRate = 1;
        if ($request->carried_over_currency_id) {
            $currency = Currency::find($request->carried_over_currency_id);
            if ($currency && ! $currency->is_default) {
                $exchangeRate = $request->carried_over_exchange_rate ?? $currency->exchange_rate;
            }
        }

        $endTime = now();

        $workDay->update([
            'status' => 'closed',
            'end_time' => $endTime,
            'closed_by' => auth()->guard('admin')->id(),
            'total_expenses_at_close' => Currency::revertToBase($request->total_expenses_at_close),
            'total_sales_at_close' => Currency::revertToBase($request->total_sales_at_close),
            'carried_over_bundles' => $calculatedBundles,
            'carried_over_money' => $request->carried_over_money,
            'carried_over_currency_id' => $request->carried_over_currency_id,
            'carried_over_exchange_rate' => $exchangeRate,
        ]);

        // Auto-close any active shifts
        $workDay->workerShifts()->whereNull('check_out')->update([
            'check_out' => $endTime
        ]);

        // Store Consumption Records
        if ($request->consumptions) {
            foreach ($request->consumptions as $categoryId => $quantity) {
                if ($quantity > 0) {
                    Consumption::create([
                        'work_day_id' => $workDay->id,
                        'category_id' => $categoryId,
                        'quantity' => $quantity,
                    ]);
                }
            }
        }

        return redirect()->route('admin.work_days.index')->with('success_message', __('Work Day closed successfully. All operations frozen.'));
    }
}
