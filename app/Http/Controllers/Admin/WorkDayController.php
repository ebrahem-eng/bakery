<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\WorkDay;

class WorkDayController extends Controller
{
    public function index()
    {
        $workDays = WorkDay::with('openedBy', 'closedBy')->orderBy('id', 'desc')->get();
        $activeWorkDay = WorkDay::where('status', 'active')->first();
        return view('Admin.WorkDays.index', compact('workDays', 'activeWorkDay'));
    }

    public function store(Request $request)
    {
        $active = WorkDay::where('status', 'active')->first();
        if ($active) {
            return redirect()->back()->with('error', __('You must close the currently active work day before starting a new one.'));
        }

        $workDay = WorkDay::create([
            'opened_by' => auth()->guard('admin')->id(),
            'status' => 'active',
            'is_holiday' => $request->has('is_holiday'),
            'holiday_reason' => $request->holiday_reason,
            'start_time' => now()
        ]);

        return redirect()->route('admin.work_days.index')->with('success', __('New Work Day started successfully.'));
    }

    public function showCloseForm(WorkDay $workDay)
    {
        if ($workDay->status === 'closed') {
            return redirect()->route('admin.work_days.index')->with('error', __('Work day is already closed.'));
        }
        
        // Note: Future dependency injection of consumed materials, shifts, cash collections will be built here
        return view('Admin.WorkDays.close', compact('workDay'));
    }

    public function close(Request $request, WorkDay $workDay)
    {
        $request->validate([
            'carried_over_bundles' => 'required|numeric',
            'carried_over_money' => 'required|numeric'
        ]);

        $workDay->update([
            'status' => 'closed',
            'end_time' => now(),
            'closed_by' => auth()->guard('admin')->id(),
            'total_expenses_at_close' => $request->total_expenses ?? 0,
            'total_sales_at_close' => $request->total_sales ?? 0,
            'carried_over_bundles' => $request->carried_over_bundles,
            'carried_over_money' => $request->carried_over_money,
        ]);

        return redirect()->route('admin.work_days.index')->with('success', __('Work Day closed successfully.'));
    }
}
