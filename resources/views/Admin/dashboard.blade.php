@extends('layouts.Admin.App')

@section('content')
<!-- Page Header -->
<div class="mb-8 flex flex-col sm:flex-row justify-between items-start sm:items-end gap-4">
    <div>
        <h1 class="text-2xl lg:text-3xl font-bold text-slate-900 dark:text-white mb-1 tracking-tight">{{ __('System') }} <span class="text-transparent bg-clip-text bg-gradient-to-r from-[#eab308] to-[#f59e0b]">{{ __('Overview') }}</span></h1>
        <p class="text-sm text-slate-400">{{ __('Bakery management dashboard is operating smoothly.') }}</p>
    </div>
    <div class="flex gap-3">
        <form action="{{ route('admin.work_days.store') }}" method="POST">
            @csrf
            <button type="submit" class="bg-[#eab308]/10 text-amber-600 dark:text-[#eab308] border border-amber-300 dark:border-[#eab308]/30 hover:bg-[#eab308] hover:text-[#451a03] transition-all px-4 py-2 rounded-xl text-sm font-bold flex items-center shadow-[0_0_15px_rgba(234,179,8,0.15)] hover:shadow-[0_0_20px_rgba(234,179,8,0.4)]">
                <svg class="w-4 h-4 {{ app()->getLocale() == 'ar' ? 'ml-2' : 'mr-2' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                {{ __('Start New Work Day') }}
            </button>
        </form>
    </div>
</div>

<!-- Quick Access Grid -->
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
    <a href="{{ route('admin.supplies.create') }}" class="glass-panel p-4 rounded-xl border border-transparent hover:border-blue-500/30 hover:bg-slate-100 dark:hover:bg-white/5 hover:-translate-y-1 transition-all flex flex-col items-center justify-center gap-2 group">
        <div class="p-3 rounded-full bg-blue-500/20 text-blue-500 group-hover:scale-110 transition-transform">
            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
        </div>
        <span class="text-sm font-medium text-slate-700 dark:text-slate-300">{{ __('Add Supply') }}</span>
    </a>
    <button class="glass-panel p-4 rounded-xl hover:bg-slate-100 dark:hover:bg-white/5 hover:-translate-y-1 transition-all flex flex-col items-center justify-center gap-2 group">
        <div class="p-3 rounded-full bg-emerald-500/20 text-emerald-600 dark:text-emerald-400 group-hover:scale-110 transition-transform">
            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
        </div>
        <span class="text-sm font-medium text-slate-700 dark:text-slate-300">{{ __('Add Distribution') }}</span>
    </button>
    <button class="glass-panel p-4 rounded-xl hover:bg-slate-100 dark:hover:bg-white/5 hover:-translate-y-1 transition-all flex flex-col items-center justify-center gap-2 group">
        <div class="p-3 rounded-full bg-red-500/20 text-red-600 dark:text-red-400 group-hover:scale-110 transition-transform">
            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/></svg>
        </div>
        <span class="text-sm font-medium text-slate-700 dark:text-slate-300">{{ __('Add Expense/Draw') }}</span>
    </button>
    <button class="glass-panel p-4 rounded-xl hover:bg-slate-100 dark:hover:bg-white/5 hover:-translate-y-1 transition-all flex flex-col items-center justify-center gap-2 group">
        <div class="p-3 rounded-full bg-purple-500/20 text-purple-600 dark:text-purple-400 group-hover:scale-110 transition-transform">
            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
        </div>
        <span class="text-sm font-medium text-slate-700 dark:text-slate-300">{{ __('Worker Advance') }}</span>
    </button>
</div>

