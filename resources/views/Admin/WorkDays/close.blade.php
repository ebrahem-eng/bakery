@extends('layouts.Admin.App')

@section('content')
{{-- ── Page Header ──────────────────────────────────────────── --}}
<div class="mb-8 flex flex-col sm:flex-row justify-between items-start sm:items-end gap-4">
    <div>
        <h1 class="text-2xl lg:text-3xl font-bold text-slate-900 dark:text-white tracking-tight">
            {{ __('End of Day') }} <span class="text-transparent bg-clip-text bg-gradient-to-r from-red-500 to-orange-500">{{ __('Settlement') }}</span>
        </h1>
        <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
            {{ __('Work Day started:') }}
            <span class="font-bold text-amber-600 dark:text-amber-400">{{ $workDay->start_time->format('Y-m-d h:i A') }}</span>
            <span class="text-slate-400 mx-1">·</span>
            <span class="text-slate-400 text-xs">{{ $workDay->start_time->diffForHumans() }}</span>
        </p>
    </div>
    <a href="{{ route('admin.work_days.index') }}" class="px-4 py-2 bg-white dark:bg-white/5 border border-slate-200 dark:border-white/10 rounded-xl text-slate-500 hover:text-slate-900 dark:hover:text-white transition-all text-sm font-medium flex items-center gap-1.5 hover:shadow-sm">
        <svg class="w-4 h-4 {{ app()->getLocale() == 'ar' ? 'rotate-180' : '' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
        </svg>
        {{ __('Back') }}
    </a>
</div>

@if($errors->any())
    <div class="mb-6 p-4 rounded-2xl bg-red-500/10 border border-red-500/20 text-red-600 dark:text-red-400 text-sm backdrop-blur-sm">
        <div class="flex items-center gap-2 mb-1 font-bold text-xs uppercase tracking-wider">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
            {{ __('Validation Errors') }}
        </div>
        <ul class="list-disc list-inside space-y-0.5">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
    </div>
@endif

