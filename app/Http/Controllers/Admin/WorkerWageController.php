<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Currency;
use App\Models\WorkDay;
use App\Models\Worker;
use App\Models\WorkerTransaction;
use Illuminate\Http\Request;

class WorkerWageController extends Controller
{
    public function index()
    {
        $activeWorkDay = WorkDay::where('status', 'active')->first();
        if (!$activeWorkDay) {
            return redirect()->route('admin.dashboard')->with('error_message', __('No active work day.'));
        }

        $workers = Worker::with('currency')->get();
        $currencies = Currency::all();
        
        $transactions = WorkerTransaction::with(['worker', 'currency', 'admin'])
            ->where('work_day_id', $activeWorkDay->id)
            ->whereIn('type', ['salary', 'wage', 'bonus'])
            ->latest()
            ->get();

        return view('Admin.Workers.Wages.index', compact('activeWorkDay', 'workers', 'currencies', 'transactions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'worker_id' => 'required|exists:workers,id',
            'type' => 'required|in:salary,wage,bonus,advance,allowance',
            'amount' => 'required|numeric|min:0.01',
            'currency_id' => 'required|exists:currencies,id',
            'exchange_rate' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        $activeWorkDay = WorkDay::where('status', 'active')->first();
        if (!$activeWorkDay) {
            return back()->with('error_message', __('No active work day found.'));
        }

        $currency = Currency::findOrFail($request->currency_id);
        $rate = 1;
        if (!$currency->is_default) {
            $rate = $request->exchange_rate ?? $currency->exchange_rate;
        }

        WorkerTransaction::create([
            'worker_id' => $request->worker_id,
            'work_day_id' => $activeWorkDay->id,
            'type' => $request->type,
            'amount' => $request->amount,
            'currency_id' => $request->currency_id,
            'exchange_rate' => $rate,
            'notes' => $request->notes,
            'admin_id' => auth()->id(),
        ]);

        $typeLabel = match($request->type) {
            'wage' => __('Wage'),
            'salary' => __('Salary'),
            'bonus' => __('Bonus'),
            'advance' => __('Advance'),
            'allowance' => __('Allowance'),
            default => ucfirst($request->type)
        };

        return back()->with('success_message', __(':type recorded successfully.', ['type' => $typeLabel]));
    }
}
