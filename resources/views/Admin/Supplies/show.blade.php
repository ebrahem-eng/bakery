@extends('layouts.Admin.App')

@section('content')
<div class="mb-8 flex items-center justify-between">
    <div>
        <h1 class="text-2xl font-bold text-slate-900 dark:text-white mb-1">{{ __('Supply Invoice Details') }} <span class="text-emerald-500 dark:text-emerald-400">#{{ $supply->id }}</span></h1>
        <p class="text-sm text-slate-500">{{ __('Work Day:') }} {{ $supply->work_day_id }} | {{ $supply->created_at->translatedFormat('M d, Y h:i A') }}</p>
    </div>
    <a href="{{ route('admin.supplies.index') }}" class="text-slate-500 hover:text-slate-900 dark:text-slate-400 dark:hover:text-white transition-colors text-sm font-medium flex items-center">
        <svg class="w-4 h-4 {{ app()->getLocale() == 'ar' ? 'ml-1 rotate-180' : 'mr-1' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
        </svg>
        {{ __('Back') }}
    </a>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Main Info -->
    <div class="lg:col-span-2 space-y-6">
        <div class="glass-panel p-6 rounded-2xl border border-slate-200 dark:border-white/5">
            <h2 class="text-lg font-bold text-slate-900 dark:text-white mb-6 border-b border-slate-200 dark:border-white/5 pb-4">{{ __('Material Specifications') }}</h2>
            
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                <div>
                    <label class="block text-xs text-slate-500 uppercase tracking-wider mb-1">{{ __('Supplier') }}</label>
                    <div class="font-bold text-slate-900 dark:text-white">{{ $supply->supplier->first_name }} {{ $supply->supplier->last_name }}</div>
                </div>
                <div>
                    <label class="block text-xs text-slate-500 uppercase tracking-wider mb-1">{{ __('Category') }}</label>
                    <div class="font-bold text-[#0ea5e9]">{{ __($supply->category->name) }}</div>
                </div>
                <div>
                    <label class="block text-xs text-slate-500 uppercase tracking-wider mb-1">{{ __('Quantity') }}</label>
                    <div class="font-bold text-slate-900 dark:text-white font-mono">{{ number_format($supply->quantity, 2) }} {{ __('Units') }}</div>
                </div>
                <div>
                    <label class="block text-xs text-slate-500 uppercase tracking-wider mb-1">{{ __('Registered By') }}</label>
                    <div class="font-bold text-slate-700 dark:text-slate-300">{{ $supply->admin->name ?? '--' }}</div>
                </div>
            </div>

            @if($supply->material_type_name || $supply->boxes_count)
            <div class="mt-6 p-4 bg-slate-50 dark:bg-black/20 rounded-xl border border-slate-200 dark:border-white/5 grid grid-cols-1 md:grid-cols-2 gap-4">
                @if($supply->material_type_name)
                <div>
                    <label class="block text-[10px] text-slate-500 uppercase tracking-wider mb-1">{{ __('Flour Type') }}</label>
                    <div class="text-amber-600 dark:text-amber-400 font-bold">{{ $supply->material_type_name }}</div>
                </div>
                @endif
                @if($supply->boxes_count)
                <div>
                    <label class="block text-[10px] text-slate-500 uppercase tracking-wider mb-1">{{ __('Box Packaging') }}</label>
                    <div class="text-emerald-600 dark:text-emerald-400 font-mono text-sm">{{ $supply->boxes_count }} {{ __('Boxes') }} <span class="text-slate-400 dark:text-slate-500">×</span> {{ $supply->box_weight }} {{ __('KG') }}</div>
                </div>
                @endif
            </div>
            @endif

            @if($supply->notes)
            <div class="mt-6">
                <label class="block text-xs text-slate-500 uppercase tracking-wider mb-2">{{ __('Notes') }}</label>
                <div class="text-sm text-slate-700 dark:text-slate-300 bg-slate-50 dark:bg-white/5 p-4 rounded-xl leading-relaxed border border-slate-200 dark:border-white/5">{{ $supply->notes }}</div>
            </div>
            @endif
        </div>
    </div>

    <!-- Financials -->
    <div class="lg:col-span-1 space-y-6">
        <div class="glass-panel p-6 rounded-2xl border border-emerald-500/20 dark:border-emerald-500/10 relative overflow-hidden">
            <div class="absolute top-0 right-0 p-4 opacity-5 pointer-events-none text-emerald-900 dark:text-white">
                <svg class="w-24 h-24" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/></svg>
            </div>
            <h2 class="text-sm font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-6">{{ __('Financial Settlement') }}</h2>
            
            <div class="flex justify-between items-end mb-4">
                <div class="text-xs text-slate-500 dark:text-slate-400">{{ __('Unit Price') }}</div>
                <div class="text-sm font-bold text-slate-900 dark:text-white font-mono">{{ number_format($supply->unit_price, 2) }} {{ $currencyCode }}</div>
            </div>
            
            <div class="flex justify-between items-end mb-4">
                <div class="text-xs text-slate-500 dark:text-slate-400">{{ __('Total Invoice Cost') }}</div>
                <div class="text-sm font-bold text-emerald-600 dark:text-emerald-400 font-mono">{{ number_format($supply->total_cost, 2) }} {{ $currencyCode }}</div>
            </div>

            <div class="flex justify-between items-end mb-6 pb-6 border-b border-slate-200 dark:border-white/5">
                <div class="text-xs text-slate-500 dark:text-slate-400">{{ __('Amount Paid') }}</div>
                <div class="text-sm font-bold {{ $supply->paid_amount < $supply->total_cost ? 'text-red-500 dark:text-red-400' : 'text-emerald-600 dark:text-emerald-400' }} font-mono">
                    {{ number_format($supply->paid_amount, 2) }} {{ $currencyCode }}
                </div>
            </div>

            @if($supply->unloading_fee > 0)
            <div class="p-4 bg-amber-50 dark:bg-amber-500/5 border border-amber-200 dark:border-amber-500/10 rounded-xl">
                <div class="text-xs font-bold text-amber-600 dark:text-amber-500 mb-2 uppercase tracking-wider">{{ __('Unloading Details') }}</div>
                <div class="flex justify-between items-center mb-1">
                    <span class="text-[10px] text-slate-500">{{ __('Fee Paid By') }}</span>
                    <span class="text-xs font-bold text-amber-600 dark:text-amber-200">{{ $supply->unloading_fee_payer == 'bakery' ? __('Bakery') : __('Supplier') }}</span>
                </div>
                <div class="flex justify-between items-center mb-1">
                    <span class="text-[10px] text-slate-500">{{ __('Fee Amount') }}</span>
                    <span class="text-xs font-bold font-mono text-slate-900 dark:text-white">{{ number_format($supply->unloading_fee, 2) }} {{ $supply->unloadingFeeCurrency->code ?? '' }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-[10px] text-slate-500">{{ __('Exchange Rate') }}</span>
                    <span class="text-[10px] font-mono text-slate-400">× {{ number_format($supply->unloading_fee_exchange_rate, 2) }}</span>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
