@extends('layouts.Admin.App')

@section('content')
<div class="mb-8 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
    <div>
        <h1 class="text-2xl lg:text-3xl font-bold text-slate-900 dark:text-white mb-2 tracking-tight">
            {{ __('Executive') }} <span class="text-transparent bg-clip-text bg-gradient-to-r from-amber-400 to-orange-500">{{ __('Overview') }}</span>
        </h1>
        <p class="text-sm text-slate-500 dark:text-slate-400">
            {{ __('Comprehensive financial analytics and performance metrics.') }}
        </p>
    </div>
    {{-- Period Filter --}}
    <div class="flex flex-wrap items-center gap-3" id="period-filter">
        @php
            $filters = [
                'today' => __('Today'),
                'week' => __('This Week'),
                'month' => __('This Month'),
                'semi' => __('6 Months'),
                'year' => __('This Year'),
                'all' => __('All Time'),
            ];
        @endphp
        
        <div class="flex flex-wrap gap-2">
            @foreach($filters as $key => $label)
                <a href="{{ route('admin.dashboard', ['period' => $key]) }}"
                   class="{{ $period === $key
                       ? 'bg-gradient-to-r from-amber-500 to-orange-500 text-white shadow-[0_0_12px_rgba(245,158,11,0.3)]'
                       : 'bg-white dark:bg-[#0f1115] text-slate-600 dark:text-slate-400 border border-slate-200 dark:border-white/10 hover:border-amber-400 hover:text-amber-600 dark:hover:text-amber-400' }}
                   px-4 py-2 rounded-xl text-xs font-bold uppercase tracking-wider transition-all">
                    {{ $label }}
                </a>
            @endforeach
        </div>

        {{-- Custom Date Range Vertical Separator (desktop) --}}
        <div class="hidden md:block w-px h-8 bg-slate-200 dark:bg-white/10 mx-2"></div>

        {{-- Standardized Custom Date Range Form --}}
        <form action="{{ route('admin.dashboard') }}" method="GET" class="flex flex-wrap items-center gap-2">
            <div class="flex items-center gap-2 bg-white dark:bg-[#0f1115] border border-slate-200 dark:border-white/10 rounded-xl px-3 py-1.5 shadow-sm">
                <input type="date" name="date_from" value="{{ request('date_from') }}" 
                    class="bg-transparent border-none text-xs text-slate-700 dark:text-slate-300 focus:ring-0 p-0 w-28 font-medium"
                    placeholder="{{ __('From') }}">
                <span class="text-slate-400 text-[10px] font-black mx-1">→</span>
                <input type="date" name="date_to" value="{{ request('date_to') }}" 
                    class="bg-transparent border-none text-xs text-slate-700 dark:text-slate-300 focus:ring-0 p-0 w-28 font-medium"
                    placeholder="{{ __('To') }}">
                
                <button type="submit" class="ml-2 p-1.5 bg-amber-500/10 text-amber-500 hover:bg-amber-500 hover:text-white rounded-lg transition-all">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                </button>
            </div>
            
            @if($period === 'custom')
                <a href="{{ route('admin.dashboard') }}" class="text-[10px] font-bold text-red-500 uppercase tracking-tighter hover:underline">
                    {{ __('Clear Range') }}
                </a>
            @endif
        </form>
    </div>
</div>

