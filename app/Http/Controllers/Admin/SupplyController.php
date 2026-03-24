<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Supply;
use App\Models\Supplier;
use App\Models\Category;
use App\Models\Currency;
use App\Models\WorkDay;

class SupplyController extends Controller
{
    public function index()
    {
        $supplies = Supply::with('supplier', 'category', 'currency', 'admin', 'workDay')->orderBy('id', 'desc')->get();
        return view('Admin.Supplies.index', compact('supplies'));
    }

    public function create()
    {
        $activeWorkDay = WorkDay::where('status', 'active')->first();
        if (!$activeWorkDay) {
            return redirect()->route('admin.dashboard')->with('error_message', __('You must start a new Work Day before adding supplies.'));
        }

        $suppliers = Supplier::with('categories')->get();
        // Passing suppliers with categories to filter selections via Alpine
        $categories = Category::where('is_active', true)->get();
        $currencies = Currency::all();

        return view('Admin.Supplies.create', compact('activeWorkDay', 'suppliers', 'categories', 'currencies'));
    }

    public function store(Request $request)
    {
        $activeWorkDay = WorkDay::where('status', 'active')->first();
        if (!$activeWorkDay) {
            return redirect()->route('admin.dashboard')->with('error_message', __('No active work day found.'));
        }

        $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'category_id' => 'required|exists:categories,id',
            'currency_id' => 'required|exists:currencies,id',
            'exchange_rate' => 'required|numeric|min:0.01',
            'quantity' => 'required|numeric|min:0.01',
            'unit_price' => 'required|numeric|min:0',
            'paid_amount' => 'required|numeric|min:0',
            'unloading_fee' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        $total_cost = $request->quantity * $request->unit_price;

        $paid = min($request->paid_amount, $total_cost);

        $supply = Supply::create([
            'admin_id' => auth()->guard('admin')->id(),
            'work_day_id' => $activeWorkDay->id,
            'supplier_id' => $request->supplier_id,
            'category_id' => $request->category_id,
            'currency_id' => $request->currency_id,
            'exchange_rate' => $request->exchange_rate,
            'quantity' => $request->quantity,
            'unit_price' => $request->unit_price,
            'total_cost' => $total_cost,
            'paid_amount' => $paid,
            'unloading_fee' => $request->unloading_fee,
            'notes' => $request->notes,
        ]);

        return redirect()->route('admin.supplies.index')->with('success', __('Supply registered and mapped securely into the Active Work Day ledger.'));
    }
}
