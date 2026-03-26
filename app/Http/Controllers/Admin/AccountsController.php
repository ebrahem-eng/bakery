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
use Illuminate\Pagination\LengthAwarePaginator;

class AccountsController extends Controller
{
    public function index(Request $request)
    {
        $period = $request->get('period', 'all');
        $dateRange = $this->getDateRange($period);
        $start = $dateRange['start'];
        $end = $dateRange['end'];

        $defaultCurrency = Currency::where('is_default', true)->first();
        $currencyCode = $defaultCurrency->code ?? 'SYP';

        // ── Period-filtered work day IDs ──────────────────────────────
        $workDayIds = WorkDay::query()
            ->when($start, fn ($q) => $q->where('start_time', '>=', $start))
            ->when($end, fn ($q) => $q->where('start_time', '<=', $end))
            ->pluck('id');

        // ═══════════════════════════════════════════════════════════════
        // PROFIT & LOSS STATEMENT
        // ═══════════════════════════════════════════════════════════════

        // ── Income ────────────────────────────────────────────────────
        $grossSales = Distribution::whereIn('work_day_id', $workDayIds)->sum('total_price');
        $salesReturns = DistributorReturn::whereIn('work_day_id', $workDayIds)->sum('total_refund');
        $netRevenue = $grossSales - $salesReturns;

        // ── Cost of Goods Sold (COGS) ─────────────────────────────────
        $rawMaterialsCost = Supply::whereIn('work_day_id', $workDayIds)->sum('total_cost');
        $freightUnloading = Supply::whereIn('work_day_id', $workDayIds)->sum('unloading_fee');
        $totalCOGS = $rawMaterialsCost + $freightUnloading;

        // ── Gross Profit ──────────────────────────────────────────────
        $grossProfit = $netRevenue - $totalCOGS;
        $grossMargin = $netRevenue > 0 ? round(($grossProfit / $netRevenue) * 100, 1) : 0;

        // ── Operating Expenses ────────────────────────────────────────
        $workerWages = WorkerShift::whereIn('work_day_id', $workDayIds)->sum('snapshot_daily_wage');
        $workerAllowances = WorkerTransaction::whereIn('work_day_id', $workDayIds)->where('type', 'allowance')->sum('amount');
        $workerAdvances = WorkerTransaction::whereIn('work_day_id', $workDayIds)->where('type', 'advance')->sum('amount');
        $workerDeductions = WorkerTransaction::whereIn('work_day_id', $workDayIds)->where('type', 'deduction')->sum('amount');
        $operationalExpenses = Expense::whereIn('work_day_id', $workDayIds)->sum('amount');

        $totalOperatingExpenses = $workerWages + $workerAllowances - $workerDeductions + $operationalExpenses;

        // ── Net Profit ────────────────────────────────────────────────
        $netProfit = $grossProfit - $totalOperatingExpenses;
        $netMargin = $netRevenue > 0 ? round(($netProfit / $netRevenue) * 100, 1) : 0;

        // ═══════════════════════════════════════════════════════════════
        // CASH FLOW
        // ═══════════════════════════════════════════════════════════════

        // Cash In
        $cashFromDistributors = DistributorTransaction::whereIn('work_day_id', $workDayIds)->sum('amount');
        $cashFromShifts = WorkerShift::whereIn('work_day_id', $workDayIds)->sum('cash_collected');

        // Carried over cash from previous days
        $carriedOverCash = WorkDay::whereIn('id', $workDayIds)
            ->where('status', 'closed')
            ->sum('carried_over_money');

        $totalCashIn = $cashFromDistributors + $cashFromShifts;

        // Cash Out
        $cashToSuppliers = Supply::whereIn('work_day_id', $workDayIds)->sum('paid_amount');
        $cashToWages = $workerWages;
        $cashToAdvances = $workerAdvances;
        $cashToAllowances = $workerAllowances;
        $cashToExpenses = $operationalExpenses;
        $totalCashOut = $cashToSuppliers + $cashToWages + $cashToAdvances + $cashToAllowances + $cashToExpenses;

        $netCashFlow = $totalCashIn - $totalCashOut;

        // ═══════════════════════════════════════════════════════════════
        // TRANSACTION LEDGER (Unified, Paginated)
        // ═══════════════════════════════════════════════════════════════
        $typeFilter = $request->get('type', 'all');
        $transactions = collect();

        // Income entries (Distributions)
        $distributions = Distribution::whereIn('work_day_id', $workDayIds)
            ->with(['distributor', 'workDay'])
            ->get()
            ->map(fn ($d) => [
                'date' => $d->created_at,
                'type' => 'income',
                'category' => __('Distribution Sales'),
                'description' => ($d->distributor->first_name ?? '').' '.($d->distributor->last_name ?? '').' — '.$d->bundle_count.' '.__('bundles'),
                'amount' => $d->total_price,
                'work_day_id' => $d->work_day_id,
            ]);

        // Expense entries (Supplies)
        $supplyEntries = Supply::whereIn('work_day_id', $workDayIds)
            ->with(['supplier', 'category', 'workDay'])
            ->get()
            ->map(fn ($s) => [
                'date' => $s->created_at,
                'type' => 'expense',
                'category' => __('Raw Materials').' ('.($s->category->name ?? '').')',
                'description' => ($s->supplier->first_name ?? '').' '.($s->supplier->last_name ?? ''),
                'amount' => $s->total_cost,
                'work_day_id' => $s->work_day_id,
            ]);

        // Expense entries (Worker wages)
        $wageEntries = WorkerShift::whereIn('work_day_id', $workDayIds)
            ->with(['worker', 'workDay'])
            ->get()
            ->map(fn ($ws) => [
                'date' => $ws->created_at,
                'type' => 'expense',
                'category' => __('Worker Wages'),
                'description' => ($ws->worker->first_name ?? '').' '.($ws->worker->last_name ?? ''),
                'amount' => $ws->snapshot_daily_wage,
                'work_day_id' => $ws->work_day_id,
            ]);

        // Expense entries (Operational expenses)
        $expenseEntries = Expense::whereIn('work_day_id', $workDayIds)
            ->with('workDay')
            ->get()
            ->map(fn ($e) => [
                'date' => $e->created_at,
                'type' => 'expense',
                'category' => __('Operations').' ('.($e->category ?? $e->title).')',
                'description' => $e->title,
                'amount' => $e->amount,
                'work_day_id' => $e->work_day_id,
            ]);

        // Refund entries
        $refundEntries = DistributorReturn::whereIn('work_day_id', $workDayIds)
            ->with(['distributor', 'workDay'])
            ->get()
            ->map(fn ($r) => [
                'date' => $r->created_at,
                'type' => 'expense',
                'category' => __('Sales Returns'),
                'description' => ($r->distributor->first_name ?? '').' '.($r->distributor->last_name ?? '').' — '.$r->bundle_count.' '.__('bundles'),
                'amount' => $r->total_refund,
                'work_day_id' => $r->work_day_id,
            ]);

        // Payment entries (Cash In)
        $paymentEntries = DistributorTransaction::whereIn('work_day_id', $workDayIds)
            ->with(['distributor', 'workDay'])
            ->get()
            ->map(fn ($t) => [
                'date' => $t->created_at,
                'type' => 'cash_in',
                'category' => __('Distributor Payment'),
                'description' => ($t->distributor->first_name ?? '').' '.($t->distributor->last_name ?? ''),
                'amount' => $t->amount,
                'work_day_id' => $t->work_day_id,
            ]);

        $transactions = $distributions
            ->concat($supplyEntries)
            ->concat($wageEntries)
            ->concat($expenseEntries)
            ->concat($refundEntries)
            ->concat($paymentEntries);

        // Apply type filter
        if ($typeFilter !== 'all') {
            $transactions = $transactions->filter(fn ($t) => $t['type'] === $typeFilter);
        }

        // Sort by date descending
        $transactions = $transactions->sortByDesc('date')->values();

        // Paginate
        $page = $request->get('page', 1);
        $perPage = 15;
        $paginatedTransactions = new LengthAwarePaginator(
            $transactions->forPage($page, $perPage),
            $transactions->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        // ═══════════════════════════════════════════════════════════════
        // PAYOUT SUMMARIES
        // ═══════════════════════════════════════════════════════════════

        // Distributor Payouts
        $distributorPayouts = Distributor::select('distributors.*')
            ->withSum(['distributions as total_billed' => function ($q) use ($workDayIds) {
                $q->whereIn('work_day_id', $workDayIds);
            }], 'total_price')
            ->withSum(['transactions as total_received' => function ($q) use ($workDayIds) {
                $q->whereIn('work_day_id', $workDayIds);
            }], 'amount')
            ->withSum(['returns as total_refunded' => function ($q) use ($workDayIds) {
                $q->whereIn('work_day_id', $workDayIds);
            }], 'total_refund')
            ->get()
            ->map(function ($d) {
                $d->balance = ($d->total_billed ?? 0) - ($d->total_received ?? 0) - ($d->total_refunded ?? 0);

                return $d;
            })
            ->sortByDesc('total_billed')
            ->values();

        // Supplier Payouts
        $supplierPayouts = Supplier::select('suppliers.*')
            ->withSum(['supplies as total_owed' => function ($q) use ($workDayIds) {
                $q->whereIn('work_day_id', $workDayIds);
            }], 'total_cost')
            ->withSum(['supplies as total_paid_to' => function ($q) use ($workDayIds) {
                $q->whereIn('work_day_id', $workDayIds);
            }], 'paid_amount')
            ->get()
            ->map(function ($s) {
                $s->balance = ($s->total_owed ?? 0) - ($s->total_paid_to ?? 0);

                return $s;
            })
            ->sortByDesc('total_owed')
            ->values();

        // Worker Payouts
        $workerPayouts = Worker::select('workers.*')
            ->withSum(['shifts as total_wages' => function ($q) use ($workDayIds) {
                $q->whereIn('work_day_id', $workDayIds);
            }], 'snapshot_daily_wage')
            ->withSum(['transactions as total_advances' => function ($q) use ($workDayIds) {
                $q->whereIn('work_day_id', $workDayIds)->where('type', 'advance');
            }], 'amount')
            ->withSum(['transactions as total_allowances' => function ($q) use ($workDayIds) {
                $q->whereIn('work_day_id', $workDayIds)->where('type', 'allowance');
            }], 'amount')
            ->withSum(['transactions as total_deductions' => function ($q) use ($workDayIds) {
                $q->whereIn('work_day_id', $workDayIds)->where('type', 'deduction');
            }], 'amount')
            ->get()
            ->map(function ($w) {
                $w->net_pay = ($w->total_wages ?? 0) + ($w->total_allowances ?? 0) - ($w->total_deductions ?? 0);
                $w->total_paid_out = ($w->total_advances ?? 0);
                $w->balance = $w->net_pay - $w->total_paid_out;

                return $w;
            })
            ->sortByDesc('net_pay')
            ->values();

        // Work days count
        $workDaysCount = $workDayIds->count();

        return view('Admin.Accounts.index', compact(
            'period', 'currencyCode', 'workDaysCount',
            // P&L
            'grossSales', 'salesReturns', 'netRevenue',
            'rawMaterialsCost', 'freightUnloading', 'totalCOGS',
            'grossProfit', 'grossMargin',
            'workerWages', 'workerAllowances', 'workerAdvances', 'workerDeductions',
            'operationalExpenses', 'totalOperatingExpenses',
            'netProfit', 'netMargin',
            // Cash Flow
            'cashFromDistributors', 'cashFromShifts', 'totalCashIn',
            'cashToSuppliers', 'cashToWages', 'cashToAdvances', 'cashToAllowances', 'cashToExpenses', 'totalCashOut',
            'netCashFlow', 'carriedOverCash',
            // Ledger
            'paginatedTransactions', 'typeFilter',
            // Payouts
            'distributorPayouts', 'supplierPayouts', 'workerPayouts'
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
            default => ['start' => null, 'end' => null],
        };
    }
}
