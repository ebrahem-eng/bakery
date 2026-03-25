<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Distributor;
use App\Models\Distribution;
use App\Models\DistributorReturn;
use App\Models\DistributorTransaction;
use App\Models\WorkDay;
use Illuminate\Http\Request;

class DistributionController extends Controller
{
    public function index()
    {
        $activeWorkDay = WorkDay::where('status', 'active')->first();
        if (!$activeWorkDay) {
            return redirect()->route('admin.dashboard')->with('error_message', 'No active work day found. Please start a day first.');
        }

        $distributors = Distributor::with(['mobiles', 'currency', 'distributions', 'returns', 'transactions'])->get();

        return view('Admin.Distributions.index', compact('distributors', 'activeWorkDay'));
    }

    public function storeDistribution(Request $request)
    {
        $request->validate([
            'distributor_id' => 'required|exists:distributors,id',
            'bundle_count' => 'required|integer|min:1',
            'price_per_bundle' => 'required|numeric|min:0',
            'amount_paid' => 'required|numeric|min:0',
            'notes' => 'nullable|string'
        ]);

        $activeWorkDay = WorkDay::where('status', 'active')->first();
        if (!$activeWorkDay) return back()->with('error_message', 'No active work day.');

        $distributor = Distributor::findOrFail($request->distributor_id);
        $total_price = $request->bundle_count * $request->price_per_bundle;
        $rate = $distributor->currency->is_local ? 1 : $distributor->currency->exchange_rate;

        Distribution::create([
            'distributor_id' => $distributor->id,
            'work_day_id' => $activeWorkDay->id,
            'bundle_count' => $request->bundle_count,
            'price_per_bundle' => $request->price_per_bundle,
            'total_price' => $total_price,
            'currency_id' => $distributor->preferred_currency_id,
            'exchange_rate' => $rate,
            'amount_paid' => $request->amount_paid,
            'notes' => $request->notes,
            'created_by' => auth('admin')->id(),
        ]);

        return back()->with('success_message', __('Distribution recorded successfully.'));
    }

    public function storeReturn(Request $request)
    {
        $request->validate([
            'distributor_id' => 'required|exists:distributors,id',
            'bundle_count' => 'required|integer|min:1',
            'refund_per_bundle' => 'required|numeric|min:0',
            'notes' => 'nullable|string'
        ]);

        $activeWorkDay = WorkDay::where('status', 'active')->first();
        if (!$activeWorkDay) return back()->with('error_message', 'No active work day.');

        $distributor = Distributor::findOrFail($request->distributor_id);
        $total_refund = $request->bundle_count * $request->refund_per_bundle;
        $rate = $distributor->currency->is_local ? 1 : $distributor->currency->exchange_rate;

        DistributorReturn::create([
            'distributor_id' => $distributor->id,
            'work_day_id' => $activeWorkDay->id,
            'bundle_count' => $request->bundle_count,
            'refund_per_bundle' => $request->refund_per_bundle,
            'total_refund' => $total_refund,
            'currency_id' => $distributor->preferred_currency_id,
            'exchange_rate' => $rate,
            'notes' => $request->notes,
            'created_by' => auth('admin')->id(),
        ]);

        return back()->with('success_message', __('Returns recorded successfully.'));
    }

    public function storeTransaction(Request $request)
    {
        $request->validate([
            'distributor_id' => 'required|exists:distributors,id',
            'type' => 'required|in:payment,discount,other_credit',
            'amount' => 'required|numeric|min:0.01',
            'notes' => 'nullable|string'
        ]);

        $activeWorkDay = WorkDay::where('status', 'active')->first();
        if (!$activeWorkDay) return back()->with('error_message', 'No active work day.');

        $distributor = Distributor::findOrFail($request->distributor_id);
        $rate = $distributor->currency->is_local ? 1 : $distributor->currency->exchange_rate;

        DistributorTransaction::create([
            'distributor_id' => $distributor->id,
            'work_day_id' => $activeWorkDay->id,
            'type' => $request->type,
            'amount' => $request->amount,
            'currency_id' => $distributor->preferred_currency_id,
            'exchange_rate' => $rate,
            'notes' => $request->notes,
            'created_by' => auth('admin')->id(),
        ]);

        return back()->with('success_message', __('Transaction recorded successfully.'));
    }
}