<!-- Stats Grid -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    
    <!-- Stat 1: Day Balance -->
    <div class="glass-panel p-6 rounded-2xl relative overflow-hidden group hover:-translate-y-1 transition-transform duration-300 border border-white/5">
        <div class="absolute -right-10 -top-10 w-32 h-32 bg-[#eab308] opacity-10 rounded-full blur-2xl group-hover:opacity-20 transition-opacity"></div>
        <div class="flex justify-between items-start mb-4">
            <div>
                <p class="text-sm font-medium text-slate-400">{{ __('Net Day Balance') }}</p>
                <h3 class="text-3xl font-bold text-white mt-1">$0.00</h3>
            </div>
            <div class="p-3 rounded-xl bg-[#eab308]/10 text-[#eab308] border border-[#eab308]/20">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            </div>
        </div>
        <div class="flex items-center text-sm">
            <span class="text-emerald-400 flex items-center font-medium">
                {{ __('Active Work Day') }}
            </span>
        </div>
    </div>

    <!-- Stat 2: Est. Expenses -->
    <div class="glass-panel p-6 rounded-2xl relative overflow-hidden group hover:-translate-y-1 transition-transform duration-300 border border-white/5">
        <div class="absolute -right-10 -top-10 w-32 h-32 bg-[#ef4444] opacity-10 rounded-full blur-2xl group-hover:opacity-20 transition-opacity"></div>
        <div class="flex justify-between items-start mb-4">
            <div>
                <p class="text-sm font-medium text-slate-400">{{ __('Total Expenses') }}</p>
                <h3 class="text-3xl font-bold text-white mt-1">$0.00</h3>
            </div>
            <div class="p-3 rounded-xl bg-[#ef4444]/10 text-[#ef4444] border border-[#ef4444]/20">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 17h8m0 0v-8m0 8l-8-8-4 4-6-6" /></svg>
            </div>
        </div>
        <div class="flex items-center text-sm">
            <span class="text-slate-500 shadow-text">{{ __('Drawings & Wages & Operational') }}</span>
        </div>
    </div>

    <!-- Stat 3: Workers Present -->
    <div class="glass-panel p-6 rounded-2xl relative overflow-hidden group hover:-translate-y-1 transition-transform duration-300 border border-white/5">
        <div class="absolute -right-10 -top-10 w-32 h-32 bg-[#3b82f6] opacity-10 rounded-full blur-2xl group-hover:opacity-20 transition-opacity"></div>
        <div class="flex justify-between items-start mb-4">
            <div>
                <p class="text-sm font-medium text-slate-400">{{ __('Workers Present') }}</p>
                <h3 class="text-3xl font-bold text-white mt-1">0</h3>
            </div>
            <div class="p-3 rounded-xl bg-[#3b82f6]/10 text-[#3b82f6] border border-[#3b82f6]/20">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
            </div>
        </div>
        <div class="flex items-center text-sm">
            <span class="text-slate-500 shadow-text">{{ __('Logged in today') }}</span>
        </div>
    </div>

    <!-- Stat 4: Sales Count -->
    <div class="glass-panel p-6 rounded-2xl relative overflow-hidden group hover:-translate-y-1 transition-transform duration-300 border border-white/5">
        <div class="absolute -right-10 -top-10 w-32 h-32 bg-[#10b981] opacity-10 rounded-full blur-2xl group-hover:opacity-20 transition-opacity"></div>
        <div class="flex justify-between items-start mb-4">
            <div>
                <p class="text-sm font-medium text-slate-400">{{ __('Distributions') }}</p>
                <h3 class="text-3xl font-bold text-white mt-1">0</h3>
            </div>
            <div class="p-3 rounded-xl bg-[#10b981]/10 text-[#10b981] border border-[#10b981]/20">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" /></svg>
            </div>
        </div>
        <div class="flex items-center text-sm">
            <span class="text-slate-500 shadow-text">{{ __('Bundles out today') }}</span>
        </div>
    </div>
</div>

<!-- Main Content Area with Chart -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
    
    <!-- Large Chart Section -->
    <div class="lg:col-span-2 glass-panel p-6 rounded-2xl border border-white/5 flex flex-col">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
            <h2 class="text-lg font-semibold text-white">{{ __('Weekly Financials') }}</h2>
            
            <div class="flex bg-slate-800/50 rounded-lg p-1 border border-white/5 shadow-inner">
                <a href="#" class="px-3 py-1.5 text-xs font-semibold rounded-md transition-all bg-[#eab308] text-[#451a03] shadow-[0_0_10px_rgba(234,179,8,0.3)]">{{ __('This Week') }}</a>
                <a href="#" class="px-3 py-1.5 text-xs font-semibold rounded-md transition-all text-slate-400 hover:text-white">{{ __('Last Week') }}</a>
            </div>
        </div>
        
        <div class="flex-1 w-full rounded-xl bg-slate-900/50 border border-white/5 flex items-center justify-center relative overflow-hidden group p-4 min-h-[300px]">
             <!-- Placeholder for chart, actual data driven -->
             <div class="text-slate-500 text-sm">{{ __('Awaiting Sales Data...') }}</div>
        </div>
    </div>

    <!-- Recent Activity List -->
    <div class="glass-panel p-6 rounded-2xl border border-white/5 flex flex-col h-[400px] lg:h-auto">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-lg font-semibold text-white">{{ __('Recent Transactions') }}</h2>
            <span class="px-2 py-1 bg-[#10b981]/20 text-[#10b981] text-[10px] uppercase font-bold tracking-wider rounded-md border border-[#10b981]/20">{{ __('Live') }}</span>
        </div>
        
        <div class="flex-1 overflow-y-auto pr-2 custom-scrollbar">
            <ul class="space-y-6">
                <!-- Activities Placeholder -->
                <div class="text-sm text-slate-500 py-8 text-center italic font-light">{{ __('No transactions registered yet in active work day.') }}</div>
            </ul>
        </div>
    </div>
</div>
@endsection
