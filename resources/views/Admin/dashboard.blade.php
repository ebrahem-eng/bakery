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
    <div class="flex flex-wrap gap-2" id="period-filter">
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
</div>

{{-- Active Work Day Banner --}}
@if($activeWorkDay)
    <div class="glass-panel rounded-2xl border border-emerald-500/20 bg-emerald-500/5 p-6 mb-8 relative overflow-hidden">
        <div class="absolute top-0 {{ app()->getLocale() == 'ar' ? 'left-0 -translate-x-1/4' : 'right-0 translate-x-1/4' }} w-64 h-64 bg-emerald-500/10 rounded-full blur-3xl -translate-y-1/2"></div>
        <div class="relative z-10 flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
            <div>
                <div class="flex items-center gap-3 mb-2">
                    <span class="relative flex h-3 w-3">
                      <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                      <span class="relative inline-flex rounded-full h-3 w-3 bg-emerald-500"></span>
                    </span>
                    <h2 class="text-xl font-bold text-emerald-900 dark:text-white tracking-wide">{{ __('ACTIVE WORK DAY') }}</h2>
                </div>
                <p class="text-emerald-400/80 text-sm font-medium tracking-widest uppercase">
                    {{ __('Commenced At: ') }} {{ $activeWorkDay->start_time->translatedFormat('Y-m-d h:i A') }}
                    <span class="text-slate-500 {{ app()->getLocale() == 'ar' ? 'mr-2' : 'ml-2' }}">({{ $activeWorkDay->start_time->diffForHumans() }})</span>
                </p>
                <div class="mt-3 flex gap-4 text-xs">
                    <span class="text-emerald-600 dark:text-emerald-400 font-bold">{{ __('Live Sales') }}: {{ number_format($todaySales, 2) }} {{ $currencyCode }}</span>
                    <span class="text-red-500 dark:text-red-400 font-bold">{{ __('Live Expenses') }}: {{ number_format($todayExpenses, 2) }} {{ $currencyCode }}</span>
                    <span class="text-blue-500 dark:text-blue-400 font-bold">{{ __('Bundles') }}: {{ $todayBundlesSold }}</span>
                </div>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('admin.work_days.showCloseForm', $activeWorkDay) }}" class="px-6 py-3 bg-gradient-to-r from-red-500 to-orange-500 hover:from-red-400 hover:to-orange-400 text-white font-bold rounded-xl text-sm shadow-[0_0_15px_rgba(239,68,68,0.3)] transition-all">
                    {{ __('Settle & Close Shift') }}
                </a>
            </div>
        </div>
    </div>
@else
    <div class="glass-panel rounded-2xl border border-slate-200 dark:border-slate-500/20 bg-slate-100/50 dark:bg-black/20 p-6 mb-8 text-center">
        <div class="w-16 h-16 mx-auto bg-slate-200 dark:bg-slate-800/50 text-slate-500 rounded-full flex items-center justify-center mb-4">
            <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4M12 20V4"/></svg>
        </div>
        <h2 class="text-xl font-bold text-slate-900 dark:text-white mb-2">{{ __('System is Idle') }}</h2>
        <p class="text-slate-500 dark:text-slate-400 text-sm mb-6">{{ __('No active work day is running. Accounting and operations are locked.') }}</p>
        <form action="{{ route('admin.work_days.store') }}" method="POST" class="inline-block">
            @csrf
            <button type="submit" class="px-8 py-3 bg-gradient-to-r from-amber-500 to-orange-500 hover:from-amber-400 hover:to-orange-400 text-[#121419] font-bold rounded-xl text-sm shadow-[0_0_15px_rgba(245,158,11,0.3)] transition-all uppercase tracking-widest">
                {{ __('Initialize New Day') }}
            </button>
        </form>
    </div>
@endif

