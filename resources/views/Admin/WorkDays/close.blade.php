@extends('layouts.Admin.App')

@section('content')
<div class="mb-8 flex flex-col sm:flex-row justify-between items-start sm:items-end gap-4">
    <div>
        <h1 class="text-2xl lg:text-3xl font-bold text-slate-900 dark:text-white mb-1 tracking-tight">{{ __('End of Day') }} <span class="text-transparent bg-clip-text bg-gradient-to-r from-red-500 to-orange-500">{{ __('Settlement') }}</span></h1>
        <p class="text-sm text-slate-400">
            {{ __('Reviewing active ledger matching exactly to sequence:') }} 
            <span class="font-bold text-amber-500">{{ $workDay->start_time->format('Y-m-d h:i A') }}</span>
        </p>
    </div>
    <a href="{{ route('admin.work_days.index') }}" class="text-slate-400 hover:text-white transition-colors text-sm font-medium flex items-center">
        <svg class="w-4 h-4 {{ app()->getLocale() == 'ar' ? 'ml-1 rotate-180' : 'mr-1' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
        </svg>
        {{ __('Back') }}
    </a>
</div>

@if($errors->any())
    <div class="mb-6 p-4 rounded-xl bg-red-500/10 border border-red-500/20 text-red-400 text-sm">
        <ul class="list-disc list-inside">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <!-- Summary Ledger -->
    <div class="lg:col-span-2 space-y-6">
        
        <!-- Sales Overview -->
        <div class="glass-panel rounded-2xl border border-emerald-500/10 overflow-hidden">
            <div class="p-5 bg-emerald-500/5 border-b border-emerald-500/10">
                <h3 class="text-lg font-bold text-emerald-400">{{ __('Daily Revenue & Sales (Expected)') }}</h3>
            </div>
            <div class="p-6">
                <div class="flex justify-between items-center py-3 border-b border-white/5">
                    <span class="text-slate-400">{{ __('Total Bread Distributions Billed') }}</span>
                    <span class="text-white font-bold">+ {{ number_format($workDay->distributions->sum('total_price'), 2) }}</span>
                </div>
                <div class="flex justify-between items-center py-3 border-b border-white/5">
                    <span class="text-slate-400">{{ __('Total Refunds Processed') }}</span>
                    <span class="text-red-400 font-bold">- {{ number_format($workDay->distributorReturns->sum('total_refund'), 2) }}</span>
                </div>
                <div class="flex justify-between items-center py-3">
                    <span class="text-slate-400">{{ __('Net General Sales Logic (Gross)') }}</span>
                    <span class="text-emerald-400 font-bold text-xl">{{ number_format($totalSales, 2) }}</span>
                </div>
            </div>
        </div>

        <!-- Expenses Overview -->
        <div class="glass-panel rounded-2xl border border-red-500/10 overflow-hidden">
            <div class="p-5 bg-red-500/5 border-b border-red-500/10">
                <h3 class="text-lg font-bold text-red-400">{{ __('Daily Consumed Expenses & Payouts') }}</h3>
            </div>
            <div class="p-6">
                <div class="flex justify-between items-center py-3 border-b border-white/5">
                    <span class="text-slate-400">{{ __('Supplies Purchased (Flour, Yeast, Diesel...)') }}</span>
                    <span class="text-white font-bold">{{ number_format($workDay->supplies->sum('total_cost'), 2) }}</span>
                </div>
                <div class="flex justify-between items-center py-3 border-b border-white/5">
                    <span class="text-slate-400">{{ __('Supplier Freight & Unloading Fees') }}</span>
                    <span class="text-white font-bold">{{ number_format($workDay->supplies->where('unloading_fee_payer', 'bakery')->sum(function($s) { return $s->unloading_fee * ($s->unloading_fee_exchange_rate ?? 1); }), 2) }}</span>
                </div>
                <div class="flex justify-between items-center py-3 border-b border-white/5">
                    <span class="text-slate-400">{{ __('Total Shift Base Wages Issued') }}</span>
                    <span class="text-white font-bold">{{ number_format($workDay->workerShifts->sum('snapshot_daily_wage'), 2) }}</span>
                </div>
                <div class="flex justify-between items-center py-3 border-b border-white/5">
                    <span class="text-slate-400">{{ __('Worker Advances/Allowances (Net Cost Add)') }}</span>
                    <span class="text-white font-bold">{{ number_format($workDay->workerTransactions->where('type', 'allowance')->sum('amount'), 2) }}</span>
                </div>
                <div class="flex justify-between items-center py-3 border-b border-white/5">
                    <span class="text-slate-400">{{ __('Worker Deductions (Net Cost Min)') }}</span>
                    <span class="text-white font-bold">- {{ number_format($workDay->workerTransactions->where('type', 'deduction')->sum('amount'), 2) }}</span>
                </div>
                <div class="flex justify-between items-center py-3 border-b border-white/5">
                    <span class="text-slate-400">{{ __('General Operating/Logistics Drawings') }}</span>
                    <span class="text-white font-bold">{{ number_format($workDay->expenses->sum('amount'), 2) }}</span>
                </div>
                <div class="flex justify-between items-center py-3">
                    <span class="text-slate-400">{{ __('Total Expected Cash Outflow') }}</span>
                    <span class="text-red-400 font-bold text-xl">{{ number_format($totalExpenses, 2) }}</span>
                </div>
            </div>
        </div>

    </div>

    <!-- Closure Panel -->
    <div class="lg:col-span-1">
        <div class="glass-panel rounded-2xl border border-amber-500/20 overflow-hidden sticky top-6">
            <div class="p-6 bg-gradient-to-br from-amber-500/10 to-orange-600/10 border-b border-amber-500/20">
                @if($workDay->status == 'closed')
                    <h2 class="text-xl font-bold text-amber-500 mb-2">{{ __('Settlement Completed') }}</h2>
                    <p class="text-xs text-slate-400">{{ __('This period has been permanently sealed and locked.') }}</p>
                @else
                    <h2 class="text-xl font-bold text-amber-500 mb-2">{{ __('Finalize Settlement') }}</h2>
                    <p class="text-xs text-slate-400">{{ __('Please carefully enter the remaining physical materials and cash left in the drawer for the next shifts.') }}</p>
                @endif
            </div>
            
            @if($workDay->status == 'active')
            <form action="{{ route('admin.work_days.close', $workDay) }}" method="POST" class="p-6 space-y-6">
                @csrf
                
                <input type="hidden" name="total_expenses_at_close" value="{{ $totalExpenses }}">
                <input type="hidden" name="total_sales_at_close" value="{{ $totalSales }}">

                <div>
                    <label class="block text-xs font-semibold text-amber-400 uppercase tracking-wider mb-2">
                        {{ __('Carried Over Bundles (Unsold)') }}
                        <span class="text-red-500">*</span>
                    </label>
                    <input type="number" name="carried_over_bundles" required min="0" value="{{ old('carried_over_bundles') }}"
                        class="block w-full px-4 py-3 bg-[#0f1115] border border-amber-500/30 rounded-xl text-lg text-white focus:outline-none focus:border-amber-400 focus:ring-1 focus:ring-amber-400 transition-all font-bold"
                        placeholder="0">
                    <p class="mt-1 text-[10px] text-slate-500">{{ __('Exact physical bundles remaining to be handed to the morning shift.') }}</p>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-amber-400 uppercase tracking-wider mb-2">
                        {{ __('Carried Over Cash Balance') }}
                        <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <input type="number" step="0.01" name="carried_over_money" required min="0" value="{{ old('carried_over_money') }}"
                            class="block w-full px-4 py-3 bg-[#0f1115] border border-amber-500/30 rounded-xl text-lg text-white focus:outline-none focus:border-amber-400 focus:ring-1 focus:ring-amber-400 transition-all font-bold"
                            placeholder="0.00">
                        <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none">
                            <span class="text-slate-500 text-sm font-bold">{{ __('DEFAULT') }}</span>
                        </div>
                    </div>
                    <p class="mt-1 text-[10px] text-slate-500">{{ __('Actual physical cash left in the drawer for the next shifts.') }}</p>
                </div>

                <div class="pt-4">
                    <button type="submit" 
                        onclick="return confirm('{{ __('WARNING: Closing a work day freezes all sales, expenses, and HR shifts inside this interval permanently. Proceed?') }}');"
                        class="w-full bg-gradient-to-r from-red-600 to-orange-600 hover:from-red-500 hover:to-orange-500 text-white py-4 rounded-xl text-sm font-bold shadow-[0_0_20px_rgba(239,68,68,0.4)] transition-all uppercase tracking-widest">
                        {{ __('Close Work Day Permanently') }}
                    </button>
                </div>
            </form>
            @else
            <div class="p-6 text-center space-y-4">
                <div class="p-4 rounded-xl border border-white/5 bg-[#0f1115]">
                    <p class="text-slate-400 text-xs uppercase tracking-widest mb-1">{{ __('Carried Over Bundles (Unsold)') }}</p>
                    <p class="text-white font-bold text-xl">{{ $workDay->carried_over_bundles }}</p>
                </div>
                <div class="p-4 rounded-xl border border-white/5 bg-[#0f1115]">
                    <p class="text-slate-400 text-xs uppercase tracking-widest mb-1">{{ __('Carried Over Cash Balance') }}</p>
                    <p class="text-white font-bold text-xl">{{ number_format($workDay->carried_over_money, 2) }}</p>
                </div>
                <p class="text-emerald-400 font-bold uppercase tracking-widest text-xs py-2">{{ __('Work day is already closed.') }}</p>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
