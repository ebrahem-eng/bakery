@extends('layouts.Admin.App')

@section('content')
<div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-end gap-4">
    <div>
        <h1 class="text-2xl font-bold text-slate-900 dark:text-white tracking-tight">{{ __('End of Day') }} <span class="text-transparent bg-clip-text bg-gradient-to-r from-red-500 to-orange-500">{{ __('Settlement') }}</span></h1>
        <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
            {{ __('Work Day started:') }} 
            <span class="font-bold text-amber-600 dark:text-amber-500">{{ $workDay->start_time->format('Y-m-d h:i A') }}</span>
        </p>
    </div>
    <a href="{{ route('admin.work_days.index') }}" class="text-slate-400 hover:text-slate-900 dark:hover:text-white transition-colors text-sm font-medium flex items-center">
        <svg class="w-4 h-4 {{ app()->getLocale() == 'ar' ? 'ml-1 rotate-180' : 'mr-1' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
        </svg>
        {{ __('Back') }}
    </a>
</div>

@if($errors->any())
    <div class="mb-6 p-4 rounded-xl bg-red-500/10 border border-red-500/20 text-red-600 dark:text-red-400 text-sm">
        <ul class="list-disc list-inside">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
    </div>
@endif

{{-- ════════════════════════════════════════════════════════════════ --}}
{{-- SUMMARY STATS CARDS                                             --}}
{{-- ════════════════════════════════════════════════════════════════ --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    {{-- Total Sales --}}
    <div class="glass-panel p-6 rounded-2xl border border-emerald-500/10 relative overflow-hidden group">
        <div class="absolute top-0 {{ app()->getLocale() == 'ar' ? 'left-0' : 'right-0' }} p-3 opacity-10 group-hover:scale-110 transition-transform">
            <svg class="w-12 h-12 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
        </div>
        <p class="text-[10px] uppercase tracking-widest text-slate-500 font-black mb-1 leading-none">{{ __('Net Sales') }}</p>
        <div class="text-2xl font-black text-slate-900 dark:text-white">{{ number_format($netSales, 2) }}</div>
        <div class="mt-1 text-[10px] text-emerald-500 font-bold uppercase tracking-wider">{{ $currencyCode }}</div>
    </div>
    {{-- Total Expenses --}}
    <div class="glass-panel p-6 rounded-2xl border border-red-500/10 relative overflow-hidden group">
        <div class="absolute top-0 {{ app()->getLocale() == 'ar' ? 'left-0' : 'right-0' }} p-3 opacity-10 group-hover:scale-110 transition-transform">
            <svg class="w-12 h-12 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"/></svg>
        </div>
        <p class="text-[10px] uppercase tracking-widest text-slate-500 font-black mb-1 leading-none">{{ __('Total Expenses') }}</p>
        <div class="text-2xl font-black text-slate-900 dark:text-white">{{ number_format($totalExpenses, 2) }}</div>
        <div class="mt-1 text-[10px] text-red-500 font-bold uppercase tracking-wider">{{ $currencyCode }}</div>
    </div>
    {{-- Net Balance --}}
    <div class="glass-panel p-6 rounded-2xl border border-amber-500/10 relative overflow-hidden group">
        <div class="absolute top-0 {{ app()->getLocale() == 'ar' ? 'left-0' : 'right-0' }} p-3 opacity-10 group-hover:scale-110 transition-transform">
            <svg class="w-12 h-12 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
        <p class="text-[10px] uppercase tracking-widest text-slate-500 font-black mb-1 leading-none">{{ __('Net Balance') }}</p>
        <div class="text-2xl font-black {{ $netDayBalance >= 0 ? 'text-emerald-500' : 'text-red-500' }}">{{ number_format($netDayBalance, 2) }}</div>
        <div class="mt-1 text-[10px] text-slate-400 font-bold uppercase tracking-wider">{{ $currencyCode }}</div>
    </div>
    {{-- Bundles Status --}}
    <div class="glass-panel p-6 rounded-2xl border border-blue-500/10 relative overflow-hidden group">
        <div class="absolute top-0 {{ app()->getLocale() == 'ar' ? 'left-0' : 'right-0' }} p-3 opacity-10 group-hover:scale-110 transition-transform">
            <svg class="w-12 h-12 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
        </div>
        <p class="text-[10px] uppercase tracking-widest text-slate-500 font-black mb-1 leading-none">{{ __('Remaining Bundles') }}</p>
        <div class="text-2xl font-black text-slate-900 dark:text-white">{{ $calculatedRemainingBundles }}</div>
        <div class="mt-1 text-[10px] text-blue-500 font-bold uppercase tracking-wider">{{ __('bundles') }}</div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    {{-- ── Main Content ─────────────────────────────────────── --}}
    <div class="lg:col-span-2 space-y-6">

        {{-- ── Bundle Flow ──────────────────────────────────── --}}
        <div class="glass-panel rounded-2xl border border-blue-500/10 overflow-hidden">
            <div class="p-4 bg-blue-500/5 border-b border-blue-500/10">
                <h3 class="text-sm font-bold text-blue-600 dark:text-blue-400 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                    {{ __('Bundle Flow Tracking') }}
                </h3>
            </div>
            <div class="p-5 space-y-3">
                <div class="flex justify-between items-center py-2.5 border-b border-slate-200 dark:border-white/5">
                    <span class="text-sm text-slate-500 dark:text-slate-400">{{ __('Previous Day Carry-Over') }}</span>
                    <span class="font-bold text-slate-900 dark:text-white">{{ $previousCarryOverBundles }} {{ __('bundles') }}</span>
                </div>
                <div class="flex justify-between items-center py-2.5 border-b border-slate-200 dark:border-white/5">
                    <span class="text-sm text-slate-500 dark:text-slate-400">{{ __('Bundles Given to Shifts') }}</span>
                    <span class="font-bold text-blue-600 dark:text-blue-400">{{ $bundlesReceivedByShifts }} {{ __('bundles') }}</span>
                </div>
                <div class="flex justify-between items-center py-2.5 border-b border-slate-200 dark:border-white/5">
                    <span class="text-sm text-slate-500 dark:text-slate-400">{{ __('Bundles Returned from Shifts') }}</span>
                    <span class="font-bold text-emerald-600 dark:text-emerald-400">+ {{ $bundlesReturnedByShifts }} {{ __('bundles') }}</span>
                </div>
                <div class="flex justify-between items-center py-2.5 border-b border-slate-200 dark:border-white/5">
                    <span class="text-sm text-slate-500 dark:text-slate-400">{{ __('Distributed to Distributors') }}</span>
                    <span class="font-bold text-red-600 dark:text-red-400">- {{ $bundlesDistributed }} {{ __('bundles') }}</span>
                </div>
                <div class="flex justify-between items-center py-2.5 border-b border-slate-200 dark:border-white/5">
                    <span class="text-sm text-slate-500 dark:text-slate-400">{{ __('Returned by Distributors') }}</span>
                    <span class="font-bold text-emerald-600 dark:text-emerald-400">+ {{ $bundlesReturnedByDistributors }} {{ __('bundles') }}</span>
                </div>
                <div class="flex justify-between items-center py-3 bg-blue-500/5 rounded-xl px-3 -mx-1">
                    <span class="text-sm font-bold text-blue-600 dark:text-blue-400">{{ __('Calculated Remaining') }}</span>
                    <span class="font-bold text-blue-600 dark:text-blue-400 text-lg">{{ $calculatedRemainingBundles }} {{ __('bundles') }}</span>
                </div>
            </div>

            {{-- Per-shift breakdown --}}
            @if($workDay->workerShifts->count() > 0)
            <div class="border-t border-slate-200 dark:border-white/5">
                <div class="p-4 bg-slate-50 dark:bg-black/20">
                    <h4 class="text-xs font-bold text-slate-500 uppercase tracking-widest">{{ __('Shift Bundle Details') }}</h4>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
                        <thead>
                            <tr class="text-[10px] uppercase tracking-wider text-slate-500 border-b border-slate-200 dark:border-white/5">
                                <th class="py-3 px-4 font-medium text-{{ app()->getLocale() == 'ar' ? 'right' : 'left' }}">{{ __('Worker') }}</th>
                                <th class="py-3 px-4 font-medium text-center">{{ __('Received') }}</th>
                                <th class="py-3 px-4 font-medium text-center">{{ __('Returned') }}</th>
                                <th class="py-3 px-4 font-medium text-center">{{ __('Net') }}</th>
                                <th class="py-3 px-4 font-medium text-center">{{ __('Status') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 dark:divide-white/5">
                            @foreach($workDay->workerShifts as $shift)
                            <tr class="text-slate-600 dark:text-slate-300">
                                <td class="py-2.5 px-4 font-medium text-slate-900 dark:text-white">{{ $shift->worker->first_name ?? '—' }}</td>
                                <td class="py-2.5 px-4 text-center text-blue-600 dark:text-blue-400 font-bold">{{ $shift->bundles_received }}</td>
                                <td class="py-2.5 px-4 text-center text-emerald-600 dark:text-emerald-400 font-bold">{{ $shift->bundles_returned }}</td>
                                <td class="py-2.5 px-4 text-center font-bold {{ ($shift->bundles_received - $shift->bundles_returned) > 0 ? 'text-red-500' : 'text-emerald-500' }}">{{ $shift->bundles_received - $shift->bundles_returned }}</td>
                                <td class="py-2.5 px-4 text-center">
                                    @if($shift->check_out)
                                        <span class="text-[10px] uppercase tracking-widest font-bold text-emerald-500 bg-emerald-500/10 px-2 py-0.5 rounded-full">{{ __('Done') }}</span>
                                    @else
                                        <span class="text-[10px] uppercase tracking-widest font-bold text-amber-500 bg-amber-500/10 px-2 py-0.5 rounded-full animate-pulse">{{ __('Active') }}</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif
        </div>

        {{-- ── Revenue Breakdown ────────────────────────────── --}}
        <div class="glass-panel rounded-2xl border border-emerald-500/10 overflow-hidden">
            <div class="p-4 bg-emerald-500/5 border-b border-emerald-500/10">
                <h3 class="text-sm font-bold text-emerald-600 dark:text-emerald-400 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                    {{ __('Revenue Breakdown') }}
                </h3>
            </div>
            <div class="p-5 space-y-1">
                <div class="flex justify-between items-center py-2.5 border-b border-slate-200 dark:border-white/5">
                    <span class="text-sm text-slate-500 dark:text-slate-400">{{ __('Total Distributions Billed') }}</span>
                    <span class="font-bold text-slate-900 dark:text-white">+ {{ number_format($totalSales, 2) }} <span class="text-xs text-slate-400">{{ $currencyCode }}</span></span>
                </div>
                <div class="flex justify-between items-center py-2.5 border-b border-slate-200 dark:border-white/5">
                    <span class="text-sm text-slate-500 dark:text-slate-400">{{ __('Refunds Processed') }}</span>
                    <span class="font-bold text-red-600 dark:text-red-400">- {{ number_format($totalRefunds, 2) }} <span class="text-xs text-red-400/50">{{ $currencyCode }}</span></span>
                </div>
                <div class="flex justify-between items-center py-2.5 border-b border-slate-200 dark:border-white/5">
                    <span class="text-sm text-slate-500 dark:text-slate-400">{{ __('Payments Received') }}</span>
                    <span class="font-bold text-emerald-600 dark:text-emerald-400">{{ number_format($totalPaymentsReceived, 2) }} <span class="text-xs text-emerald-400/50">{{ $currencyCode }}</span></span>
                </div>
                <div class="flex justify-between items-center py-3 bg-emerald-500/5 rounded-xl px-3 -mx-1 mt-2">
                    <span class="text-sm font-bold text-emerald-600 dark:text-emerald-400">{{ __('Net Sales') }}</span>
                    <span class="font-bold text-emerald-600 dark:text-emerald-400 text-lg">{{ number_format($netSales, 2) }} {{ $currencyCode }}</span>
                </div>
            </div>
        </div>

        {{-- ── Expense Breakdown ────────────────────────────── --}}
        <div class="glass-panel rounded-2xl border border-red-500/10 overflow-hidden">
            <div class="p-4 bg-red-500/5 border-b border-red-500/10">
                <h3 class="text-sm font-bold text-red-600 dark:text-red-400 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"/></svg>
                    {{ __('Expense Breakdown') }}
                </h3>
            </div>
            <div class="p-5 space-y-1">
                <div class="flex justify-between items-center py-2.5 border-b border-slate-200 dark:border-white/5">
                    <span class="text-sm text-slate-500 dark:text-slate-400">{{ __('Supplies Purchased') }}</span>
                    <span class="font-bold text-slate-900 dark:text-white">{{ number_format($suppliesCost, 2) }} <span class="text-xs text-slate-400">{{ $currencyCode }}</span></span>
                </div>
                <div class="flex justify-between items-center py-2.5 border-b border-slate-200 dark:border-white/5">
                    <span class="text-sm text-slate-500 dark:text-slate-400">{{ __('Freight & Unloading Fees') }}</span>
                    <span class="font-bold text-slate-900 dark:text-white">{{ number_format($unloadingFees, 2) }} <span class="text-xs text-slate-400">{{ $currencyCode }}</span></span>
                </div>
                <div class="flex justify-between items-center py-2.5 border-b border-slate-200 dark:border-white/5">
                    <span class="text-sm text-slate-500 dark:text-slate-400">{{ __('Worker Shift Wages') }}</span>
                    <span class="font-bold text-slate-900 dark:text-white">{{ number_format($shiftWages, 2) }} <span class="text-xs text-slate-400">{{ $currencyCode }}</span></span>
                </div>
                <div class="flex justify-between items-center py-2.5 border-b border-slate-200 dark:border-white/5">
                    <span class="text-sm text-slate-500 dark:text-slate-400">{{ __('Worker Allowances') }}</span>
                    <span class="font-bold text-slate-900 dark:text-white">+ {{ number_format($workerAllowances, 2) }} <span class="text-xs text-slate-400">{{ $currencyCode }}</span></span>
                </div>
                <div class="flex justify-between items-center py-2.5 border-b border-slate-200 dark:border-white/5">
                    <span class="text-sm text-slate-500 dark:text-slate-400">{{ __('Worker Advances Paid') }}</span>
                    <span class="font-bold text-amber-600 dark:text-amber-400">{{ number_format($workerAdvances, 2) }} <span class="text-xs text-amber-400/50">{{ $currencyCode }}</span></span>
                </div>
                <div class="flex justify-between items-center py-2.5 border-b border-slate-200 dark:border-white/5">
                    <span class="text-sm text-slate-500 dark:text-slate-400">{{ __('Worker Deductions') }}</span>
                    <span class="font-bold text-emerald-600 dark:text-emerald-400">- {{ number_format($workerDeductions, 2) }} <span class="text-xs text-emerald-400/50">{{ $currencyCode }}</span></span>
                </div>
                <div class="flex justify-between items-center py-2.5 border-b border-slate-200 dark:border-white/5">
                    <span class="text-sm text-slate-500 dark:text-slate-400">{{ __('Operational Expenses') }}</span>
                    <span class="font-bold text-slate-900 dark:text-white">{{ number_format($operationalExpenses, 2) }} <span class="text-xs text-slate-400">{{ $currencyCode }}</span></span>
                </div>
                <div class="flex justify-between items-center py-3 bg-red-500/5 rounded-xl px-3 -mx-1 mt-2">
                    <span class="text-sm font-bold text-red-600 dark:text-red-400">{{ __('Total Expenses') }}</span>
                    <span class="font-bold text-red-600 dark:text-red-400 text-lg">{{ number_format($totalExpenses, 2) }} {{ $currencyCode }}</span>
                </div>
            </div>
        </div>

    </div>

    {{-- ── Closure Panel ────────────────────────────────────── --}}
    <div class="lg:col-span-1">
        <div class="glass-panel rounded-2xl border border-amber-500/20 overflow-hidden sticky top-6">
            <div class="p-5 bg-gradient-to-br from-amber-500/10 to-orange-600/10 border-b border-amber-500/20">
                @if($workDay->status == 'closed')
                    <h2 class="text-lg font-bold text-amber-600 dark:text-amber-500 mb-1">{{ __('Settlement Completed') }}</h2>
                    <p class="text-xs text-slate-500 dark:text-slate-400">{{ __('This period has been permanently sealed and locked.') }}</p>
                @else
                    <h2 class="text-lg font-bold text-amber-600 dark:text-amber-500 mb-1">{{ __('Finalize Settlement') }}</h2>
                    <p class="text-xs text-slate-500 dark:text-slate-400">{{ __('Confirm remaining bread bundles and cash for the next work day.') }}</p>
                @endif
            </div>
            
            @if($workDay->status == 'active')
            <form action="{{ route('admin.work_days.close', $workDay) }}" method="POST" class="p-5 space-y-5">
                @csrf
                
                <input type="hidden" name="total_expenses_at_close" value="{{ $totalExpenses }}">
                <input type="hidden" name="total_sales_at_close" value="{{ $netSales }}">

                {{-- Net Balance Summary --}}
                <div class="p-4 rounded-xl border border-amber-500/20 bg-amber-500/5">
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-xs text-slate-500 font-bold uppercase tracking-widest">{{ __('Net Balance') }}</span>
                        <span class="text-lg font-bold {{ $netDayBalance >= 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-600 dark:text-red-400' }}">{{ number_format($netDayBalance, 2) }} {{ $currencyCode }}</span>
                    </div>
                    <div class="flex justify-between items-center text-xs text-slate-400">
                        <span>{{ __('Bundles Sold') }}: {{ $bundlesDistributed - $bundlesReturnedByDistributors }}</span>
                        <span>{{ __('Shifts') }}: {{ $workDay->workerShifts->count() }}</span>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-amber-600 dark:text-amber-400 uppercase tracking-wider mb-2">
                        {{ __('Carried Over Bundles (Unsold)') }}
                        <span class="text-red-500">*</span>
                    </label>
                    <input type="number" name="carried_over_bundles" required min="0" value="{{ old('carried_over_bundles', $calculatedRemainingBundles) }}"
                        class="block w-full px-4 py-3 bg-white dark:bg-[#0f1115] border border-amber-500/30 rounded-xl text-lg text-slate-900 dark:text-white focus:outline-none focus:border-amber-400 focus:ring-1 focus:ring-amber-400 transition-all font-bold"
                        placeholder="0">
                    <p class="mt-1 text-[10px] text-slate-500">{{ __('Auto-calculated from shift returns. Adjust if needed.') }}</p>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-amber-600 dark:text-amber-400 uppercase tracking-wider mb-2">
                        {{ __('Carried Over Cash Balance') }}
                        <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <input type="number" step="0.01" name="carried_over_money" required min="0" value="{{ old('carried_over_money') }}"
                            class="block w-full px-4 py-3 bg-white dark:bg-[#0f1115] border border-amber-500/30 rounded-xl text-lg text-slate-900 dark:text-white focus:outline-none focus:border-amber-400 focus:ring-1 focus:ring-amber-400 transition-all font-bold"
                            placeholder="0.00">
                        <div class="absolute inset-y-0 {{ app()->getLocale() == 'ar' ? 'left-0 pl-4' : 'right-0 pr-4' }} flex items-center pointer-events-none">
                            <span class="text-slate-500 text-sm font-bold">{{ $currencyCode }}</span>
                        </div>
                    </div>
                    <p class="mt-1 text-[10px] text-slate-500">{{ __('Physical cash left in the drawer for the next work day.') }}</p>
                </div>

                <div class="pt-2">
                    <button type="submit" 
                        onclick="return confirm('{{ __('WARNING: Closing a work day freezes all sales, expenses, and HR shifts permanently. Proceed?') }}');"
                        class="w-full bg-gradient-to-r from-red-600 to-orange-600 hover:from-red-500 hover:to-orange-500 text-white py-4 rounded-xl text-sm font-bold shadow-[0_0_20px_rgba(239,68,68,0.4)] transition-all uppercase tracking-widest">
                        {{ __('Close Work Day Permanently') }}
                    </button>
                </div>
            </form>
            @else
            <div class="p-5 text-center space-y-4">
                <div class="p-4 rounded-xl border border-slate-200 dark:border-white/5 bg-slate-50 dark:bg-[#0f1115]">
                    <p class="text-slate-400 text-xs uppercase tracking-widest mb-1">{{ __('Carried Over Bundles (Unsold)') }}</p>
                    <p class="text-slate-900 dark:text-white font-bold text-xl">{{ $workDay->carried_over_bundles }}</p>
                </div>
                <div class="p-4 rounded-xl border border-slate-200 dark:border-white/5 bg-slate-50 dark:bg-[#0f1115]">
                    <p class="text-slate-400 text-xs uppercase tracking-widest mb-1">{{ __('Carried Over Cash Balance') }}</p>
                    <p class="text-slate-900 dark:text-white font-bold text-xl">{{ number_format($workDay->carried_over_money, 2) }} {{ $currencyCode }}</p>
                </div>
                <p class="text-emerald-600 dark:text-emerald-400 font-bold uppercase tracking-widest text-xs py-2">{{ __('Work day is already closed.') }}</p>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
