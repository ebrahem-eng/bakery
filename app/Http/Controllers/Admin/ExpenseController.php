<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Currency;
use App\Models\Expense;
use App\Models\WorkDay;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    public function index()
    {
        $activeWorkDay = WorkDay::where('status', 'active')->first();
        if (! $activeWorkDay) {
            return redirect()->route('admin.dashboard')->with('error_message', 'No active work day found. Please start a day first.');
        }

        $expenses = Expense::where('work_day_id', $activeWorkDay->id)
            ->with('currency')
            ->orderBy('id', 'desc')
            ->get();

        $currencies = Currency::all();
        $defaultCurrency = Currency::where('is_default', true)->first();

        $workerPaymentsLocal = \App\Models\WorkerTransaction::where('work_day_id', $activeWorkDay->id)
            ->whereIn('type', ['salary', 'wage', 'bonus', 'advance', 'allowance'])
            ->get()
            ->sum(fn($t) => Currency::convertAmount($t->amount, $t->exchange_rate));

        return view('Admin.Expenses.index', compact('expenses', 'currencies', 'activeWorkDay', 'defaultCurrency', 'workerPaymentsLocal'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'category' => 'required|in:personal,operating,logistics,other,bread',
            'quantity' => 'nullable|required_if:category,bread|integer|min:1',
            'unit_price' => 'nullable|required_if:category,bread|numeric|min:0.01',
            'title' => 'nullable|required_unless:category,bread|string|max:255',
            'amount' => 'nullable|required_unless:category,bread|numeric|min:0.01',
            'currency_id' => 'required|exists:currencies,id',
            'exchange_rate' => 'nullable|numeric|min:0.000001',
            'notes' => 'nullable|string',
        ]);

        $activeWorkDay = WorkDay::where('status', 'active')->first();
        if (! $activeWorkDay) {
            return back()->with('error_message', 'No active work day.');
        }

        $currency = Currency::findOrFail($request->currency_id);

        // Automated Conversion Logic
        $rate = $request->exchange_rate;
        if (! $rate) {
            if ($currency->code === 'SYP' || $currency->is_default) {
                $rate = 1.0;
            } else {
                $rate = $currency->exchange_rate;
            }
        }

        $amount = $request->amount;
        $title = $request->title;
        $notes = $request->notes;

        if ($request->category === 'bread') {
            $amount = $request->quantity * $request->unit_price;
            $title = __('Bread Expense') . ' - ' . $request->quantity . ' ' . __('bundles');
            $notes = $request->notes ?: ($request->quantity . ' ' . __('bundles') . ' @ ' . number_format($request->unit_price, 2) . ' ' . $currency->code);
        }

        Expense::create([
            'work_day_id' => $activeWorkDay->id,
            'admin_id' => auth()->id(),
            'category' => $request->category,
            'quantity' => $request->category === 'bread' ? $request->quantity : null,
            'unit_price' => $request->category === 'bread' ? $request->unit_price : null,
            'title' => $title,
            'amount' => $amount,
            'currency_id' => $currency->id,
            'exchange_rate' => $rate,
            'notes' => $notes,
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
        if (! $activeWorkDay || $expense->work_day_id !== $activeWorkDay->id) {
            return back()->with('error_message', __('Cannot delete past constraints.'));
        }

        $expense->delete();

        return back()->with('success_message', __('Expense record removed completely.'));
    }
}
