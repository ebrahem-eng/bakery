@extends('layouts.Admin.App')

@section('title', __('Warehouse'))

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            <div class="hidden sm:flex w-12 h-12 bg-emerald-500/10 rounded-2xl items-center justify-center border border-emerald-500/20">
                <i class="fas fa-boxes text-xl text-emerald-500"></i>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-slate-900 dark:text-white">{{ __('Warehouse & Inventory') }}</h1>
                <p class="text-sm text-slate-500 dark:text-slate-400">
                    {{ __('Monitor raw material inventory and stock levels.') }}
                    @if($totalWarehouseValue > 0)
                        <span class="ml-2 px-2 py-0.5 bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 rounded text-xs font-bold border border-emerald-500/20">
                            {{ __('Est. Value:') }} {{ number_format($totalWarehouseValue, 2) }} {{ $currencyCode }}
                        </span>
                    @endif
                </p>
            </div>
        </div>

        @can('manage inventory')
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.warehouse.inventory.create') }}" class="glass-btn px-4 py-2.5 rounded-xl text-sm font-bold bg-amber-500 text-amber-950 hover:bg-amber-400 border border-amber-600 transition-all shadow-lg shadow-amber-500/20 flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" /></svg>
                {{ __('Take Inventory') }}
            </a>
        </div>
        @endcan
    </div>

    {{-- Stock Overview Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        @foreach($categories as $category)
            <div class="glass-panel p-5 rounded-2xl border border-slate-200 dark:border-white/5 overflow-hidden relative group">
                <div class="absolute -right-4 -top-4 w-24 h-24 bg-amber-500/10 rounded-full blur-2xl group-hover:bg-amber-500/20 transition-colors"></div>
                
                <div class="relative flex flex-col h-full justify-between">
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <span class="p-2 bg-amber-500/10 text-amber-600 dark:text-amber-400 rounded-xl">
                                <i class="fas fa-box"></i>
                            </span>
                            <span class="text-[10px] font-bold uppercase tracking-widest text-slate-400">{{ __('Current Stock') }}</span>
                        </div>
                        <h3 class="text-lg font-bold text-slate-800 dark:text-slate-200">{{ $category->name }}</h3>
                    </div>
                    
                    <div class="mt-4">
                        <div class="text-2xl font-black text-slate-900 dark:text-white">
                            {{ number_format($category->current_stock, 2) }}
                            <span class="text-xs font-normal text-slate-500 uppercase">{{ __($category->unit ?? 'Units') }}</span>
                        </div>
                        <div class="flex items-center gap-4 mt-2 text-[10px] font-bold uppercase tracking-wider">
                            <span class="text-emerald-500">{{ __('In') }}: {{ number_format($category->total_in ?? 0, 1) }}</span>
                            <span class="text-red-500">{{ __('Out') }}: {{ number_format($category->total_out ?? 0, 1) }}</span>
                        </div>
                    </div>
                </div>
            </div>
    </div>

    @can('filter warehouse')
    {{-- History Filters --}}
    <div class="glass-panel p-4 rounded-2xl border border-slate-200 dark:border-white/5 mb-6">
        <form action="{{ route('admin.warehouse.index') }}" method="GET" class="flex flex-col md:flex-row items-end gap-4">
            <div class="flex-1 w-full grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-[10px] uppercase font-bold text-slate-500 tracking-widest mb-1 shadow-sm">{{ __('Date From') }}</label>
                    <input type="date" name="date_from" value="{{ request('date_from') }}" class="w-full px-3 py-2 bg-white dark:bg-[#0f1115] border border-slate-200 dark:border-white/5 rounded-xl text-xs text-slate-900 dark:text-white focus:outline-none focus:border-amber-500/50 transition-all font-medium">
                </div>
                <div>
                    <label class="block text-[10px] uppercase font-bold text-slate-500 tracking-widest mb-1 shadow-sm">{{ __('Date To') }}</label>
                    <input type="date" name="date_to" value="{{ request('date_to') }}" class="w-full px-3 py-2 bg-white dark:bg-[#0f1115] border border-slate-200 dark:border-white/5 rounded-xl text-xs text-slate-900 dark:text-white focus:outline-none focus:border-amber-500/50 transition-all font-medium">
                </div>
            </div>
            <div class="flex gap-2 w-full md:w-auto">
                <button type="submit" class="flex-1 md:flex-none px-6 py-2.5 bg-amber-500 hover:bg-amber-400 text-amber-950 font-bold text-xs uppercase tracking-wider rounded-xl transition-all shadow-lg shadow-amber-500/20">
                    {{ __('Filter') }}
                </button>
                <a href="{{ route('admin.warehouse.index') }}" class="flex-1 md:flex-none px-6 py-2.5 bg-slate-100 dark:bg-white/5 hover:bg-slate-200 dark:hover:bg-white/10 text-slate-600 dark:text-slate-400 font-bold text-xs uppercase tracking-wider rounded-xl transition-all text-center">
                    {{ __('Reset') }}
                </a>
            </div>
        </form>
    </div>
    @endcan

    {{-- History Tabs --}}
    <div class="glass-panel rounded-2xl border border-slate-200 dark:border-white/5 overflow-hidden" x-data="{ tab: 'supplies' }">
        <div class="flex border-b border-slate-200 dark:border-white/5 bg-slate-50/50 dark:bg-white/5 p-1">
            <button @click="tab = 'supplies'" 
                :class="tab === 'supplies' ? 'bg-white dark:bg-slate-800 text-amber-600 shadow-sm' : 'text-slate-500 hover:text-slate-700 dark:hover:text-slate-300'"
                class="flex-1 py-2.5 text-xs font-bold uppercase tracking-widest rounded-xl transition-all">
                {{ __('Recent Supplies') }}
            </button>
            <button @click="tab = 'consumptions'" 
                :class="tab === 'consumptions' ? 'bg-white dark:bg-slate-800 text-amber-600 shadow-sm' : 'text-slate-500 hover:text-slate-700 dark:hover:text-slate-300'"
                class="flex-1 py-2.5 text-xs font-bold uppercase tracking-widest rounded-xl transition-all">
                {{ __('Recent Consumptions') }}
            </button>
        </div>

        <div class="p-4">
            {{-- Supplies History --}}
            <div x-show="tab === 'supplies'" x-transition>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="text-[10px] uppercase tracking-widest text-slate-400 border-b border-slate-200 dark:border-white/5">
                                <th class="px-4 py-3 font-bold">{{ __('Date') }}</th>
                                <th class="px-4 py-3 font-bold">{{ __('Item') }}</th>
                                <th class="px-4 py-3 font-bold">{{ __('Supplier') }}</th>
                                <th class="px-4 py-3 font-bold text-right">{{ __('Quantity') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-white/5">
                            @foreach($recentSupplies as $supply)
                                <tr class="hover:bg-slate-50 dark:hover:bg-white/5 transition-colors">
                                    <td class="px-4 py-3 text-xs text-slate-500 dark:text-slate-400">
                                        {{ $supply->created_at->translatedFormat('Y-m-d') }}
                                        <p class="text-[10px] opacity-50">{{ $supply->created_at->translatedFormat('h:i A') }}</p>
                                    </td>
                                    <td class="px-4 py-3">
                                        <span class="text-xs font-bold text-slate-700 dark:text-slate-300">{{ $supply->category->name }}</span>
                                        @if($supply->material_type_name)
                                            <p class="text-[10px] text-slate-400">{{ $supply->material_type_name }}</p>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-xs text-slate-600 dark:text-slate-400">{{ $supply->supplier->name ?? '-' }}</td>
                                    <td class="px-4 py-3 text-right">
                                        <span class="text-sm font-black text-emerald-600 dark:text-emerald-400">+{{ number_format($supply->quantity, 2) }}</span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-4">{{ $recentSupplies->links() }}</div>
            </div>

            {{-- Consumptions History --}}
            <div x-show="tab === 'consumptions'" x-transition>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="text-[10px] uppercase tracking-widest text-slate-400 border-b border-slate-200 dark:border-white/5">
                                <th class="px-4 py-3 font-bold">{{ __('Date') }}</th>
                                <th class="px-4 py-3 font-bold">{{ __('Item') }}</th>
                                <th class="px-4 py-3 font-bold">{{ __('Work Day') }}</th>
                                <th class="px-4 py-3 font-bold text-right">{{ __('Quantity') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-white/5">
                            @foreach($recentConsumptions as $cons)
                                <tr class="hover:bg-slate-50 dark:hover:bg-white/5 transition-colors">
                                    <td class="px-4 py-3 text-xs text-slate-500 dark:text-slate-400">
                                        {{ $cons->created_at->translatedFormat('Y-m-d') }}
                                        <p class="text-[10px] opacity-50">{{ $cons->created_at->translatedFormat('h:i A') }}</p>
                                    </td>
                                    <td class="px-4 py-3">
                                        <span class="text-xs font-bold text-slate-700 dark:text-slate-300">{{ $cons->category->name }}</span>
                                    </td>
                                    <td class="px-4 py-3 text-xs text-slate-600 dark:text-slate-400">
                                        #{{ $cons->work_day_id }} ({{ $cons->workDay->start_time->translatedFormat('M j') }})
                                    </td>
                                    <td class="px-4 py-3 text-right">
                                        <span class="text-sm font-black text-red-600 dark:text-red-400">-{{ number_format($cons->quantity, 2) }}</span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-4">{{ $recentConsumptions->links() }}</div>
            </div>
        </div>
    </div>
</div>
@endsection
