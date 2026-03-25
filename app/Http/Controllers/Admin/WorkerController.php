<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Worker;
use App\Models\WorkerMobile;
use App\Models\Currency;

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
            'mobiles' => 'array'
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
                if(!empty($number)){
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
            'mobiles' => 'array'
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
                if(!empty($number)){
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
        if ($request->filled('date_from')) {
            $queryShifts->whereHas('workDay', fn($q) => $q->where('start_time', '>=', $request->date_from));
            $queryTransactions->whereHas('workDay', fn($q) => $q->where('start_time', '>=', $request->date_from));
        }
        if ($request->filled('date_to')) {
            $queryShifts->whereHas('workDay', fn($q) => $q->where('start_time', '<=', $request->date_to . ' 23:59:59'));
            $queryTransactions->whereHas('workDay', fn($q) => $q->where('start_time', '<=', $request->date_to . ' 23:59:59'));
        }

        $shifts = $queryShifts->get();
        $transactions = $queryTransactions->get();

        // Calculate Stats
        $totalEarnedSYPN = $shifts->sum(function($s) {
            return $s->snapshot_daily_wage * ($s->snapshot_exchange_rate ?: 1);
        });

        $totalAdvancesSYPN = $transactions->whereIn('type', ['advance', 'payment'])->sum(function($t) {
            return $t->amount * ($t->exchange_rate ?: 1);
        });

        $totalDiscountsSYPN = $transactions->where('type', 'discount')->sum(function($t) {
            return $t->amount * ($t->exchange_rate ?: 1);
        });

        $balanceSYPN = $totalEarnedSYPN - $totalAdvancesSYPN - $totalDiscountsSYPN;

        // Unified History for Display
        $history = collect();
        foreach($shifts as $s) {
            $history->push([
                'date' => $s->workDay?->start_time,
                'type' => 'wage',
                'description' => __('Daily Wage'),
                'amount' => $s->snapshot_daily_wage,
                'currency' => $s->currency?->code ?? 'SYPN',
                'rate' => $s->snapshot_exchange_rate,
                'total_sypn' => $s->snapshot_daily_wage * ($s->snapshot_exchange_rate ?: 1),
                'icon' => '<svg class="w-4 h-4 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>'
            ]);
        }
        foreach($transactions as $t) {
            $history->push([
                'date' => $t->workDay?->start_time ?? $t->created_at,
                'type' => $t->type,
                'description' => $t->notes ?: __($t->type),
                'amount' => $t->amount,
                'currency' => $t->currency?->code ?? 'SYPN',
                'rate' => $t->exchange_rate,
                'total_sypn' => $t->amount * ($t->exchange_rate ?: 1),
                'icon' => $t->type == 'discount' 
                    ? '<svg class="w-4 h-4 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>'
                    : '<svg class="w-4 h-4 text-sky-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" /></svg>'
            ]);
        }

        $history = $history->sortByDesc('date');

        return view('Admin.Workers.show', compact(
            'worker', 'history', 'totalEarnedSYPN', 
            'totalAdvancesSYPN', 'totalDiscountsSYPN', 'balanceSYPN'
        ));
    }

    public function destroy(Worker $worker)
    {
        $worker->delete();
        return redirect()->route('admin.workers.index')->with('success', __('Worker record completely deleted.'));
    }
}