{{-- ── KPI Cards ─────────────────────────────────────────────── --}}
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-5 mb-8">
    {{-- Revenue --}}
    <div class="glass-panel p-5 rounded-2xl border border-slate-200 dark:border-white/5 relative overflow-hidden group">
        <div class="absolute top-0 {{ app()->getLocale() == 'ar' ? 'left-0' : 'right-0' }} w-20 h-20 bg-emerald-500/10 rounded-full blur-2xl group-hover:bg-emerald-500/20 transition-all"></div>
        <div class="flex items-center gap-2 mb-2">
            <div class="w-8 h-8 bg-emerald-500/10 rounded-lg flex items-center justify-center">
                <svg class="w-4 h-4 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
            </div>
            <p class="text-[10px] text-slate-500 dark:text-slate-400 font-bold uppercase tracking-widest">{{ __('Total Revenue') }}</p>
        </div>
        <h3 class="text-2xl font-black text-slate-900 dark:text-white">{{ number_format($totalRevenue, 2) }}</h3>
        <p class="text-[10px] text-emerald-600 dark:text-emerald-400 font-medium mt-1">{{ $currencyCode }}</p>
    </div>

    {{-- Expenses --}}
    <div class="glass-panel p-5 rounded-2xl border border-slate-200 dark:border-white/5 relative overflow-hidden group">
        <div class="absolute top-0 {{ app()->getLocale() == 'ar' ? 'left-0' : 'right-0' }} w-20 h-20 bg-red-500/10 rounded-full blur-2xl group-hover:bg-red-500/20 transition-all"></div>
        <div class="flex items-center gap-2 mb-2">
            <div class="w-8 h-8 bg-red-500/10 rounded-lg flex items-center justify-center">
                <svg class="w-4 h-4 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"/></svg>
            </div>
            <p class="text-[10px] text-slate-500 dark:text-slate-400 font-bold uppercase tracking-widest">{{ __('Total Expenses') }}</p>
        </div>
        <h3 class="text-2xl font-black text-slate-900 dark:text-white">{{ number_format($totalExpenses, 2) }}</h3>
        <p class="text-[10px] text-red-600 dark:text-red-400 font-medium mt-1">{{ $currencyCode }}</p>
    </div>

    {{-- Net Profit --}}
    <div class="glass-panel p-5 rounded-2xl border border-slate-200 dark:border-white/5 relative overflow-hidden group">
        <div class="absolute top-0 {{ app()->getLocale() == 'ar' ? 'left-0' : 'right-0' }} w-20 h-20 bg-amber-500/10 rounded-full blur-2xl group-hover:bg-amber-500/20 transition-all"></div>
        <div class="flex items-center gap-2 mb-2">
            <div class="w-8 h-8 bg-amber-500/10 rounded-lg flex items-center justify-center">
                <svg class="w-4 h-4 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/></svg>
            </div>
            <p class="text-[10px] text-slate-500 dark:text-slate-400 font-bold uppercase tracking-widest">{{ __('Net Profit') }}</p>
        </div>
        <h3 class="text-2xl font-black {{ $netProfit >= 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-600 dark:text-red-400' }}">{{ $netProfit >= 0 ? '+' : '' }}{{ number_format($netProfit, 2) }}</h3>
        <p class="text-[10px] text-slate-400 font-medium mt-1">{{ $currencyCode }}</p>
    </div>

    {{-- Margin --}}
    <div class="glass-panel p-5 rounded-2xl border border-slate-200 dark:border-white/5 relative overflow-hidden group">
        <div class="absolute top-0 {{ app()->getLocale() == 'ar' ? 'left-0' : 'right-0' }} w-20 h-20 bg-violet-500/10 rounded-full blur-2xl group-hover:bg-violet-500/20 transition-all"></div>
        <div class="flex items-center gap-2 mb-2">
            <div class="w-8 h-8 bg-violet-500/10 rounded-lg flex items-center justify-center">
                <svg class="w-4 h-4 text-violet-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
            </div>
            <p class="text-[10px] text-slate-500 dark:text-slate-400 font-bold uppercase tracking-widest">{{ __('Profit Margin') }}</p>
        </div>
        <h3 class="text-2xl font-black text-slate-900 dark:text-white">{{ $profitMargin }}%</h3>
        <p class="text-[10px] {{ $profitMargin >= 0 ? 'text-emerald-500' : 'text-red-500' }} font-medium mt-1">{{ $profitMargin >= 20 ? __('Healthy') : ($profitMargin >= 0 ? __('Low') : __('Loss')) }}</p>
    </div>

    {{-- Bundles Sold --}}
    <div class="glass-panel p-5 rounded-2xl border border-slate-200 dark:border-white/5 relative overflow-hidden group">
        <div class="absolute top-0 {{ app()->getLocale() == 'ar' ? 'left-0' : 'right-0' }} w-20 h-20 bg-blue-500/10 rounded-full blur-2xl group-hover:bg-blue-500/20 transition-all"></div>
        <div class="flex items-center gap-2 mb-2">
            <div class="w-8 h-8 bg-blue-500/10 rounded-lg flex items-center justify-center">
                <svg class="w-4 h-4 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
            </div>
            <p class="text-[10px] text-slate-500 dark:text-slate-400 font-bold uppercase tracking-widest">{{ __('Bundles Sold') }}</p>
        </div>
        <h3 class="text-2xl font-black text-slate-900 dark:text-white">{{ number_format($netBundlesSold) }}</h3>
        <p class="text-[10px] text-blue-500 font-medium mt-1">{{ __('bundles') }}</p>
    </div>

    {{-- Work Days --}}
    <div class="glass-panel p-5 rounded-2xl border border-slate-200 dark:border-white/5 relative overflow-hidden group">
        <div class="absolute top-0 {{ app()->getLocale() == 'ar' ? 'left-0' : 'right-0' }} w-20 h-20 bg-cyan-500/10 rounded-full blur-2xl group-hover:bg-cyan-500/20 transition-all"></div>
        <div class="flex items-center gap-2 mb-2">
            <div class="w-8 h-8 bg-cyan-500/10 rounded-lg flex items-center justify-center">
                <svg class="w-4 h-4 text-cyan-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            </div>
            <p class="text-[10px] text-slate-500 dark:text-slate-400 font-bold uppercase tracking-widest">{{ __('Work Days') }}</p>
        </div>
        <h3 class="text-2xl font-black text-slate-900 dark:text-white">{{ $workDaysCount }}</h3>
        <p class="text-[10px] text-cyan-500 font-medium mt-1">{{ __('Settled Periods') }}</p>
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