{{-- Active Work Day Banner --}}
@if($activeWorkDay)
    <div class="glass-panel rounded-2xl border border-emerald-500/20 bg-emerald-500/5 p-6 mb-8 relative overflow-hidden">
        <div class="absolute top-0 {{ app()->getLocale() == 'ar' ? 'left-0 -translate-x-1/4' : 'right-0 translate-x-1/4' }} w-64 h-64 bg-emerald-500/10 rounded-full blur-3xl -translate-y-1/2"></div>
        <div class="relative z-10 flex flex-col lg:flex-row justify-between items-start lg:items-center gap-6">
            <div class="flex-1">
                @if($todayBundlesInActiveShifts > 0)
                <div class="mb-4 p-4 bg-amber-500/10 border border-amber-500/20 rounded-xl flex items-center gap-3">
                    <div class="w-8 h-8 bg-amber-500/20 rounded-lg flex items-center justify-center text-amber-500 shadow-sm">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                    </div>
                    <div class="flex-1">
                        <p class="text-[10px] font-black uppercase tracking-widest text-amber-600 dark:text-amber-500 leading-tight">{{ __('Warning: Active Shifts Detected') }}</p>
                        <p class="text-xs text-amber-700/70 dark:text-amber-400/70 mt-0.5 font-bold">{{ __('There are employees still clocked in. Please ensure they are settled before closing the day.') }}</p>
                    </div>
                </div>
                @endif
                <div class="flex items-center gap-3 mb-2">
                    <span class="relative flex h-3 w-3">
                      <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                      <span class="relative inline-flex rounded-full h-3 w-3 bg-emerald-500"></span>
                    </span>
                    <h2 class="text-xl font-bold text-emerald-900 dark:text-white tracking-wide uppercase">{{ __('Active Work Day') }}</h2>
                </div>
                <p class="text-emerald-400/80 text-sm font-medium tracking-widest uppercase mb-4">
                    {{ __('Commenced At: ') }} {{ $activeWorkDay->start_time->translatedFormat('Y-m-d h:i A') }}
                    <span class="text-slate-500 {{ app()->getLocale() == 'ar' ? 'mr-2' : 'ml-2' }}">({{ $activeWorkDay->start_time->diffForHumans() }})</span>
                </p>
                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-4">
                    <div class="flex flex-col">
                        <span class="text-[10px] uppercase tracking-widest text-slate-500 font-bold mb-1">{{ __('Starting Bundles') }}</span>
                        <span class="text-sm font-black text-blue-500 dark:text-blue-400">{{ number_format($startingBundles) }}</span>
                    </div>
                    <div class="flex flex-col">
                        <span class="text-[10px] uppercase tracking-widest text-slate-500 font-bold mb-1">{{ __('Live Sales') }}</span>
                        <span class="text-sm font-black text-amber-600">{{ number_format($todaySales, 2) }} <span class="text-[10px]">{{ $currencyCode }}</span></span>
                    </div>
                    <div class="flex flex-col">
                        <span class="text-[10px] uppercase tracking-widest text-slate-500 font-bold mb-1">{{ __('Live Expenses') }}</span>
                        <span class="text-sm font-black text-red-500">{{ number_format($todayExpenses, 2) }} <span class="text-[10px]">{{ $currencyCode }}</span></span>
                    </div>
                    <div class="flex flex-col">
                        <span class="text-[10px] uppercase tracking-widest text-slate-500 font-bold mb-1">{{ __('Bundles Sold') }}</span>
                        <span class="text-sm font-black text-emerald-600 dark:text-emerald-400">{{ number_format($todayBundlesSold) }}</span>
                    </div>
                    @if($todayBundlesInActiveShifts > 0)
                    <div class="flex flex-col">
                        <span class="text-[10px] uppercase tracking-widest text-slate-500 font-bold mb-1">{{ __('In Active Shifts') }}</span>
                        <span class="text-sm font-black text-amber-500">{{ number_format($todayBundlesInActiveShifts) }}</span>
                    </div>
                    @endif
                    <div class="flex flex-col">
                        <span class="text-[10px] uppercase tracking-widest text-slate-500 font-bold mb-1">{{ __('Debt Payments') }}</span>
                        <span class="text-sm font-black text-sky-500">{{ number_format($todaySupplierPayments, 2) }} <span class="text-[10px]">{{ $currencyCode }}</span></span>
                    </div>
                </div>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('admin.work_days.showCloseForm', $activeWorkDay) }}" class="px-6 py-4 bg-gradient-to-r from-red-500 to-orange-500 hover:from-red-400 hover:to-orange-400 text-white font-bold rounded-xl text-sm shadow-[0_10px_20px_rgba(239,68,68,0.2)] transition-all uppercase tracking-widest">
                    {{ __('Settle & Close Shift') }}
                </a>
            </div>
        </div>
    </div>
@else
    <div class="glass-panel rounded-2xl border border-slate-200 dark:border-slate-500/20 bg-slate-100/50 dark:bg-black/20 p-6 mb-8 text-center relative overflow-hidden group">
        <div class="absolute inset-0 bg-gradient-to-r from-amber-500/5 to-orange-500/5 opacity-0 group-hover:opacity-100 transition-opacity"></div>
        <div class="relative z-10">
            <div class="w-16 h-16 mx-auto bg-slate-200 dark:bg-slate-800/50 text-slate-500 rounded-full flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4M12 20V4"/></svg>
            </div>
            <h2 class="text-xl font-bold text-slate-900 dark:text-white mb-2 uppercase tracking-wide">{{ __('System is Idle') }}</h2>
            <p class="text-slate-500 dark:text-slate-400 text-sm mb-6 max-w-md mx-auto">{{ __('No active work day is running. Accounting and operations are locked.') }}</p>
            
            @if($startingBundles > 0)
            <div class="flex justify-center gap-8 mb-8">
                <div class="text-center">
                    <p class="text-[10px] uppercase tracking-widest text-slate-500 font-bold mb-1">{{ __('Remaining Bundles (Inventory)') }}</p>
                    <p class="text-xl font-black text-blue-500">{{ number_format($startingBundles) }}</p>
                </div>
            </div>
            @endif

            <div class="flex flex-col sm:flex-row justify-center items-center gap-4">
                <form action="{{ route('admin.work_days.store') }}" method="POST" class="inline-block">
                    @csrf
                    <button type="submit" class="px-10 py-4 bg-gradient-to-r from-amber-500 to-orange-500 hover:from-amber-400 hover:to-orange-400 text-[#121419] font-black rounded-xl text-sm shadow-[0_10px_25px_rgba(245,158,11,0.3)] transition-all uppercase tracking-widest scale-100 hover:scale-105">
                        {{ __('Initialize New Day') }}
                    </button>
                </form>

                @can('create work days')
                <a href="{{ route('admin.work_days.create') }}" class="px-8 py-4 bg-indigo-500/10 text-indigo-400 hover:bg-indigo-500 hover:text-white border border-indigo-500/30 font-black rounded-xl text-sm shadow-[0_10px_25px_rgba(99,102,241,0.15)] transition-all uppercase tracking-widest">
                    {{ __('Start Custom Work Day') }}
                </a>
                @endcan
            </div>
        </div>
    </div>
