<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Consumption;
use App\Models\Currency;
use App\Models\WorkDay;
use Illuminate\Http\Request;

class WorkDayController extends Controller
{
    public function index()
    {
        $workDays = WorkDay::with('openedBy', 'closedBy')->orderBy('id', 'desc')->get();
        $activeWorkDay = WorkDay::where('status', 'active')->first();

        return view('Admin.WorkDays.index', compact('workDays', 'activeWorkDay'));
    }

    public function create()
    {
        $active = WorkDay::where('status', 'active')->first();
        if ($active) {
            return redirect()->route('admin.work_days.index')->with('error', __('You must close the currently active work day before starting a new one.'));
        }

        return view('Admin.WorkDays.create');
    }

    public function show($id)
    {
        $workDay = WorkDay::with([
            'supplies.currency',
            'workerShifts.worker',
            'workerShifts.currency',
            'workerTransactions.currency',
            'distributions.distributor',
            'distributions.currency',
            'distributorReturns.distributor',
            'distributorReturns.currency',
            'distributorTransactions.currency',
            'expenses.currency',
            'supplierPayments.currency',
        ])->findOrFail($id);

        $stats = $workDay->getStatistics();

        return view('Admin.WorkDays.close', array_merge(['workDay' => $workDay], $stats));
    }

    public function store(Request $request)
    {
        $active = WorkDay::where('status', 'active')->first();
        if ($active) {
            return redirect()->back()->with('error', __('You must close the currently active work day before starting a new one.'));
        }
        
        $startTime = now();
        
        if ($request->filled('custom_start_time')) {
            $startTime = \Carbon\Carbon::parse($request->custom_start_time);
            
            // Check if there is already a workday on this exact date
            $exists = WorkDay::whereDate('start_time', $startTime->toDateString())->exists();
            if ($exists) {
                return redirect()->back()->with('error', __('A work day already exists for the selected date. Please choose another date.'));
            }
        }

        $workDay = WorkDay::create([
            'opened_by' => auth()->guard('admin')->id(),
            'status' => 'active',
            'is_holiday' => $request->has('is_holiday'),
            'holiday_reason' => $request->holiday_reason,
            'start_time' => $startTime,
        ]);

        return redirect()->route('admin.work_days.index')->with('success', __('New Work Day started successfully.'));
    }

    public function showCloseForm(WorkDay $workDay)
    {
        if ($workDay->status === 'closed') {
            return redirect()->route('admin.work_days.index')->with('error_message', __('Work day is already closed.'));
        }

        $workDay->load([
            'supplies.currency',
            'supplies.category',
            'workerShifts.worker',
            'workerShifts.currency',
            'workerTransactions.currency',
            'distributions.distributor',
            'distributions.currency',
            'distributorReturns.distributor',
            'distributorReturns.currency',
            'distributorTransactions.currency',
            'expenses.currency',
            'supplierPayments.currency',
        ]);

        return view('Admin.WorkDays.close', array_merge(['workDay' => $workDay], $workDay->getStatistics()));
    }


    public function close(Request $request, WorkDay $workDay)
    {
        if ($workDay->workerShifts()->whereNull('check_out')->exists()) {
            return back()->withInput()->with('error_message', __('There are employees still clocked in. Please ensure you have recorded their returned bread and cash totals before finalizing the day.'));
        }

        $request->validate([
            'carried_over_money' => 'required|numeric|min:0',
            'carried_over_currency_id' => 'nullable|exists:currencies,id',
            'carried_over_exchange_rate' => 'nullable|numeric|min:0',
            'total_expenses_at_close' => 'required|numeric',
            'total_sales_at_close' => 'required|numeric',
            'consumptions' => 'nullable|array',
            'consumptions.*' => 'nullable|numeric|min:0',
        ]);

        // Validate consumptions against available stock
        if ($request->consumptions) {
            foreach ($request->consumptions as $categoryId => $quantity) {
                if ($quantity > 0) {
                    $cat = Category::withSum('supplies', 'quantity')
                        ->withSum('consumptions', 'quantity')
                        ->find($categoryId);
                        
                    if ($cat) {
                        $available = ($cat->supplies_sum_quantity ?? 0) - ($cat->consumptions_sum_quantity ?? 0);
                        // Using a small tolerance to handle floating point errors
                        if ($quantity > ($available + 0.05)) {
                            return back()->withInput()->with('error_message', __("Recorded consumption for category :name (:quantity) exceeds strictly available stock (:available).", [
                                'name' => __($cat->name),
                                'quantity' => round($quantity, 2),
                                'available' => round($available, 2)
                            ]));
                        }
                    }
                }
            }
        }

        // Use centralized statistics for final checks and carry-over calculation
        $stats = $workDay->getStatistics();
        $calculatedBundles = $stats['calculatedRemainingBundles'];

        // Resolve exchange rate
        $exchangeRate = 1;
        if ($request->carried_over_currency_id) {
            $currency = Currency::find($request->carried_over_currency_id);
            if ($currency && ! $currency->is_default) {
                $exchangeRate = $request->carried_over_exchange_rate ?? $currency->exchange_rate;
            }
        }

        $endTime = now();
        if ($request->filled('custom_end_time')) {
            $parsedEndTime = \Carbon\Carbon::parse($request->custom_end_time);
            if ($parsedEndTime->lt($workDay->start_time)) {
                return back()->withInput()->with('error_message', __('The end time cannot be before the start time.'));
            }
            $endTime = $parsedEndTime;
        }

        $workDay->update([
            'status' => 'closed',
            'end_time' => $endTime,
            'closed_by' => auth()->guard('admin')->id(),
            'total_expenses_at_close' => Currency::revertToBase($request->total_expenses_at_close),
            'total_sales_at_close' => Currency::revertToBase($request->total_sales_at_close),
            'carried_over_bundles' => $calculatedBundles,
            'carried_over_money' => $request->carried_over_money,
            'carried_over_currency_id' => $request->carried_over_currency_id,
            'carried_over_exchange_rate' => $exchangeRate,
        ]);

        // Auto-close logic removed — shifts must be closed manually before finalizing the day

        // Store Consumption Records
        if ($request->consumptions) {
            foreach ($request->consumptions as $categoryId => $quantity) {
                if ($quantity > 0) {
                    Consumption::create([
                        'work_day_id' => $workDay->id,
                        'category_id' => $categoryId,
                        'quantity' => $quantity,
                    ]);
                }
            }
        }

        return redirect()->route('admin.work_days.index')->with('success_message', __('Work Day closed successfully. All operations frozen.'));
    }
}
