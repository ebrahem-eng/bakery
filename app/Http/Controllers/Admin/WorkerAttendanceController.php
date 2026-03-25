<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Worker;
use App\Models\WorkerShift;
use App\Models\WorkerTransaction;
use App\Models\WorkDay;
use Illuminate\Http\Request;

class WorkerAttendanceController extends Controller
{
    public function index()
    {
        $activeWorkDay = WorkDay::where('status', 'active')->first();
        if (!$activeWorkDay) {
            return redirect()->route('admin.dashboard')->with('error_message', __('No active work day found. Please start a day first.'));
        }

        $workers = Worker::with([
            'shifts' => function ($query) use ($activeWorkDay) {
                $query->where('work_day_id', $activeWorkDay->id)->orderBy('id', 'asc');
            },
            'transactions' => function ($query) use ($activeWorkDay) {
                $query->where('work_day_id', $activeWorkDay->id);
            },
            'currency'
        ])->get();

        return view('Admin.Attendance.index', compact('workers', 'activeWorkDay'));
    }

    public function clockIn(Request $request)
    {
        $request->validate([
            'worker_id' => 'required|exists:workers,id',
            'check_in' => 'nullable|date',
            'bundles_received' => 'nullable|integer|min:0',
        ]);

        $activeWorkDay = WorkDay::where('status', 'active')->first();
        if (!$activeWorkDay) {
            return back()->with('error_message', __('No active work day found.'));
        }

        $worker = Worker::findOrFail($request->worker_id);

        // Check if already checked in and not checked out
        $existingShift = WorkerShift::where('worker_id', $worker->id)
            ->where('work_day_id', $activeWorkDay->id)
            ->whereNull('check_out')
            ->first();

        if ($existingShift) {
            return back()->with('error_message', __('Worker is already clocked in.'));
        }

        $rate = $worker->currency->is_local ? 1 : $worker->currency->exchange_rate;

        WorkerShift::create([
            'worker_id' => $worker->id,
            'work_day_id' => $activeWorkDay->id,
            'check_in' => $request->check_in ?? now(),
            'snapshot_daily_wage' => $worker->daily_wage,
            'snapshot_currency_id' => $worker->currency_id,
            'snapshot_exchange_rate' => $rate,
            'bundles_received' => $request->bundles_received ?? 0,
            'admin_id' => auth()->id(),
        ]);

        return back()->with('success_message', __('Worker clocked in successfully.'));
    }

    public function clockOut(Request $request, WorkerShift $shift)
    {
        if ($shift->check_out) {
            return back()->with('error_message', __('Worker is already clocked out.'));
        }

        $shift->update([
            'check_out' => now(),
            'bundles_returned' => $request->bundles_returned ?? 0,
        ]);

        return back()->with('success_message', __('Worker clocked out successfully.'));
    }

    public function storeTransaction(Request $request)
    {
        $request->validate([
            'worker_id' => 'required|exists:workers,id',
            'type' => 'required|in:advance,allowance,deduction',
            'amount' => 'required|numeric|min:0.01',
            'notes' => 'nullable|string',
        ]);

        $activeWorkDay = WorkDay::where('status', 'active')->first();
        if (!$activeWorkDay) return back()->with('error_message', __('No active work day.'));

        $worker = Worker::findOrFail($request->worker_id);
        $rate = $worker->currency->is_local ? 1 : $worker->currency->exchange_rate;

        WorkerTransaction::create([
            'worker_id' => $worker->id,
            'work_day_id' => $activeWorkDay->id,
            'type' => $request->type,
            'amount' => $request->amount,
            'currency_id' => $worker->currency_id,
            'exchange_rate' => $rate,
            'notes' => $request->notes,
            'admin_id' => auth()->id(),
        ]);

        return back()->with('success_message', __(':type recorded successfully.', ['type' => __(ucfirst($request->type))]));
    }
}