@endif

@can('view quick access')
{{-- ── Quick Access Actions ───────────────────────────────────── --}}
<div class="mb-8">
    <h3 class="text-xs font-black text-slate-500 dark:text-slate-400 uppercase tracking-[0.2em] mb-4 flex items-center gap-2">
        <span class="w-8 h-px bg-slate-200 dark:bg-white/10"></span>
        {{ __('Quick Access') }}
    </h3>
    <div class="grid grid-cols-2 md:grid-cols-4 xl:grid-cols-8 gap-3">
        {{-- 1. Receive Payment --}}
        @can('view distributions')
        <a href="{{ route('admin.distributors.index') }}" class="glass-panel p-4 rounded-xl border border-slate-200 dark:border-white/5 hover:border-emerald-500/50 hover:bg-emerald-500/5 group transition-all text-center">
            <div class="w-10 h-10 mx-auto bg-emerald-500/10 text-emerald-500 rounded-lg flex items-center justify-center mb-3 group-hover:scale-110 transition-transform shadow-sm">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
            </div>
            <p class="text-[10px] font-black uppercase tracking-tighter text-slate-900 dark:text-white leading-tight">
                {{ __('Receive Payment') }}
            </p>
        </a>
        @endcan

        {{-- 2. New Supply --}}
        @can('create supplies')
        <a href="{{ route('admin.supplies.create') }}" class="glass-panel p-4 rounded-xl border border-slate-200 dark:border-white/5 hover:border-amber-500/50 hover:bg-amber-500/5 group transition-all text-center">
            <div class="w-10 h-10 mx-auto bg-amber-500/10 text-amber-500 rounded-lg flex items-center justify-center mb-3 group-hover:scale-110 transition-transform shadow-sm">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
            </div>
            <p class="text-[10px] font-black uppercase tracking-tighter text-slate-900 dark:text-white leading-tight">
                {{ __('Record Supply') }}
            </p>
        </a>
        @endcan

        {{-- 3. Pay Supplier --}}
        @can('view accounts debts')
        <a href="{{ route('admin.accounts.debts') }}" class="glass-panel p-4 rounded-xl border border-slate-200 dark:border-white/5 hover:border-blue-500/50 hover:bg-blue-500/5 group transition-all text-center">
            <div class="w-10 h-10 mx-auto bg-blue-500/10 text-blue-500 rounded-lg flex items-center justify-center mb-3 group-hover:scale-110 transition-transform shadow-sm">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
            </div>
            <p class="text-[10px] font-black uppercase tracking-tighter text-slate-900 dark:text-white leading-tight">
                {{ __('Pay Supplier') }}
            </p>
        </a>
        @endcan

        {{-- 4. Worker Clock In --}}
        @can('create attendance')
        <a href="{{ route('admin.attendance.index') }}" class="glass-panel p-4 rounded-xl border border-slate-200 dark:border-white/5 hover:border-indigo-500/50 hover:bg-indigo-500/5 group transition-all text-center">
            <div class="w-10 h-10 mx-auto bg-indigo-500/10 text-indigo-500 rounded-lg flex items-center justify-center mb-3 group-hover:scale-110 transition-transform shadow-sm">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <p class="text-[10px] font-black uppercase tracking-tighter text-slate-900 dark:text-white leading-tight">
                {{ __('Worker Clock In') }}
            </p>
        </a>
        @endcan

        {{-- 5. Record Expense --}}
        @can('create expenses')
        <a href="{{ route('admin.expenses.index') }}" class="glass-panel p-4 rounded-xl border border-slate-200 dark:border-white/5 hover:border-red-500/50 hover:bg-red-500/5 group transition-all text-center">
            <div class="w-10 h-10 mx-auto bg-red-500/10 text-red-500 rounded-lg flex items-center justify-center mb-3 group-hover:scale-110 transition-transform shadow-sm">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <p class="text-[10px] font-black uppercase tracking-tighter text-slate-900 dark:text-white leading-tight">
                {{ __('Record Expense') }}
            </p>
        </a>
        @endcan

        {{-- 6. New Distribution --}}
        @can('create distributions')
        <a href="{{ route('admin.distributions.index') }}" class="glass-panel p-4 rounded-xl border border-slate-200 dark:border-white/5 hover:border-cyan-500/50 hover:bg-cyan-500/5 group transition-all text-center">
            <div class="w-10 h-10 mx-auto bg-cyan-500/10 text-cyan-500 rounded-lg flex items-center justify-center mb-3 group-hover:scale-110 transition-transform shadow-sm">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"/></svg>
            </div>
            <p class="text-[10px] font-black uppercase tracking-tighter text-slate-900 dark:text-white leading-tight">
                {{ __('New Distribution') }}
            </p>
        </a>
        @endcan

        {{-- 7. Inventory Stocktake --}}
        @can('create warehouse')
        <a href="{{ route('admin.warehouse.inventory.create') }}" class="glass-panel p-4 rounded-xl border border-slate-200 dark:border-white/5 hover:border-violet-500/50 hover:bg-violet-500/5 group transition-all text-center">
            <div class="w-10 h-10 mx-auto bg-violet-500/10 text-violet-500 rounded-lg flex items-center justify-center mb-3 group-hover:scale-110 transition-transform shadow-sm">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
            </div>
            <p class="text-[10px] font-black uppercase tracking-tighter text-slate-900 dark:text-white leading-tight">
                {{ __('Inventory Stocktake') }}
            </p>
        </a>
        @endcan

        {{-- 8. System Settings --}}
        @if(auth()->user()->can('manage settings') || auth()->user()->can('view settings general') || auth()->user()->can('view settings currencies') || auth()->user()->can('view settings categories'))
        <a href="{{ route('admin.settings.index') }}" class="glass-panel p-4 rounded-xl border border-slate-200 dark:border-white/5 hover:border-slate-400 group transition-all text-center">
            <div class="w-10 h-10 mx-auto bg-slate-500/10 text-slate-500 rounded-lg flex items-center justify-center mb-3 group-hover:scale-110 transition-transform shadow-sm">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            </div>
            <p class="text-[10px] font-black uppercase tracking-tighter text-slate-900 dark:text-white leading-tight">
                {{ __('System Settings') }}
            </p>
        </a>
        @endif
    </div>
