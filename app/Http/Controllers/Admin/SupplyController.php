<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Currency;
use App\Models\Supplier;
use App\Models\Supply;
use App\Models\WorkDay;
use Illuminate\Http\Request;

class SupplyController extends Controller
{
    public function index()
    {
        $supplies = Supply::with('supplier', 'category', 'currency', 'admin', 'workDay')->orderBy('id', 'desc')->get();
        $currencyCode = Currency::where('is_default', true)->value('code') ?? 'SYP';

        return view('Admin.Supplies.index', compact('supplies', 'currencyCode'));
    }

    public function create()
    {
        $activeWorkDay = WorkDay::where('status', 'active')->first();
        if (! $activeWorkDay) {
            return redirect()->route('admin.dashboard')->with('error_message', __('You must start a new Work Day before adding supplies.'));
        }

        $suppliers = Supplier::with('categories')->get();
        // Passing suppliers with categories to filter selections via Alpine
        $categories = Category::where('is_active', true)->get();
        $currencies = Currency::all();

        return view('Admin.Supplies.create', compact('activeWorkDay', 'suppliers', 'categories', 'currencies'));
    }

    public function show($id)
    {
        $supply = Supply::with(['supplier', 'category', 'currency', 'unloadingFeeCurrency', 'admin', 'workDay'])->findOrFail($id);
        $currencyCode = Currency::where('is_default', true)->value('code') ?? 'SYP';

        return view('Admin.Supplies.show', compact('supply', 'currencyCode'));
    }

    public function store(Request $request)
    {
        $activeWorkDay = WorkDay::where('status', 'active')->first();
        if (! $activeWorkDay) {
            return redirect()->route('admin.dashboard')->with('error_message', __('No active work day found.'));
        }

        $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'supplies' => 'required|array|min:1',
            'supplies.*.category_id' => 'required|exists:categories,id',
            'supplies.*.currency_id' => 'required|exists:currencies,id',
            'supplies.*.exchange_rate' => 'required|numeric|min:0.01',
            'supplies.*.quantity' => 'required|numeric|min:0',
            'supplies.*.unit_price' => 'required|numeric|min:0',
            'supplies.*.paid_amount' => 'required|numeric|min:0',
            'supplies.*.paid_currency_id' => 'required|exists:currencies,id',
            'supplies.*.paid_exchange_rate' => 'required|numeric|min:0.01',
            'supplies.*.unloading_fee' => 'required|numeric|min:0',
            'supplies.*.unloading_fee_payer' => 'required|in:bakery,supplier',
            'supplies.*.unloading_fee_currency_id' => 'nullable|exists:currencies,id',
            'supplies.*.unloading_fee_exchange_rate' => 'nullable|numeric|min:0.01',
            'supplies.*.material_type_name' => 'nullable|string',
            'supplies.*.boxes_count' => 'nullable|numeric|min:1',
            'supplies.*.box_weight' => 'nullable|numeric|min:0.01',
            'supplies.*.bags_count' => 'nullable|integer|min:1',
            'supplies.*.bag_weight' => 'nullable|numeric|min:0.01',
            'supplies.*.molds_per_carton' => 'nullable|integer|min:1',
            'supplies.*.bag_type' => 'nullable|string',
            'supplies.*.notes' => 'nullable|string',
            'supplies.*.due_date' => 'nullable|date',
        ]);

        foreach ($request->supplies as $item) {
            $category = Category::find($item['category_id']);
            $inputMode = $category->input_mode ?? 'simple_quantity';

            // Calculate total_cost based on material type
            if ($inputMode === 'bags_weight') {
                // Flour: (total_kg / 1000) * price_per_ton
                $totalKg = ($item['bags_count'] ?? 0) * ($item['bag_weight'] ?? 50);
                $total_cost = ($totalKg / 1000) * ($item['unit_price'] ?? 0);
            } elseif ($inputMode === 'cartons_molds') {
                // Yeast: cartons * price_per_carton
                $total_cost = ($item['boxes_count'] ?? 0) * ($item['unit_price'] ?? 0);
            } else {
                // Salt, Diesel, Bags: quantity * unit_price
                $total_cost = ($item['quantity'] ?? 0) * ($item['unit_price'] ?? 0);
            }

            $paid = $item['paid_amount'];
            $fee_currency = ! empty($item['unloading_fee_currency_id']) ? $item['unloading_fee_currency_id'] : $item['currency_id'];

            Supply::create([
                'admin_id' => auth()->guard('admin')->id(),
                'work_day_id' => $activeWorkDay->id,
                'supplier_id' => $request->supplier_id,
                'category_id' => $item['category_id'],
                'currency_id' => $item['currency_id'],
                'exchange_rate' => $item['exchange_rate'],
                'quantity' => $item['quantity'],
                'unit_price' => $item['unit_price'],
                'total_cost' => $total_cost,
                'paid_amount' => $paid,
                'paid_currency_id' => $item['paid_currency_id'],
                'paid_exchange_rate' => $item['paid_exchange_rate'],
                'unloading_fee' => $item['unloading_fee'],
                'unloading_fee_payer' => $item['unloading_fee_payer'] ?? 'bakery',
                'unloading_fee_currency_id' => $fee_currency,
                'unloading_fee_exchange_rate' => $item['unloading_fee_exchange_rate'] ?? 1,
                'material_type_name' => $item['material_type_name'] ?? null,
                'boxes_count' => $item['boxes_count'] ?? null,
                'box_weight' => $item['box_weight'] ?? null,
                'bags_count' => $item['bags_count'] ?? null,
                'bag_weight' => $item['bag_weight'] ?? null,
                'molds_per_carton' => $item['molds_per_carton'] ?? null,
                'bag_type' => $item['bag_type'] ?? null,
                'notes' => $item['notes'] ?? null,
                'due_date' => $item['due_date'] ?? null,
            ]);
        }

        return redirect()->route('admin.supplies.index')->with('success', __('Supply registered and mapped securely into the Active Work Day ledger.'));
    }

    public function showPaymentForm(Supply $supply)
    {
        $supply->load(['supplier', 'category', 'currency', 'paidCurrency']);
        $currencies = \App\Models\Currency::all();
        $defaultCurrency = \App\Models\Currency::where('is_default', true)->first();
        $currencyCode = $defaultCurrency->code ?? 'SYP';

        return view('Admin.Supplies.pay', compact('supply', 'currencies', 'currencyCode'));
    }

    public function registerPayment(Request $request, Supply $supply)
    {
        $request->validate([
            'payment_amount' => 'required|numeric|min:0.01',
            'payment_currency_id' => 'required|exists:currencies,id',
            'payment_exchange_rate' => 'required|numeric|min:0.01',
        ]);

        $activeWorkDay = \App\Models\WorkDay::where('status', 'active')->first();
        if (!$activeWorkDay) {
            return redirect()->back()->with('error_message', __('No active work day to log payment.'));
        }

        \App\Models\SupplierPayment::create([
            'supply_id' => $supply->id,
            'work_day_id' => $activeWorkDay->id,
            'admin_id' => auth()->id(),
            'amount' => $request->payment_amount,
            'currency_id' => $request->payment_currency_id,
            'exchange_rate' => $request->payment_exchange_rate,
        ]);

        // If the entire debt is essentially paid within a 1 currency unit rounding error
        if ($supply->unpaid_amount <= 1) {
            $supply->due_date = null; // Cleared if fully paid
            $supply->save();
        }

        return redirect()->route('admin.accounts.debts')->with('success', __('Payment logged and linked to the supplier balance.'));
    }
}
