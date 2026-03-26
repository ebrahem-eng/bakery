<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Currency;
use App\Models\Distributor;
use App\Models\DistributorMobile;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class DistributorController extends Controller
{
    public function index()
    {
        $distributors = Distributor::with('mobiles', 'currency')->orderBy('id', 'desc')->get();

        return view('Admin.Distributors.index', compact('distributors'));
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
                'debit' => $d->total_price,
                'credit' => 0,
                'color' => 'blue',
                'admin' => $d->createdBy ? $d->createdBy->first_name : '--',
            ]);

            if ($d->amount_paid > 0) {
                $activities->push([
                    'date' => $d->created_at->addSecond(),
                    'type' => 'Payment',
                    'ref' => __('Down Payment for').' #'.$d->id,
                    'debit' => 0,
                    'credit' => $d->amount_paid,
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
                'debit' => 0,
                'credit' => $r->total_refund,
                'color' => 'amber',
                'admin' => $r->createdBy ? $r->createdBy->first_name : '--',
            ]);
        }

        // 3. Transactions
        foreach ($distributor->transactions as $t) {
            $activities->push([
                'date' => $t->created_at,
                'type' => ucfirst($t->type), // payment, discount, etc.
                'ref' => $t->notes ?? __('Direct Transaction'),
                'debit' => 0,
                'credit' => $t->amount,
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

        return view('Admin.Distributors.show', compact('distributor', 'history'));
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
}
