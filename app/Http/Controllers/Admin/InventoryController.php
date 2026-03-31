<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Inventory;
use App\Models\InventoryItem;
use App\Models\WorkDay;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InventoryController extends Controller
{
    public function create()
    {
        // Calculate the current system expected stock dynamically
        $categories = Category::where('is_active', true)
            ->withSum('supplies as total_in', 'quantity')
            ->withSum('consumptions as total_out', 'quantity')
            ->get()
            ->map(function ($category) {
                // Get previous adjustments
                $adjustments = InventoryItem::where('category_id', $category->id)->sum('adjustment_quantity');

                // Expected stock formula
                $category->system_stock = ($category->total_in ?? 0) - ($category->total_out ?? 0) + $adjustments;
                
                // Fetch the latest unit price recorded for this category (either from last supply or last stocktake)
                $lastItem = InventoryItem::where('category_id', $category->id)->orderByDesc('id')->first();
                $category->latest_price = $lastItem ? $lastItem->unit_price : 0;
                
                return $category;
            });

        return view('Admin.Warehouse.inventory.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'notes' => 'nullable|string',
            'items' => 'required|array',
            'items.*.category_id' => 'required|exists:categories,id',
            'items.*.system_quantity' => 'required|numeric',
            'items.*.actual_quantity' => 'required|numeric|min:0',
            'items.*.unit_price' => 'required|numeric|min:0',
        ]);

        $activeWorkDay = WorkDay::where('status', 'active')->first();

        // Transaction to ensure data integrity
        DB::transaction(function () use ($request, $activeWorkDay) {
            $inventory = Inventory::create([
                'admin_id' => auth()->id(),
                'work_day_id' => $activeWorkDay ? $activeWorkDay->id : null,
                'status' => 'completed',
                'notes' => $request->notes,
                'total_value' => 0, // We will calculate this
            ]);

            $totalValue = 0;

            foreach ($request->items as $itemData) {
                // Determine adjustment value
                $adjustedQty = $itemData['actual_quantity'] - $itemData['system_quantity'];
                
                // Calculate valuation based on new actual quantity and new price
                $lineValue = $itemData['actual_quantity'] * $itemData['unit_price'];

                InventoryItem::create([
                    'inventory_id' => $inventory->id,
                    'category_id' => $itemData['category_id'],
                    'system_quantity' => $itemData['system_quantity'],
                    'actual_quantity' => $itemData['actual_quantity'],
                    'adjustment_quantity' => $adjustedQty,
                    'unit_price' => $itemData['unit_price'],
                    'line_total_value' => $lineValue,
                ]);

                $totalValue += $lineValue;
            }

            $inventory->update(['total_value' => $totalValue]);
        });

        return redirect()->route('admin.warehouse.index')->with('success_message', __('Inventory saved. System quantities and valuations have been updated!'));
    }
}
