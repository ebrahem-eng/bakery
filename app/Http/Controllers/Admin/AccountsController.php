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
        
        if ($request->filled('date_from') && $request->filled('date_to')) {
            $start = Carbon::parse($request->date_from)->startOfDay();
            $end = Carbon::parse($request->date_to)->endOfDay();
            $period = 'custom';
        } else {
            $dateRange = $this->getDateRange($period);
            $start = $dateRange['start'];
            $end = $dateRange['end'];
        }

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
        $allWorkDays = WorkDay::whereIn('id', $workDayIds)->get();
        $totalSettlementCash = 0;
        $totalActiveShiftCash = 0;

        foreach ($allWorkDays as $wd) {
            if ($wd->status === 'closed') {
                $totalSettlementCash += Currency::convertAmount($wd->carried_over_money, $wd->carried_over_exchange_rate);
            } else {
                $totalActiveShiftCash += WorkerShift::where('work_day_id', $wd->id)->sum(Currency::getSelectRaw('cash_collected', 'cash_exchange_rate'));
            }
        }

        $wholesaleSales = Distribution::whereIn('work_day_id', $workDayIds)->sum(Currency::getSelectRaw('total_price'));
        $cashFromShifts = $totalActiveShiftCash;
        $endOfDayCash = $totalSettlementCash;
        $grossSales = $wholesaleSales + $cashFromShifts + $endOfDayCash;
        
        $salesReturns = DistributorReturn::whereIn('work_day_id', $workDayIds)->sum(Currency::getSelectRaw('total_refund'));
        $netRevenue = $grossSales - $salesReturns;

        // ── Cost of Goods Sold (COGS) ─────────────────────────────────
        $rawMaterialsCost = Supply::whereIn('work_day_id', $workDayIds)->sum(Currency::getSelectRaw('total_cost'));
        $freightUnloading = Supply::whereIn('work_day_id', $workDayIds)->sum(Currency::getSelectRaw('unloading_fee', 'unloading_fee_exchange_rate'));
        $totalCOGS = $rawMaterialsCost + $freightUnloading;

        // ── Gross Profit ──────────────────────────────────────────────
        $grossProfit = $netRevenue - $totalCOGS;
        $grossMargin = $netRevenue > 0 ? round(($grossProfit / $netRevenue) * 100, 1) : 0;

        // ── Operating Expenses ────────────────────────────────────────
        $workerAllowances = WorkerTransaction::whereIn('work_day_id', $workDayIds)->where('type', 'allowance')->sum(Currency::getSelectRaw('amount'));
        $workerAdvances = WorkerTransaction::whereIn('work_day_id', $workDayIds)->where('type', 'advance')->sum(Currency::getSelectRaw('amount'));
        $workerSalaries = WorkerTransaction::whereIn('work_day_id', $workDayIds)->whereIn('type', ['salary', 'wage', 'bonus'])->sum(Currency::getSelectRaw('amount'));
        $workerDeductions = WorkerTransaction::whereIn('work_day_id', $workDayIds)->where('type', 'deduction')->sum(Currency::getSelectRaw('amount'));
        $operationalExpenses = Expense::whereIn('work_day_id', $workDayIds)->sum(Currency::getSelectRaw('amount'));

        $totalOperatingExpenses = ($workerAllowances + $workerAdvances + $workerSalaries) - $workerDeductions + $operationalExpenses;

        // ── Net Profit ────────────────────────────────────────────────
        $netProfit = $grossProfit - $totalOperatingExpenses;
        $netMargin = $netRevenue > 0 ? round(($netProfit / $netRevenue) * 100, 1) : 0;

        // ═══════════════════════════════════════════════════════════════
        // CASH FLOW
        // ═══════════════════════════════════════════════════════════════

        // Cash In
        $cashFromDistributors = DistributorTransaction::whereIn('work_day_id', $workDayIds)->sum(Currency::getSelectRaw('amount'));

        // ── Employee Contributions ──────────────────────────────────
        $employeeContributions = WorkerShift::whereIn('work_day_id', $workDayIds)
            ->with('worker')
            ->get()
            ->groupBy('worker_id')
            ->map(function ($shifts) {
                return [
                    'worker_name' => $shifts->first()->worker->first_name . ' ' . $shifts->first()->worker->last_name,
                    'total_collected' => $shifts->sum('cash_collected_base'),
                    'total_bundles_sold' => $shifts->sum(fn($s) => $s->bundles_received - $s->bundles_returned),
                    'shift_count' => $shifts->count(),
                ];
            })
            ->sortByDesc('total_collected')
            ->values();

        $totalCashIn = $cashFromDistributors + $totalActiveShiftCash + $totalSettlementCash;

        $cashToSuppliers = Supply::whereIn('work_day_id', $workDayIds)->sum(Currency::getSelectRaw('paid_amount'));
        $cashToSupplierDebts = \App\Models\SupplierPayment::whereIn('work_day_id', $workDayIds)->sum(Currency::getSelectRaw('amount', 'exchange_rate'));
        $cashToFreight = Supply::whereIn('work_day_id', $workDayIds)->where('unloading_fee_payer', 'bakery')->sum(Currency::getSelectRaw('unloading_fee', 'unloading_fee_exchange_rate'));
        
        $cashToWages = $workerSalaries;
        $cashToAdvances = $workerAdvances;
        $cashToAllowances = $workerAllowances;
        $cashToExpenses = $operationalExpenses;
        $totalCashOut = $cashToSuppliers + $cashToSupplierDebts + $cashToFreight + $cashToAdvances + $cashToAllowances + $cashToWages + $cashToExpenses;

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
                'amount' => Currency::convertAmount($d->total_price, $d->exchange_rate),
                'work_day_id' => $d->work_day_id,
            ]);

        // Income entries (Shifts)
        $shiftEntries = WorkerShift::whereIn('work_day_id', $workDayIds)
            ->with(['worker', 'workDay'])
            ->get()
            ->map(fn ($s) => [
                'date' => $s->created_at,
                'type' => 'income',
                'category' => __('Retail Sales'),
                'description' => ($s->worker->first_name ?? '').' '.($s->worker->last_name ?? '').' — '.__('Shift Cash'),
                'amount' => Currency::convertAmount($s->cash_collected, $s->cash_exchange_rate),
                'work_day_id' => $s->work_day_id,
            ]);

        // Income entries (End-of-Day Settlement Cash)
        $settlementEntries = WorkDay::whereIn('id', $workDayIds)
            ->where('status', 'closed')
            ->where('carried_over_money', '>', 0)
            ->get()
            ->map(fn ($wd) => [
                'date' => $wd->end_time,
                'type' => 'income',
                'category' => __('Settlement Cash'),
                'description' => __('End-of-Day Terminal Balance'),
                'amount' => Currency::convertAmount($wd->carried_over_money, $wd->carried_over_exchange_rate),
                'work_day_id' => $wd->id,
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
                'amount' => Currency::convertAmount($s->total_cost, $s->exchange_rate) + Currency::convertAmount($s->unloading_fee, $s->unloading_fee_exchange_rate),
                'work_day_id' => $s->work_day_id,
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
                'amount' => Currency::convertAmount($e->amount, $e->exchange_rate),
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
                'amount' => Currency::convertAmount($r->total_refund, $r->exchange_rate),
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
                'amount' => Currency::convertAmount($t->amount, $t->exchange_rate),
                'work_day_id' => $t->work_day_id,
            ]);

        // Down Payment entries (Cash In)
        $downPaymentEntries = Distribution::whereIn('work_day_id', $workDayIds)
            ->where('amount_paid', '>', 0)
            ->with(['distributor', 'workDay'])
            ->get()
            ->map(fn ($d) => [
                'date' => $d->created_at,
                'type' => 'cash_in',
                'category' => __('Distributor Payment').' ('.__('Down Payment').')',
                'description' => ($d->distributor->first_name ?? '').' '.($d->distributor->last_name ?? '').' - '.__('Invoice #').$d->id,
                'amount' => Currency::convertAmount($d->amount_paid, $d->exchange_rate),
                'work_day_id' => $d->work_day_id,
            ]);

        // Debt Settlement entries (Cash Out)
        $debtPaymentEntries = \App\Models\SupplierPayment::whereIn('work_day_id', $workDayIds)
            ->with(['supply.supplier', 'workDay'])
            ->get()
            ->map(fn ($sp) => [
                'date' => $sp->created_at,
                'type' => 'expense',
                'category' => __('Debt Settlement'),
                'description' => ($sp->supply->supplier->first_name ?? '').' '.($sp->supply->supplier->last_name ?? '').' - '.__('Invoice #').$sp->supply_id,
                'amount' => Currency::convertAmount($sp->amount, $sp->exchange_rate),
                'work_day_id' => $sp->work_day_id,
            ]);

        $transactions = $distributions
            ->concat($shiftEntries)
            ->concat($settlementEntries)
            ->concat($supplyEntries)
            ->concat($expenseEntries)
            ->concat($refund_entries ?? collect())
            ->concat($refundEntries)
            ->concat($paymentEntries)
            ->concat($downPaymentEntries)
            ->concat($debtPaymentEntries);

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
            }], Currency::getSelectRaw('total_price'))
            ->withSum(['distributions as total_down_payments' => function ($q) use ($workDayIds) {
                $q->whereIn('work_day_id', $workDayIds);
            }], Currency::getSelectRaw('amount_paid'))
            ->withSum(['transactions as total_received' => function ($q) use ($workDayIds) {
                $q->whereIn('work_day_id', $workDayIds);
            }], Currency::getSelectRaw('amount'))
            ->withSum(['returns as total_refunded' => function ($q) use ($workDayIds) {
                $q->whereIn('work_day_id', $workDayIds);
            }], Currency::getSelectRaw('total_refund'))
            ->get()
            ->map(function ($d) {
                $d->balance = ($d->total_billed ?? 0) - ($d->total_received ?? 0) - ($d->total_down_payments ?? 0) - ($d->total_refunded ?? 0);

                return $d;
            })
            ->sortByDesc('total_billed')
            ->values();

        // Supplier Payouts
        $supplierPayouts = Supplier::select('suppliers.*')
            ->withSum(['supplies as total_owed' => function ($q) use ($workDayIds) {
                $q->whereIn('supplies.work_day_id', $workDayIds);
            }], Currency::getSelectRaw('supplies.total_cost', 'supplies.exchange_rate'))
            ->withSum(['supplies as total_paid_to' => function ($q) use ($workDayIds) {
                $q->whereIn('supplies.work_day_id', $workDayIds);
            }], Currency::getSelectRaw('supplies.paid_amount', 'supplies.exchange_rate'))
            ->withSum(['payments as total_settlements' => function ($q) use ($workDayIds) {
                $q->whereIn('supplier_payments.work_day_id', $workDayIds);
            }], Currency::getSelectRaw('supplier_payments.amount', 'supplier_payments.exchange_rate'))
            ->get()
            ->map(function ($s) {
                $s->total_paid_combined = ($s->total_paid_to ?? 0) + ($s->total_settlements ?? 0);
                $s->balance = ($s->total_owed ?? 0) - $s->total_paid_combined;

                return $s;
            })
            ->sortByDesc('total_owed')
            ->values();

        // Worker Payouts (Earned vs Paid)
        $workerPayouts = Worker::select('workers.*')
            ->withSum(['shifts as total_wages' => function ($q) use ($workDayIds) {
                $q->whereIn('work_day_id', $workDayIds);
            }], Currency::getSelectRaw('snapshot_daily_wage', 'snapshot_exchange_rate'))
            ->withSum(['transactions as total_manual_payouts' => function ($q) use ($workDayIds) {
                $q->whereIn('work_day_id', $workDayIds)->whereIn('type', ['salary', 'wage', 'advance', 'allowance', 'bonus']);
            }], Currency::getSelectRaw('amount'))
            ->withSum(['transactions as total_allowances' => function ($q) use ($workDayIds) {
                $q->whereIn('work_day_id', $workDayIds)->where('type', 'allowance');
            }], Currency::getSelectRaw('amount'))
            ->withSum(['transactions as total_bonuses' => function ($q) use ($workDayIds) {
                $q->whereIn('work_day_id', $workDayIds)->where('type', 'bonus');
            }], Currency::getSelectRaw('amount'))
            ->withSum(['transactions as total_deductions' => function ($q) use ($workDayIds) {
                $q->whereIn('work_day_id', $workDayIds)->where('type', 'deduction');
            }], Currency::getSelectRaw('amount'))
            ->get()
            ->map(function ($w) {
                // Earned = Basic Wage + Allowances + Bonuses
                $w->earned = ($w->total_wages ?? 0) + ($w->total_allowances ?? 0) + ($w->total_bonuses ?? 0);
                
                // Deductions reduce the net liability
                $w->net_earned = $w->earned - ($w->total_deductions ?? 0);
                
                // Total Paid Out is the sum of actual cash payments
                $w->total_paid_out = ($w->total_manual_payouts ?? 0);
                
                // Remaining Balance
                $w->balance = $w->net_earned - $w->total_paid_out;

                // For the view, keep legacy net_pay as net_earned
                $w->net_pay = $w->net_earned;

                return $w;
            })
            ->sortByDesc('earned')
            ->values();

        // Work days count
        $workDaysCount = $workDayIds->count();

        return view('Admin.Accounts.index', compact(
            'period', 'start', 'end', 'currencyCode', 'workDaysCount',
            // P&L
            'grossSales', 'salesReturns', 'netRevenue', 
            'rawMaterialsCost', 'freightUnloading', 'totalCOGS',
            'grossProfit', 'grossMargin',
            'workerAllowances', 'workerAdvances', 'workerSalaries', 'workerDeductions', 'operationalExpenses',
            'totalOperatingExpenses', 'netProfit', 'netMargin',
            // Cash Flow
            'cashFromDistributors', 'cashFromShifts', 'endOfDayCash', 'totalCashIn',
            'cashToSuppliers', 'cashToSupplierDebts', 'cashToFreight', 'cashToWages', 'cashToAdvances', 'cashToAllowances', 'cashToExpenses', 'totalCashOut',
            'netCashFlow',
            // Contributions
            'employeeContributions',
            'netRevenue', 
            // Ledger
            'paginatedTransactions', 'typeFilter',
            // Payouts
            'distributorPayouts', 'supplierPayouts', 'workerPayouts'
        ));
    }

    public function debts(Request $request)
    {
        $defaultCurrency = Currency::where('is_default', true)->first();
        $currencyCode = $defaultCurrency->code ?? 'SYP';

        // Load all supplies that aren't fully paid. We sort so older debts or those with closest due dates appear first.
        $allUnpaidSupplies = Supply::with(['supplier', 'category', 'currency', 'paidCurrency'])
            ->orderByRaw('due_date IS NULL ASC, due_date ASC')
            ->orderBy('id', 'desc')
            ->get()
            ->filter(fn($s) => !$s->is_fully_paid);

        $totalOutstanding = 0;
        $overdueAmount = 0;
        $dueSoonAmount = 0;

        foreach ($allUnpaidSupplies as $s) {
            $costInBase = Currency::convertAmount($s->total_cost, $s->exchange_rate);
            $paidInBase = $s->getTotalPaidBase();
            $unpaid = max(0, $costInBase - $paidInBase);
            
            $totalOutstanding += $unpaid;

            if ($s->due_date) {
                if ($s->due_date->startOfDay()->isPast()) {
                    $overdueAmount += $unpaid;
                } elseif ($s->due_date->startOfDay()->diffInDays(now()->startOfDay()) <= 7 && !$s->due_date->startOfDay()->isPast()) {
                    $dueSoonAmount += $unpaid;
                }
            }
        }

        // Paginate the collection manually
        $perPage = 15;
        $page = $request->get('page', 1);
        $supplies = new \Illuminate\Pagination\LengthAwarePaginator(
            $allUnpaidSupplies->forPage($page, $perPage),
            $allUnpaidSupplies->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view('Admin.Accounts.debts', compact('supplies', 'currencyCode', 'totalOutstanding', 'overdueAmount', 'dueSoonAmount'));
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
