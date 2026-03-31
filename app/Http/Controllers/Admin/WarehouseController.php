<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Consumption;
use App\Models\Supply;
use Illuminate\Http\Request;

class WarehouseController extends Controller
{
    public function index(Request $request)
    {
        // Get all active categories with their totals
        $categories = Category::where('is_active', true)
            ->withSum('supplies as total_in', 'quantity')
            ->withSum('consumptions as total_out', 'quantity')
            ->get()
            ->map(function ($category) {
                // Get previous adjustments
                $adjustments = \App\Models\InventoryItem::where('category_id', $category->id)->sum('adjustment_quantity');
                
                $category->current_stock = ($category->total_in ?? 0) - ($category->total_out ?? 0) + $adjustments;

                // Value calculation based on latest inventory or supply
                $lastInventory = \App\Models\InventoryItem::where('category_id', $category->id)->orderByDesc('id')->first();
                $category->unit_price = $lastInventory ? $lastInventory->unit_price : 0;
                $category->total_value = $category->current_stock * $category->unit_price;

                return $category;
            });

        $totalWarehouseValue = $categories->sum('total_value');
        $currencyCode = \App\Models\Currency::where('is_default', true)->first()->code ?? 'SYP';

        // Recent supplies and consumptions for history
        $recentSupplies = Supply::with(['category', 'supplier', 'workDay'])
            ->orderBy('created_at', 'desc')
            ->paginate(10, ['*'], 'supplies_page');

        $recentConsumptions = Consumption::with(['category', 'workDay'])
            ->orderBy('created_at', 'desc')
            ->paginate(10, ['*'], 'consumptions_page');

        return view('Admin.Warehouse.index', compact('categories', 'recentSupplies', 'recentConsumptions', 'totalWarehouseValue', 'currencyCode'));
    }
}
