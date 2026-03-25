<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\WorkDay;
use App\Models\Consumption;

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
        $workDay = WorkDay::with(['distributions', 'supplies', 'expenses', 'workerShifts', 'workerTransactions', 'distributorReturns'])->findOrFail($id);
        
        $totalSales = $workDay->distributions->sum('total_price') - $workDay->distributorReturns->sum('total_refund');
        
        $totalExpenses = 0;
        $totalExpenses += $workDay->supplies->sum('total_cost');
        $totalExpenses += $workDay->supplies->where('unloading_fee_payer', 'bakery')->sum(function($supply) {
            return $supply->unloading_fee * ($supply->unloading_fee_exchange_rate ?? 1);
        });
        $totalExpenses += $workDay->workerShifts->sum('snapshot_daily_wage');
        $totalExpenses += $workDay->workerTransactions->where('type', 'allowance')->sum('amount');
        $totalExpenses -= $workDay->workerTransactions->where('type', 'deduction')->sum('amount');
        $totalExpenses += $workDay->expenses->sum('amount');

        $defaultCurrency = \App\Models\Currency::where('is_default', true)->first();
        return view('Admin.WorkDays.close', compact('workDay', 'totalSales', 'totalExpenses', 'defaultCurrency'));
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
            'start_time' => now()
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
            'workerShifts.worker',
            'workerShifts.currency',
            'workerTransactions.currency',
            'distributions.distributor',
            'distributions.currency',
            'distributorReturns.distributor',
            'distributorReturns.currency',
            'distributorTransactions.currency',
            'expenses.currency'
        ]);

        $defaultCurrency = \App\Models\Currency::where('is_default', true)->first();
        $currencyCode = $defaultCurrency->code ?? '';

        // ── Sales Statistics ──────────────────────────────────────────
        $totalSales = $workDay->distributions->sum('total_price');
        $totalRefunds = $workDay->distributorReturns->sum('total_refund');
        $totalPaymentsReceived = $workDay->distributorTransactions->sum('amount');
        $netSales = $totalSales - $totalRefunds;

        // ── Expense Breakdown ─────────────────────────────────────────
        $suppliesCost = $workDay->supplies->sum('total_cost');
        $unloadingFees = $workDay->supplies->where('unloading_fee_payer', 'bakery')->sum(function($s) {
            return $s->unloading_fee * ($s->unloading_fee_exchange_rate ?? 1);
        });
        $shiftWages = $workDay->workerShifts->sum('snapshot_daily_wage');
        $workerAllowances = $workDay->workerTransactions->where('type', 'allowance')->sum('amount');
        $workerAdvances = $workDay->workerTransactions->where('type', 'advance')->sum('amount');
        $workerDeductions = $workDay->workerTransactions->where('type', 'deduction')->sum('amount');
        $operationalExpenses = $workDay->expenses->sum('amount');
        
        $totalExpenses = $suppliesCost + $unloadingFees + $shiftWages + $workerAllowances - $workerDeductions + $operationalExpenses;
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
        if ($calculatedRemainingBundles < 0) $calculatedRemainingBundles = 0;

        // ── Currencies for settlement form ────────────────────────────
        $currencies = \App\Models\Currency::all();

        // ── Cash collected from shifts ────────────────────────────────
        $totalCashFromShifts = $workDay->workerShifts->sum(function($s) {
            return $s->cash_collected * $s->cash_exchange_rate;
        });

        // ── Raw Material Categories for Consumption ──────────────────
        $materialNames = ['طحين', 'مازوت', 'خميرة', 'ملح'];
        $materialCategories = \App\Models\Category::whereIn('name', $materialNames)
            ->withSum('supplies as total_in', 'quantity')
            ->withSum('consumptions as total_out', 'quantity')
            ->get()
            ->map(function($cat) {
                $cat->available = ($cat->total_in ?? 0) - ($cat->total_out ?? 0);
                return $cat;
            });

        return view('Admin.WorkDays.close', compact(
            'workDay', 'defaultCurrency', 'currencyCode', 'currencies', 'materialCategories', 'calculatedRemainingBundles', 'totalCashFromShifts',
            // Sales
            'totalSales', 'totalRefunds', 'totalPaymentsReceived', 'netSales',
            // Expenses
            'suppliesCost', 'unloadingFees', 'shiftWages', 'workerAllowances',
            'workerAdvances', 'workerDeductions', 'operationalExpenses', 'totalExpenses',
            'netDayBalance',
            // Bundles
            'bundlesDistributed', 'bundlesReturnedByDistributors',
            'bundlesReceivedByShifts', 'bundlesReturnedByShifts',
            'previousCarryOverBundles', 'calculatedRemainingBundles',
            // Cash
            'totalCashFromShifts'
        ));
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
        if ($calculatedBundles < 0) $calculatedBundles = 0;

        // Resolve exchange rate
        $exchangeRate = 1;
        if ($request->carried_over_currency_id) {
            $currency = \App\Models\Currency::find($request->carried_over_currency_id);
            if ($currency && !$currency->is_default) {
                $exchangeRate = $request->carried_over_exchange_rate ?? $currency->exchange_rate;
            }
        }

        $workDay->update([
            'status' => 'closed',
            'end_time' => now(),
            'closed_by' => auth()->guard('admin')->id(),
            'total_expenses_at_close' => $request->total_expenses_at_close,
            'total_sales_at_close' => $request->total_sales_at_close,
            'carried_over_bundles' => $calculatedBundles,
            'carried_over_money' => $request->carried_over_money,
            'carried_over_currency_id' => $request->carried_over_currency_id,
            'carried_over_exchange_rate' => $exchangeRate,
        ]);

        // Store Consumption Records
        if ($request->consumptions) {
            foreach ($request->consumptions as $categoryId => $quantity) {
                if ($quantity > 0) {
                    \App\Models\Consumption::create([
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
