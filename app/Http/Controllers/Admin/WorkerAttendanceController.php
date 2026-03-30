<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Currency;
use App\Models\WorkDay;
use App\Models\Worker;
use App\Models\WorkerAttendance;
use App\Models\WorkerShift;
use App\Models\WorkerTransaction;
use Illuminate\Http\Request;

class WorkerAttendanceController extends Controller
{
    public function index()
    {
        $activeWorkDay = WorkDay::where('status', 'active')->first();
        if (! $activeWorkDay) {
            return redirect()->route('admin.dashboard')->with('error_message', __('No active work day found. Please start a day first.'));
        }

        $workers = Worker::with([
            'shifts' => function ($query) use ($activeWorkDay) {
                $query->where('work_day_id', $activeWorkDay->id)->orderBy('id', 'asc');
            },
            'transactions' => function ($query) use ($activeWorkDay) {
                $query->where('work_day_id', $activeWorkDay->id);
            },
            'attendances' => function ($query) use ($activeWorkDay) {
                $query->where('work_day_id', $activeWorkDay->id);
            },
            'currency',
        ])->get();

        $currencies = Currency::all();

        // Get the last closed shift's returned bundles for hand-off
        $lastShift = WorkerShift::where('work_day_id', $activeWorkDay->id)
            ->whereNotNull('check_out')
            ->orderBy('id', 'desc')
            ->first();
        
        $defaultBundles = $lastShift ? $lastShift->bundles_returned : 0;

        return view('Admin.Attendance.index', compact('workers', 'activeWorkDay', 'currencies', 'defaultBundles'));
    }

    public function presence()
    {
        $activeWorkDay = WorkDay::where('status', 'active')->first();
        if (!$activeWorkDay) {
            return view('Admin.Attendance.presence', [
                'workers' => collect(),
                'activeWorkDay' => null,
            ]);
        }

        $workers = Worker::with([
            'attendances' => function ($query) use ($activeWorkDay) {
                $query->where('work_day_id', $activeWorkDay->id);
            },
        ])->get();

        return view('Admin.Attendance.presence', compact('workers', 'activeWorkDay'));
    }

