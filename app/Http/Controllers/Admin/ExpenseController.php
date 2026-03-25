<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use App\Models\WorkDay;
use App\Models\Currency;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    public function index()
    {
        $activeWorkDay = WorkDay::where('status', 'active')->first();
        if (!$activeWorkDay) {
            return redirect()->route('admin.dashboard')->with('error_message', 'No active work day found. Please start a day first.');
        }

        $expenses = Expense::with('currency')->where('work_day_id', $activeWorkDay->id)->orderBy('id', 'desc')->get();
        $currencies = Currency::all();
        $defaultCurrency = \App\Models\Currency::where('is_default', true)->first();

        return view('Admin.Expenses.index', compact('expenses', 'currencies', 'activeWorkDay', 'defaultCurrency'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'category' => 'required|in:personal,operating,logistics,other',
            'title' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0.01',
            'currency_id' => 'required|exists:currencies,id',
            'exchange_rate' => 'nullable|numeric|min:0.000001',
            'notes' => 'nullable|string'
        ]);

        $activeWorkDay = WorkDay::where('status', 'active')->first();
        if (!$activeWorkDay) return back()->with('error_message', 'No active work day.');

        $currency = Currency::findOrFail($request->currency_id);
        
        // Automated Conversion Logic
        $rate = $request->exchange_rate;
        if (!$rate) {
            if ($currency->code === 'SYPN' || $currency->is_default) {
                $rate = 1.0;
            } elseif ($currency->code === 'SYPO') {
                $rate = 0.01; // The "Remove 00" rule: 120,000 SYPO = 1,200 SYPN
            } else {
                $rate = $currency->exchange_rate;
            }
        }

        Expense::create([
            'work_day_id' => $activeWorkDay->id,
            'admin_id' => auth()->id(),
            'category' => $request->category,
            'title' => $request->title,
            'amount' => $request->amount,
            'currency_id' => $currency->id,
            'exchange_rate' => $rate,
            'notes' => $request->notes,
        ]);

        return back()->with('success_message', __('Expense registered successfully.'));
    }

    public function show(Expense $expense)
    {
        $expense->load(['currency', 'workDay', 'admin']);
        return view('Admin.Expenses.show', compact('expense'));
    }

    public function destroy(Expense $expense)
    {
        $activeWorkDay = WorkDay::where('status', 'active')->first();
        // Additional security layer: Only allow deletion if the expense belongs to current active day.
        // Normally, past historical ledgers shouldn't be edited freely.
        if (!$activeWorkDay || $expense->work_day_id !== $activeWorkDay->id) {
            return back()->with('error_message', __('Cannot delete past constraints.'));
        }

        $expense->delete();
        return back()->with('success_message', __('Expense record removed completely.'));
    }
}
