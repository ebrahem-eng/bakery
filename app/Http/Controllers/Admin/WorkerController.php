<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Currency;
use App\Models\Worker;
use App\Models\WorkerMobile;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class WorkerController extends Controller
{
    public function index()
    {
        $workers = Worker::with('mobiles', 'currency')->orderBy('id', 'desc')->get();

        return view('Admin.Workers.index', compact('workers'));
    }

    public function create()
    {
        $currencies = Currency::all();

        return view('Admin.Workers.create', compact('currencies'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'daily_wage' => 'required|numeric|min:0',
            'currency_id' => 'required|exists:currencies,id',
            'exchange_rate' => 'nullable|numeric|min:0',
            'mobiles' => 'array',
        ]);

        $worker = Worker::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'title' => $request->title,
            'daily_wage' => $request->daily_wage,
            'currency_id' => $request->currency_id,
            'exchange_rate' => $request->exchange_rate ?? 1.0,
        ]);

        if ($request->has('mobiles')) {
            foreach ($request->mobiles as $number) {
                if (! empty($number)) {
                    WorkerMobile::create(['worker_id' => $worker->id, 'mobile_number' => $number]);
                }
            }
        }

        return redirect()->route('admin.workers.index')->with('success', __('Worker profile established successfully.'));
    }

    public function edit(Worker $worker)
    {
        $worker->load('mobiles', 'currency');
        $currencies = Currency::all();

        return view('Admin.Workers.edit', compact('worker', 'currencies'));
    }

    public function update(Request $request, Worker $worker)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'daily_wage' => 'required|numeric|min:0',
            'currency_id' => 'required|exists:currencies,id',
            'exchange_rate' => 'nullable|numeric|min:0',
            'mobiles' => 'array',
        ]);

        $worker->update([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'title' => $request->title,
            'daily_wage' => $request->daily_wage,
            'currency_id' => $request->currency_id,
            'exchange_rate' => $request->exchange_rate ?? 1.0,
        ]);

        if ($request->has('mobiles')) {
            $worker->mobiles()->delete();
            foreach ($request->mobiles as $number) {
                if (! empty($number)) {
                    WorkerMobile::create(['worker_id' => $worker->id, 'mobile_number' => $number]);
                }
            }
        }

        return redirect()->route('admin.workers.index')->with('success', __('Worker profile updated.'));
    }

    public function show(Worker $worker, Request $request)
    {
        $worker->load('mobiles', 'currency');

        $queryShifts = $worker->shifts()->with('workDay')->orderBy('id', 'desc');
        $queryTransactions = $worker->transactions()->with(['workDay', 'currency'])->orderBy('id', 'desc');

        // Apply Date Filters
        $startDate = $request->date_from;
        $endDate = $request->date_to;
        $type = $request->type;

        if ($request->filled('date_from')) {
            $queryShifts->whereHas('workDay', fn ($q) => $q->where('start_time', '>=', $request->date_from));
            $queryTransactions->whereHas('workDay', fn ($q) => $q->where('start_time', '>=', $request->date_from));
        }
        if ($request->filled('date_to')) {
            $queryShifts->whereHas('workDay', fn ($q) => $q->where('start_time', '<=', $request->date_to.' 23:59:59'));
            $queryTransactions->whereHas('workDay', fn ($q) => $q->where('start_time', '<=', $request->date_to.' 23:59:59'));
        }

        $shifts = $queryShifts->with('admin', 'currency', 'workDay')->get();
        $transactions = $queryTransactions->with('admin', 'currency')->get();

        // Calculate Stats
        $totalEarnedSYP = $shifts->sum(function ($s) {
            return Currency::convertAmount($s->snapshot_daily_wage, $s->snapshot_exchange_rate);
        });

        // Actual cash paid to worker
        $totalAdvancesSYP = $transactions->whereIn('type', ['advance', 'salary', 'wage', 'bonus', 'allowance'])->sum(function ($t) {
            return Currency::convertAmount($t->amount, $t->exchange_rate);
        });

        $totalDiscountsSYP = $transactions->where('type', 'deduction')->sum(function ($t) {
            return Currency::convertAmount($t->amount, $t->exchange_rate);
        });

        // For display: specifically Salary/Wage vs Advances
        $totalSalariesSYP = $transactions->whereIn('type', ['salary', 'wage', 'bonus'])->sum(function ($t) {
            return Currency::convertAmount($t->amount, $t->exchange_rate);
        });

        $balanceSYP = $totalEarnedSYP - ($totalAdvancesSYP - $totalDiscountsSYP);

        // Unified History for Display
        $historyList = collect();
        foreach ($shifts as $s) {
            $historyList->push([
                'id' => 'shift_'.$s->id,
                'date' => $s->check_in,
                'type' => 'wage',
                'description' => __('Daily Wage'),
                'amount' => $s->snapshot_daily_wage,
                'currency' => $s->currency?->code ?? 'SYP',
                'rate' => $s->snapshot_exchange_rate,
                'total_sypn' => Currency::convertAmount($s->snapshot_daily_wage, $s->snapshot_exchange_rate),
                'admin' => $s->admin?->name ?? __('System'),
                'icon' => '<svg class="w-4 h-4 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>',
            ]);
        }
        foreach ($transactions as $t) {
            $historyList->push([
                'id' => 'trans_'.$t->id,
                'date' => $t->created_at,
                'type' => $t->type,
                'description' => $t->notes ?: __(ucfirst($t->type)),
                'amount' => $t->amount,
                'currency' => $t->currency?->code ?? 'SYP',
                'rate' => $t->exchange_rate,
                'total_sypn' => Currency::convertAmount($t->amount, $t->exchange_rate),
                'admin' => $t->admin?->name ?? __('System'),
                'icon' => $t->type == 'deduction'
                    ? '<svg class="w-4 h-4 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>'
                    : '<svg class="w-4 h-4 text-sky-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" /></svg>',
            ]);
        }

        $sortedHistory = $historyList->sortByDesc('date');

        // Manual Pagination
        $page = request()->get('page', 1);
        $perPage = 15;
        $paginatedHistory = new LengthAwarePaginator(
            $sortedHistory->forPage($page, $perPage),
            $sortedHistory->count(),
            $perPage,
            $page,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        return view('Admin.Workers.show', [
            'worker' => $worker,
            'history' => $paginatedHistory,
            'totalEarnedSYP' => $totalEarnedSYP,
            'totalAdvancesSYP' => $totalAdvancesSYP,
            'totalDiscountsSYP' => $totalDiscountsSYP,
            'totalSalariesSYP' => $totalSalariesSYP,
            'balanceSYP' => $balanceSYP,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'type' => $type,
        ]);
    }

    public function destroy(Worker $worker)
    {
        $worker->delete();

        return redirect()->route('admin.workers.index')->with('success', __('Worker record completely deleted.'));
    }
}