{{-- ════════════════════════════════════════════════════════════════ --}}
{{-- KPI STAT CARDS                                                  --}}
{{-- ════════════════════════════════════════════════════════════════ --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
    {{-- Net Sales --}}
    <div class="group relative glass-panel p-5 rounded-2xl border border-slate-200 dark:border-white/5 overflow-hidden hover:border-emerald-500/30 transition-all duration-300">
        <div class="absolute inset-0 bg-gradient-to-br from-emerald-500/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
        <div class="absolute top-3 {{ app()->getLocale() == 'ar' ? 'left-3' : 'right-3' }}">
            <div class="w-10 h-10 rounded-xl bg-emerald-500/10 flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                <svg class="w-5 h-5 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
            </div>
        </div>
        <div class="relative">
            <p class="text-[10px] uppercase tracking-widest text-slate-400 dark:text-slate-500 font-bold mb-2">{{ __('Net Sales') }}</p>
            <div class="text-2xl font-black text-slate-900 dark:text-white leading-none">{{ number_format($netSales, 2) }}</div>
            <div class="mt-2 inline-flex items-center gap-1 px-2 py-0.5 bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 rounded-md text-[10px] font-bold">{{ __($currencyCode) }}</div>
        </div>
    </div>

    {{-- Total Expenses --}}
    <div class="group relative glass-panel p-5 rounded-2xl border border-slate-200 dark:border-white/5 overflow-hidden hover:border-red-500/30 transition-all duration-300">
        <div class="absolute inset-0 bg-gradient-to-br from-red-500/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
        <div class="absolute top-3 {{ app()->getLocale() == 'ar' ? 'left-3' : 'right-3' }}">
            <div class="w-10 h-10 rounded-xl bg-red-500/10 flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                <svg class="w-5 h-5 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"/></svg>
            </div>
        </div>
        <div class="relative">
            <p class="text-[10px] uppercase tracking-widest text-slate-400 dark:text-slate-500 font-bold mb-2">{{ __('Total Expenses') }}</p>
            <div class="text-2xl font-black text-slate-900 dark:text-white leading-none">{{ number_format($totalExpenses, 2) }}</div>
            <div class="mt-2 inline-flex items-center gap-1 px-2 py-0.5 bg-red-500/10 text-red-600 dark:text-red-400 rounded-md text-[10px] font-bold">{{ __($currencyCode) }}</div>
        </div>
    </div>

    {{-- Net Balance --}}
    <div class="group relative glass-panel p-5 rounded-2xl border border-slate-200 dark:border-white/5 overflow-hidden hover:border-amber-500/30 transition-all duration-300">
        <div class="absolute inset-0 bg-gradient-to-br from-amber-500/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
        <div class="absolute top-3 {{ app()->getLocale() == 'ar' ? 'left-3' : 'right-3' }}">
            <div class="w-10 h-10 rounded-xl bg-amber-500/10 flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                <svg class="w-5 h-5 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
        </div>
        <div class="relative">
            <p class="text-[10px] uppercase tracking-widest text-slate-400 dark:text-slate-500 font-bold mb-2">{{ __('Net Balance') }}</p>
            <div class="text-2xl font-black {{ $netDayBalance >= 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-600 dark:text-red-400' }} leading-none">{{ $netDayBalance >= 0 ? '+' : '' }}{{ number_format($netDayBalance, 2) }}</div>
            <div class="mt-2 inline-flex items-center gap-1 px-2 py-0.5 bg-amber-500/10 text-amber-600 dark:text-amber-400 rounded-md text-[10px] font-bold">{{ __($currencyCode) }}</div>
        </div>
    </div>

    {{-- Remaining Bundles --}}
    <div class="group relative glass-panel p-5 rounded-2xl border border-slate-200 dark:border-white/5 overflow-hidden hover:border-blue-500/30 transition-all duration-300">
        <div class="absolute inset-0 bg-gradient-to-br from-blue-500/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
        <div class="absolute top-3 {{ app()->getLocale() == 'ar' ? 'left-3' : 'right-3' }}">
            <div class="w-10 h-10 rounded-xl bg-blue-500/10 flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                <svg class="w-5 h-5 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
            </div>
        </div>
        <div class="relative">
            <p class="text-[10px] uppercase tracking-widest text-slate-400 dark:text-slate-500 font-bold mb-2">{{ __('Remaining Bundles') }}</p>
            <div class="text-2xl font-black text-slate-900 dark:text-white leading-none">{{ $calculatedRemainingBundles }}</div>
            <div class="mt-2 inline-flex items-center gap-1 px-2 py-0.5 bg-blue-500/10 text-blue-600 dark:text-blue-400 rounded-md text-[10px] font-bold">{{ __('bundles') }}</div>
        </div>
    </div>
</div>

{{-- ═══════════════════════════════════════════════════════════════ --}}
{{-- MAIN GRID                                                       --}}
{{-- ═══════════════════════════════════════════════════════════════ --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    {{-- ── LEFT: Details ───────────────────────────────────── --}}
    <div class="lg:col-span-2 space-y-6">

        {{-- ── Bundle Flow ──────────────────────────────────── --}}
        <div class="glass-panel rounded-2xl border border-slate-200 dark:border-white/5 overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-200 dark:border-white/5 bg-gradient-to-r from-blue-500/5 to-transparent flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-blue-500/10 flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-slate-900 dark:text-white">{{ __('Bundle Flow Tracking') }}</h3>
                    <p class="text-[10px] text-slate-400">{{ __('Inbound, outbound and remaining bundles') }}</p>
                </div>
            </div>
            <div class="p-5 space-y-0">
                @php
                    $bundleItems = [
                        ['label' => __('Previous Day Carry-Over'), 'value' => $previousCarryOverBundles, 'suffix' => __('bundles'), 'color' => 'text-slate-900 dark:text-white', 'icon' => '📦'],
                        ['label' => __('Bundles Given to Shifts'), 'value' => $bundlesReceivedByShifts, 'suffix' => __('bundles'), 'color' => 'text-blue-600 dark:text-blue-400', 'icon' => '🔵'],
                        ['label' => __('Bundles Returned from Shifts'), 'value' => '+ '.$bundlesReturnedByShifts, 'suffix' => __('bundles'), 'color' => 'text-emerald-600 dark:text-emerald-400', 'icon' => '🟢'],
                        ['label' => __('Distributed to Distributors'), 'value' => '- '.$bundlesDistributed, 'suffix' => __('bundles'), 'color' => 'text-red-600 dark:text-red-400', 'icon' => '🔴'],
                        ['label' => __('Returned by Distributors'), 'value' => '+ '.$bundlesReturnedByDistributors, 'suffix' => __('bundles'), 'color' => 'text-emerald-600 dark:text-emerald-400', 'icon' => '🟢'],
                    ];
                @endphp
                @foreach($bundleItems as $item)
                <div class="flex justify-between items-center py-3 px-1 border-b border-slate-100 dark:border-white/[0.03] last:border-0 hover:bg-slate-50/50 dark:hover:bg-white/[0.02] rounded-lg transition-colors">
                    <span class="text-sm text-slate-600 dark:text-slate-400 flex items-center gap-2">
                        <span class="text-xs">{{ $item['icon'] }}</span>
                        {{ $item['label'] }}
                    </span>
                    <span class="font-bold {{ $item['color'] }} tabular-nums">{{ $item['value'] }} <span class="text-xs text-slate-400 font-medium">{{ $item['suffix'] }}</span></span>
                </div>
                @endforeach
                {{-- Total row --}}
                <div class="flex justify-between items-center py-3 px-4 mt-2 bg-gradient-to-r from-blue-500/10 to-blue-500/5 rounded-xl border border-blue-500/10">
                    <span class="text-sm font-bold text-blue-600 dark:text-blue-400 flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                        {{ __('Calculated Remaining') }}
                    </span>
                    <span class="font-black text-blue-600 dark:text-blue-400 text-xl tabular-nums">{{ $calculatedRemainingBundles }} <span class="text-xs font-bold">{{ __('bundles') }}</span></span>
                </div>
            </div>

            {{-- Per-shift breakdown --}}
            @if($workDay->workerShifts->count() > 0)
            <div class="border-t border-slate-200 dark:border-white/5">
                <div class="px-5 py-3 bg-slate-50/80 dark:bg-black/20 flex items-center gap-2">
                    <svg class="w-3.5 h-3.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    <h4 class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest">{{ __('Shift Bundle Details') }}</h4>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
                        <thead>
                            <tr class="text-[10px] uppercase tracking-wider text-slate-400 border-b border-slate-200 dark:border-white/5 bg-slate-50/50 dark:bg-black/10">
                                <th class="py-3 px-5 font-bold text-{{ app()->getLocale() == 'ar' ? 'right' : 'left' }}">{{ __('Worker') }}</th>
                                <th class="py-3 px-4 font-bold text-center">{{ __('Received') }}</th>
                                <th class="py-3 px-4 font-bold text-center">{{ __('Returned') }}</th>
                                <th class="py-3 px-4 font-bold text-center">{{ __('Net') }}</th>
                                <th class="py-3 px-4 font-bold text-center">{{ __('Status') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-white/[0.03]">
                            @foreach($workDay->workerShifts as $shift)
                            <tr class="hover:bg-slate-50/80 dark:hover:bg-white/[0.02] transition-colors">
                                <td class="py-3 px-5 font-semibold text-slate-900 dark:text-white">{{ $shift->worker->first_name ?? '—' }}</td>
                                <td class="py-3 px-4 text-center text-blue-600 dark:text-blue-400 font-bold tabular-nums">{{ $shift->bundles_received }}</td>
                                <td class="py-3 px-4 text-center text-emerald-600 dark:text-emerald-400 font-bold tabular-nums">{{ $shift->bundles_returned }}</td>
                                <td class="py-3 px-4 text-center font-bold tabular-nums {{ ($shift->bundles_received - $shift->bundles_returned) > 0 ? 'text-red-500' : 'text-emerald-500' }}">{{ $shift->bundles_received - $shift->bundles_returned }}</td>
                                <td class="py-3 px-4 text-center">
                                    @if($shift->check_out)
                                        <span class="inline-flex items-center gap-1 text-[10px] uppercase tracking-widest font-bold text-emerald-600 dark:text-emerald-400 bg-emerald-500/10 px-2.5 py-1 rounded-full">
                                            <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full"></span>{{ __('Done') }}
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 text-[10px] uppercase tracking-widest font-bold text-amber-600 dark:text-amber-400 bg-amber-500/10 px-2.5 py-1 rounded-full">
                                            <span class="w-1.5 h-1.5 bg-amber-500 rounded-full animate-pulse"></span>{{ __('Active') }}
                                        </span>
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
        <div class="glass-panel rounded-2xl border border-slate-200 dark:border-white/5 overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-200 dark:border-white/5 bg-gradient-to-r from-emerald-500/5 to-transparent flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-emerald-500/10 flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-slate-900 dark:text-white">{{ __('Revenue Breakdown') }}</h3>
                    <p class="text-[10px] text-slate-400">{{ __('Sales, refunds and net income') }}</p>
                </div>
            </div>
            <div class="p-5 space-y-0">
                @php
                    $revenueItems = [
                        ['label' => __('Total Distributions Billed'), 'value' => '+ '.number_format($totalSales, 2), 'color' => 'text-slate-900 dark:text-white'],
                        ['label' => __('Refunds Processed'), 'value' => '- '.number_format($totalRefunds, 2), 'color' => 'text-red-600 dark:text-red-400'],
                        ['label' => __('Payments Received'), 'value' => number_format($totalPaymentsReceived, 2), 'color' => 'text-emerald-600 dark:text-emerald-400'],
                    ];
                @endphp
                @foreach($revenueItems as $item)
                <div class="flex justify-between items-center py-3 px-1 border-b border-slate-100 dark:border-white/[0.03] last:border-0 hover:bg-slate-50/50 dark:hover:bg-white/[0.02] rounded-lg transition-colors">
                    <span class="text-sm text-slate-600 dark:text-slate-400">{{ $item['label'] }}</span>
                    <span class="font-bold {{ $item['color'] }} tabular-nums">{{ $item['value'] }} <span class="text-xs text-slate-400 font-medium">{{ __($currencyCode) }}</span></span>
                </div>
                @endforeach
                <div class="flex justify-between items-center py-3 px-4 mt-2 bg-gradient-to-r from-emerald-500/10 to-emerald-500/5 rounded-xl border border-emerald-500/10">
                    <span class="text-sm font-bold text-emerald-600 dark:text-emerald-400">{{ __('Net Sales') }}</span>
                    <span class="font-black text-emerald-600 dark:text-emerald-400 text-xl tabular-nums">{{ number_format($netSales, 2) }} <span class="text-xs font-bold">{{ __($currencyCode) }}</span></span>
                </div>
            </div>
        </div>

        {{-- ── Expense Breakdown ────────────────────────────── --}}
        <div class="glass-panel rounded-2xl border border-slate-200 dark:border-white/5 overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-200 dark:border-white/5 bg-gradient-to-r from-red-500/5 to-transparent flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-red-500/10 flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"/></svg>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-slate-900 dark:text-white">{{ __('Expense Breakdown') }}</h3>
                    <p class="text-[10px] text-slate-400">{{ __('All operational costs and deductions') }}</p>
                </div>
            </div>
            <div class="p-5 space-y-0">
                @php
                    $expenseItems = [
                        ['label' => __('Supplies Purchased'), 'value' => number_format($suppliesCost, 2), 'color' => 'text-slate-900 dark:text-white'],
                        ['label' => __('Freight & Unloading Fees'), 'value' => number_format($unloadingFees, 2), 'color' => 'text-slate-900 dark:text-white'],
                        ['label' => __('Worker Shift Wages'), 'value' => number_format($shiftWages, 2), 'color' => 'text-slate-900 dark:text-white'],
                        ['label' => __('Worker Allowances'), 'value' => '+ '.number_format($workerAllowances, 2), 'color' => 'text-slate-900 dark:text-white'],
                        ['label' => __('Worker Advances Paid'), 'value' => number_format($workerAdvances, 2), 'color' => 'text-amber-600 dark:text-amber-400'],
                        ['label' => __('Worker Deductions'), 'value' => '- '.number_format($workerDeductions, 2), 'color' => 'text-emerald-600 dark:text-emerald-400'],
                        ['label' => __('Operational Expenses'), 'value' => number_format($operationalExpenses, 2), 'color' => 'text-slate-900 dark:text-white'],
                    ];
                @endphp
                @foreach($expenseItems as $item)
                <div class="flex justify-between items-center py-3 px-1 border-b border-slate-100 dark:border-white/[0.03] last:border-0 hover:bg-slate-50/50 dark:hover:bg-white/[0.02] rounded-lg transition-colors">
                    <span class="text-sm text-slate-600 dark:text-slate-400">{{ $item['label'] }}</span>
                    <span class="font-bold {{ $item['color'] }} tabular-nums">{{ $item['value'] }} <span class="text-xs text-slate-400 font-medium">{{ __($currencyCode) }}</span></span>
                </div>
                @endforeach
                <div class="flex justify-between items-center py-3 px-4 mt-2 bg-gradient-to-r from-red-500/10 to-red-500/5 rounded-xl border border-red-500/10">
                    <span class="text-sm font-bold text-red-600 dark:text-red-400">{{ __('Total Expenses') }}</span>
                    <span class="font-black text-red-600 dark:text-red-400 text-xl tabular-nums">{{ number_format($totalExpenses, 2) }} <span class="text-xs font-bold">{{ __($currencyCode) }}</span></span>
                </div>
            </div>
        </div>

    </div>

    {{-- ── RIGHT: Finalize Settlement ──────────────────────── --}}
    <div class="lg:col-span-1">
        <div class="glass-panel rounded-2xl border border-slate-200 dark:border-white/5 overflow-hidden sticky top-6">
            {{-- Header --}}
            <div class="relative p-6 border-b border-slate-200 dark:border-white/5 overflow-hidden">
                <div class="absolute inset-0 bg-gradient-to-br from-amber-500/10 via-orange-500/5 to-red-500/5"></div>
                <div class="absolute top-0 {{ app()->getLocale() == 'ar' ? 'left-0' : 'right-0' }} w-32 h-32 bg-amber-400/10 rounded-full blur-2xl -translate-y-1/2 {{ app()->getLocale() == 'ar' ? '-translate-x-1/2' : 'translate-x-1/2' }}"></div>
                <div class="relative">
                    @if($workDay->status == 'closed')
                        <div class="w-12 h-12 rounded-2xl bg-emerald-500/10 flex items-center justify-center mb-3">
                            <svg class="w-6 h-6 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <h2 class="text-lg font-bold text-slate-900 dark:text-white mb-1">{{ __('Settlement Completed') }}</h2>
                        <p class="text-xs text-slate-500 dark:text-slate-400">{{ __('This period has been permanently sealed and locked.') }}</p>
                    @else
                        <div class="w-12 h-12 rounded-2xl bg-amber-500/10 flex items-center justify-center mb-3">
                            <svg class="w-6 h-6 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                        </div>
                        <h2 class="text-lg font-bold text-slate-900 dark:text-white mb-1">{{ __('Finalize Settlement') }}</h2>
                        <p class="text-xs text-slate-500 dark:text-slate-400">{{ __('Confirm remaining bread bundles and cash for the next work day.') }}</p>
                    @endif
                </div>
            </div>

            @if($workDay->status == 'active')
            <form action="{{ route('admin.work_days.close', $workDay) }}" method="POST" class="p-6 space-y-5"
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

                {{-- Quick Summary --}}
                <div class="p-4 rounded-xl bg-gradient-to-br from-slate-50 to-slate-100/50 dark:from-white/[0.03] dark:to-white/[0.01] border border-slate-200 dark:border-white/5 space-y-2">
                    <div class="flex justify-between items-center">
                        <span class="text-xs text-slate-500 font-bold uppercase tracking-widest">{{ __('Net Balance') }}</span>
                        <span class="text-lg font-black {{ $netDayBalance >= 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-600 dark:text-red-400' }} tabular-nums">{{ number_format($netDayBalance, 2) }} {{ __($currencyCode) }}</span>
                    </div>
                    <div class="flex justify-between items-center text-[11px] text-slate-400">
                        <span>{{ __('Bundles Sold') }}: <span class="font-bold text-slate-600 dark:text-slate-300">{{ $bundlesDistributed - $bundlesReturnedByDistributors }}</span></span>
                        <span>{{ __('Shifts') }}: <span class="font-bold text-slate-600 dark:text-slate-300">{{ $workDay->workerShifts->count() }}</span></span>
                    </div>
                </div>

                {{-- Auto-Calculated Bundles --}}
                <div class="p-4 rounded-xl bg-blue-500/5 border border-blue-500/15 dark:border-blue-500/10">
                    <div class="flex justify-between items-center">
                        <div>
                            <p class="text-xs font-bold text-blue-600 dark:text-blue-400 uppercase tracking-wider">{{ __('Carried Over Bundles (Unsold)') }}</p>
                            <p class="text-[10px] text-slate-500 mt-0.5">{{ __('Auto-calculated from shift returns.') }}</p>
                        </div>
                        <span class="text-2xl font-black text-blue-600 dark:text-blue-400 tabular-nums">{{ $calculatedRemainingBundles }}</span>
                    </div>
                </div>

                {{-- Cash from Shifts --}}
                @if($totalCashFromShifts > 0)
                <div class="p-4 rounded-xl bg-emerald-500/5 border border-emerald-500/15 dark:border-emerald-500/10">
                    <div class="flex justify-between items-center">
                        <p class="text-xs font-bold text-emerald-600 dark:text-emerald-400 uppercase tracking-wider">{{ __('Cash Collected from Shifts') }}</p>
                        <span class="text-lg font-bold text-emerald-600 dark:text-emerald-400 tabular-nums">{{ number_format($totalCashFromShifts, 2) }} {{ __($currencyCode) }}</span>
                    </div>
                </div>
                @endif

                {{-- Material Consumption --}}
                <div class="space-y-3 p-4 rounded-xl bg-gradient-to-br from-amber-500/5 to-orange-500/5 border border-amber-500/15 dark:border-amber-500/10">
                    <div class="flex items-center gap-2 mb-2">
                        <svg class="w-4 h-4 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/></svg>
                        <h3 class="text-xs font-bold text-amber-600 dark:text-amber-400 uppercase tracking-wider">{{ __('Raw Material Consumption') }}</h3>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        @foreach($materialCategories as $cat)
                        <div>
                            <label class="block text-[10px] text-slate-500 uppercase font-bold mb-1.5">{{ $cat->name }}</label>
                            <div class="relative">
                                <input type="number" step="0.01" name="consumptions[{{ $cat->id }}]"
                                    class="w-full px-3 py-2.5 bg-white dark:bg-white/5 border border-amber-400/20 rounded-xl text-xs text-slate-700 dark:text-slate-300 focus:outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500/30 transition-all font-medium"
                                    placeholder="0.00">
                                <div class="absolute {{ app()->getLocale() == 'ar' ? 'left-0 pl-2' : 'right-0 pr-2' }} top-0 h-full flex items-center pointer-events-none">
                                    <span class="text-[8px] font-bold text-amber-500/50 bg-amber-500/10 px-1.5 py-0.5 rounded">{{ number_format($cat->available, 1) }}</span>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    <p class="text-[10px] text-slate-400 italic flex items-center gap-1">
                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        {{ __('Available stock shown in top right of each field.') }}
                    </p>
                </div>

                {{-- Cash Carry-over --}}
                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider">
                        {{ __('Carried Over Cash Balance') }}
                        <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <input type="number" step="0.01" name="carried_over_money" required min="0" value="{{ old('carried_over_money') }}"
                            class="block w-full px-4 py-3.5 bg-white dark:bg-white/5 border border-slate-200 dark:border-white/10 rounded-xl text-lg text-slate-900 dark:text-white focus:outline-none focus:border-amber-400 focus:ring-2 focus:ring-amber-400/20 transition-all font-bold placeholder-slate-300 dark:placeholder-slate-600"
                            placeholder="0.00">
                        <div class="absolute {{ app()->getLocale() == 'ar' ? 'left-3' : 'right-3' }} top-1/2 -translate-y-1/2 pointer-events-none">
                            <span class="text-xs font-bold text-slate-400">{{ __($currencyCode) }}</span>
                        </div>
                    </div>
                    <p class="text-[10px] text-slate-400">{{ __('Physical cash left in the drawer for the next work day.') }}</p>
                </div>

                {{-- Currency --}}
                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider">{{ __('Currency') }}</label>
                    <select name="carried_over_currency_id" x-model="currencyId" @change="setCurrency($event.target.value)"
                        class="block w-full px-4 py-3 bg-white dark:bg-white/5 border border-slate-200 dark:border-white/10 rounded-xl text-sm text-slate-900 dark:text-white focus:outline-none focus:border-amber-400 focus:ring-2 focus:ring-amber-400/20 transition-all font-medium appearance-none cursor-pointer">
                        @foreach($currencies as $curr)
                            <option value="{{ $curr->id }}" {{ $curr->is_default ? 'selected' : '' }}>{{ $curr->code }} - {{ $curr->name }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Exchange Rate --}}
                <div x-show="!isLocal" x-transition.opacity class="space-y-1.5">
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider">{{ __('Exchange Rate') }}</label>
                    <input type="number" step="0.01" name="carried_over_exchange_rate" :value="exchangeRate" min="0"
                        class="block w-full px-4 py-3.5 bg-white dark:bg-white/5 border border-slate-200 dark:border-white/10 rounded-xl text-lg text-slate-900 dark:text-white focus:outline-none focus:border-amber-400 focus:ring-2 focus:ring-amber-400/20 transition-all font-bold"
                        placeholder="1.00">
                </div>

                {{-- Submit --}}
                <div class="pt-3">
                    <button type="submit"
                        onclick="return confirm('{{ __('WARNING: Closing a work day freezes all sales, expenses, and HR shifts permanently. Proceed?') }}');"
                        class="relative w-full group overflow-hidden bg-gradient-to-r from-red-600 to-orange-600 text-white py-4 rounded-xl text-sm font-bold shadow-lg shadow-red-500/20 transition-all uppercase tracking-widest hover:shadow-xl hover:shadow-red-500/30 active:scale-[0.98]">
                        <span class="absolute inset-0 bg-gradient-to-r from-red-500 to-orange-500 opacity-0 group-hover:opacity-100 transition-opacity"></span>
                        <span class="relative flex items-center justify-center gap-2">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                            {{ __('Close Work Day Permanently') }}
                        </span>
                    </button>
                </div>
            </form>
            @else
            {{-- Closed state --}}
            <div class="p-6 space-y-4">
                <div class="p-5 rounded-xl bg-gradient-to-br from-slate-50 to-slate-100/50 dark:from-white/[0.03] dark:to-white/[0.01] border border-slate-200 dark:border-white/5 text-center">
                    <p class="text-slate-400 text-[10px] uppercase tracking-widest mb-2 font-bold">{{ __('Carried Over Bundles (Unsold)') }}</p>
                    <p class="text-slate-900 dark:text-white font-black text-3xl tabular-nums">{{ $workDay->carried_over_bundles }}</p>
                    <p class="text-blue-500 text-[10px] font-bold mt-1">{{ __('bundles') }}</p>
                </div>
                <div class="p-5 rounded-xl bg-gradient-to-br from-slate-50 to-slate-100/50 dark:from-white/[0.03] dark:to-white/[0.01] border border-slate-200 dark:border-white/5 text-center">
                    <p class="text-slate-400 text-[10px] uppercase tracking-widest mb-2 font-bold">{{ __('Carried Over Cash Balance') }}</p>
                    <p class="text-slate-900 dark:text-white font-black text-3xl tabular-nums">{{ number_format($workDay->carried_over_money, 2) }}</p>
                    <p class="text-amber-500 text-[10px] font-bold mt-1">{{ $workDay->carriedOverCurrency ? $workDay->carriedOverCurrency->code : __($currencyCode) }}</p>
                </div>
                <div class="flex items-center justify-center gap-2 py-3">
                    <svg class="w-5 h-5 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <p class="text-emerald-600 dark:text-emerald-400 font-bold uppercase tracking-widest text-xs">{{ __('Work day is already closed.') }}</p>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
