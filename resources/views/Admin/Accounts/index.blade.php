@extends('layouts.Admin.App')

@section('content')
{{-- ── Header + Period Filter ─────────────────────────────── --}}
<div class="mb-8 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
    <div>
        <h1 class="text-2xl lg:text-3xl font-bold text-slate-900 dark:text-white mb-2 tracking-tight">
            <span class="text-transparent bg-clip-text bg-gradient-to-r from-amber-400 to-orange-500">{{ __('Accounts') }}</span>
        </h1>
        <p class="text-sm text-slate-500 dark:text-slate-400 mb-3">
            {{ __('Profit & Loss, Cash Flow, and Transaction Ledger.') }}
        </p>
        <a href="{{ route('admin.accounts.debts') }}" class="inline-flex items-center gap-2 bg-red-500/10 hover:bg-red-500/20 text-red-600 dark:text-red-400 px-4 py-2 rounded-xl text-sm font-bold border border-red-500/20 transition-colors">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            {{ __('View All Outstanding Debts') }}
        </a>
    </div>
    <div class="flex flex-wrap gap-2">
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
            <a href="{{ route('admin.accounts.index', ['period' => $key]) }}"
               class="{{ $period === $key
                   ? 'bg-gradient-to-r from-amber-500 to-orange-500 text-white shadow-[0_0_12px_rgba(245,158,11,0.3)]'
                   : 'bg-white dark:bg-[#0f1115] text-slate-600 dark:text-slate-400 border border-slate-200 dark:border-white/10 hover:border-amber-400 hover:text-amber-600 dark:hover:text-amber-400' }}
               px-4 py-2 rounded-xl text-xs font-bold uppercase tracking-wider transition-all">
                {{ $label }}
            </a>
        @endforeach
    </div>
</div>

