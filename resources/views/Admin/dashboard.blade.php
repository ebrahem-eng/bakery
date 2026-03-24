@extends('layouts.Admin.App')

@section('content')
<div class="mb-8">
    <h1 class="text-2xl lg:text-3xl font-bold text-slate-900 dark:text-white mb-2 tracking-tight">
        {{ __('Executive') }} <span class="text-transparent bg-clip-text bg-gradient-to-r from-amber-400 to-orange-500">{{ __('Overview') }}</span>
    </h1>
    <p class="text-sm text-slate-500 dark:text-slate-400">
        {{ __('System live telemetry and financial metrics mapping.') }}
    </p>
</div>

<!-- Active Work Day Status Area -->
@if($activeWorkDay)
    <div class="glass-panel rounded-2xl border border-emerald-500/20 bg-emerald-500/5 p-6 mb-8 relative overflow-hidden">
        <div class="absolute top-0 {{ app()->getLocale() == 'ar' ? 'left-0 -translate-x-1/4' : 'right-0 translate-x-1/4' }} w-64 h-64 bg-emerald-500/10 rounded-full blur-3xl -translate-y-1/2"></div>
        <div class="relative z-10 flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
            <div>
                <div class="flex items-center gap-3 mb-2">
                    <span class="relative flex h-3 w-3">
                      <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                      <span class="relative inline-flex rounded-full h-3 w-3 bg-emerald-500"></span>
                    </span>
                    <h2 class="text-xl font-bold text-white tracking-wide">{{ __('ACTIVE WORK DAY') }}</h2>
                </div>
                <p class="text-emerald-400/80 text-sm font-medium tracking-widest uppercase">
                    {{ __('Commenced At: ') }} {{ $activeWorkDay->start_time->format('Y-m-d h:i A') }} 
                    <span class="text-slate-500 ml-2">({{ $activeWorkDay->start_time->diffForHumans() }})</span>
                </p>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('admin.work_days.showCloseForm', $activeWorkDay) }}" class="px-6 py-3 bg-gradient-to-r from-red-500 to-orange-500 hover:from-red-400 hover:to-orange-400 text-white font-bold rounded-xl text-sm shadow-[0_0_15px_rgba(239,68,68,0.3)] transition-all">
                    {{ __('Settle & Close Shift') }}
                </a>
            </div>
        </div>
    </div>
@else
    <div class="glass-panel rounded-2xl border border-slate-500/20 bg-black/20 p-6 mb-8 text-center">
        <div class="w-16 h-16 mx-auto bg-slate-800/50 text-slate-500 rounded-full flex items-center justify-center mb-4">
            <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4M12 20V4"/></svg>
        </div>
        <h2 class="text-xl font-bold text-white mb-2">{{ __('System is Idle') }}</h2>
        <p class="text-slate-400 text-sm mb-6">{{ __('No active work day is running. Accounting and operations are locked.') }}</p>
        <form action="{{ route('admin.work_days.store') }}" method="POST" class="inline-block">
            @csrf
            <button type="submit" class="px-8 py-3 bg-gradient-to-r from-amber-500 to-orange-500 hover:from-amber-400 hover:to-orange-400 text-[#121419] font-bold rounded-xl text-sm shadow-[0_0_15px_rgba(245,158,11,0.3)] transition-all uppercase tracking-widest">
                {{ __('Initialize New Day') }}
            </button>
        </form>
    </div>
@endif

