<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Distributor;
use App\Models\DistributorMobile;
use App\Models\Currency;

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
            'mobiles' => 'array'
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
                if(!empty($number)){
                    DistributorMobile::create(['distributor_id' => $distributor->id, 'number' => $number]);
                }
            }
        }

        return redirect()->route('admin.distributors.index')->with('success_message', __('Distributor profile established successfully.'));
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
            'mobiles' => 'array'
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
                if(!empty($number)){
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
