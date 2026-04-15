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
        
        $perPage = 10;
        
        $supplies = $suppliesQuery->orderBy('id', 'desc')->paginate($perPage, ['*'], 'deliveries_page');
        $supplies->appends($request->all());

        // 2. Handle Payments Filtering & Unification
        $paymentStart = $request->get('payment_start');
        $paymentEnd = $request->get('payment_end');

        // Aggregation: Initial Payments & Subsequent Debt Settlements
        $initialPayments = collect();

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

        $allPaymentsCollection = $initialPayments->concat($settlementPayments)->sortByDesc('date');
        $paymentsPage = \Illuminate\Pagination\Paginator::resolveCurrentPage('payments_page');
        
        $allPayments = new \Illuminate\Pagination\LengthAwarePaginator(
            $allPaymentsCollection->forPage($paymentsPage, $perPage),
            $allPaymentsCollection->count(),
            $perPage,
            $paymentsPage,
            [
                'path' => \Illuminate\Pagination\Paginator::resolveCurrentPath(),
                'pageName' => 'payments_page',
            ]
        );
        $allPayments->appends($request->all());

        $supplier->load(['mobiles', 'categories']);

        // 3. Handle Supplier-Paid Delivery Fees
        $feeStart = $request->get('fee_start');
        $feeEnd = $request->get('fee_end');

        $supplierFeesQuery = $supplier->supplies()
            ->where('unloading_fee_payer', 'supplier')
            ->where('unloading_fee', '>', 0)
            ->with(['category', 'currency', 'unloadingFeeCurrency']);

        if ($feeStart) {
            $supplierFeesQuery->whereDate('created_at', '>=', $feeStart);
        }
        if ($feeEnd) {
            $supplierFeesQuery->whereDate('created_at', '<=', $feeEnd);
        }

        $supplierFees = $supplierFeesQuery->orderBy('id', 'desc')->paginate($perPage, ['*'], 'fees_page');
        $supplierFees->appends($request->all());

        return view('Admin.Suppliers.show', compact('supplier', 'supplies', 'allPayments', 'supplierFees', 'deliveryStart', 'deliveryEnd', 'paymentStart', 'paymentEnd', 'feeStart', 'feeEnd'));
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
