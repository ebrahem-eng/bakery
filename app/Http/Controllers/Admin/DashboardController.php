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
        $dateRange = $this->getDateRange($period);
        $start = $dateRange['start'];
        $end = $dateRange['end'];

        // ── Active work day (always shown) ────────────────────────────
        $activeWorkDay = WorkDay::where('status', 'active')
            ->with(['distributions', 'supplies', 'expenses', 'workerShifts', 'workerTransactions', 'distributorReturns'])
            ->first();

        $defaultCurrency = Currency::where('is_default', true)->first();
        $currencyCode = $defaultCurrency->code ?? 'SYP';

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
        $totalDistributions = Distribution::whereIn('work_day_id', $periodWorkDayIds)->sum(Currency::getSelectRaw('total_price'));
        $totalRefunds = DistributorReturn::whereIn('work_day_id', $periodWorkDayIds)->sum(Currency::getSelectRaw('total_refund'));
        $totalRevenue = $totalDistributions - $totalRefunds;
        $totalPaymentsReceived = DistributorTransaction::whereIn('work_day_id', $periodWorkDayIds)->sum(Currency::getSelectRaw('amount'));

        // ── Expense Metrics ───────────────────────────────────────────
        $suppliesCost = Supply::whereIn('work_day_id', $periodWorkDayIds)->sum(Currency::getSelectRaw('total_cost'));
        $unloadingFees = Supply::whereIn('work_day_id', $periodWorkDayIds)->sum(Currency::getSelectRaw('unloading_fee', 'unloading_fee_exchange_rate'));
        $shiftWages = WorkerShift::whereIn('work_day_id', $periodWorkDayIds)->sum(Currency::getSelectRaw('snapshot_daily_wage', 'snapshot_exchange_rate'));
        $workerAllowances = WorkerTransaction::whereIn('work_day_id', $periodWorkDayIds)->where('type', 'allowance')->sum(Currency::getSelectRaw('amount'));
        $workerAdvances = WorkerTransaction::whereIn('work_day_id', $periodWorkDayIds)->where('type', 'advance')->sum(Currency::getSelectRaw('amount'));
        $workerDeductions = WorkerTransaction::whereIn('work_day_id', $periodWorkDayIds)->where('type', 'deduction')->sum(Currency::getSelectRaw('amount'));
        $operationalExpenses = Expense::whereIn('work_day_id', $periodWorkDayIds)->sum(Currency::getSelectRaw('amount'));

        $totalExpenses = $suppliesCost + $unloadingFees + $shiftWages + $workerAllowances - $workerDeductions + $operationalExpenses;
        $netProfit = $totalRevenue - $totalExpenses;
        $profitMargin = $totalRevenue > 0 ? round(($netProfit / $totalRevenue) * 100, 1) : 0;

        // ── Bundle metrics ────────────────────────────────────────────
        $bundlesSold = Distribution::whereIn('work_day_id', $periodWorkDayIds)->sum('bundle_count');
        $bundlesReturned = DistributorReturn::whereIn('work_day_id', $periodWorkDayIds)->sum('bundle_count');
        $netBundlesSold = $bundlesSold - $bundlesReturned;

        // ── Counts ────────────────────────────────────────────────────
        $workDaysCount = $periodWorkDayIds->count();
        $totalWorkers = Worker::count();
        $totalDistributors = Distributor::count();

        // ── Expense Breakdown ─────────────────────────────────────────
        $expenseBreakdown = [
            ['label' => __('Raw Materials'), 'value' => $suppliesCost, 'color' => '#f59e0b'],
            ['label' => __('Freight & Unloading'), 'value' => $unloadingFees, 'color' => '#ef4444'],
            ['label' => __('Worker Wages'), 'value' => $shiftWages, 'color' => '#3b82f6'],
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
            ->get()
            ->map(function ($s) {
                $s->outstanding = ($s->total_owed ?? 0) - ($s->total_paid_amount ?? 0);

                return $s;
            })
            ->sortByDesc('outstanding')
            ->values();

        // ── Revenue Trend (daily aggregates) ──────────────────────────
        $trendData = WorkDay::where('status', 'closed')
            ->when($start, fn ($q) => $q->where('start_time', '>=', $start))
            ->when($end, fn ($q) => $q->where('start_time', '<=', $end))
            ->orderBy('start_time')
            ->get()
            ->map(function ($wd) {
                return [
                    'date' => $wd->start_time->format('m/d'),
                    'revenue' => Currency::convertAmount($wd->total_sales_at_close ?? 0),
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

        if ($activeWorkDay) {
            $todaySales = $activeWorkDay->distributions->sum(fn($d) => Currency::convertAmount($d->total_price, $d->exchange_rate)) 
                        - $activeWorkDay->distributorReturns->sum(fn($r) => Currency::convertAmount($r->total_refund, $r->exchange_rate));
            $todayBundlesSold = $activeWorkDay->distributions->sum('bundle_count');
            $todayExpenses += $activeWorkDay->supplies->sum(fn($s) => Currency::convertAmount($s->total_cost, $s->exchange_rate)) 
                           + $activeWorkDay->supplies->sum(fn($s) => Currency::convertAmount($s->unloading_fee, $s->unloading_fee_exchange_rate));
            $todayExpenses += $activeWorkDay->workerShifts->sum(fn($w) => Currency::convertAmount($w->snapshot_daily_wage, $w->snapshot_exchange_rate));
            $todayExpenses += $activeWorkDay->workerTransactions->where('type', 'allowance')->sum(fn($wtf) => Currency::convertAmount($wtf->amount, $wtf->exchange_rate));
            $todayExpenses -= $activeWorkDay->workerTransactions->where('type', 'deduction')->sum(fn($wtf) => Currency::convertAmount($wtf->amount, $wtf->exchange_rate));
            $todayExpenses += $activeWorkDay->expenses->sum(fn($e) => Currency::convertAmount($e->amount, $e->exchange_rate));
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
            'todaySales', 'todayExpenses', 'todayBundlesSold'
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
