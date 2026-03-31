@extends('layouts.Admin.App')

@section('content')
<div class="mb-8 flex items-center justify-between">
    <div>
        <h1 class="text-2xl font-bold text-slate-900 dark:text-white mb-1">{{ __('Expense Details') }} <span class="text-red-500 dark:text-red-400">#{{ $expense->id }}</span></h1>
        <p class="text-sm text-slate-500">{{ __('Work Day #') }}{{ $expense->work_day_id }} | {{ $expense->created_at->translatedFormat('M d, Y h:i A') }}</p>
    </div>
    <a href="{{ route('admin.expenses.index') }}" class="text-slate-500 hover:text-slate-900 dark:text-slate-400 dark:hover:text-white transition-colors text-sm font-medium flex items-center">
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
            <h2 class="text-lg font-bold text-slate-900 dark:text-white mb-6 border-b border-slate-200 dark:border-white/5 pb-4">{{ __('Entry Information') }}</h2>
            
            <div class="grid grid-cols-2 gap-6">
                <div>
                    <label class="block text-xs text-slate-500 uppercase tracking-wider mb-1">{{ __('Title / Label') }}</label>
                    <div class="font-bold text-slate-900 dark:text-white text-lg">{{ $expense->title }}</div>
                </div>
                <div>
                    <label class="block text-xs text-slate-500 uppercase tracking-wider mb-1">{{ __('Category') }}</label>
                    <div class="inline-flex px-3 py-1 bg-slate-100 dark:bg-white/5 text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-white/10 rounded-lg text-xs font-bold uppercase tracking-wider">
                        @php
                            $catLabels = [
                                'operating' => __('Operating Costs'),
                                'logistics' => __('Logistics / Patrols'),
                                'personal' => __('Personal Drawings'),
                                'other' => __('Other Expenses'),
                            ];
                        @endphp
                        {{ $catLabels[$expense->category] ?? __('Other') }}
                    </div>
                </div>
                <div>
                    <label class="block text-xs text-slate-500 uppercase tracking-wider mb-1">{{ __('Logged By') }}</label>
                    <div class="font-bold text-slate-700 dark:text-slate-300">{{ $expense->admin->name ?? '--' }}</div>
                </div>
                <div>
                    <label class="block text-xs text-slate-500 uppercase tracking-wider mb-1">{{ __('Work Day Association') }}</label>
                    <div class="font-bold text-amber-600">ID #{{ $expense->work_day_id }}</div>
                </div>
            </div>

            @if($expense->notes)
            <div class="mt-8 pt-8 border-t border-slate-200 dark:border-white/5">
                <label class="block text-xs text-slate-500 uppercase tracking-wider mb-2">{{ __('Administrative Notes') }}</label>
                <div class="text-sm text-slate-700 dark:text-slate-300 bg-slate-50 dark:bg-white/5 p-4 rounded-xl leading-relaxed border border-slate-200 dark:border-white/5">
                    {{ $expense->notes }}
                </div>
            </div>
            @endif
        </div>
    </div>

    <!-- Financial Summary -->
    <div class="lg:col-span-1 space-y-6">
        <div class="glass-panel p-6 rounded-2xl border border-red-500/20 dark:border-red-500/10 relative overflow-hidden">
            <div class="absolute top-0 right-0 p-4 opacity-5 pointer-events-none text-red-900 dark:text-white">
                <svg class="w-24 h-24" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm.31-8.86c-1.77-.45-2.34-.94-2.34-1.67 0-.84.79-1.39 2.1-1.39 1.47 0 2.01.59 2.06 1.47h1.3c-.07-1.44-1.11-2.43-2.52-2.73V5h-1.6v1.8c-1.28.27-2.33 1.11-2.33 2.5 0 1.6 1.33 2.4 3.28 2.89 2.04.51 2.5 1.15 2.5 1.83 0 .55-.38 1.49-2.31 1.49-1.73 0-2.34-.84-2.43-1.66h-1.35c.08 1.41 1 2.39 2.45 2.76V19h1.6v-1.84c1.47-.3 2.53-1.11 2.53-2.55 0-1.86-1.55-2.61-3.58-3.13z"/></svg>
            </div>
            <h2 class="text-sm font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-6">{{ __('Financial Record') }}</h2>
            
            <div class="space-y-4">
                <div class="flex justify-between items-end">
                    <div class="text-xs text-slate-500 dark:text-slate-400">{{ __('Amount Logged') }}</div>
                    <div class="text-xl font-black text-red-600 dark:text-red-400 font-mono">
                        {{ number_format($expense->amount, 2) }} {{ $expense->currency->code ?? '' }}
                    </div>
                </div>
                
                <div class="pt-4 border-t border-slate-200 dark:border-white/5">
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-[11px] text-slate-500">{{ __('Exchange Rate Cache') }}</span>
                        <span class="text-xs font-mono font-bold text-slate-700 dark:text-slate-300">1 {{ $expense->currency->code ?? '' }} = {{ number_format($expense->exchange_rate, 4) }} {{ \App\Models\Currency::where('is_default', true)->value('code') ?? 'SYP' }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-[11px] text-slate-500 font-bold uppercase">{{ __('Final Local Value') }}</span>
                        <span class="text-sm font-black text-slate-900 dark:text-white font-mono">
                            {{ number_format(\App\Models\Currency::convertAmount($expense->amount, $expense->exchange_rate), 2) }} {{ \App\Models\Currency::where('is_default', true)->value('code') ?? 'SYP' }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="p-4 bg-slate-900/5 dark:bg-white/5 border border-slate-200 dark:border-white/10 rounded-2xl">
            <p class="text-[10px] text-slate-500 leading-tight">
                {{ __('This expense record is bound to Work Day #') }}{{ $expense->work_day_id }}. {{ __('Edits to historical ledgers are restricted once the work day is settled and closed to maintain accounting integrity.') }}
            </p>
        </div>
    </div>
</div>
@endsection
