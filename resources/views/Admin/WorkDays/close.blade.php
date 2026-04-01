@extends('layouts.Admin.App')

@section('content')
<div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-end gap-4">
    <div>
        <h1 class="text-2xl font-bold text-slate-900 dark:text-white tracking-tight">{{ __('End of Day') }} <span class="text-transparent bg-clip-text bg-gradient-to-r from-red-500 to-orange-500">{{ __('Settlement') }}</span></h1>
        <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
            {{ __('Work Day started:') }} 
            <span class="font-bold text-amber-600 dark:text-amber-500">{{ $workDay->start_time->translatedFormat('Y-m-d h:i A') }}</span>
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

@if(isset($activeShifts) && $activeShifts->count() > 0)
    <a href="{{ route('admin.attendance.index') }}" target="_blank" class="block mb-6 p-5 rounded-2xl bg-amber-500/10 border border-amber-500/20 shadow-[0_0_20px_rgba(245,158,11,0.1)] hover:bg-amber-500/20 transition-all group">
        <div class="flex items-start gap-4">
            <div class="w-10 h-10 rounded-xl bg-amber-500 flex items-center justify-center shrink-0 shadow-lg shadow-amber-500/20 group-hover:scale-110 transition-transform">
                <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
            </div>
            <div class="flex-1 min-w-0">
                <div class="flex justify-between items-start">
                    <h3 class="text-sm font-bold text-amber-600 dark:text-amber-400 leading-tight">{{ __('Active Workers Warning') }}</h3>
                    <div class="flex items-center gap-1 text-[10px] font-bold text-amber-600 uppercase tracking-widest bg-amber-500/20 px-2 py-0.5 rounded-md">
                        {{ __('View Details') }}
                        <svg class="w-3 h-3 {{ app()->getLocale() == 'ar' ? 'rotate-180' : '' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </div>
                </div>
                <p class="text-xs text-amber-600/70 dark:text-amber-400/60 mt-1 font-medium">{{ __('There are employees still clocked in. Please ensure you have recorded their returned bread and cash totals before finalizing the day.') }}</p>
                
                <div class="mt-4 flex flex-wrap gap-2">
                    @foreach($activeShifts as $as)
                        <div class="inline-flex items-center gap-2 px-3 py-1.5 bg-white/50 dark:bg-black/20 border border-amber-500/30 rounded-lg">
                            <div class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></div>
                            <span class="text-xs font-bold text-slate-700 dark:text-slate-200">{{ $as->worker->first_name }}</span>
                            <span class="text-[10px] text-slate-500">{{ $as->check_in->translatedFormat('h:i A') }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </a>
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
        <div class="mt-1 text-[10px] text-emerald-500 font-bold uppercase tracking-wider">{{ __($currencyCode) }}</div>
    </div>
    {{-- Total Expenses --}}
    <div class="glass-panel p-6 rounded-2xl border border-red-500/10 relative overflow-hidden group">
        <div class="absolute top-0 {{ app()->getLocale() == 'ar' ? 'left-0' : 'right-0' }} p-3 opacity-10 group-hover:scale-110 transition-transform">
            <svg class="w-12 h-12 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"/></svg>
        </div>
        <p class="text-[10px] uppercase tracking-widest text-slate-500 font-black mb-1 leading-none">{{ __('Total Expenses') }}</p>
        <div class="text-2xl font-black text-slate-900 dark:text-white">{{ number_format($totalExpenses, 2) }}</div>
        <div class="mt-1 text-[10px] text-red-500 font-bold uppercase tracking-wider">{{ __($currencyCode) }}</div>
    </div>
    {{-- Net Balance --}}
    <div class="glass-panel p-6 rounded-2xl border border-amber-500/10 relative overflow-hidden group">
        <div class="absolute top-0 {{ app()->getLocale() == 'ar' ? 'left-0' : 'right-0' }} p-3 opacity-10 group-hover:scale-110 transition-transform">
            <svg class="w-12 h-12 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
        <p class="text-[10px] uppercase tracking-widest text-slate-500 font-black mb-1 leading-none">{{ __('Net Balance') }}</p>
        <div class="text-2xl font-black {{ $netDayBalance >= 0 ? 'text-emerald-500' : 'text-red-500' }}">{{ number_format($netDayBalance, 2) }}</div>
        <div class="mt-1 text-[10px] text-slate-400 font-bold uppercase tracking-wider">{{ __($currencyCode) }}</div>
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

<form action="{{ route('admin.work_days.close', $workDay) }}" method="POST"
      x-data="{
          currencyId: '{{ $defaultCurrency->id ?? '' }}',
          isLocal: true,
          exchangeRate: 1,
          setCurrency(id) {
              this.currencyId = id;
              const currencies = @js($currencies->map(fn($c) => ['id' => $c->id, 'is_default' => $c->is_default, 'exchange_rate' => $c->exchange_rate]));
              const found = currencies.find(c => c.id == id);
              this.isLocal = found ? found.is_default : true;
              this.exchangeRate = found ? found.exchange_rate : 1;
          }
      }">
    @csrf
    
    <input type="hidden" name="total_expenses_at_close" value="{{ $totalExpenses }}">
    <input type="hidden" name="total_sales_at_close" value="{{ $netSales }}">

    {{-- Main Container --}}
    <div class="space-y-6">

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
            <div id="shift-details" class="border-t border-slate-200 dark:border-white/5 scroll-mt-6">
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
                    <span class="font-bold text-slate-900 dark:text-white">+ {{ number_format($totalSales, 2) }} <span class="text-xs text-slate-400">{{ __($currencyCode) }}</span></span>
                </div>
                <div class="flex justify-between items-center py-2.5 border-b border-slate-200 dark:border-white/5">
                    <span class="text-sm text-slate-500 dark:text-slate-400">{{ __('Refunds Processed') }}</span>
                    <span class="font-bold text-red-600 dark:text-red-400">- {{ number_format($totalRefunds, 2) }} <span class="text-xs text-red-400/50">{{ __($currencyCode) }}</span></span>
                </div>
                <div class="flex justify-between items-center py-2.5 border-b border-slate-200 dark:border-white/5">
                    <span class="text-sm text-slate-500 dark:text-slate-400">{{ __('Payments Received') }}</span>
                    <span class="font-bold text-emerald-600 dark:text-emerald-400">{{ number_format($totalPaymentsReceived, 2) }} <span class="text-xs text-emerald-400/50">{{ __($currencyCode) }}</span></span>
                </div>
                <div class="flex justify-between items-center py-3 bg-emerald-500/5 rounded-xl px-3 -mx-1 mt-2">
                    <span class="text-sm font-bold text-emerald-600 dark:text-emerald-400">{{ __('Net Sales') }}</span>
                    <span class="font-bold text-emerald-600 dark:text-emerald-400 text-lg">{{ number_format($netSales, 2) }} {{ __($currencyCode) }}</span>
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
                    <span class="font-bold text-slate-900 dark:text-white">{{ number_format($suppliesCost, 2) }} <span class="text-xs text-slate-400">{{ __($currencyCode) }}</span></span>
                </div>
                <div class="flex justify-between items-center py-2.5 border-b border-slate-200 dark:border-white/5">
                    <span class="text-sm text-slate-500 dark:text-slate-400">{{ __('Freight & Unloading Fees') }}</span>
                    <span class="font-bold text-slate-900 dark:text-white">{{ number_format($unloadingFees, 2) }} <span class="text-xs text-slate-400">{{ __($currencyCode) }}</span></span>
                </div>
                <div class="flex justify-between items-center py-2.5 border-b border-slate-200 dark:border-white/5">
                    <span class="text-sm text-slate-500 dark:text-slate-400 font-bold uppercase tracking-tight">{{ __('Employee Wages & Payments') }}</span>
                    <span class="font-bold text-slate-900 dark:text-white">{{ number_format($workerPayments, 2) }} <span class="text-xs text-slate-400">{{ __($currencyCode) }}</span></span>
                </div>
                <div class="flex justify-between items-center py-2.5 border-b border-slate-200 dark:border-white/5">
                    <span class="text-sm text-slate-500 dark:text-slate-400">{{ __('Worker Deductions') }}</span>
                    <span class="font-bold text-emerald-600 dark:text-emerald-400">- {{ number_format($workerDeductions, 2) }} <span class="text-xs text-emerald-400/50">{{ __($currencyCode) }}</span></span>
                </div>
                <div class="flex justify-between items-center py-2.5 border-b border-slate-200 dark:border-white/5">
                    <span class="text-sm text-slate-500 dark:text-slate-400">{{ __('Operational Expenses') }}</span>
                    <span class="font-bold text-slate-900 dark:text-white">{{ number_format($operationalExpenses, 2) }} <span class="text-xs text-slate-400">{{ __($currencyCode) }}</span></span>
                </div>
                <div class="flex justify-between items-center py-3 bg-red-500/5 rounded-xl px-3 -mx-1 mt-2">
                    <span class="text-sm font-bold text-red-600 dark:text-red-400">{{ __('Total Expenses') }}</span>
                    <span class="font-bold text-red-600 dark:text-red-400 text-lg">{{ number_format($totalExpenses, 2) }} {{ __($currencyCode) }}</span>
                </div>
            </div>
        </div>

        {{-- Raw Material Consumption Section --}}
        <div class="glass-panel rounded-2xl border border-amber-500/20 overflow-hidden shadow-lg shadow-amber-500/5">
            <div class="px-5 py-4 bg-amber-500/10 border-b border-amber-500/10 flex items-center gap-3">
                <div class="p-2 bg-amber-500/20 rounded-lg text-amber-600 dark:text-amber-500">
                     <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" /></svg>
                </div>
                <h3 class="text-xs font-black text-amber-700 dark:text-amber-500 uppercase tracking-widest">
                    {{ __('Raw Material Consumption') }}
                </h3>
            </div>
            
            <div class="p-5 space-y-6">
                @foreach($materialCategories as $cat)
                <div class="p-4 bg-slate-50 dark:bg-white/5 rounded-xl border border-slate-200 dark:border-white/5">
                    <div class="flex justify-between items-center mb-3">
                        <label class="text-sm text-slate-700 dark:text-slate-300 font-bold">{{ $cat->name }}</label>
                        <span class="px-3 py-1 bg-amber-500/10 text-amber-600 dark:text-amber-500 border border-amber-500/20 rounded-full text-[10px] font-black uppercase whitespace-nowrap">
                            {{ __('Available') }}: {{ number_format($cat->available, ($cat->unit === 'molds' ? 0 : 1)) }} {{ __($cat->unit ?? 'units') }}
                        </span>
                    </div>

                    @if($cat->input_mode === 'bags_weight')
                        {{-- FLOUR: bags × weight --}}
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-3" x-data="{
                            bags: 0, weight: 50,
                            get total() { return this.bags * this.weight; }
                        }">
                            <div>
                                <label class="block text-[10px] text-slate-500 uppercase font-bold mb-1">{{ __('Bags Used') }}</label>
                                <input type="number" step="1" min="0" x-model.number="bags"
                                    class="w-full px-4 py-3 bg-white dark:bg-[#0f1115] border border-slate-200 dark:border-white/10 rounded-xl text-sm text-slate-900 dark:text-white focus:outline-none focus:border-amber-500 focus:ring-4 focus:ring-amber-500/10 transition-all font-bold placeholder-slate-400/50"
                                    placeholder="0">
                            </div>
                            <div>
                                <label class="block text-[10px] text-slate-500 uppercase font-bold mb-1">{{ __('Weight per Bag (kg)') }}</label>
                                <input type="number" step="0.01" min="0" x-model.number="weight"
                                    class="w-full px-4 py-3 bg-white dark:bg-[#0f1115] border border-slate-200 dark:border-white/10 rounded-xl text-sm text-slate-900 dark:text-white focus:outline-none focus:border-amber-500 focus:ring-4 focus:ring-amber-500/10 transition-all font-bold placeholder-slate-400/50"
                                    placeholder="50">
                            </div>
                            <div class="flex flex-col justify-end">
                                <div class="text-[10px] text-emerald-500 uppercase font-bold mb-1">{{ __('Total kg') }}</div>
                                <div class="text-lg font-black text-emerald-500 font-mono" x-text="total.toFixed(2) + ' kg'"></div>
                            </div>
                            <input type="hidden" name="consumptions[{{ $cat->id }}]" :value="total">
                            <div x-show="total > {{ $cat->available }}" class="col-span-1 md:col-span-3 text-xs font-bold text-red-500 bg-red-500/10 p-2.5 rounded-lg flex items-center gap-2 mt-1" x-cloak>
                                <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                                {{ __('Consumption exceeds available warehouse stock!') }}
                            </div>
                        </div>

                    @elseif($cat->input_mode === 'cartons_molds')
                        {{-- YEAST: molds dispensed --}}
                        <div x-data="{ dispensed: '' }">
                            <label class="block text-[10px] text-slate-500 uppercase font-bold mb-1">{{ __('Molds Dispensed') }}</label>
                            <input type="number" step="1" min="0" max="{{ floor($cat->available) }}" name="consumptions[{{ $cat->id }}]" x-model.number="dispensed"
                                class="w-full md:w-1/2 px-4 py-3 bg-white dark:bg-[#0f1115] border border-slate-200 dark:border-white/10 rounded-xl text-sm text-slate-900 dark:text-white focus:outline-none focus:border-amber-500 focus:ring-4 focus:ring-amber-500/10 transition-all font-bold placeholder-slate-400/50"
                                placeholder="0">
                            <div x-show="dispensed > {{ $cat->available }}" class="w-full md:w-1/2 text-xs font-bold text-red-500 bg-red-500/10 p-2.5 rounded-lg flex items-center gap-2 mt-3" x-cloak>
                                <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                                {{ __('Consumption exceeds available warehouse stock!') }}
                            </div>
                        </div>

                    @else
                        {{-- SALT / DIESEL: simple quantity --}}
                        <div x-data="{ dispensed: '' }">
                            <label class="block text-[10px] text-slate-500 uppercase font-bold mb-1">
                                {{ $cat->unit === 'liters' ? __('Liters Dispensed') : __('kg Dispensed') }}
                            </label>
                            <input type="number" step="0.01" min="0" max="{{ $cat->available }}" name="consumptions[{{ $cat->id }}]" x-model.number="dispensed"
                                class="w-full md:w-1/2 px-4 py-3 bg-white dark:bg-[#0f1115] border border-slate-200 dark:border-white/10 rounded-xl text-sm text-slate-900 dark:text-white focus:outline-none focus:border-amber-500 focus:ring-4 focus:ring-amber-500/10 transition-all font-bold placeholder-slate-400/50"
                                placeholder="0.00">
                            <div x-show="dispensed > {{ $cat->available }}" class="w-full md:w-1/2 text-xs font-bold text-red-500 bg-red-500/10 p-2.5 rounded-lg flex items-center gap-2 mt-3" x-cloak>
                                <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                                {{ __('Consumption exceeds available warehouse stock!') }}
                            </div>
                        </div>
                    @endif
                </div>
                @endforeach

                <p class="mt-2 text-[10px] text-slate-400 italic flex items-center gap-2">
                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    {{ __('Material usage will be subtracted from current stock levels.') }}
                </p>
            </div>
        </div>

        {{-- ── Finalize Settlement Section ───────────────────── --}}
        <div class="glass-panel rounded-2xl border border-amber-500/20 overflow-hidden shadow-lg shadow-amber-500/5 mt-6">
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
            <div class="p-6 space-y-6">
                {{-- Grid for Final Stats --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    {{-- Net Balance Summary --}}
                    <div class="p-4 rounded-xl border border-amber-500/20 bg-amber-500/5">
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-xs text-slate-500 font-bold uppercase tracking-widest">{{ __('Net Balance') }}</span>
                            <span class="text-lg font-bold {{ $netDayBalance >= 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-600 dark:text-red-400' }}">{{ number_format($netDayBalance, 2) }} {{ __($currencyCode) }}</span>
                        </div>
                        <div class="flex justify-between items-center text-xs text-slate-400">
                            <span>{{ __('Bundles Sold') }}: {{ $bundlesDistributed - $bundlesReturnedByDistributors }}</span>
                            <span>{{ __('Shifts') }}: {{ $workDay->workerShifts->count() }}</span>
                        </div>
                    </div>

                    {{-- Auto-Calculated Bundles (Read-Only) --}}
                    <div class="p-4 rounded-xl border border-blue-500/20 bg-blue-500/5">
                        <div class="flex justify-between items-center">
                            <div>
                                <p class="text-xs font-bold text-blue-600 dark:text-blue-400 uppercase tracking-wider">{{ __('Carried Over Bundles (Unsold)') }}</p>
                                <p class="text-[10px] text-slate-500 mt-0.5">{{ __('Auto-calculated from shift returns.') }}</p>
                            </div>
                            <span class="text-2xl font-black text-blue-600 dark:text-blue-400">{{ $calculatedRemainingBundles }}</span>
                        </div>
                    </div>

                    {{-- Cash Collected from Shifts Summary --}}
                    @if($totalCashFromShifts > 0)
                    <div class="p-4 rounded-xl border border-emerald-500/20 bg-emerald-500/5">
                        <div class="flex justify-between items-center">
                            <p class="text-xs font-bold text-emerald-600 dark:text-emerald-400 uppercase tracking-wider">{{ __('Cash Collected from Shifts') }}</p>
                            <span class="text-lg font-bold text-emerald-600 dark:text-emerald-400">{{ number_format($totalCashFromShifts, 2) }} {{ __($currencyCode) }}</span>
                        </div>
                    </div>
                    @endif
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 items-end">
                    {{-- Carried Over Cash --}}
                    <div>
                        <label class="block text-xs font-semibold text-amber-600 dark:text-amber-400 uppercase tracking-wider mb-2 px-1">
                            {{ __('Carried Over Cash Balance') }}
                            <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <input type="number" step="0.01" name="carried_over_money" required min="0" value="{{ old('carried_over_money') }}"
                                class="block w-full px-4 py-4 bg-slate-50 dark:bg-[#0f1115] border border-amber-500/30 rounded-xl text-xl text-slate-900 dark:text-white focus:outline-none focus:border-amber-400 focus:ring-4 focus:ring-amber-400/10 transition-all font-bold"
                                placeholder="0.00">
                        </div>
                    </div>

                    {{-- Currency Selection --}}
                    <div>
                        <label class="block text-xs font-semibold text-amber-600 dark:text-amber-400 uppercase tracking-wider mb-2 px-1">
                            {{ __('Currency') }}
                        </label>
                        <select name="carried_over_currency_id" x-model="currencyId" @change="setCurrency($event.target.value)"
                            class="block w-full px-4 py-4 bg-slate-50 dark:bg-[#0f1115] border border-amber-500/30 rounded-xl text-sm text-slate-900 dark:text-white focus:outline-none focus:border-amber-400 focus:ring-4 focus:ring-amber-400/10 transition-all font-medium appearance-none">
                            @foreach($currencies as $curr)
                                <option value="{{ $curr->id }}" {{ $curr->is_default ? 'selected' : '' }}>{{ $curr->code }} - {{ $curr->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Exchange Rate (if non-local) --}}
                    <div x-show="!isLocal" x-transition>
                        <label class="block text-xs font-semibold text-amber-600 dark:text-amber-400 uppercase tracking-wider mb-2 px-1">
                            {{ __('Exchange Rate') }}
                        </label>
                        <input type="number" step="0.01" name="carried_over_exchange_rate" :value="exchangeRate" min="0"
                            class="block w-full px-4 py-4 bg-slate-50 dark:bg-[#0f1115] border border-amber-500/30 rounded-xl text-xl text-slate-900 dark:text-white focus:outline-none focus:border-amber-400 focus:ring-4 focus:ring-amber-400/10 transition-all font-bold"
                            placeholder="1.00">
                    </div>

                    {{-- Custom End Time (Optional) --}}
                    <div class="md:col-span-3 mt-2">
                        <label class="block text-xs font-semibold text-amber-600 dark:text-amber-400 uppercase tracking-wider mb-2 px-1">
                            {{ __('Historical End Time (Optional)') }}
                        </label>
                        <div class="relative">
                            <input type="datetime-local" name="custom_end_time"
                                class="block w-full px-4 py-4 bg-slate-50 dark:bg-[#0f1115] border border-slate-200 dark:border-white/10 border-amber-500/30 rounded-xl text-sm text-slate-900 dark:text-white focus:outline-none focus:border-amber-400 focus:ring-4 focus:ring-amber-400/10 transition-all font-medium">
                            <p class="mt-2 text-[10px] text-slate-500 font-bold uppercase tracking-widest">{{ __('Leave blank to use current time.') }}</p>
                        </div>
                    </div>
                </div>

                <div class="pt-4">
                    <button type="submit" 
                        onclick="return confirm('{{ __('WARNING: Closing a work day freezes all sales, expenses, and HR shifts permanently. Proceed?') }}');"
                        class="w-full bg-gradient-to-r from-red-600 to-orange-600 hover:from-red-500 hover:to-orange-500 text-white py-5 rounded-2xl text-base font-black shadow-[0_10px_30px_rgba(239,68,68,0.3)] hover:shadow-[0_15px_40px_rgba(239,68,68,0.4)] transition-all uppercase tracking-[0.2em]">
                        {{ __('Close Work Day Permanently') }}
                    </button>
                    <p class="mt-4 text-center text-[10px] text-slate-400 uppercase tracking-widest font-bold">
                        {{ __('Physical cash left in drawer for the next work day.') }}
                    </p>
                </div>
            </div>
            @else
            <div class="p-8 text-center space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="p-6 rounded-2xl border border-slate-200 dark:border-white/5 bg-slate-50 dark:bg-[#0f1115]">
                        <p class="text-slate-400 text-[10px] uppercase tracking-widest mb-2 font-bold">{{ __('Carried Over Bundles (Unsold)') }}</p>
                        <p class="text-slate-900 dark:text-white font-black text-3xl">{{ $workDay->carried_over_bundles }}</p>
                    </div>
                    <div class="p-6 rounded-2xl border border-slate-200 dark:border-white/5 bg-slate-50 dark:bg-[#0f1115]">
                        <p class="text-slate-400 text-[10px] uppercase tracking-widest mb-2 font-bold">{{ __('Carried Over Cash Balance') }}</p>
                        <p class="text-slate-900 dark:text-white font-black text-3xl">{{ number_format($workDay->carried_over_money, 2) }} {{ $workDay->carriedOverCurrency ? $workDay->carriedOverCurrency->code : __($currencyCode) }}</p>
                    </div>
                </div>
                <div class="flex items-center justify-center gap-2 text-emerald-600 dark:text-emerald-400 font-black uppercase tracking-[0.2em] text-sm py-4">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" /></svg>
                    {{ __('Work day is permanently sealed.') }}
                </div>
            </div>
            @endif
        </div>

    </div>
</form>
@endsection
