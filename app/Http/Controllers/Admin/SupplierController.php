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

    public function show(Request $request, Supplier $supplier)
    {
        // 1. Handle Deliveries Filtering
        $deliveryStart = $request->get('delivery_start');
        $deliveryEnd = $request->get('delivery_end');

        $suppliesQuery = $supplier->supplies()->with(['category', 'currency', 'workDay.openedBy', 'admin']);
        
        if ($deliveryStart) {
            $suppliesQuery->whereDate('created_at', '>=', $deliveryStart);
        }
        if ($deliveryEnd) {
            $suppliesQuery->whereDate('created_at', '<=', $deliveryEnd);
        }
        
        $supplies = $suppliesQuery->orderBy('id', 'desc')->get();

        // 2. Handle Payments Filtering & Unification
        $paymentStart = $request->get('payment_start');
        $paymentEnd = $request->get('payment_end');

        // Aggregation A: Initial Payments on Supplies
        $initialPayments = $supplier->supplies()
            ->where('paid_amount', '>', 0)
            ->when($paymentStart, fn($q) => $q->whereDate('created_at', '>=', $paymentStart))
            ->when($paymentEnd, fn($q) => $q->whereDate('created_at', '<=', $paymentEnd))
            ->with(['paidCurrency', 'currency', 'admin'])
            ->get()
            ->map(function ($s) {
                $amount = (float) $s->paid_amount;
                $rate = (float) ($s->paid_exchange_rate ?? $s->exchange_rate ?? 1);
                return (object) [
                    'date' => $s->created_at,
                    'type' => 'initial_payment',
                    'amount' => $amount,
                    'currency' => $s->paidCurrency ?? $s->currency,
                    'exchange_rate' => $rate,
                    'base_amount' => \App\Models\Currency::convertAmount($amount, $rate),
                    'supply_id' => $s->id,
                    'admin_name' => ($s->admin->first_name ?? '') . ' ' . ($s->admin->last_name ?? ''),
                ];
            });

        // Aggregation B: Subsequent Debt Settlements
        $settlementPayments = \App\Models\SupplierPayment::whereIn('supply_id', $supplier->supplies()->pluck('id'))
            ->when($paymentStart, fn($q) => $q->whereDate('created_at', '>=', $paymentStart))
            ->when($paymentEnd, fn($q) => $q->whereDate('created_at', '<=', $paymentEnd))
            ->with(['currency', 'admin', 'supply'])
            ->get()
            ->map(function ($p) {
                $amount = (float) $p->amount;
                $rate = (float) ($p->exchange_rate ?? 1);
                return (object) [
                    'date' => $p->created_at,
                    'type' => 'settlement',
                    'amount' => $amount,
                    'currency' => $p->currency,
                    'exchange_rate' => $rate,
                    'base_amount' => \App\Models\Currency::convertAmount($amount, $rate),
                    'supply_id' => $p->supply_id,
                    'admin_name' => ($p->admin->first_name ?? '') . ' ' . ($p->admin->last_name ?? ''),
                ];
            });

        $allPayments = $initialPayments->concat($settlementPayments)
            ->sortByDesc('date');

        $supplier->load(['mobiles', 'categories']);

        return view('Admin.Suppliers.show', compact('supplier', 'supplies', 'allPayments', 'deliveryStart', 'deliveryEnd', 'paymentStart', 'paymentEnd'));
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
