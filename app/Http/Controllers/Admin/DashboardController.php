<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Currency;
use App\Models\Distribution;
use App\Models\Distributor;
use App\Models\DistributorReturn;
use App\Models\DistributorTransaction;
use App\Models\Expense;
use App\Models\Supplier;
use App\Models\Supply;
use App\Models\WorkDay;
use App\Models\Worker;
use App\Models\WorkerShift;
use App\Models\WorkerTransaction;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $period = $request->get('period', 'today');
        
        if ($request->filled('date_from') && $request->filled('date_to')) {
            $start = \Carbon\Carbon::parse($request->date_from)->startOfDay();
            $end = \Carbon\Carbon::parse($request->date_to)->endOfDay();
            $period = 'custom';
        } else {
            $dateRange = $this->getDateRange($period);
            $start = $dateRange['start'];
            $end = $dateRange['end'];
        }

        // ── Active work day (always shown) ────────────────────────────
        $activeWorkDay = WorkDay::where('status', 'active')
            ->with(['distributions', 'supplies', 'supplierPayments', 'expenses', 'workerShifts', 'workerTransactions', 'distributorReturns'])
            ->first();

        $defaultCurrency = Currency::where('is_default', true)->first();
        $currencyCode = $defaultCurrency->code ?? 'SYP';

        // ── Previous carry-over (Bundles only — for inventory tracking)
        $previousWorkDay = WorkDay::where('status', 'closed')
            ->orderBy('id', 'desc')
            ->first();
        
        $startingBundles = $previousWorkDay ? $previousWorkDay->carried_over_bundles : 0;

        // ── Period-filtered work day IDs ──────────────────────────────
        $periodWorkDayIds = WorkDay::where('status', 'closed')
            ->when($start, fn ($q) => $q->where('start_time', '>=', $start))
            ->when($end, fn ($q) => $q->where('start_time', '<=', $end))
            ->pluck('id');

        // Include active work day in "today" filter
        if ($period === 'today' && $activeWorkDay) {
            $periodWorkDayIds = $periodWorkDayIds->push($activeWorkDay->id);
        }

        // ── Revenue Metrics ───────────────────────────────────────────
        $totalDistributionsInitial = Distribution::whereIn('work_day_id', $periodWorkDayIds)->sum(Currency::getSelectRaw('amount_paid'));
        $totalDistributionsSettlements = DistributorTransaction::whereIn('work_day_id', $periodWorkDayIds)->sum(Currency::getSelectRaw('amount'));
        $totalDistributions = $totalDistributionsInitial + $totalDistributionsSettlements;
        $totalRefunds = DistributorReturn::whereIn('work_day_id', $periodWorkDayIds)->sum(Currency::getSelectRaw('total_refund'));

        $allWorkDays = WorkDay::whereIn('id', $periodWorkDayIds)->get();
        $totalSettlementCash = 0;
        $totalActiveShiftCash = 0;

        foreach ($allWorkDays as $wd) {
            if ($wd->status === 'closed') {
                $totalSettlementCash += Currency::convertAmount($wd->carried_over_money, $wd->carried_over_exchange_rate);
            } else {
                $totalActiveShiftCash += \App\Models\WorkerShift::where('work_day_id', $wd->id)->sum(Currency::getSelectRaw('cash_collected', 'cash_exchange_rate'));
            }
        }

        $totalRevenue = $totalDistributions + $totalActiveShiftCash + $totalSettlementCash - $totalRefunds;
        $totalPaymentsReceived = DistributorTransaction::whereIn('work_day_id', $periodWorkDayIds)->sum(Currency::getSelectRaw('amount')) + $totalActiveShiftCash + $totalSettlementCash;

        // ── Expense Metrics ───────────────────────────────────────────
        $suppliesCost = Supply::whereIn('work_day_id', $periodWorkDayIds)->sum(Currency::getSelectRaw('total_cost'));
        $unloadingFees = Supply::whereIn('work_day_id', $periodWorkDayIds)->sum(Currency::getSelectRaw('unloading_fee', 'unloading_fee_exchange_rate'));
        
        // Manual worker payments (Cash-based reporting for expenses as requested)
        $workerAllowances = WorkerTransaction::whereIn('work_day_id', $periodWorkDayIds)->where('type', 'allowance')->sum(Currency::getSelectRaw('amount'));
        $workerAdvances = WorkerTransaction::whereIn('work_day_id', $periodWorkDayIds)->where('type', 'advance')->sum(Currency::getSelectRaw('amount'));
        $workerSalaries = WorkerTransaction::whereIn('work_day_id', $periodWorkDayIds)->whereIn('type', ['salary', 'wage', 'bonus'])->sum(Currency::getSelectRaw('amount'));
        $workerDeductions = WorkerTransaction::whereIn('work_day_id', $periodWorkDayIds)->where('type', 'deduction')->sum(Currency::getSelectRaw('amount'));
        
        $operationalExpenses = Expense::whereIn('work_day_id', $periodWorkDayIds)->sum(Currency::getSelectRaw('amount'));
        
        $totalSupplierPayments = \App\Models\SupplierPayment::whereIn('work_day_id', $periodWorkDayIds)->sum(Currency::getSelectRaw('amount', 'exchange_rate'));

        $totalExpenses = $suppliesCost + $unloadingFees + $workerAllowances + $workerAdvances + $workerSalaries - $workerDeductions + $operationalExpenses;
        $netProfit = $totalRevenue - $totalExpenses;
        $profitMargin = $totalRevenue > 0 ? round(($netProfit / $totalRevenue) * 100, 1) : 0;

        // ── Bundle metrics ────────────────────────────────────────────
        $distributorBundlesSold = Distribution::whereIn('work_day_id', $periodWorkDayIds)->sum('bundle_count');
        $distributorBundlesReturned = DistributorReturn::whereIn('work_day_id', $periodWorkDayIds)->sum('bundle_count');
        
        $shiftBundlesReceived = \App\Models\WorkerShift::whereIn('work_day_id', $periodWorkDayIds)->sum('bundles_received');
        $shiftBundlesReturned = \App\Models\WorkerShift::whereIn('work_day_id', $periodWorkDayIds)->sum('bundles_returned');
        $shiftBundlesSold = $shiftBundlesReceived - $shiftBundlesReturned;

        $netBundlesSold = ($distributorBundlesSold - $distributorBundlesReturned) + $shiftBundlesSold;

        // ── Counts ────────────────────────────────────────────────────
        $workDaysCount = $periodWorkDayIds->count();
        $totalWorkers = Worker::count();
        $totalDistributors = Distributor::count();

        // ── Expense Breakdown ─────────────────────────────────────────
        $expenseBreakdown = [
            ['label' => __('Raw Materials'), 'value' => $suppliesCost, 'color' => '#f59e0b'],
            ['label' => __('Freight & Unloading'), 'value' => $unloadingFees, 'color' => '#ef4444'],
            ['label' => __('Allowances & Advances'), 'value' => $workerAllowances + $workerAdvances, 'color' => '#8b5cf6'],
            ['label' => __('Deductions'), 'value' => $workerDeductions, 'color' => '#10b981'],
            ['label' => __('Operations'), 'value' => $operationalExpenses, 'color' => '#6366f1'],
        ];

        // ── Distributor Outstanding Balances ──────────────────────────
        $distributorBalances = Distributor::select('distributors.*')
            ->withSum('distributions as total_billed', Currency::getSelectRaw('total_price'))
            ->withSum('transactions as total_paid', Currency::getSelectRaw('amount'))
            ->withSum('returns as total_refunded', Currency::getSelectRaw('total_refund'))
            ->get()
            ->map(function ($d) {
                $d->outstanding = ($d->total_billed ?? 0) - ($d->total_paid ?? 0) - ($d->total_refunded ?? 0);

                return $d;
            })
            ->sortByDesc('outstanding')
            ->values();

        // ── Supplier Outstanding Balances ─────────────────────────────
        $supplierBalances = Supplier::select('suppliers.*')
            ->withSum('supplies as total_owed', Currency::getSelectRaw('total_cost'))
            ->withSum('supplies as total_paid_amount', Currency::getSelectRaw('paid_amount'))
            ->withSum('payments as total_later_payments', Currency::getSelectRaw('supplier_payments.amount', 'supplier_payments.exchange_rate'))
            ->get()
            ->map(function ($s) {
                $s->outstanding = ($s->total_owed ?? 0) - ($s->total_paid_amount ?? 0) - ($s->total_later_payments ?? 0);

                return $s;
            })
            ->sortByDesc('outstanding')
            ->values();

        // ── Revenue Trend (daily aggregates) ──────────────────────────
        $trendData = WorkDay::where('status', 'closed')
            ->when($start, fn ($q) => $q->where('start_time', '>=', $start))
            ->when($end, fn ($q) => $q->where('start_time', '<=', $end))
            ->withSum('distributions', Currency::getSelectRaw('total_price'))
            ->withSum('workerShifts as retail_sales', Currency::getSelectRaw('cash_collected', 'cash_exchange_rate'))
            ->orderBy('start_time')
            ->get()
            ->map(function ($wd) {
                // Combine wholesale + retail
                $dailyRevenue = ($wd->distributions_sum_total_price ?? 0) + ($wd->retail_sales ?? 0);
                
                return [
                    'date' => $wd->start_time->translatedFormat('m/d'),
                    'revenue' => Currency::convertAmount($dailyRevenue),
                    'expenses' => Currency::convertAmount($wd->total_expenses_at_close ?? 0),
                ];
            });

        // ── Top Distributors ──────────────────────────────────────────
        $topDistributors = Distributor::select('distributors.*')
            ->withSum(['distributions as period_sales' => function ($q) use ($periodWorkDayIds) {
                $q->whereIn('work_day_id', $periodWorkDayIds);
            }], Currency::getSelectRaw('total_price'))
            ->withSum(['distributions as period_bundles' => function ($q) use ($periodWorkDayIds) {
                $q->whereIn('work_day_id', $periodWorkDayIds);
            }], 'bundle_count')
            ->get()
            ->sortByDesc('period_sales')
            ->take(5)
            ->values();

        // ── Recent settled days ───────────────────────────────────────
        $lastDays = WorkDay::where('status', 'closed')
            ->with('closedBy')
            ->orderBy('id', 'desc')
            ->take(7)
            ->get();

        // ── Live stats for active day ─────────────────────────────────
        $todaySales = 0;
        $todayExpenses = 0;
        $todayBundlesSold = 0;
        $todaySupplierPayments = 0;

        if ($activeWorkDay) {
            $stats = $activeWorkDay->getStatistics();
            
            $todaySales = $stats['totalSales'] - $stats['totalRefunds'];
            $todayExpenses = $stats['totalExpenses'];
            $todayBundlesSold = $stats['bundlesSold'];
            $todaySupplierPayments = $stats['supplierPayments'];
        }

        return view('Admin.dashboard', compact(
            'period', 'activeWorkDay', 'defaultCurrency', 'currencyCode',
            // KPI
            'totalRevenue', 'totalExpenses', 'netProfit', 'profitMargin',
            'netBundlesSold', 'workDaysCount', 'totalWorkers', 'totalDistributors',
            // Breakdowns
            'expenseBreakdown', 'distributorBalances', 'supplierBalances',
            'topDistributors', 'trendData', 'lastDays',
            // Live stats
            'todaySales', 'todayExpenses', 'todayBundlesSold', 'todaySupplierPayments',
            // Starting balances (bundles only)
            'startingBundles',
            // Specific Debt tracking
            'totalSupplierPayments'
        ));
    }

    private function getDateRange(string $period): array
    {
        return match ($period) {
            'today' => ['start' => Carbon::today(), 'end' => Carbon::tomorrow()],
            'week' => ['start' => Carbon::now()->startOfWeek(), 'end' => Carbon::now()->endOfWeek()],
            'month' => ['start' => Carbon::now()->startOfMonth(), 'end' => Carbon::now()->endOfMonth()],
            'semi' => ['start' => Carbon::now()->subMonths(6)->startOfDay(), 'end' => Carbon::now()->endOfDay()],
            'year' => ['start' => Carbon::now()->startOfYear(), 'end' => Carbon::now()->endOfYear()],
            'all' => ['start' => null, 'end' => null],
            default => ['start' => Carbon::today(), 'end' => Carbon::tomorrow()],
        };
    }
}