    public function clockIn(Request $request)
    {
        $activeWorkDay = WorkDay::where('status', 'active')->first();
        if (! $activeWorkDay) {
            return back()->with('error_message', __('No active work day found.'));
        }

        // Get minimum bundles required (from last hand-off)
        $lastShift = WorkerShift::where('work_day_id', $activeWorkDay->id)
            ->whereNotNull('check_out')
            ->orderBy('id', 'desc')
            ->first();
        $minBundles = $lastShift ? $lastShift->bundles_returned : 0;

        $request->validate([
            'worker_id' => 'required|exists:workers,id',
            'check_in' => 'nullable|date',
            'bundles_received' => "nullable|integer|min:$minBundles",
        ]);

        $worker = Worker::findOrFail($request->worker_id);

        // Check if already checked in and not checked out
        $existingShift = WorkerShift::where('worker_id', $worker->id)
            ->where('work_day_id', $activeWorkDay->id)
            ->whereNull('check_out')
            ->first();

        if ($existingShift) {
            return back()->with('error_message', __('Worker is already clocked in.'));
        }

        // STRICT CHECK: Must be marked as present in WorkerAttendance for this WorkDay
        $isPresent = WorkerAttendance::where('worker_id', $worker->id)
            ->where('work_day_id', $activeWorkDay->id)
            ->exists();

        if (!$isPresent) {
            return back()->with('error_message', __('Worker is not present today. Please mark attendance first.'));
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

        $request->validate([
            'bundles_returned' => 'nullable|integer|min:0',
            'cash_collected' => 'nullable|numeric|min:0',
            'cash_currency_id' => 'nullable|exists:currencies,id',
            'cash_exchange_rate' => 'nullable|numeric|min:0',
        ]);

        // Resolve exchange rate
        $cashExchangeRate = 1;
        if ($request->cash_currency_id) {
            $currency = Currency::find($request->cash_currency_id);
            if ($currency && ! $currency->is_default) {
                $cashExchangeRate = $request->cash_exchange_rate ?? $currency->exchange_rate;
            }
        }

        $shift->update([
            'check_out' => $request->check_out ?? now(),
            'bundles_returned' => $request->bundles_returned ?? 0,
            'cash_collected' => $request->cash_collected ?? 0,
            'cash_currency_id' => $request->cash_currency_id,
            'cash_exchange_rate' => $cashExchangeRate,
        ]);

        return back()->with('success_message', __('Worker clocked out successfully.'));
    }

    public function markAttendance(Request $request)
    {
        $activeWorkDay = WorkDay::where('status', 'active')->first();
        if (!$activeWorkDay) {
            return back()->with('error_message', __('No active work day found.'));
        }

        $startTimeStr = $activeWorkDay->start_time->format('Y-m-d H:i:s');

        $request->validate([
            'worker_id' => 'required|exists:workers,id',
            'arrival_time' => 'required|date|after_or_equal:' . $startTimeStr,
        ]);

        // Check if already present
        $alreadyPresent = WorkerAttendance::where('worker_id', $request->worker_id)
            ->where('work_day_id', $activeWorkDay->id)
            ->exists();

        if ($alreadyPresent) {
            return back()->with('error_message', __('Worker is already marked as present for this workday.'));
        }

        WorkerAttendance::create([
            'worker_id' => $request->worker_id,
            'work_day_id' => $activeWorkDay->id,
            'arrival_time' => $request->arrival_time,
            'admin_id' => auth()->id(),
        ]);

        return back()->with('success_message', __('Attendance marked successfully.'));
    }

    public function bulkMarkAttendance(Request $request)
    {
        $activeWorkDay = WorkDay::where('status', 'active')->first();
        if (!$activeWorkDay) {
            return back()->with('error_message', __('No active work day found.'));
        }

        $startTimeStr = $activeWorkDay->start_time->format('Y-m-d H:i:s');

        $request->validate([
            'worker_ids' => 'required|array',
            'worker_ids.*' => 'exists:workers,id',
            'arrival_time' => 'required|date|after_or_equal:' . $startTimeStr,
        ]);

        $count = 0;
        foreach ($request->worker_ids as $workerId) {
            $exists = WorkerAttendance::where('worker_id', $workerId)
                ->where('work_day_id', $activeWorkDay->id)
                ->exists();

            if (!$exists) {
                WorkerAttendance::create([
                    'worker_id' => $workerId,
                    'work_day_id' => $activeWorkDay->id,
                    'arrival_time' => $request->arrival_time,
                    'admin_id' => auth()->id(),
                ]);
                $count++;
            }
        }

        return back()->with('success_message', __(':count workers marked as present.', ['count' => $count]));
    }

    public function markDeparture(Request $request, WorkerAttendance $attendance)
    {
        $request->validate([
            'departure_time' => 'required|date|after:arrival_time',
        ]);

        $attendance->update([
            'departure_time' => $request->departure_time,
        ]);

        return back()->with('success_message', __('Departure time recorded successfully.'));
    }

    public function bulkMarkDeparture(Request $request)
    {
        $activeWorkDay = WorkDay::where('status', 'active')->first();
        if (!$activeWorkDay) {
            return back()->with('error_message', __('No active work day found.'));
        }

        $request->validate([
            'worker_ids' => 'required|array',
            'worker_ids.*' => 'exists:workers,id',
            'departure_time' => 'required|date',
        ]);

        $count = 0;
        foreach ($request->worker_ids as $workerId) {
            $attendance = WorkerAttendance::where('worker_id', $workerId)
                ->where('work_day_id', $activeWorkDay->id)
                ->whereNull('departure_time')
                ->first();

            if ($attendance && $request->departure_time > $attendance->arrival_time->format('Y-m-d H:i:s')) {
                $attendance->update([
                    'departure_time' => $request->departure_time,
                ]);
                $count++;
            }
        }

        return back()->with('success_message', __(':count workers marked as departed.', ['count' => $count]));
    }

    public function storeTransaction(Request $request)
    {
        $request->validate([
            'worker_id' => 'required|exists:workers,id',
            'type' => 'required|in:advance,allowance,deduction,salary,wage,bonus',
            'amount' => 'required|numeric|min:0.01',
            'currency_id' => 'nullable|exists:currencies,id',
            'exchange_rate' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        $activeWorkDay = WorkDay::where('status', 'active')->first();
        if (! $activeWorkDay) {
            return back()->with('error_message', __('No active work day.'));
        }

        $worker = Worker::findOrFail($request->worker_id);
        
        // Resolve Currency & Exchange Rate
        $currencyId = $request->currency_id ?? $worker->currency_id;
        $currency = Currency::find($currencyId);
        
        $rate = 1;
        if ($currency && !$currency->is_default) {
            $rate = $request->exchange_rate ?? $currency->exchange_rate;
        }

        WorkerTransaction::create([
            'worker_id' => $worker->id,
            'work_day_id' => $activeWorkDay->id,
            'type' => $request->type,
            'amount' => $request->amount,
            'currency_id' => $currencyId,
            'exchange_rate' => $rate,
            'notes' => $request->notes,
            'admin_id' => auth()->id(),
        ]);

        $typeLabel = match($request->type) {
            'advance' => __('Advance'),
            'allowance' => __('Allowance'),
            'deduction' => __('Deduction'),
            'salary' => __('Salary'),
            'wage' => __('Wage'),
            'bonus' => __('Bonus'),
            default => ucfirst($request->type)
        };

        return back()->with('success_message', __(':type recorded successfully.', ['type' => $typeLabel]));
    }
}
