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

        return view('Admin.Attendance.index', compact('workers', 'activeWorkDay', 'currencies'));
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
        $request->validate([
            'worker_id' => 'required|exists:workers,id',
            'check_in' => 'nullable|date',
            'bundles_received' => 'nullable|integer|min:0',
        ]);

        $activeWorkDay = WorkDay::where('status', 'active')->first();
        if (! $activeWorkDay) {
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

        $workDayDate = $activeWorkDay->start_time->format('Y-m-d');

        $request->validate([
            'worker_id' => 'required|exists:workers,id',
            'arrival_time' => [
                'required',
                'date',
                function ($attribute, $value, $fail) use ($workDayDate) {
                    if (date('Y-m-d', strtotime($value)) !== $workDayDate) {
                        $fail(__('The arrival date must be the same as the active work day date (:date).', ['date' => $workDayDate]));
                    }
                },
            ],
        ]);

        WorkerAttendance::create([
            'worker_id' => $request->worker_id,
            'work_day_id' => $activeWorkDay->id,
            'arrival_time' => $request->arrival_time,
            'admin_id' => auth()->id(),
        ]);

        return back()->with('success_message', __('Attendance marked successfully.'));
    }

    public function markDeparture(Request $request, WorkerAttendance $attendance)
    {
        $activeWorkDay = WorkDay::where('status', 'active')->first();
        if (!$activeWorkDay) {
            return back()->with('error_message', __('No active work day found.'));
        }

        $workDayDate = $activeWorkDay->start_time->format('Y-m-d');

        $request->validate([
            'departure_time' => [
                'required',
                'date',
                'after:arrival_time',
                function ($attribute, $value, $fail) use ($workDayDate) {
                    if (date('Y-m-d', strtotime($value)) !== $workDayDate) {
                        $fail(__('The departure date must be the same as the active work day date (:date).', ['date' => $workDayDate]));
                    }
                },
            ],
        ]);

        $attendance->update([
            'departure_time' => $request->departure_time,
        ]);

        return back()->with('success_message', __('Departure time recorded successfully.'));
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
