<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Worker;
use App\Models\WorkerMobile;
use App\Models\Currency;

class WorkerController extends Controller
{
    public function index()
    {
        $workers = Worker::with('mobiles', 'currency')->orderBy('id', 'desc')->get();
        return view('Admin.Workers.index', compact('workers'));
    }

    public function create()
    {
        $currencies = Currency::all();
        return view('Admin.Workers.create', compact('currencies'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'daily_wage' => 'required|numeric|min:0',
            'currency_id' => 'required|exists:currencies,id',
            'exchange_rate' => 'nullable|numeric|min:0',
            'mobiles' => 'array'
        ]);

        $worker = Worker::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'title' => $request->title,
            'daily_wage' => $request->daily_wage,
            'currency_id' => $request->currency_id,
            'exchange_rate' => $request->exchange_rate ?? 1.0,
        ]);

        if ($request->has('mobiles')) {
            foreach ($request->mobiles as $number) {
                if(!empty($number)){
                    WorkerMobile::create(['worker_id' => $worker->id, 'mobile_number' => $number]);
                }
            }
        }

        return redirect()->route('admin.workers.index')->with('success', __('Worker profile established successfully.'));
    }

    public function edit(Worker $worker)
    {
        $worker->load('mobiles', 'currency');
        $currencies = Currency::all();
        return view('Admin.Workers.edit', compact('worker', 'currencies'));
    }

    public function update(Request $request, Worker $worker)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'daily_wage' => 'required|numeric|min:0',
            'currency_id' => 'required|exists:currencies,id',
            'exchange_rate' => 'nullable|numeric|min:0',
            'mobiles' => 'array'
        ]);

        $worker->update([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'title' => $request->title,
            'daily_wage' => $request->daily_wage,
            'currency_id' => $request->currency_id,
            'exchange_rate' => $request->exchange_rate ?? 1.0,
        ]);

        if ($request->has('mobiles')) {
            $worker->mobiles()->delete();
            foreach ($request->mobiles as $number) {
                if(!empty($number)){
                    WorkerMobile::create(['worker_id' => $worker->id, 'mobile_number' => $number]);
                }
            }
        }

        return redirect()->route('admin.workers.index')->with('success', __('Worker profile updated.'));
    }

    public function destroy(Worker $worker)
    {
        $worker->delete();
        return redirect()->route('admin.workers.index')->with('success', __('Worker record completely deleted.'));
    }
}
