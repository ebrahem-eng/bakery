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
                $category->current_stock = ($category->total_in ?? 0) - ($category->total_out ?? 0);

                return $category;
            });

        // Recent supplies and consumptions for history
        $recentSupplies = Supply::with(['category', 'supplier', 'workDay'])
            ->orderBy('created_at', 'desc')
            ->paginate(10, ['*'], 'supplies_page');

        $recentConsumptions = Consumption::with(['category', 'workDay'])
            ->orderBy('created_at', 'desc')
            ->paginate(10, ['*'], 'consumptions_page');

        return view('Admin.Warehouse.index', compact('categories', 'recentSupplies', 'recentConsumptions'));
    }
}