{{-- ═══════════════════════════════════════════════════════════ --}}
{{-- SECTION B: PROFIT & LOSS STATEMENT                        --}}
{{-- ═══════════════════════════════════════════════════════════ --}}
<div class="glass-panel rounded-2xl border border-slate-200 dark:border-white/5 mb-8 overflow-hidden">
    <div class="p-5 border-b border-slate-200 dark:border-white/5 bg-gradient-to-r from-emerald-500/5 to-amber-500/5">
        <h2 class="text-lg font-bold text-slate-900 dark:text-white flex items-center gap-2">
            <svg class="w-5 h-5 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
            {{ __('Profit & Loss Statement') }}
        </h2>
        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">{{ __('Income statement for the selected period.') }} · {{ $workDaysCount }} {{ __('Work Days') }}</p>
    </div>
    <div class="p-5 space-y-1">
        {{-- Income Section --}}
        <div class="flex justify-between items-center py-3 px-4 bg-emerald-500/5 rounded-xl mb-1">
            <span class="text-xs font-bold text-emerald-700 dark:text-emerald-400 uppercase tracking-wider">{{ __('Income') }}</span>
            <span class="text-xs text-slate-400">{{ $currencyCode }}</span>
        </div>
        <div class="flex justify-between items-center py-2.5 px-4 border-b border-slate-100 dark:border-white/5">
            <span class="text-sm text-slate-600 dark:text-slate-400">{{ __('Gross Sales') }}</span>
            <span class="font-semibold text-slate-900 dark:text-white">{{ number_format($grossSales, 2) }} <span class="text-[9px] font-bold text-slate-400 ms-1 lowercase">{{ $currencyCode }}</span></span>
        </div>
        <div class="flex justify-between items-center py-2.5 px-4 border-b border-slate-100 dark:border-white/5">
            <span class="text-sm text-slate-600 dark:text-slate-400">{{ __('Less: Sales Returns') }}</span>
            <span class="font-semibold text-red-500">- {{ number_format($salesReturns, 2) }} <span class="text-[9px] font-bold text-slate-400 ms-1 lowercase">{{ $currencyCode }}</span></span>
        </div>
        <div class="flex justify-between items-center py-3 px-4 bg-emerald-500/10 rounded-xl mt-1 mb-3">
            <span class="text-sm font-bold text-emerald-700 dark:text-emerald-400">{{ __('Net Revenue') }}</span>
            <span class="text-lg font-black text-emerald-600 dark:text-emerald-400">{{ number_format($netRevenue, 2) }} <span class="text-[9px] font-bold text-slate-400 ms-1 lowercase">{{ $currencyCode }}</span></span>
        </div>

        {{-- COGS Section --}}
        <div class="flex justify-between items-center py-3 px-4 bg-red-500/5 rounded-xl mb-1">
            <span class="text-xs font-bold text-red-700 dark:text-red-400 uppercase tracking-wider">{{ __('Cost of Goods Sold (COGS)') }}</span>
            <span></span>
        </div>
        <div class="flex justify-between items-center py-2.5 px-4 border-b border-slate-100 dark:border-white/5">
            <span class="text-sm text-slate-600 dark:text-slate-400">{{ __('Raw Materials') }}</span>
            <span class="font-semibold text-slate-900 dark:text-white">{{ number_format($rawMaterialsCost, 2) }} <span class="text-[9px] font-bold text-slate-400 ms-1 lowercase">{{ $currencyCode }}</span></span>
        </div>
        <div class="flex justify-between items-center py-2.5 px-4 border-b border-slate-100 dark:border-white/5">
            <span class="text-sm text-slate-600 dark:text-slate-400">{{ __('Freight & Unloading') }}</span>
            <span class="font-semibold text-slate-900 dark:text-white">{{ number_format($freightUnloading, 2) }} <span class="text-[9px] font-bold text-slate-400 ms-1 lowercase">{{ $currencyCode }}</span></span>
        </div>
        <div class="flex justify-between items-center py-3 px-4 bg-red-500/10 rounded-xl mt-1 mb-3">
            <span class="text-sm font-bold text-red-700 dark:text-red-400">{{ __('Total COGS') }}</span>
            <span class="text-lg font-black text-red-600 dark:text-red-400">{{ number_format($totalCOGS, 2) }} <span class="text-[9px] font-bold text-slate-400 ms-1 lowercase">{{ $currencyCode }}</span></span>
        </div>

        {{-- Gross Profit --}}
        <div class="flex justify-between items-center py-4 px-4 bg-gradient-to-r {{ $grossProfit >= 0 ? 'from-emerald-500/10 to-emerald-500/5 border-emerald-500/20' : 'from-red-500/10 to-red-500/5 border-red-500/20' }} rounded-xl border mb-3">
            <div>
                <span class="text-sm font-bold {{ $grossProfit >= 0 ? 'text-emerald-700 dark:text-emerald-400' : 'text-red-700 dark:text-red-400' }}">{{ __('Gross Profit') }}</span>
                <span class="text-xs text-slate-400 {{ app()->getLocale() == 'ar' ? 'mr-2' : 'ml-2' }}">({{ $grossMargin }}%)</span>
            </div>
            <span class="text-xl font-black {{ $grossProfit >= 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-600 dark:text-red-400' }}">{{ $grossProfit >= 0 ? '+' : '' }}{{ number_format($grossProfit, 2) }} <span class="text-[9px] font-bold text-slate-400 ms-1 lowercase">{{ $currencyCode }}</span></span>
        </div>

        {{-- Operating Expenses --}}
        <div class="flex justify-between items-center py-3 px-4 bg-amber-500/5 rounded-xl mb-1">
            <span class="text-xs font-bold text-amber-700 dark:text-amber-400 uppercase tracking-wider">{{ __('Operating Expenses') }}</span>
            <span></span>
        </div>
        <div class="flex justify-between items-center py-2.5 px-4 border-b border-slate-100 dark:border-white/5">
            <span class="text-sm text-slate-600 dark:text-slate-400">{{ __('Worker Wages') }}</span>
            <span class="font-semibold text-slate-900 dark:text-white">{{ number_format($workerWages, 2) }} <span class="text-[9px] font-bold text-slate-400 ms-1 lowercase">{{ $currencyCode }}</span></span>
        </div>
        <div class="flex justify-between items-center py-2.5 px-4 border-b border-slate-100 dark:border-white/5">
            <span class="text-sm text-slate-600 dark:text-slate-400">{{ __('Allowances & Advances') }}</span>
            <span class="font-semibold text-slate-900 dark:text-white">{{ number_format($workerAllowances, 2) }} <span class="text-[9px] font-bold text-slate-400 ms-1 lowercase">{{ $currencyCode }}</span></span>
        </div>
        <div class="flex justify-between items-center py-2.5 px-4 border-b border-slate-100 dark:border-white/5">
            <span class="text-sm text-slate-600 dark:text-slate-400">{{ __('Less: Deductions') }}</span>
            <span class="font-semibold text-emerald-500">- {{ number_format($workerDeductions, 2) }} <span class="text-[9px] font-bold text-slate-400 ms-1 lowercase">{{ $currencyCode }}</span></span>
        </div>
        <div class="flex justify-between items-center py-2.5 px-4 border-b border-slate-100 dark:border-white/5">
            <span class="text-sm text-slate-600 dark:text-slate-400">{{ __('Operational Expenses') }}</span>
            <span class="font-semibold text-slate-900 dark:text-white">{{ number_format($operationalExpenses, 2) }} <span class="text-[9px] font-bold text-slate-400 ms-1 lowercase">{{ $currencyCode }}</span></span>
        </div>
        <div class="flex justify-between items-center py-3 px-4 bg-amber-500/10 rounded-xl mt-1 mb-3">
            <span class="text-sm font-bold text-amber-700 dark:text-amber-400">{{ __('Total Operating Expenses') }}</span>
            <span class="text-lg font-black text-amber-600 dark:text-amber-400">{{ number_format($totalOperatingExpenses, 2) }} <span class="text-[9px] font-bold text-slate-400 ms-1 lowercase">{{ $currencyCode }}</span></span>
        </div>

        {{-- NET PROFIT --}}
        <div class="flex justify-between items-center py-5 px-5 bg-gradient-to-r {{ $netProfit >= 0 ? 'from-emerald-600/20 to-emerald-500/10' : 'from-red-600/20 to-red-500/10' }} rounded-2xl border {{ $netProfit >= 0 ? 'border-emerald-500/30' : 'border-red-500/30' }}">
            <div>
                <span class="text-lg font-bold {{ $netProfit >= 0 ? 'text-emerald-700 dark:text-emerald-400' : 'text-red-700 dark:text-red-400' }}">{{ __('Net Profit') }}</span>
                <span class="text-xs {{ $netProfit >= 0 ? 'text-emerald-500' : 'text-red-500' }} {{ app()->getLocale() == 'ar' ? 'mr-2' : 'ml-2' }}">({{ $netMargin }}% {{ __('margin') }})</span>
            </div>
            <span class="text-2xl font-black {{ $netProfit >= 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-600 dark:text-red-400' }}">{{ $netProfit >= 0 ? '+' : '' }}{{ number_format($netProfit, 2) }} <span class="text-sm">{{ $currencyCode }}</span></span>
        </div>
    </div>
