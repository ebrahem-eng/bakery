<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Supplier;
use App\Models\SupplierMobile;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    public function index()
    {
        $suppliers = Supplier::with('mobiles', 'categories')->orderBy('id', 'desc')->get();

        return view('Admin.Suppliers.index', compact('suppliers'));
    }

    public function show(Supplier $supplier)
    {
        $supplier->load(['mobiles', 'categories', 'supplies.category', 'supplies.currency', 'supplies.workDay.openedBy']);

        return view('Admin.Suppliers.show', compact('supplier'));
    }

    public function create()
    {
        $categories = Category::where('is_active', true)->get();

        return view('Admin.Suppliers.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'categories' => 'required|array',
            'mobiles' => 'array',
        ]);

        $supplier = Supplier::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'title' => $request->title,
        ]);

        if ($request->has('mobiles')) {
            foreach ($request->mobiles as $number) {
                if (! empty($number)) {
                    SupplierMobile::create(['supplier_id' => $supplier->id, 'mobile_number' => $number]);
                }
            }
        }

        $supplier->categories()->sync($request->categories);

        return redirect()->route('admin.suppliers.index')->with('success', __('Supplier profile established.'));
    }

    public function edit(Supplier $supplier)
    {
        $supplier->load('mobiles', 'categories');
        $categories = Category::where('is_active', true)->get();

        return view('Admin.Suppliers.edit', compact('supplier', 'categories'));
    }

    public function update(Request $request, Supplier $supplier)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'categories' => 'required|array',
            'mobiles' => 'array',
        ]);

        $supplier->update([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'title' => $request->title,
        ]);

        if ($request->has('mobiles')) {
            $supplier->mobiles()->delete();
            foreach ($request->mobiles as $number) {
                if (! empty($number)) {
                    SupplierMobile::create(['supplier_id' => $supplier->id, 'mobile_number' => $number]);
                }
            }
        }

        $supplier->categories()->sync($request->categories);

        return redirect()->route('admin.suppliers.index')->with('success', __('Supplier profile updated.'));
    }

    public function destroy(Supplier $supplier)
    {
        $supplier->delete();

        return redirect()->route('admin.suppliers.index')->with('success', __('Supplier permanently removed.'));
    }
}