<!-- Today's Financial Quick Stats -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <div class="glass-panel p-6 rounded-2xl border border-white/5 relative overflow-hidden group">
        <div class="absolute top-0 {{ app()->getLocale() == 'ar' ? 'left-0' : 'right-0' }} w-24 h-24 bg-emerald-500/10 rounded-full blur-2xl group-hover:bg-emerald-500/20 transition-all"></div>
        <p class="text-xs text-slate-400 font-bold uppercase tracking-widest mb-1">{{ __('Live Sales Volume') }}</p>
        <h3 class="text-3xl font-black text-white">{{ number_format($todaySales, 2) }} <span class="text-sm text-emerald-400">USD / Gross</span></h3>
        <p class="text-xs text-slate-500 mt-2">{{ number_format($todayBundlesSold) }} {{ __('Bundles Distributed') }}</p>
    </div>
    
    <div class="glass-panel p-6 rounded-2xl border border-white/5 relative overflow-hidden group">
        <div class="absolute top-0 {{ app()->getLocale() == 'ar' ? 'left-0' : 'right-0' }} w-24 h-24 bg-red-500/10 rounded-full blur-2xl group-hover:bg-red-500/20 transition-all"></div>
        <p class="text-xs text-slate-400 font-bold uppercase tracking-widest mb-1">{{ __('Live Cost / Expenses') }}</p>
        <h3 class="text-3xl font-black text-white">{{ number_format($todayExpenses, 2) }} <span class="text-sm text-red-400">USD / Out</span></h3>
        <p class="text-xs text-slate-500 mt-2">{{ __('Raw materials, HR deductions & logistics') }}</p>
    </div>

    <div class="glass-panel p-6 rounded-2xl border border-white/5 relative overflow-hidden group">
        <div class="absolute top-0 {{ app()->getLocale() == 'ar' ? 'left-0' : 'right-0' }} w-24 h-24 bg-amber-500/10 rounded-full blur-2xl group-hover:bg-amber-500/20 transition-all"></div>
        <p class="text-xs text-slate-400 font-bold uppercase tracking-widest mb-1">{{ __('Registered Workers') }}</p>
        <h3 class="text-3xl font-black text-white">{{ number_format($totalWorkers) }}</h3>
        <p class="text-xs text-slate-500 mt-2">{{ __('Manageable via Personnel HR') }}</p>
    </div>

    <div class="glass-panel p-6 rounded-2xl border border-white/5 relative overflow-hidden group">
        <div class="absolute top-0 {{ app()->getLocale() == 'ar' ? 'left-0' : 'right-0' }} w-24 h-24 bg-blue-500/10 rounded-full blur-2xl group-hover:bg-blue-500/20 transition-all"></div>
        <p class="text-xs text-slate-400 font-bold uppercase tracking-widest mb-1">{{ __('Sales Channels') }}</p>
        <h3 class="text-3xl font-black text-white">{{ number_format($totalDistributors) }}</h3>
        <p class="text-xs text-slate-500 mt-2">{{ __('Active Wholesale Distributors') }}</p>
    </div>
</div>

<!-- Financial Sequence Log -->
<div class="glass-panel rounded-2xl border border-white/5 overflow-hidden">
    <div class="p-6 border-b border-white/5 bg-black/20">
        <h3 class="text-lg font-bold text-white">{{ __('Recent Financial Periods (Settled)') }}</h3>
        <p class="text-xs text-slate-400 mt-1">{{ __('Historical read-only ledger arrays mapping settled shifts.') }}</p>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-black/10 border-b border-white/5 text-[10px] uppercase tracking-widest text-slate-500 font-bold">
                    <th class="p-4 w-16">{{ __('Period') }}</th>
                    <th class="p-4">{{ __('Date Interval') }}</th>
                    <th class="p-4">{{ __('Closing Admin') }}</th>
                    <th class="p-4">{{ __('Closed Sales') }}</th>
                    <th class="p-4">{{ __('Closed Expenses') }}</th>
                    <th class="p-4">{{ __('Net Return') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-white/5">
                @forelse($lastDays as $day)
                    @php
                        // Simple net calculation
                        $netProfit = $day->total_sales_at_close - $day->total_expenses_at_close;
                    @endphp
                    <tr class="hover:bg-white/5 transition-colors">
                        <td class="p-4 text-xs font-medium text-slate-500">#{{ $day->id }}</td>
                        <td class="p-4">
                            <p class="text-sm font-bold text-white tracking-wide">{{ $day->start_time->format('Y-m-d') }}</p>
                            <p class="text-[10px] text-slate-500 mt-0.5">{{ $day->start_time->format('H:i') }} - {{ $day->end_time->format('H:i') }}</p>
                        </td>
                        <td class="p-4">
                            <span class="text-xs text-slate-300 font-medium px-2.5 py-1 bg-white/5 border border-white/10 rounded border">
                                {{ $day->closedBy->name ?? 'System' }}
                            </span>
                        </td>
                        <td class="p-4">
                            <span class="text-emerald-400 font-bold text-sm tracking-wide">+ {{ number_format($day->total_sales_at_close, 2) }}</span>
                        </td>
                        <td class="p-4">
                            <span class="text-red-400 font-bold text-sm tracking-wide">- {{ number_format($day->total_expenses_at_close, 2) }}</span>
                        </td>
                        <td class="p-4">
                            <span class="{{ $netProfit >= 0 ? 'text-amber-400' : 'text-rose-400' }} font-bold text-sm">
                                {{ $netProfit >= 0 ? '+' : '' }}{{ number_format($netProfit, 2) }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="p-8 text-center text-slate-500 text-sm">
                            {{ __('No historical periods settled yet.') }}
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
