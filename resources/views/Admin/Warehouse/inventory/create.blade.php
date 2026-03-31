@extends('layouts.Admin.App')

@section('content')
<div class="max-w-4xl mx-auto" x-data="inventoryForm()">
    <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-black bg-gradient-to-r from-emerald-600 to-teal-400 bg-clip-text text-transparent flex items-center gap-3">
                <svg class="w-8 h-8 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                </svg>
                {{ __('Take Inventory') }}
            </h2>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">{{ __('Update expected system stock with physical actual counts and current valuation metrics.') }}</p>
        </div>
        
        <div>
            <a href="{{ route('admin.warehouse.index') }}" class="glass-btn px-4 py-2 rounded-xl text-sm font-medium flex items-center text-slate-700 dark:text-slate-300 hover:text-emerald-500 transition-colors">
                <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                {{ __('Back to Warehouse') }}
            </a>
        </div>
    </div>

    <form action="{{ route('admin.warehouse.inventory.store') }}" method="POST">
        @csrf

        {{-- Main Settings --}}
        <div class="glass-panel p-6 rounded-2xl border border-slate-200 dark:border-white/5 mb-6">
            <h3 class="text-xs font-bold text-emerald-500 dark:text-emerald-400 uppercase tracking-widest mb-4">{{ __('General Details') }}</h3>
            
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">{{ __('Inventory Notes (Optional)') }}</label>
                    <textarea name="notes" rows="2" class="glass-input block w-full px-4 py-3 rounded-xl border-slate-200 dark:border-white/10 text-sm focus:ring-emerald-500" placeholder="{{ __('e.g., Monthly end-of-period stocktake, counting flour & sugar...') }}"></textarea>
                </div>
            </div>
        </div>

        {{-- Categories List --}}
        <div class="glass-panel rounded-2xl border border-slate-200 dark:border-white/5 mb-6 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left whitespace-nowrap">
                    <thead>
                        <tr class="bg-slate-50 dark:bg-black/40 border-b border-slate-200 dark:border-white/5">
                            <th class="py-4 px-5 text-[10px] font-bold text-slate-600 dark:text-slate-400 uppercase tracking-wider">{{ __('Material / Category') }}</th>
                            <th class="py-4 px-5 text-[10px] font-bold text-slate-600 dark:text-slate-400 uppercase tracking-wider text-center">{{ __('System Quantity') }}</th>
                            <th class="py-4 px-5 text-[10px] font-bold text-emerald-600 dark:text-emerald-400 uppercase tracking-wider">{{ __('Actual Count') }} <span class="text-red-500">*</span></th>
                            <th class="py-4 px-5 text-[10px] font-bold text-emerald-600 dark:text-emerald-400 uppercase tracking-wider">{{ __('Unit Price Valuation') }} <span class="text-red-500">*</span></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-white/5 bg-white/20 dark:bg-black/10">
                        @foreach($categories as $index => $category)
                            <tr class="hover:bg-slate-50/50 dark:hover:bg-white/5 transition-colors">
                                <td class="py-4 px-5">
                                    <div class="font-bold text-slate-900 dark:text-white text-sm">{{ $category->name }}</div>
                                    <input type="hidden" name="items[{{ $index }}][category_id]" value="{{ $category->id }}">
                                </td>
                                <td class="py-4 px-5 text-center">
                                    <div class="px-3 py-1 bg-slate-100 dark:bg-black/20 text-slate-700 dark:text-slate-300 rounded font-mono font-bold text-sm inline-block">
                                        {{ number_format($category->system_stock, 2) }} <span class="text-[10px]">{{ $category->unit ?? '' }}</span>
                                    </div>
                                    <input type="hidden" name="items[{{ $index }}][system_quantity]" value="{{ $category->system_stock }}">
                                    <!-- Provide the system stock to alpine js model dynamically so user can quick-fill -->
                                    <button type="button" onclick="document.getElementById('actual_{{ $index }}').value = '{{ $category->system_stock }}'" class="block mx-auto mt-2 text-[10px] text-emerald-500 hover:text-emerald-400 font-bold underline decoration-dotted">
                                        {{ __('Match System') }}
                                    </button>
                                </td>
                                <td class="py-4 px-5">
                                    <div class="relative max-w-[150px]">
                                        <input type="number" step="0.01" name="items[{{ $index }}][actual_quantity]" id="actual_{{ $index }}"
                                            class="glass-input block w-full pl-4 pr-10 py-2 rounded-lg text-sm font-bold text-emerald-900 dark:text-emerald-400 border border-slate-200 dark:border-white/20 bg-emerald-50/50 dark:bg-emerald-900/10 focus:ring-emerald-500 focus:border-emerald-500"
                                            placeholder="{{ __('Skip') }}">
                                        <div class="absolute inset-y-0 {{ app()->getLocale() == 'ar' ? 'left-0 pl-3' : 'right-0 pr-3' }} flex items-center pointer-events-none">
                                            <span class="text-[10px] font-bold text-slate-400">{{ $category->unit ?? '' }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-4 px-5">
                                    <div class="relative max-w-[150px]">
                                        <input type="number" step="0.01" name="items[{{ $index }}][unit_price]" value="{{ $category->latest_price }}"
                                            class="glass-input block w-full pl-4 pr-10 py-2 rounded-lg text-sm font-bold border border-slate-200 dark:border-white/10 dark:bg-black/40 focus:ring-emerald-500 focus:border-emerald-500"
                                            placeholder="0.00">
                                        <div class="absolute inset-y-0 {{ app()->getLocale() == 'ar' ? 'left-0 pl-3' : 'right-0 pr-3' }} flex items-center pointer-events-none">
                                            <span class="text-[10px] font-bold text-amber-500">SYSTEM<br>UNIT</span>
                                        </div>
                                    </div>
                                    <div class="mt-1 text-[9px] text-slate-400">{{ __('Latest known price:') }} {{ number_format($category->latest_price, 2) }}</div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Submit Action --}}
        <div class="glass-panel p-5 rounded-2xl border border-emerald-500/20 bg-emerald-500/5">
            <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                <div class="flex items-start gap-3">
                    <div class="p-2 bg-emerald-500/20 text-emerald-600 rounded-lg shrink-0 mt-1">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    </div>
                    <div>
                        <h4 class="text-sm font-bold text-emerald-800 dark:text-emerald-400">{{ __('Confirm Inventory Adjustment') }}</h4>
                        <p class="text-xs text-emerald-600 dark:text-emerald-500 mt-1">
                            {{ __('By saving, the system stock will be reset to match your actual counts. This action provides accurate numbers for tracking and dashboard valuations.') }}
                        </p>
                    </div>
                </div>

                <button type="submit" class="w-full sm:w-auto shrink-0 bg-emerald-500 hover:bg-emerald-400 text-emerald-950 font-black py-3 px-8 rounded-xl transition-all shadow-lg shadow-emerald-500/20 text-sm flex items-center justify-center gap-2">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                    {{ __('Save & Adjust Warehouse') }}
                </button>
            </div>
        </div>
    </form>
</div>

@endsection