</div>

{{-- ═══════════════════════════════════════════════════════════ --}}
{{-- CASH FLOW SUMMARY                                          --}}
{{-- ═══════════════════════════════════════════════════════════ --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
    {{-- Cash In --}}
    <div class="glass-panel rounded-2xl border border-emerald-500/20 p-5 relative overflow-hidden">
        <div class="absolute top-0 {{ app()->getLocale() == 'ar' ? 'left-0' : 'right-0' }} w-32 h-32 bg-emerald-500/10 rounded-full blur-3xl -translate-y-1/2"></div>
        <div class="relative z-10">
            <div class="flex items-center gap-2 mb-4">
                <div class="w-8 h-8 bg-emerald-500/10 rounded-lg flex items-center justify-center">
                    <svg class="w-4 h-4 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                </div>
                <h3 class="text-sm font-bold text-emerald-700 dark:text-emerald-400 uppercase tracking-wider">{{ __('Cash In') }}</h3>
            </div>
            <div class="space-y-2">
                <div class="flex justify-between text-xs">
                    <span class="text-slate-500">{{ __('Distributor Payments') }}</span>
                    <span class="font-bold text-slate-900 dark:text-white">{{ number_format($cashFromDistributors, 2) }} <span class="text-[9px] font-bold text-slate-400 ms-1 lowercase">{{ $currencyCode }}</span></span>
                </div>
                <div class="flex justify-between text-xs">
                    <span class="text-slate-500">{{ __('Cash from Shifts') }}</span>
                    <span class="font-bold text-slate-900 dark:text-white">{{ number_format($cashFromShifts, 2) }} <span class="text-[9px] font-bold text-slate-400 ms-1 lowercase">{{ $currencyCode }}</span></span>
                </div>
                <div class="border-t border-emerald-500/20 pt-2 flex justify-between">
                    <span class="text-sm font-bold text-emerald-600 dark:text-emerald-400">{{ __('Total') }}</span>
                    <span class="text-lg font-black text-emerald-600 dark:text-emerald-400">{{ number_format($totalCashIn, 2) }} <span class="text-[9px] font-bold text-slate-400 ms-1 lowercase">{{ $currencyCode }}</span></span>
                </div>
            </div>
        </div>
    </div>

    {{-- Cash Out --}}
    <div class="glass-panel rounded-2xl border border-red-500/20 p-5 relative overflow-hidden">
        <div class="absolute top-0 {{ app()->getLocale() == 'ar' ? 'left-0' : 'right-0' }} w-32 h-32 bg-red-500/10 rounded-full blur-3xl -translate-y-1/2"></div>
        <div class="relative z-10">
            <div class="flex items-center gap-2 mb-4">
                <div class="w-8 h-8 bg-red-500/10 rounded-lg flex items-center justify-center">
                    <svg class="w-4 h-4 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/></svg>
                </div>
                <h3 class="text-sm font-bold text-red-700 dark:text-red-400 uppercase tracking-wider">{{ __('Cash Out') }}</h3>
            </div>
            <div class="space-y-2">
                <div class="flex justify-between text-xs">
                    <span class="text-slate-500">{{ __('Supplier Payments') }}</span>
                    <span class="font-bold text-slate-900 dark:text-white">{{ number_format($cashToSuppliers, 2) }} <span class="text-[9px] font-bold text-slate-400 ms-1 lowercase">{{ $currencyCode }}</span></span>
                </div>
                <div class="flex justify-between text-xs">
                    <span class="text-slate-500">{{ __('Supplier Debts Settled') }}</span>
                    <span class="font-bold text-slate-900 dark:text-white">{{ number_format($cashToSupplierDebts, 2) }} <span class="text-[9px] font-bold text-slate-400 ms-1 lowercase">{{ $currencyCode }}</span></span>
                </div>
                <div class="flex justify-between text-xs">
                    <span class="text-slate-500">{{ __('Freight & Unloading') }}</span>
                    <span class="font-bold text-slate-900 dark:text-white">{{ number_format($cashToFreight, 2) }} <span class="text-[9px] font-bold text-slate-400 ms-1 lowercase">{{ $currencyCode }}</span></span>
                </div>
                <div class="flex justify-between text-xs">
                    <span class="text-slate-500">{{ __('Worker Wages') }}</span>
                    <span class="font-bold text-slate-900 dark:text-white">{{ number_format($cashToWages, 2) }} <span class="text-[9px] font-bold text-slate-400 ms-1 lowercase">{{ $currencyCode }}</span></span>
                </div>
                <div class="flex justify-between text-xs">
                    <span class="text-slate-500">{{ __('Advances & Allowances') }}</span>
                    <span class="font-bold text-slate-900 dark:text-white">{{ number_format($cashToAdvances + $cashToAllowances, 2) }} <span class="text-[9px] font-bold text-slate-400 ms-1 lowercase">{{ $currencyCode }}</span></span>
                </div>
                <div class="flex justify-between text-xs">
                    <span class="text-slate-500">{{ __('Operations') }}</span>
                    <span class="font-bold text-slate-900 dark:text-white">{{ number_format($cashToExpenses, 2) }} <span class="text-[9px] font-bold text-slate-400 ms-1 lowercase">{{ $currencyCode }}</span></span>
                </div>
                <div class="border-t border-red-500/20 pt-2 flex justify-between">
                    <span class="text-sm font-bold text-red-600 dark:text-red-400">{{ __('Total') }}</span>
                    <span class="text-lg font-black text-red-600 dark:text-red-400">{{ number_format($totalCashOut, 2) }} <span class="text-[9px] font-bold text-slate-400 ms-1 lowercase">{{ $currencyCode }}</span></span>
                </div>
            </div>
        </div>
    </div>

    {{-- Net Cash Flow --}}
    <div class="glass-panel rounded-2xl border {{ $netCashFlow >= 0 ? 'border-emerald-500/30' : 'border-red-500/30' }} p-5 relative overflow-hidden flex flex-col justify-center">
        <div class="absolute top-0 {{ app()->getLocale() == 'ar' ? 'left-0' : 'right-0' }} w-32 h-32 {{ $netCashFlow >= 0 ? 'bg-emerald-500/10' : 'bg-red-500/10' }} rounded-full blur-3xl -translate-y-1/2"></div>
        <div class="relative z-10 text-center">
            <p class="text-xs font-bold text-slate-500 uppercase tracking-widest mb-2">{{ __('Net Cash Position') }}</p>
            <p class="text-3xl font-black {{ $netCashFlow >= 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-600 dark:text-red-400' }}">
                {{ $netCashFlow >= 0 ? '+' : '' }}{{ number_format($netCashFlow, 2) }} <span class="text-[9px] font-bold text-slate-400 ms-1 lowercase">{{ $currencyCode }}</span>
            </p>
            <p class="text-xs text-slate-400 mt-1">{{ $currencyCode }}</p>
            @if($carriedOverCash > 0)
            <div class="mt-3 pt-3 border-t border-slate-100 dark:border-white/5">
                <p class="text-[10px] text-slate-400 uppercase tracking-wider">{{ __('Carried Over Cash') }}</p>
                <p class="text-sm font-bold text-amber-600 dark:text-amber-400">{{ number_format($carriedOverCash, 2) }} {{ $currencyCode }}</p>
            </div>
            @endif
        </div>
    </div>
</div>

{{-- ═══════════════════════════════════════════════════════════ --}}
{{-- SECTION C: TRANSACTION LEDGER                              --}}
{{-- ═══════════════════════════════════════════════════════════ --}}
<div class="glass-panel rounded-2xl border border-slate-200 dark:border-white/5 mb-8 overflow-hidden">
    <div class="p-5 border-b border-slate-200 dark:border-white/5 bg-slate-50 dark:bg-black/20 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
        <div>
            <h2 class="text-lg font-bold text-slate-900 dark:text-white">{{ __('Transaction Ledger') }}</h2>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">{{ __('All financial movements for the selected period.') }}</p>
        </div>
        <div class="flex gap-2">
            @php
                $typeOptions = [
                    'all' => __('All'),
                    'income' => __('Income'),
                    'expense' => __('Expense'),
                    'cash_in' => __('Cash In'),
                ];
            @endphp
            @foreach($typeOptions as $tKey => $tLabel)
                <a href="{{ route('admin.accounts.index', array_merge(request()->query(), ['type' => $tKey, 'page' => 1])) }}"
                   class="{{ $typeFilter === $tKey
                       ? 'bg-amber-500 text-white'
                       : 'bg-white dark:bg-[#0f1115] text-slate-500 border border-slate-200 dark:border-white/10 hover:border-amber-400' }}
                   px-3 py-1.5 rounded-lg text-[10px] font-bold uppercase tracking-wider transition-all">
                    {{ $tLabel }}
                </a>
            @endforeach
        </div>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead>
                <tr class="bg-slate-100 dark:bg-black/10 border-b border-slate-200 dark:border-white/5 text-[10px] uppercase tracking-widest text-slate-500 font-bold">
                    <th class="p-4">{{ __('Date') }}</th>
                    <th class="p-4">{{ __('Type') }}</th>
                    <th class="p-4">{{ __('Category') }}</th>
                    <th class="p-4">{{ __('Description') }}</th>
                    <th class="p-4">{{ __('Amount') }}</th>
                    <th class="p-4">{{ __('Day #') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-white/5">
                @forelse($paginatedTransactions as $txn)
                <tr class="hover:bg-slate-50 dark:hover:bg-white/5 transition-colors">
                    <td class="p-4 text-xs text-slate-500 whitespace-nowrap">{{ $txn['date'] ? $txn['date']->translatedFormat('m/d H:i') : '-' }}</td>
                    <td class="p-4">
                        @if($txn['type'] === 'income')
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 text-[10px] font-bold uppercase">↑ {{ __('Income') }}</span>
                        @elseif($txn['type'] === 'cash_in')
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-blue-500/10 text-blue-600 dark:text-blue-400 text-[10px] font-bold uppercase">$ {{ __('Cash In') }}</span>
                        @else
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-red-500/10 text-red-600 dark:text-red-400 text-[10px] font-bold uppercase">↓ {{ __('Expense') }}</span>
                        @endif
                    </td>
                    <td class="p-4 text-xs text-slate-600 dark:text-slate-300">{{ $txn['category'] }}</td>
                    <td class="p-4 text-xs text-slate-700 dark:text-slate-300 font-medium max-w-[200px] truncate">{{ $txn['description'] }}</td>
                    <td class="p-4">
                        <span class="font-bold text-sm {{ $txn['type'] === 'income' || $txn['type'] === 'cash_in' ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-500' }}">
                            {{ $txn['type'] === 'income' || $txn['type'] === 'cash_in' ? '+' : '-' }}{{ number_format($txn['amount'], 2) }} <span class="text-[9px] font-bold text-slate-400 ms-1 lowercase">{{ $currencyCode }}</span>
                        </span>
                    </td>
                    <td class="p-4 text-xs text-slate-400">#{{ $txn['work_day_id'] }}</td>
                </tr>
                @empty
                <tr><td colspan="6" class="p-8 text-center text-slate-400 text-sm">{{ __('No transactions found for this period.') }}</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($paginatedTransactions->hasPages())
    <div class="p-4 border-t border-slate-200 dark:border-white/5">
        {{ $paginatedTransactions->links() }}
    </div>
    @endif
</div>

{{-- ═══════════════════════════════════════════════════════════ --}}
{{-- SECTION D: PAYOUT SUMMARIES                                --}}
{{-- ═══════════════════════════════════════════════════════════ --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
    {{-- Distributor Payouts --}}
    <div class="glass-panel rounded-2xl border border-slate-200 dark:border-white/5 overflow-hidden">
        <div class="p-4 border-b border-slate-200 dark:border-white/5 bg-slate-50 dark:bg-black/20">
            <h3 class="text-sm font-bold text-slate-900 dark:text-white">{{ __('Distributor Balances') }}</h3>
            <p class="text-[10px] text-slate-500 mt-0.5">{{ __('Billing vs payments received.') }}</p>
        </div>
        <div class="max-h-64 overflow-y-auto">
            <table class="w-full text-left">
                <thead class="sticky top-0 bg-slate-100 dark:bg-[#0f1115]">
                    <tr class="text-[10px] uppercase tracking-widest text-slate-500 font-bold border-b border-slate-200 dark:border-white/5">
                        <th class="p-3">{{ __('Name') }}</th>
                        <th class="p-3">{{ __('Billed') }}</th>
                        <th class="p-3">{{ __('Received') }}</th>
                        <th class="p-3">{{ __('Balance') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-white/5">
                    @forelse($distributorPayouts->filter(fn($d) => ($d->total_billed ?? 0) > 0) as $dp)
                    <tr class="hover:bg-slate-50 dark:hover:bg-white/5">
                        <td class="p-3 text-xs font-semibold text-slate-900 dark:text-white">{{ $dp->first_name }} {{ $dp->last_name }}</td>
                        <td class="p-3 text-xs text-slate-500">{{ number_format($dp->total_billed ?? 0, 2) }} <span class="text-[9px] font-bold text-slate-400 ms-1 lowercase">{{ $currencyCode }}</span></td>
                        <td class="p-3 text-xs text-emerald-500">{{ number_format(($dp->total_received ?? 0) + ($dp->total_refunded ?? 0), 2) }} <span class="text-[9px] font-bold text-slate-400 ms-1 lowercase">{{ $currencyCode }}</span></td>
                        <td class="p-3 text-xs font-bold {{ ($dp->balance ?? 0) > 0 ? 'text-amber-600 dark:text-amber-400' : 'text-emerald-500' }}">{{ number_format($dp->balance ?? 0, 2) }} <span class="text-[9px] font-bold text-slate-400 ms-1 lowercase">{{ $currencyCode }}</span></td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="p-4 text-center text-slate-400 text-[10px]">{{ __('No data.') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Supplier Payouts --}}
    <div class="glass-panel rounded-2xl border border-slate-200 dark:border-white/5 overflow-hidden">
        <div class="p-4 border-b border-slate-200 dark:border-white/5 bg-slate-50 dark:bg-black/20">
            <h3 class="text-sm font-bold text-slate-900 dark:text-white">{{ __('Supplier Balances') }}</h3>
            <p class="text-[10px] text-slate-500 mt-0.5">{{ __('Amounts owed vs paid to suppliers.') }}</p>
        </div>
        <div class="max-h-64 overflow-y-auto">
            <table class="w-full text-left">
                <thead class="sticky top-0 bg-slate-100 dark:bg-[#0f1115]">
                    <tr class="text-[10px] uppercase tracking-widest text-slate-500 font-bold border-b border-slate-200 dark:border-white/5">
                        <th class="p-3">{{ __('Name') }}</th>
                        <th class="p-3">{{ __('Owed') }}</th>
                        <th class="p-3">{{ __('Paid') }}</th>
                        <th class="p-3">{{ __('Balance') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-white/5">
                    @forelse($supplierPayouts->filter(fn($s) => ($s->total_owed ?? 0) > 0) as $sp)
                    <tr class="hover:bg-slate-50 dark:hover:bg-white/5">
                        <td class="p-3 text-xs font-semibold text-slate-900 dark:text-white">{{ $sp->first_name }} {{ $sp->last_name }}</td>
                        <td class="p-3 text-xs text-slate-500">{{ number_format($sp->total_owed ?? 0, 2) }} <span class="text-[9px] font-bold text-slate-400 ms-1 lowercase">{{ $currencyCode }}</span></td>
                        <td class="p-3 text-xs text-emerald-500">{{ number_format($sp->total_paid_to ?? 0, 2) }} <span class="text-[9px] font-bold text-slate-400 ms-1 lowercase">{{ $currencyCode }}</span></td>
                        <td class="p-3 text-xs font-bold {{ ($sp->balance ?? 0) > 0 ? 'text-red-600 dark:text-red-400' : 'text-emerald-500' }}">{{ number_format($sp->balance ?? 0, 2) }} <span class="text-[9px] font-bold text-slate-400 ms-1 lowercase">{{ $currencyCode }}</span></td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="p-4 text-center text-slate-400 text-[10px]">{{ __('No data.') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Worker Payouts --}}
    <div class="glass-panel rounded-2xl border border-slate-200 dark:border-white/5 overflow-hidden">
        <div class="p-4 border-b border-slate-200 dark:border-white/5 bg-slate-50 dark:bg-black/20">
            <h3 class="text-sm font-bold text-slate-900 dark:text-white">{{ __('Worker Payouts') }}</h3>
            <p class="text-[10px] text-slate-500 mt-0.5">{{ __('Earned wages vs advances paid.') }}</p>
        </div>
        <div class="max-h-64 overflow-y-auto">
            <table class="w-full text-left">
                <thead class="sticky top-0 bg-slate-100 dark:bg-[#0f1115]">
                    <tr class="text-[10px] uppercase tracking-widest text-slate-500 font-bold border-b border-slate-200 dark:border-white/5">
                        <th class="p-3">{{ __('Name') }}</th>
                        <th class="p-3">{{ __('Net Pay') }}</th>
                        <th class="p-3">{{ __('Advances') }}</th>
                        <th class="p-3">{{ __('Balance') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-white/5">
                    @forelse($workerPayouts->filter(fn($w) => ($w->net_pay ?? 0) > 0) as $wp)
                    <tr class="hover:bg-slate-50 dark:hover:bg-white/5">
                        <td class="p-3 text-xs font-semibold text-slate-900 dark:text-white">{{ $wp->first_name }} {{ $wp->last_name }}</td>
                        <td class="p-3 text-xs text-slate-500">{{ number_format($wp->net_pay, 2) }} <span class="text-[9px] font-bold text-slate-400 ms-1 lowercase">{{ $currencyCode }}</span></td>
                        <td class="p-3 text-xs text-amber-500">{{ number_format($wp->total_paid_out, 2) }} <span class="text-[9px] font-bold text-slate-400 ms-1 lowercase">{{ $currencyCode }}</span></td>
                        <td class="p-3 text-xs font-bold {{ $wp->balance > 0 ? 'text-red-600 dark:text-red-400' : 'text-emerald-500' }}">{{ number_format($wp->balance, 2) }} <span class="text-[9px] font-bold text-slate-400 ms-1 lowercase">{{ $currencyCode }}</span></td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="p-4 text-center text-slate-400 text-[10px]">{{ __('No data.') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
