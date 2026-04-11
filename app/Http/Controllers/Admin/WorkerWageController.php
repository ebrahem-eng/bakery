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
    public function index(Request $request)
    {
        $activeWorkDay = WorkDay::where('status', 'active')->first();
        if (!$activeWorkDay) {
            return redirect()->route('admin.dashboard')->with('error_message', __('No active work day.'));
        }

        $workers = Worker::with('currency')
            ->withCount(['shifts' => function ($query) use ($activeWorkDay) {
                $query->where('work_day_id', $activeWorkDay->id);
            }])
            ->get();
        $currencies = Currency::all();
        
        $query = WorkerTransaction::with(['worker', 'currency', 'admin'])
            ->whereIn('type', ['salary', 'wage', 'bonus', 'advance', 'allowance']);

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // If no date filters, default to active work day
        if (!$request->filled('date_from') && !$request->filled('date_to')) {
            $query->where('work_day_id', $activeWorkDay->id);
            $listTitle = __('Payments Released Today');
        } else {
            $listTitle = __('Payment History');
        }

        $transactions = $query->latest()->paginate(20)->withQueryString();

        return view('Admin.Workers.Wages.index', compact('activeWorkDay', 'workers', 'currencies', 'transactions', 'listTitle'));
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
