<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Currency;
use App\Models\Distributor;
use App\Models\DistributorMobile;
use App\Models\DistributorTransaction;
use App\Models\WorkDay;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class DistributorController extends Controller
{
    public function index()
    {
        $distributors = Distributor::with('mobiles', 'currency')->orderBy('id', 'desc')->get();
        $currencies = Currency::all();

        return view('Admin.Distributors.index', compact('distributors', 'currencies'));
    }

    public function create()
    {
        $currencies = Currency::all();

        return view('Admin.Distributors.create', compact('currencies'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'preferred_currency_id' => 'required|exists:currencies,id',
            'mobiles' => 'array',
        ]);

        $distributor = Distributor::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'title' => $request->title,
            'preferred_currency_id' => $request->preferred_currency_id,
            'notes' => $request->notes,
        ]);

        if ($request->has('mobiles')) {
            foreach ($request->mobiles as $number) {
                if (! empty($number)) {
                    DistributorMobile::create(['distributor_id' => $distributor->id, 'number' => $number]);
                }
            }
        }

        return redirect()->route('admin.distributors.index')->with('success_message', __('Distributor profile established successfully.'));
    }

    public function show(Request $request, Distributor $distributor)
    {
        $distributor->load(['mobiles', 'currency', 'distributions.createdBy', 'returns.createdBy', 'transactions.createdBy']);

        $activities = collect();

        // 1. Sales (Charges)
        foreach ($distributor->distributions as $d) {
            $activities->push([
                'date' => $d->created_at,
                'type' => 'Sale',
                'ref' => '#'.$d->id.' - '.$d->bundle_count.' '.__('Bundles'),
                'notes' => $d->notes,
                'debit' => Currency::convertAmount($d->total_price, $d->exchange_rate),
                'credit' => 0,
                'color' => 'blue',
                'admin' => $d->createdBy ? $d->createdBy->first_name : '--',
            ]);

            if ($d->amount_paid > 0) {
                $activities->push([
                    'date' => $d->created_at->addSecond(),
                    'type' => 'Payment',
                    'ref' => __('Down Payment for').' #'.$d->id,
                    'notes' => null,
                    'debit' => 0,
                    'credit' => Currency::convertAmount($d->amount_paid, $d->exchange_rate),
                    'color' => 'emerald',
                    'admin' => $d->createdBy ? $d->createdBy->first_name : '--',
                ]);
            }
        }

        // 2. Returns
        foreach ($distributor->returns as $r) {
            $activities->push([
                'date' => $r->created_at,
                'type' => 'Return',
                'ref' => '#'.$r->id.' - '.$r->bundle_count.' '.__('Bundles'),
                'notes' => $r->notes,
                'debit' => 0,
                'credit' => Currency::convertAmount($r->total_refund, $r->exchange_rate),
                'color' => 'amber',
                'admin' => $r->createdBy ? $r->createdBy->first_name : '--',
            ]);
        }

        // 3. Transactions
        foreach ($distributor->transactions as $t) {
            $activities->push([
                'date' => $t->created_at,
                'type' => ucfirst($t->type), // payment, discount, etc.
                'ref' => __('Direct Transaction'),
                'notes' => $t->notes,
                'debit' => 0,
                'credit' => Currency::convertAmount($t->amount, $t->exchange_rate),
                'color' => $t->type == 'payment' ? 'emerald' : ($t->type == 'discount' ? 'rose' : 'slate'),
                'admin' => $t->createdBy ? $t->createdBy->first_name : '--',
            ]);
        }

        // Filtering
        if ($request->filled('type')) {
            $activities = $activities->filter(fn ($a) => $a['type'] === $request->type);
        }
        if ($request->filled('search')) {
            $search = strtolower($request->search);
            $activities = $activities->filter(fn ($a) => str_contains(strtolower($a['ref']), $search));
        }

        // Running Balance Calculation (Chronological)
        $runningBalance = 0;
        $counter = 0;
        $sortedActivities = $activities->sortBy('date')->map(function ($activity) use (&$runningBalance, &$counter) {
            $counter++;
            $runningBalance += ($activity['debit'] - $activity['credit']);
            $activity['running_balance'] = $runningBalance;
            $activity['index'] = $counter;

            return $activity;
        });

        // Paginate (Latest first)
        $page = $request->get('page', 1);
        $perPage = 10;
        $paginatedItems = $sortedActivities->reverse()->forPage($page, $perPage);

        $history = new LengthAwarePaginator(
            $paginatedItems,
            $sortedActivities->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        // Final Totals for Summary Cards
        $grossSales = $distributor->distributions->sum(fn($d) => Currency::convertAmount($d->total_price, $d->exchange_rate));
        $totalReturns = $distributor->returns->sum(fn($r) => Currency::convertAmount($r->total_refund, $r->exchange_rate));
        $netSales = $grossSales - $totalReturns;
        
        $directPayments = $distributor->transactions->where('type', 'payment')->sum(fn($t) => Currency::convertAmount($t->amount, $t->exchange_rate));
        $downPayments = $distributor->distributions->sum(fn($d) => Currency::convertAmount($d->amount_paid, $d->exchange_rate));
        $totalPaid = $directPayments + $downPayments;
        
        $totalDiscounts = $distributor->transactions->where('type', 'discount')->sum(fn($t) => Currency::convertAmount($t->amount, $t->exchange_rate));
        $outstandingBalance = $netSales - $totalPaid - $totalDiscounts;

        $currencies = Currency::all();

        return view('Admin.Distributors.show', compact(
            'distributor', 
            'history', 
            'currencies', 
            'netSales', 
            'totalReturns', 
            'totalPaid', 
            'outstandingBalance'
        ));
    }

    public function edit(Distributor $distributor)
    {
        $distributor->load('mobiles', 'currency');
        $currencies = Currency::all();

        return view('Admin.Distributors.edit', compact('distributor', 'currencies'));
    }

    public function update(Request $request, Distributor $distributor)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'preferred_currency_id' => 'required|exists:currencies,id',
            'mobiles' => 'array',
        ]);

        $distributor->update([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'title' => $request->title,
            'preferred_currency_id' => $request->preferred_currency_id,
            'notes' => $request->notes,
        ]);

        if ($request->has('mobiles')) {
            $distributor->mobiles()->delete();
            foreach ($request->mobiles as $number) {
                if (! empty($number)) {
                    DistributorMobile::create(['distributor_id' => $distributor->id, 'number' => $number]);
                }
            }
        }

        return redirect()->route('admin.distributors.index')->with('success_message', __('Distributor profile updated.'));
    }

    public function destroy(Distributor $distributor)
    {
        $distributor->delete();

        return redirect()->route('admin.distributors.index')->with('success_message', __('Distributor removed completely.'));
    }

    public function storeTransaction(Request $request, Distributor $distributor)
    {
        $activeWorkDay = WorkDay::whereNull('end_time')->first();
        if (! $activeWorkDay) {
            return redirect()->back()->with('error_message', __('No active work day found. Please start a day first.'));
        }

        $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'currency_id' => 'required|exists:currencies,id',
            'exchange_rate' => 'nullable|numeric|min:0.01',
            'type' => 'required|in:payment,discount',
            'notes' => 'nullable|string|max:500',
        ]);

        $currency = Currency::find($request->currency_id);

        DistributorTransaction::create([
            'distributor_id' => $distributor->id,
            'work_day_id' => $activeWorkDay->id,
            'type' => $request->type,
            'amount' => $request->amount,
            'currency_id' => $request->currency_id,
            'exchange_rate' => $request->exchange_rate ?? $currency->exchange_rate,
            'notes' => $request->notes,
            'created_by' => auth()->id(),
        ]);

        $msg = $request->type == 'payment' ? __('Payment recorded successfully.') : __('Discount recorded successfully.');

        return redirect()->route('admin.distributors.show', $distributor)->with('success_message', $msg);
    }

    public function createTransaction(Distributor $distributor)
    {
        $distributor->load(['currency', 'distributions', 'returns', 'transactions']);
        $currencies = Currency::all();

        $balance = $distributor->distributions->sum(fn($d) => Currency::convertAmount($d->total_price, $d->exchange_rate))
                 - $distributor->returns->sum(fn($r) => Currency::convertAmount($r->total_refund, $r->exchange_rate))
                 - $distributor->distributions->sum(fn($d) => Currency::convertAmount($d->amount_paid, $d->exchange_rate))
                 - $distributor->transactions->where('type', 'payment')->sum(fn($t) => Currency::convertAmount($t->amount, $t->exchange_rate))
                 - $distributor->transactions->where('type', 'discount')->sum(fn($t) => Currency::convertAmount($t->amount, $t->exchange_rate));

        return view('Admin.Distributors.record_payment', compact('distributor', 'currencies', 'balance'));
    }
}
