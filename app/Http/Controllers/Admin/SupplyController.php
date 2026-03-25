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
            'supplies' => 'required|array|min:1',
            'supplies.*.category_id' => 'required|exists:categories,id',
            'supplies.*.currency_id' => 'required|exists:currencies,id',
            'supplies.*.exchange_rate' => 'required|numeric|min:0.01',
            'supplies.*.quantity' => 'required|numeric|min:0.001',
            'supplies.*.unit_price' => 'required|numeric|min:0',
            'supplies.*.paid_amount' => 'required|numeric|min:0',
            'supplies.*.unloading_fee' => 'required|numeric|min:0',
            'supplies.*.unloading_fee_payer' => 'required|in:bakery,supplier',
            'supplies.*.unloading_fee_currency_id' => 'nullable|exists:currencies,id',
            'supplies.*.material_type_name' => 'nullable|string',
            'supplies.*.boxes_count' => 'nullable|numeric|min:1',
            'supplies.*.box_weight' => 'nullable|numeric|min:0.01',
            'supplies.*.notes' => 'nullable|string',
        ]);

        foreach ($request->supplies as $item) {
            $total_cost = $item['quantity'] * $item['unit_price'];
            $paid = min($item['paid_amount'], $total_cost);
            
            $fee_currency = !empty($item['unloading_fee_currency_id']) ? $item['unloading_fee_currency_id'] : $item['currency_id'];

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
                'unloading_fee' => $item['unloading_fee'],
                'unloading_fee_payer' => $item['unloading_fee_payer'] ?? 'bakery',
                'unloading_fee_currency_id' => $fee_currency,
                'material_type_name' => $item['material_type_name'] ?? null,
                'boxes_count' => $item['boxes_count'] ?? null,
                'box_weight' => $item['box_weight'] ?? null,
                'notes' => $item['notes'] ?? null,
            ]);
        }

        return redirect()->route('admin.supplies.index')->with('success', __('Supply registered and mapped securely into the Active Work Day ledger.'));
    }
}
