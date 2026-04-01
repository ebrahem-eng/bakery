@extends('layouts.Admin.App')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white tracking-tight">{{ __('Start Custom Work Day') }}</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">{{ __('Create a retroactive accounting period for historical data entry.') }}</p>
        </div>
        <a href="{{ route('admin.work_days.index') }}" class="text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200 transition-colors flex items-center gap-2 text-sm font-medium">
            <svg class="w-4 h-4 {{ app()->getLocale() == 'ar' ? 'rotate-180' : '' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
            {{ __('Back to Work Days') }}
        </a>
    </div>

    @if(session('error'))
    <div class="mb-6 px-4 py-3 bg-red-500/10 border border-red-500/20 text-red-400 rounded-xl text-sm">
        {{ session('error') }}
    </div>
    @endif

    <div class="glass-panel rounded-2xl border border-slate-200 dark:border-white/10 overflow-hidden shadow-xl">
        <form action="{{ route('admin.work_days.store') }}" method="POST">
            @csrf
            <div class="p-6 sm:p-8">
                <div class="flex items-start gap-4 mb-8">
                    <div class="shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-indigo-500/20">
                        <svg class="h-6 w-6 text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-slate-900 dark:text-white">{{ __('Retroactive Entry Details') }}</h3>
                        <p class="text-sm text-slate-500 dark:text-slate-400">{{ __('Ensure the selected date does not already have an existing workday record.') }}</p>
                    </div>
                </div>

                <div class="space-y-6">
                    <div>
                        <label class="block text-[10px] uppercase tracking-widest text-slate-500 font-bold mb-2">{{ __('Historical Start Timestamp') }} <span class="text-red-500">*</span></label>
                        <input type="datetime-local" name="custom_start_time" required autofocus
                            class="block w-full px-4 py-4 bg-white dark:bg-[#0f1115] border border-slate-200 dark:border-white/10 rounded-xl text-md text-slate-900 dark:text-white focus:outline-none focus:border-indigo-500/50 focus:ring-1 focus:ring-indigo-500/50 transition-all shadow-sm">
                        <p class="mt-2 text-xs text-slate-500 italic">{{ __('Pick the exact date and time when this shift actually began.') }}</p>
                    </div>

                    <div class="p-4 bg-amber-500/5 border border-amber-500/20 rounded-xl flex gap-3">
                        <svg class="w-5 h-5 text-amber-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        <p class="text-xs text-amber-600/80 dark:text-amber-500/80 leading-relaxed font-medium">
                            {{ __('Starting a custom workday will allow you to record historical accounting data. You can close this workday directly after creation if you are filling past records.') }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="bg-slate-50 dark:bg-black/20 px-6 py-4 sm:px-8 border-t border-slate-200 dark:border-white/5 flex items-center justify-end gap-3">
                <a href="{{ route('admin.work_days.index') }}" class="px-6 py-2.5 rounded-xl text-sm font-bold text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-white/5 transition-all uppercase tracking-widest">
                    {{ __('Discard') }}
                </a>
                <button type="submit" class="px-8 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl text-sm shadow-[0_10px_20px_rgba(79,70,229,0.2)] transition-all uppercase tracking-widest">
                    {{ __('Initialize Custom Day') }}
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
