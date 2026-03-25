@extends('layouts.admin')

@section('title', __('Warehouse'))

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white">{{ __('Warehouse Management') }}</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400">{{ __('Monitor raw material inventory and stock levels.') }}</p>
        </div>
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
                            <span class="text-xs font-normal text-slate-500 uppercase">{{ __('Units') }}</span>
                        </div>
                        <div class="flex items-center gap-4 mt-2 text-[10px] font-bold uppercase tracking-wider">
                            <span class="text-emerald-500">{{ __('In') }}: {{ number_format($category->total_in ?? 0, 1) }}</span>
                            <span class="text-red-500">{{ __('Out') }}: {{ number_format($category->total_out ?? 0, 1) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

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
                                        {{ $supply->created_at->format('Y-m-d') }}
                                        <p class="text-[10px] opacity-50">{{ $supply->created_at->format('h:i A') }}</p>
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
                                        {{ $cons->created_at->format('Y-m-d') }}
                                        <p class="text-[10px] opacity-50">{{ $cons->created_at->format('h:i A') }}</p>
                                    </td>
                                    <td class="px-4 py-3">
                                        <span class="text-xs font-bold text-slate-700 dark:text-slate-300">{{ $cons->category->name }}</span>
                                    </td>
                                    <td class="px-4 py-3 text-xs text-slate-600 dark:text-slate-400">
                                        #{{ $cons->work_day_id }} ({{ $cons->workDay->start_time->format('M j') }})
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