</div>
@endcan

@can('view analytics')
{{-- ── Financial Summary ──────────────────────────────────────── --}}
<div class="glass-panel rounded-2xl border border-slate-200 dark:border-white/5 overflow-hidden mb-8">
    {{-- Section Header --}}
    <div class="px-6 py-4 border-b border-slate-200 dark:border-white/5 bg-slate-50 dark:bg-black/20 flex items-center justify-between">
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-amber-500 to-orange-500 flex items-center justify-center shadow-lg shadow-amber-500/20">
                <svg class="w-4.5 h-4.5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
            </div>
            <div>
                <h3 class="text-sm font-bold text-slate-900 dark:text-white">{{ __('Financial Summary') }}</h3>
                <p class="text-[10px] text-slate-500 dark:text-slate-400 mt-0.5">{{ __('Key performance indicators for the selected period.') }}</p>
            </div>
        </div>
    </div>

    {{-- Metric Rows --}}
    <div class="divide-y divide-slate-100 dark:divide-white/5">

        {{-- 1 · Total Revenue --}}
        <div class="flex items-center gap-5 px-6 py-5 hover:bg-emerald-500/[0.03] transition-colors group">
            <div class="flex-shrink-0 w-12 h-12 rounded-xl bg-emerald-500/10 flex items-center justify-center group-hover:scale-110 transition-transform">
                <svg class="w-6 h-6 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest">{{ __('Collected Revenue') }}</p>
                <p class="text-[10px] text-slate-400 dark:text-slate-500 mt-0.5">{{ __('Actual cash collected from distributions, shifts and settlements.') }}</p>
            </div>
            <div class="text-{{ app()->getLocale() == 'ar' ? 'left' : 'right' }} flex-shrink-0">
                <p class="text-2xl font-black text-slate-900 dark:text-white tabular-nums">{{ number_format($totalRevenue, 2) }}</p>
                <p class="text-[10px] text-emerald-500 font-bold mt-0.5">{{ $currencyCode }}</p>
            </div>
        </div>

        {{-- 2 · Total Expenses --}}
        <div class="flex items-center gap-5 px-6 py-5 hover:bg-red-500/[0.03] transition-colors group">
            <div class="flex-shrink-0 w-12 h-12 rounded-xl bg-red-500/10 flex items-center justify-center group-hover:scale-110 transition-transform">
                <svg class="w-6 h-6 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"/></svg>
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest">{{ __('Total Expenses') }}</p>
                <p class="text-[10px] text-slate-400 dark:text-slate-500 mt-0.5">{{ __('Supplies, wages, freight, and operational costs.') }}</p>
            </div>
            <div class="text-{{ app()->getLocale() == 'ar' ? 'left' : 'right' }} flex-shrink-0">
                <p class="text-2xl font-black text-slate-900 dark:text-white tabular-nums">{{ number_format($totalExpenses, 2) }}</p>
                <p class="text-[10px] text-red-500 font-bold mt-0.5">{{ $currencyCode }}</p>
            </div>
        </div>

        {{-- 3 · Cash Flow (Debts) --}}
        <div class="flex items-center gap-5 px-6 py-5 hover:bg-sky-500/[0.03] transition-colors group">
            <div class="flex-shrink-0 w-12 h-12 rounded-xl bg-sky-500/10 flex items-center justify-center group-hover:scale-110 transition-transform">
                <svg class="w-6 h-6 text-sky-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest">{{ __('Cash Flow (Debts)') }}</p>
                <p class="text-[10px] text-slate-400 dark:text-slate-500 mt-0.5">{{ __('Supplier debt settlements (not counted in expenses).') }}</p>
            </div>
            <div class="text-{{ app()->getLocale() == 'ar' ? 'left' : 'right' }} flex-shrink-0">
                <p class="text-2xl font-black text-slate-900 dark:text-white tabular-nums">{{ number_format($totalSupplierPayments, 2) }}</p>
                <p class="text-[10px] text-sky-500 font-bold mt-0.5">{{ $currencyCode }}</p>
            </div>
        </div>

        {{-- 4 · Net Profit (Highlighted) --}}
        <div class="flex items-center gap-5 px-6 py-6 {{ $netProfit >= 0 ? 'bg-emerald-500/[0.04]' : 'bg-red-500/[0.04]' }} group">
            <div class="flex-shrink-0 w-12 h-12 rounded-xl {{ $netProfit >= 0 ? 'bg-emerald-500/15' : 'bg-red-500/15' }} flex items-center justify-center group-hover:scale-110 transition-transform">
                <svg class="w-6 h-6 {{ $netProfit >= 0 ? 'text-emerald-500' : 'text-red-500' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/></svg>
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-xs font-bold {{ $netProfit >= 0 ? 'text-emerald-700 dark:text-emerald-400' : 'text-red-700 dark:text-red-400' }} uppercase tracking-widest">{{ __('Collected Net Profit') }}</p>
                <p class="text-[10px] text-slate-400 dark:text-slate-500 mt-0.5">{{ __('Actual cash in minus all operational expenses.') }}</p>
            </div>
            <div class="text-{{ app()->getLocale() == 'ar' ? 'left' : 'right' }} flex-shrink-0">
                <p class="text-3xl font-black {{ $netProfit >= 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-600 dark:text-red-400' }} tabular-nums">{{ $netProfit >= 0 ? '+' : '' }}{{ number_format($netProfit, 2) }}</p>
                <p class="text-[10px] {{ $netProfit >= 0 ? 'text-emerald-500' : 'text-red-500' }} font-bold mt-0.5">{{ $currencyCode }}</p>
            </div>
        </div>

        {{-- 5 · Profit Margin --}}
        <div class="flex items-center gap-5 px-6 py-5 hover:bg-violet-500/[0.03] transition-colors group">
            <div class="flex-shrink-0 w-12 h-12 rounded-xl bg-violet-500/10 flex items-center justify-center group-hover:scale-110 transition-transform">
                <svg class="w-6 h-6 text-violet-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest">{{ __('Profit Margin') }}</p>
                <div class="w-full max-w-xs bg-slate-200 dark:bg-white/5 rounded-full h-1.5 mt-2 overflow-hidden">
                    <div class="h-full rounded-full transition-all duration-700 {{ $profitMargin >= 20 ? 'bg-gradient-to-r from-emerald-400 to-emerald-500' : ($profitMargin >= 0 ? 'bg-gradient-to-r from-amber-400 to-amber-500' : 'bg-gradient-to-r from-red-400 to-red-500') }}" style="width: {{ min(abs($profitMargin), 100) }}%;"></div>
                </div>
            </div>
            <div class="text-{{ app()->getLocale() == 'ar' ? 'left' : 'right' }} flex-shrink-0 flex items-center gap-3">
                <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-[10px] font-bold uppercase tracking-widest {{ $profitMargin >= 20 ? 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400' : ($profitMargin >= 0 ? 'bg-amber-500/10 text-amber-600 dark:text-amber-400' : 'bg-red-500/10 text-red-600 dark:text-red-400') }}">
                    {{ $profitMargin >= 20 ? __('Healthy') : ($profitMargin >= 0 ? __('Low') : __('Loss')) }}
                </span>
                <p class="text-2xl font-black text-slate-900 dark:text-white tabular-nums">{{ $profitMargin }}%</p>
            </div>
        </div>

        {{-- 6 · Bundles Sold --}}
        <div class="flex items-center gap-5 px-6 py-5 hover:bg-blue-500/[0.03] transition-colors group">
            <div class="flex-shrink-0 w-12 h-12 rounded-xl bg-blue-500/10 flex items-center justify-center group-hover:scale-110 transition-transform">
                <svg class="w-6 h-6 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest">{{ __('Bundles Sold') }}</p>
                <p class="text-[10px] text-slate-400 dark:text-slate-500 mt-0.5">{{ __('Total bread bundle units distributed.') }}</p>
            </div>
            <div class="text-{{ app()->getLocale() == 'ar' ? 'left' : 'right' }} flex-shrink-0">
                <p class="text-2xl font-black text-slate-900 dark:text-white tabular-nums">{{ number_format($netBundlesSold) }}</p>
                <p class="text-[10px] text-blue-500 font-bold mt-0.5">{{ __('bundles') }}</p>
            </div>
        </div>

        {{-- 7 · Work Days --}}
        <div class="flex items-center gap-5 px-6 py-5 hover:bg-cyan-500/[0.03] transition-colors group">
            <div class="flex-shrink-0 w-12 h-12 rounded-xl bg-cyan-500/10 flex items-center justify-center group-hover:scale-110 transition-transform">
                <svg class="w-6 h-6 text-cyan-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest">{{ __('Work Days') }}</p>
                <p class="text-[10px] text-slate-400 dark:text-slate-500 mt-0.5">{{ __('Settled accounting periods in this range.') }}</p>
            </div>
            <div class="text-{{ app()->getLocale() == 'ar' ? 'left' : 'right' }} flex-shrink-0">
                <p class="text-2xl font-black text-slate-900 dark:text-white tabular-nums">{{ $workDaysCount }}</p>
                <p class="text-[10px] text-cyan-500 font-bold mt-0.5">{{ __('Settled Periods') }}</p>
            </div>
        </div>

    </div>
</div>

{{-- ── Revenue vs Expenses Chart ─────────────────────────────── --}}
@if($trendData->count() > 1)
<div class="glass-panel rounded-2xl border border-slate-200 dark:border-white/5 p-6 mb-8">
    <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-1">{{ __('Revenue vs Expenses Trend') }}</h3>
    <p class="text-xs text-slate-500 dark:text-slate-400 mb-4">{{ __('Daily comparison of income and expenditure.') }}</p>
    <div class="relative" style="height: 260px;">
        <canvas id="trendChart"></canvas>
    </div>
</div>
@endif

{{-- ── Two-column: Outstanding Balances ─────────────────────── --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
    {{-- Distributor Balances --}}
    <div class="glass-panel rounded-2xl border border-slate-200 dark:border-white/5 overflow-hidden">
        <div class="p-5 border-b border-slate-200 dark:border-white/5 bg-slate-50 dark:bg-black/20 flex items-center justify-between">
            <div>
                <h3 class="text-sm font-bold text-slate-900 dark:text-white">{{ __('Distributor Outstanding Balances') }}</h3>
                <p class="text-[10px] text-slate-500 dark:text-slate-400 mt-0.5">{{ __('Amounts owed by distributors (all time).') }}</p>
            </div>
            @php $totalDistOutstanding = $distributorBalances->sum('outstanding'); @endphp
            <span class="text-sm font-bold {{ $totalDistOutstanding > 0 ? 'text-amber-600 dark:text-amber-400' : 'text-emerald-600 dark:text-emerald-400' }}">
                {{ number_format($totalDistOutstanding, 2) }} {{ $currencyCode }}
            </span>
        </div>
        <div class="overflow-x-auto max-h-64 overflow-y-auto">
            <table class="w-full text-left">
                <thead class="sticky top-0 bg-slate-100 dark:bg-[#0f1115]">
                    <tr class="text-[10px] uppercase tracking-widest text-slate-500 font-bold border-b border-slate-200 dark:border-white/5">
                        <th class="p-3">{{ __('Distributor') }}</th>
                        <th class="p-3">{{ __('Billed') }}</th>
                        <th class="p-3">{{ __('Paid') }}</th>
                        <th class="p-3">{{ __('Outstanding') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-white/5">
                    @forelse($distributorBalances->where('outstanding', '>', 0) as $dist)
                    <tr class="hover:bg-slate-50 dark:hover:bg-white/5 transition-colors">
                        <td class="p-3 text-sm font-semibold text-slate-900 dark:text-white">{{ $dist->first_name }} {{ $dist->last_name }}</td>
                        <td class="p-3 text-xs text-slate-600 dark:text-slate-300">{{ number_format($dist->total_billed ?? 0, 2) }}</td>
                        <td class="p-3 text-xs text-emerald-600 dark:text-emerald-400">{{ number_format(($dist->total_paid ?? 0) + ($dist->total_refunded ?? 0), 2) }}</td>
                        <td class="p-3 text-sm font-bold text-amber-600 dark:text-amber-400">{{ number_format($dist->outstanding, 2) }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="p-6 text-center text-slate-400 text-xs">{{ __('No outstanding balances.') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Supplier Balances --}}
    <div class="glass-panel rounded-2xl border border-slate-200 dark:border-white/5 overflow-hidden">
        <div class="p-5 border-b border-slate-200 dark:border-white/5 bg-slate-50 dark:bg-black/20 flex items-center justify-between">
            <div>
                <h3 class="text-sm font-bold text-slate-900 dark:text-white">{{ __('Supplier Outstanding Balances') }}</h3>
                <p class="text-[10px] text-slate-500 dark:text-slate-400 mt-0.5">{{ __('Amounts owed to suppliers (all time).') }}</p>
            </div>
            @php $totalSupOutstanding = $supplierBalances->sum('outstanding'); @endphp
            <span class="text-sm font-bold {{ $totalSupOutstanding > 0 ? 'text-red-600 dark:text-red-400' : 'text-emerald-600 dark:text-emerald-400' }}">
                {{ number_format($totalSupOutstanding, 2) }} {{ $currencyCode }}
            </span>
        </div>
        <div class="overflow-x-auto max-h-64 overflow-y-auto">
            <table class="w-full text-left">
                <thead class="sticky top-0 bg-slate-100 dark:bg-[#0f1115]">
                    <tr class="text-[10px] uppercase tracking-widest text-slate-500 font-bold border-b border-slate-200 dark:border-white/5">
                        <th class="p-3">{{ __('Supplier') }}</th>
                        <th class="p-3">{{ __('Owed') }}</th>
                        <th class="p-3">{{ __('Paid') }}</th>
                        <th class="p-3">{{ __('Outstanding') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-white/5">
                    @forelse($supplierBalances->where('outstanding', '>', 0) as $sup)
                    <tr class="hover:bg-slate-50 dark:hover:bg-white/5 transition-colors">
                        <td class="p-3 text-sm font-semibold text-slate-900 dark:text-white">{{ $sup->first_name }} {{ $sup->last_name }}</td>
                        <td class="p-3 text-xs text-slate-600 dark:text-slate-300">{{ number_format($sup->total_owed ?? 0, 2) }}</td>
                        <td class="p-3 text-xs text-emerald-600 dark:text-emerald-400">{{ number_format($sup->total_paid_amount ?? 0, 2) }}</td>
                        <td class="p-3 text-sm font-bold text-red-600 dark:text-red-400">{{ number_format($sup->outstanding, 2) }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="p-6 text-center text-slate-400 text-xs">{{ __('No outstanding balances.') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- ── Two-column: Expense Breakdown + Top Distributors ──────── --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
    {{-- Expense Breakdown --}}
    <div class="glass-panel rounded-2xl border border-slate-200 dark:border-white/5 p-6">
        <h3 class="text-sm font-bold text-slate-900 dark:text-white mb-1">{{ __('Expense Breakdown') }}</h3>
        <p class="text-[10px] text-slate-500 dark:text-slate-400 mb-4">{{ __('Categorized expenditure for the selected period.') }}</p>
        <div class="space-y-3">
            @foreach($expenseBreakdown as $item)
                @php $pct = $totalExpenses > 0 ? round(($item['value'] / $totalExpenses) * 100, 1) : 0; @endphp
                <div>
                    <div class="flex justify-between items-center mb-1">
                        <span class="text-xs font-semibold text-slate-700 dark:text-slate-300">{{ $item['label'] }}</span>
                        <span class="text-xs font-bold text-slate-900 dark:text-white">{{ number_format($item['value'], 2) }} <span class="text-slate-400 text-[10px]">{{ $currencyCode }} ({{ $pct }}%)</span></span>
                    </div>
                    <div class="w-full bg-slate-200 dark:bg-white/5 rounded-full h-2 overflow-hidden">
                        <div class="h-full rounded-full transition-all duration-700" style="width: {{ $pct }}%; background-color: {{ $item['color'] }};"></div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    {{-- Top Distributors --}}
    <div class="glass-panel rounded-2xl border border-slate-200 dark:border-white/5 p-6">
        <h3 class="text-sm font-bold text-slate-900 dark:text-white mb-1">{{ __('Top Distributors') }}</h3>
        <p class="text-[10px] text-slate-500 dark:text-slate-400 mb-4">{{ __('Ranked by sales volume for the selected period.') }}</p>
        <div class="space-y-3">
            @forelse($topDistributors as $i => $td)
                @php $maxSales = $topDistributors->first()->period_sales ?? 1; $pct = $maxSales > 0 ? round((($td->period_sales ?? 0) / $maxSales) * 100, 1) : 0; @endphp
                <div class="flex items-center gap-3">
                    <div class="w-7 h-7 rounded-lg flex items-center justify-center text-xs font-black
                        {{ $i === 0 ? 'bg-amber-500/20 text-amber-600' : ($i === 1 ? 'bg-slate-400/20 text-slate-500' : 'bg-orange-500/20 text-orange-500') }}">
                        {{ $i + 1 }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex justify-between items-baseline mb-1">
                            <span class="text-xs font-semibold text-slate-900 dark:text-white truncate">{{ $td->first_name }} {{ $td->last_name }}</span>
                            <span class="text-xs font-bold text-amber-600 dark:text-amber-400 {{ app()->getLocale() == 'ar' ? 'mr-2' : 'ml-2' }} whitespace-nowrap">{{ number_format($td->period_sales ?? 0, 2) }} {{ $currencyCode }}</span>
                        </div>
                        <div class="w-full bg-slate-200 dark:bg-white/5 rounded-full h-1.5 overflow-hidden">
                            <div class="h-full rounded-full bg-gradient-to-r from-amber-400 to-orange-500 transition-all duration-700" style="width: {{ $pct }}%;"></div>
                        </div>
                        <p class="text-[10px] text-slate-400 mt-0.5">{{ number_format($td->period_bundles ?? 0) }} {{ __('bundles') }}</p>
                    </div>
                </div>
            @empty
                <p class="text-xs text-slate-400 text-center py-4">{{ __('No distribution data for this period.') }}</p>
            @endforelse
        </div>
    </div>
</div>

{{-- ── Recent Settled Periods ────────────────────────────────── --}}
<div class="glass-panel rounded-2xl border border-slate-200 dark:border-white/5 overflow-hidden">
    <div class="p-6 border-b border-slate-200 dark:border-white/5 bg-slate-50 dark:bg-black/20">
        <h3 class="text-lg font-bold text-slate-900 dark:text-white">{{ __('Recent Financial Periods (Settled)') }}</h3>
        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">{{ __('Historical read-only ledger arrays mapping settled shifts.') }}</p>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-100 dark:bg-black/10 border-b border-slate-200 dark:border-white/5 text-[10px] uppercase tracking-widest text-slate-500 font-bold">
                    <th class="p-4 w-16">{{ __('Period') }}</th>
                    <th class="p-4">{{ __('Date Interval') }}</th>
                    <th class="p-4">{{ __('Closing Admin') }}</th>
                    <th class="p-4">{{ __('Closed Sales') }}</th>
                    <th class="p-4">{{ __('Closed Expenses') }}</th>
                    <th class="p-4">{{ __('Net Return') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-white/5">
                @forelse($lastDays as $day)
                    @php $dayProfit = $day->total_sales_at_close - $day->total_expenses_at_close; @endphp
                    <tr class="hover:bg-slate-50 dark:hover:bg-white/5 transition-colors">
                        <td class="p-4 text-xs font-medium text-slate-500">#{{ $day->id }}</td>
                        <td class="p-4">
                            <p class="text-sm font-bold text-slate-900 dark:text-white tracking-wide">{{ $day->start_time->translatedFormat('Y-m-d') }}</p>
                            <p class="text-[10px] text-slate-500 mt-0.5">{{ $day->start_time->translatedFormat('H:i') }} - {{ $day->end_time->translatedFormat('H:i') }}</p>
                        </td>
                        <td class="p-4">
                            <span class="text-xs text-slate-700 dark:text-slate-300 font-medium px-2.5 py-1 bg-slate-100 dark:bg-white/5 border border-slate-200 dark:border-white/10 rounded">
                                {{ $day->closedBy->name ?? 'System' }}
                            </span>
                        </td>
                        <td class="p-4"><span class="text-emerald-600 dark:text-emerald-400 font-bold text-sm">+ {{ number_format($day->total_sales_at_close, 2) }}</span></td>
                        <td class="p-4"><span class="text-red-500 dark:text-red-400 font-bold text-sm">- {{ number_format($day->total_expenses_at_close, 2) }}</span></td>
                        <td class="p-4">
                            <span class="{{ $dayProfit >= 0 ? 'text-amber-500' : 'text-rose-400' }} font-bold text-sm">
                                {{ $dayProfit >= 0 ? '+' : '' }}{{ number_format($dayProfit, 2) }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="p-8 text-center text-slate-500 text-sm">{{ __('No historical periods settled yet.') }}</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endcan

@endsection

@push('scripts')
@if($trendData->count() > 1)
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('trendChart').getContext('2d');
    const isDark = document.documentElement.classList.contains('dark');
    const gridColor = isDark ? 'rgba(255,255,255,0.05)' : 'rgba(0,0,0,0.06)';
    const textColor = isDark ? '#94a3b8' : '#64748b';

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: @json($trendData->pluck('date')),
            datasets: [
                {
                    label: '{{ __("Revenue") }}',
                    data: @json($trendData->pluck('revenue')),
                    backgroundColor: isDark ? 'rgba(16,185,129,0.4)' : 'rgba(16,185,129,0.6)',
                    borderColor: '#10b981',
                    borderWidth: 1,
                    borderRadius: 6,
                    barPercentage: 0.6,
                },
                {
                    label: '{{ __("Expenses") }}',
                    data: @json($trendData->pluck('expenses')),
                    backgroundColor: isDark ? 'rgba(239,68,68,0.4)' : 'rgba(239,68,68,0.6)',
                    borderColor: '#ef4444',
                    borderWidth: 1,
                    borderRadius: 6,
                    barPercentage: 0.6,
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top',
                    labels: { color: textColor, font: { size: 11, weight: 'bold' }, padding: 20 }
                }
            },
            scales: {
                x: { grid: { display: false }, ticks: { color: textColor, font: { size: 10 } } },
                y: { grid: { color: gridColor }, ticks: { color: textColor, font: { size: 10 } }, beginAtZero: true }
            }
        }
    });
});
</script>
@endif
@endpush
