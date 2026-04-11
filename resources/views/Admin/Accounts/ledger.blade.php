@extends('layouts.Admin.App')

@section('content')
@can('view accounts ledger')
<div x-data="{ activeTab: 'distributors' }">
    {{-- ── Header ────────────────────────────────────────────────────── --}}
    <div class="mb-8 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <h1 class="text-2xl lg:text-3xl font-bold text-slate-900 dark:text-white mb-2 tracking-tight">
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-emerald-400 to-cyan-500">{{ __('Financial Ledger') }}</span>
            </h1>
            <p class="text-sm text-slate-500 dark:text-slate-400">
                {{ __('Manage lifetime outstanding balances and payment history.') }}
            </p>
        </div>
        
        {{-- Tab Switcher --}}
        <div class="flex bg-slate-200/50 dark:bg-white/5 p-1 rounded-xl border border-slate-200 dark:border-white/5 shadow-inner">
            <button @click="activeTab = 'distributors'" :class="activeTab === 'distributors' ? 'bg-white dark:bg-white/10 text-emerald-600 dark:text-emerald-400 shadow-sm' : 'text-slate-500 hover:text-slate-700 dark:hover:text-slate-300'" class="px-4 py-2 rounded-lg text-xs font-bold uppercase tracking-wider transition-all">
                {{ __('Distributors') }}
            </button>
            <button @click="activeTab = 'suppliers'" :class="activeTab === 'suppliers' ? 'bg-white dark:bg-white/10 text-emerald-600 dark:text-emerald-400 shadow-sm' : 'text-slate-500 hover:text-slate-700 dark:hover:text-slate-300'" class="px-4 py-2 rounded-lg text-xs font-bold uppercase tracking-wider transition-all">
                {{ __('Suppliers') }}
            </button>
            <button @click="activeTab = 'history'" :class="activeTab === 'history' ? 'bg-white dark:bg-white/10 text-emerald-600 dark:text-emerald-400 shadow-sm' : 'text-slate-500 hover:text-slate-700 dark:hover:text-slate-300'" class="px-4 py-2 rounded-lg text-xs font-bold uppercase tracking-wider transition-all">
                {{ __('History') }}
            </button>
        </div>
    </div>

    {{-- ── Distributor Debts Tab ─────────────────────────────────────── --}}
    <div x-show="activeTab === 'distributors'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0" class="space-y-6">
        <div class="glass-panel rounded-2xl border border-slate-200 dark:border-white/5 overflow-hidden">
            <div class="p-6 border-b border-slate-200 dark:border-white/5 flex justify-between items-center">
                <h2 class="text-lg font-bold text-slate-900 dark:text-white flex items-center gap-2">
                    <svg class="w-5 h-5 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    {{ __('Distributor Balances') }}
                </h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-slate-50 dark:bg-black/20 border-b border-slate-200 dark:border-white/5">
                        <tr class="text-[10px] uppercase tracking-widest text-slate-500 font-bold">
                            <th class="p-4">{{ __('Name') }}</th>
                            <th class="p-4">{{ __('Billed') }}</th>
                            <th class="p-4">{{ __('Paid') }}</th>
                            <th class="p-4">{{ __('Refunded') }}</th>
                            <th class="p-4">{{ __('Outstanding') }}</th>
                            <th class="p-4">{{ __('Action') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-white/5">
                        @forelse($distributorBalances as $d)
                        <tr class="hover:bg-slate-50 dark:hover:bg-white/5 transition-colors">
                            <td class="p-4">
                                <div class="text-sm font-bold text-slate-900 dark:text-white">{{ $d->first_name }} {{ $d->last_name }}</div>
                                <div class="text-[10px] text-slate-500 capitalize">{{ $d->title ?? 'Distributor' }}</div>
                            </td>
                            <td class="p-4 text-sm font-medium text-slate-600 dark:text-slate-400">
                                {{ number_format($d->total_billed ?? 0, 2) }}
                            </td>
                            <td class="p-4 text-sm font-bold text-emerald-500">
                                {{ number_format($d->total_paid ?? 0, 2) }}
                            </td>
                            <td class="p-4 text-sm font-medium text-amber-500">
                                {{ number_format($d->total_refunded ?? 0, 2) }}
                            </td>
                            <td class="p-4">
                                <span class="px-3 py-1 rounded-lg text-xs font-bold {{ $d->outstanding > 1 ? 'bg-red-500/10 text-red-600 dark:text-red-400' : 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400' }}">
                                    {{ number_format($d->outstanding, 2) }} <span class="text-[9px] lowercase">{{ $currencyCode }}</span>
                                </span>
                            </td>
                            <td class="p-4">
                                <a href="{{ route('admin.distributors.show', $d->id) }}" class="p-1 px-3 bg-white dark:bg-white/5 border border-slate-200 dark:border-white/10 rounded-lg text-[10px] font-bold text-slate-600 dark:text-slate-400 hover:bg-emerald-500 hover:text-white transition-all">
                                    {{ __('View Profile') }}
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="6" class="p-8 text-center text-slate-500 italic">{{ __('No distributor activity found.') }}</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- ── Supplier Debts Tab ────────────────────────────────────────── --}}
    <div x-show="activeTab === 'suppliers'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0" class="space-y-6">
        <div class="glass-panel rounded-2xl border border-slate-200 dark:border-white/5 overflow-hidden">
            <div class="p-6 border-b border-slate-200 dark:border-white/5 flex justify-between items-center">
                <h2 class="text-lg font-bold text-slate-900 dark:text-white flex items-center gap-2">
                    <svg class="w-5 h-5 text-cyan-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    {{ __('Supplier Balances') }}
                </h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-slate-50 dark:bg-black/20 border-b border-slate-200 dark:border-white/5">
                        <tr class="text-[10px] uppercase tracking-widest text-slate-500 font-bold">
                            <th class="p-4">{{ __('Supplier') }}</th>
                            <th class="p-4">{{ __('Owed') }}</th>
                            <th class="p-4">{{ __('Total Paid') }}</th>
                            <th class="p-4">{{ __('Outstanding') }}</th>
                            <th class="p-4">{{ __('Status') }}</th>
                            <th class="p-4">{{ __('Action') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-white/5">
                        @forelse($supplierBalances as $s)
                        <tr class="hover:bg-slate-50 dark:hover:bg-white/5 transition-colors">
                            <td class="p-4">
                                <div class="text-sm font-bold text-slate-900 dark:text-white">{{ $s->first_name }} {{ $s->last_name }}</div>
                                <div class="text-[10px] text-slate-500 uppercase">{{ $s->title ?? '' }}</div>
                            </td>
                            <td class="p-4 text-sm font-medium text-slate-600 dark:text-slate-400">
                                {{ number_format($s->total_owed ?? 0, 2) }}
                            </td>
                            <td class="p-4 text-sm font-bold text-emerald-500">
                                {{ number_format($s->total_paid ?? 0, 2) }}
                            </td>
                            <td class="p-4">
                                <span class="px-3 py-1 rounded-lg text-xs font-bold {{ $s->outstanding > 1 ? 'text-red-500' : 'text-emerald-500' }}">
                                    {{ number_format($s->outstanding, 2) }}
                                </span>
                            </td>
                            <td class="p-4">
                                @if($s->outstanding > 1)
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-red-500/10 text-red-600 dark:text-red-400 text-[10px] font-bold">
                                        <span class="w-1.5 h-1.5 rounded-full bg-red-500 animate-pulse"></span>
                                        {{ __('Debt') }}
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 text-[10px] font-bold">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                        {{ __('Paid') }}
                                    </span>
                                @endif
                            </td>
                            <td class="p-4">
                                <a href="{{ route('admin.suppliers.show', $s->id) }}" class="p-1 px-3 bg-white dark:bg-white/5 border border-slate-200 dark:border-white/10 rounded-lg text-[10px] font-bold text-slate-600 dark:text-slate-400 hover:bg-cyan-500 hover:text-white transition-all">
                                    {{ __('Details') }}
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="6" class="p-8 text-center text-slate-500 italic">{{ __('No supplier records found.') }}</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- ── Activity History Tab ─────────────────────────────────────── --}}
    <div x-show="activeTab === 'history'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0" class="space-y-6">
        <div class="glass-panel rounded-2xl border border-slate-200 dark:border-white/5 overflow-hidden">
            <div class="p-6 border-b border-slate-200 dark:border-white/5">
                <h2 class="text-lg font-bold text-slate-900 dark:text-white flex items-center gap-2">
                    <svg class="w-5 h-5 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    {{ __('Payment Timeline') }}
                </h2>
                <p class="text-xs text-slate-500 mt-1">{{ __('Recent financial interactions showing actual cash flow.') }}</p>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-slate-50 dark:bg-black/20 border-b border-slate-200 dark:border-white/5">
                        <tr class="text-[10px] uppercase tracking-widest text-slate-500 font-bold">
                            <th class="p-4">{{ __('Date') }}</th>
                            <th class="p-4">{{ __('Type') }}</th>
                            <th class="p-4">{{ __('Entity') }}</th>
                            <th class="p-4">{{ __('Method') }}</th>
                            <th class="p-4">{{ __('Amount') }}</th>
                            <th class="p-4 text-right">{{ __('Base Equivalent') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-white/5">
                        @forelse($paymentHistory as $p)
                        <tr class="hover:bg-slate-50 dark:hover:bg-white/5 transition-colors">
                            <td class="p-4 text-xs text-slate-500">{{ $p['date']->translatedFormat('Y-m-d H:i') }}</td>
                            <td class="p-4">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold {{ $p['type'] === 'in' ? 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400' : 'bg-red-500/10 text-red-600 dark:text-red-400' }}">
                                    {{ $p['type'] === 'in' ? __('IN') : __('OUT') }}
                                </span>
                            </td>
                            <td class="p-4">
                                <div class="text-sm font-bold text-slate-900 dark:text-white">{{ $p['name'] }}</div>
                                <div class="text-[10px] text-slate-500">{{ $p['category'] }}</div>
                            </td>
                            <td class="p-4 text-[10px] text-slate-500 uppercase font-medium">
                                {{ __('Cash Payment') }}
                            </td>
                            <td class="p-4">
                                <div class="text-sm font-bold {{ $p['type'] === 'in' ? 'text-emerald-500' : 'text-red-500' }}">
                                    {{ $p['type'] === 'in' ? '+' : '-' }} {{ number_format($p['amount'], 2) }}
                                </div>
                                <div class="text-[9px] text-slate-400 uppercase font-bold">{{ $p['currency'] }}</div>
                            </td>
                            <td class="p-4 text-right">
                                <div class="text-sm font-bold text-slate-900 dark:text-white">{{ number_format($p['amount_base'], 2) }}</div>
                                <div class="text-[9px] text-slate-400 uppercase font-bold">{{ $currencyCode }}</div>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="6" class="p-8 text-center text-slate-500 italic">{{ __('No recent transactions recorded.') }}</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endcan
@endsection
