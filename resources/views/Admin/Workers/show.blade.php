@extends('layouts.Admin.App')

@section('content')
<div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
    <div>
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.workers.index') }}" class="p-2 bg-slate-100 dark:bg-white/5 rounded-xl text-slate-500 hover:text-slate-900 dark:hover:text-white transition-colors">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
            </a>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white tracking-tight">{{ $worker->first_name }} {{ $worker->last_name }}</h1>
        </div>
        <p class="text-sm text-slate-500 mt-1 ms-12">{{ $worker->title ?: __('Worker') }} • {{ __('Daily Wage:') }} {{ number_format($worker->daily_wage, 2) }} {{ $worker->currency?->code }}</p>
    </div>
    <div class="flex gap-3">
        <a href="{{ route('admin.workers.edit', $worker->id) }}" class="px-4 py-2 bg-slate-100 dark:bg-white/5 border border-slate-200 dark:border-white/10 rounded-xl text-sm font-bold text-slate-700 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-white/10 transition-all flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
            {{ __('Edit') }}
        </a>
    </div>
</div>

<!-- Stats Overview -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
    <!-- Contribution Stats -->
    <div class="glass-panel p-5 rounded-2xl relative overflow-hidden group border-amber-500/20 bg-amber-500/5">
        <div class="absolute top-0 {{ app()->getLocale() == 'ar' ? 'left-0' : 'right-0' }} p-2 opacity-10 group-hover:scale-110 transition-transform text-amber-500">
            <svg class="w-10 h-10" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" /></svg>
        </div>
        <h3 class="text-[10px] font-bold text-amber-600 dark:text-amber-400 uppercase tracking-widest mb-1">{{ __('Total Revenue Generated') }}</h3>
        <div class="text-xl font-black text-slate-900 dark:text-white">{{ number_format($totalRevenueSYP, 0) }} <span class="text-[10px] text-slate-400 font-medium ms-1">SYP</span></div>
        <div class="mt-1 text-[9px] text-amber-500 font-bold uppercase">{{ number_format($totalBundlesSold) }} {{ __('Bundles Sold') }}</div>
    </div>

    <div class="glass-panel p-5 rounded-2xl relative overflow-hidden group border-emerald-500/20 bg-emerald-500/5">
        <div class="absolute top-0 {{ app()->getLocale() == 'ar' ? 'left-0' : 'right-0' }} p-2 opacity-10 group-hover:scale-110 transition-transform text-emerald-500">
            <svg class="w-10 h-10" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
        </div>
        <h3 class="text-[10px] font-bold text-emerald-600 dark:text-emerald-400 uppercase tracking-widest mb-1">{{ __('Avg. Revenue per Shift') }}</h3>
        <div class="text-xl font-black text-slate-900 dark:text-white">{{ number_format($avgRevenueSYP, 0) }} <span class="text-[10px] text-slate-400 font-medium ms-1">SYP</span></div>
        <div class="mt-1 text-[9px] text-emerald-500 font-bold uppercase">{{ $shiftCount }} {{ __('Total Shifts') }}</div>
    </div>

    <!-- Liability (Replaced position) -->
    <div class="glass-panel p-5 rounded-2xl relative overflow-hidden group border-rose-500/30 bg-rose-500/5">
        <div class="absolute top-0 {{ app()->getLocale() == 'ar' ? 'left-0' : 'right-0' }} p-2 opacity-20 group-hover:scale-110 transition-transform text-rose-500">
            <svg class="w-10 h-10" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
        </div>
        <h3 class="text-[10px] font-bold text-rose-500 uppercase tracking-widest mb-1">{{ __('Net Liability') }}</h3>
        <div class="text-xl font-black text-slate-900 dark:text-white">{{ number_format($balanceSYP, 0) }} <span class="text-[10px] text-slate-400 font-medium ms-1">SYP</span></div>
        <div class="mt-1 text-[9px] text-slate-500 font-bold uppercase">{{ __('To be paid') }}</div>
    </div>

    <div class="glass-panel p-5 rounded-2xl relative overflow-hidden group">
        <div class="absolute top-0 {{ app()->getLocale() == 'ar' ? 'left-0' : 'right-0' }} p-2 opacity-10 group-hover:scale-110 transition-transform text-slate-400">
            <svg class="w-10 h-10" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
        </div>
        <h3 class="text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-1">{{ __('Total Earned') }}</h3>
        <div class="text-xl font-black text-slate-900 dark:text-white">{{ number_format($totalEarnedSYP, 0) }} <span class="text-[10px] text-slate-400 font-medium ms-1">SYP</span></div>
        <div class="mt-1 text-[9px] text-emerald-500 font-bold uppercase">{{ __('From Shifts') }}</div>
    </div>
</div>

<div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-8">
    <!-- Existing Detail Stats -->
    <div class="glass-panel p-4 rounded-2xl flex items-center gap-4">
        <div class="w-10 h-10 rounded-xl bg-emerald-500/10 flex items-center justify-center text-emerald-500">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
        </div>
        <div>
            <h3 class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">{{ __('Wages & Bonuses') }}</h3>
            <div class="text-lg font-black text-slate-900 dark:text-white">{{ number_format($totalSalariesSYP, 0) }} <span class="text-[9px] text-slate-400 font-medium ms-1 uppercase">SYP</span></div>
        </div>
    </div>

    <div class="glass-panel p-4 rounded-2xl flex items-center gap-4">
        <div class="w-10 h-10 rounded-xl bg-sky-500/10 flex items-center justify-center text-sky-500">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" /></svg>
        </div>
        <div>
            <h3 class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">{{ __('Active Advances') }}</h3>
            <div class="text-lg font-black text-slate-900 dark:text-white">{{ number_format($totalAdvancesSYP - $totalSalariesSYP, 0) }} <span class="text-[9px] text-slate-400 font-medium ms-1 uppercase">SYP</span></div>
        </div>
    </div>

    <div class="glass-panel p-4 rounded-2xl flex items-center gap-4">
        <div class="w-10 h-10 rounded-xl bg-amber-500/10 flex items-center justify-center text-amber-500">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
        </div>
        <div>
            <h3 class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">{{ __('Discounts') }}</h3>
            <div class="text-lg font-black text-slate-900 dark:text-white">{{ number_format($totalDiscountsSYP, 0) }} <span class="text-[9px] text-slate-400 font-medium ms-1 uppercase">SYP</span></div>
        </div>
    </div>
</div>

<!-- Filters & History -->
<div class="glass-panel rounded-2xl border border-slate-200 dark:border-white/5 overflow-hidden shadow-sm">
    <div class="p-4 bg-slate-50 dark:bg-black/20 border-b border-slate-200 dark:border-white/5">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
            <h2 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
                <svg class="w-4 h-4 text-[#0ea5e9]" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                {{ __('Full Financial History') }}
            </h2>
            
            <form action="{{ route('admin.workers.show', $worker->id) }}" method="GET" class="flex flex-wrap items-center gap-3">
                <div class="flex items-center gap-2 glass-input rounded-xl px-3 py-1.5 focus-within:ring-1 focus-within:ring-sky-500/50 transition-all">
                    <label class="text-[10px] uppercase font-black text-slate-400 dark:text-slate-500 leading-none">{{ __('From') }}</label>
                    <input type="date" name="date_from" value="{{ request('date_from') }}" class="bg-transparent border-none text-xs text-slate-700 dark:text-slate-200 focus:ring-0 p-0 w-28 [color-scheme:light] dark:[color-scheme:dark]">
                </div>
                <div class="flex items-center gap-2 glass-input rounded-xl px-3 py-1.5 focus-within:ring-1 focus-within:ring-sky-500/50 transition-all">
                    <label class="text-[10px] uppercase font-black text-slate-400 dark:text-slate-500 leading-none">{{ __('To') }}</label>
                    <input type="date" name="date_to" value="{{ request('date_to') }}" class="bg-transparent border-none text-xs text-slate-700 dark:text-slate-200 focus:ring-0 p-0 w-28 [color-scheme:light] dark:[color-scheme:dark]">
                </div>
                <button type="submit" class="p-2 bg-[#0ea5e9] text-white rounded-xl hover:bg-[#38bdf8] shadow-sm transition-colors">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                </button>
                @if(request()->anyFilled(['date_from', 'date_to']))
                <a href="{{ route('admin.workers.show', $worker->id) }}" class="p-2 bg-slate-200 dark:bg-white/10 text-slate-500 dark:text-slate-400 rounded-xl hover:bg-slate-300 dark:hover:bg-white/20 transition-colors">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                </a>
                @endif
            </form>
        </div>
    </div>

    <div class="overflow-x-auto custom-scrollbar">
        <table class="w-full text-start border-collapse" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
            <thead>
                <tr class="text-slate-500 dark:text-slate-400 text-xs uppercase tracking-wider border-b border-slate-200 dark:border-white/5 opacity-70">
                    <th class="py-4 px-6 font-bold">{{ __('Date / Time') }}</th>
                    <th class="py-4 px-6 font-bold">{{ __('Type / Description') }}</th>
                    <th class="py-4 px-6 font-bold">{{ __('Recorded By') }}</th>
                    <th class="py-4 px-6 font-bold">{{ __('Amount Recorded') }}</th>
                    <th class="py-4 px-6 font-bold">{{ __('Exchange') }}</th>
                    <th class="py-4 px-6 font-bold {{ app()->getLocale() == 'ar' ? 'text-left' : 'text-right' }}">{{ __('Total Local') }} ({{ \App\Models\Currency::where('is_default', true)->value('code') ?? 'SYP' }})</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200 dark:divide-white/5 text-sm">
                @forelse($history as $item)
                <tr class="hover:bg-slate-50 dark:hover:bg-white/5 transition-colors group">
                    <td class="py-4 px-6">
                        <div class="text-slate-900 dark:text-white font-bold opacity-80 flex items-center gap-2 whitespace-nowrap">
                             <span class="w-1.5 h-1.5 rounded-full {{ $item['type'] == 'wage' ? 'bg-emerald-500' : ($item['type'] == 'deduction' ? 'bg-amber-500' : 'bg-sky-500') }}"></span>
                             {{ $item['date'] ? $item['date']->translatedFormat('Y-m-d H:i') : '-' }}
                        </div>
                        <div class="text-[10px] text-slate-400 uppercase mt-0.5">{{ $item['date'] ? $item['date']->translatedFormat('l') : '' }}</div>
                    </td>
                    <td class="py-4 px-6">
                        <div class="flex items-center gap-3">
                            <div class="p-2 rounded-lg {{ $item['type'] == 'wage' ? 'bg-emerald-500/10' : ($item['type'] == 'deduction' ? 'bg-amber-500/10' : 'bg-sky-500/10') }}">
                                {!! $item['icon'] !!}
                            </div>
                            <div>
                                <div class="text-slate-900 dark:text-white font-medium">{{ $item['description'] }}</div>
                                <div class="text-[10px] text-slate-400 uppercase tracking-widest font-bold">{{ __(ucfirst($item['type'])) }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="py-4 px-6">
                        <div class="text-slate-500 dark:text-slate-400 text-xs font-medium">{{ $item['admin'] }}</div>
                    </td>
                    <td class="py-4 px-6">
                        <div class="text-slate-900 dark:text-white font-black">{{ number_format($item['amount'], 2) }} <span class="text-[10px] text-slate-400 font-normal ms-1">{{ $item['currency'] }}</span></div>
                    </td>
                    <td class="py-4 px-6">
                        <div class="text-xs text-slate-500 font-medium italic">
                            1 {{ $item['currency'] }} ≈ {{ number_format($item['rate'], 0) }}
                        </div>
                    </td>
                    <td class="py-4 px-6 {{ app()->getLocale() == 'ar' ? 'text-left' : 'text-right' }}">
                        <span class="px-2 py-1 rounded-md text-[11px] font-black tracking-tight {{ $item['type'] == 'wage' ? 'bg-emerald-500/10 text-emerald-500' : ($item['type'] == 'deduction' ? 'bg-amber-500/10 text-amber-500' : 'bg-sky-500/10 text-sky-500') }}">
                            {{ in_array($item['type'], ['wage', 'allowance']) ? '+' : '-' }} {{ number_format($history->total() > 0 ? $item['total_sypn'] : 0, 0) }}
                        </span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="py-12 text-center text-slate-500 italic">
                        <div class="flex flex-col items-center gap-2 opacity-50">
                            <svg class="w-12 h-12" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" /></svg>
                            {{ __('No transactions or shifts found for the selected period.') }}
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-4 bg-slate-50 dark:bg-black/20 border-t border-slate-200 dark:border-white/5">
        {{ $history->links() }}
    </div>
</div>

<!-- Shift Performance History -->
<div class="glass-panel rounded-2xl border border-emerald-500/20 overflow-hidden shadow-sm mt-8">
    <div class="p-4 bg-emerald-500/5 border-b border-emerald-500/10 flex justify-between items-center">
        <h2 class="text-sm font-bold text-emerald-600 dark:text-emerald-400 flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
            {{ __('Shift Performance History') }}
        </h2>
    </div>
    <div class="overflow-x-auto custom-scrollbar">
        <table class="w-full text-start border-collapse" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
            <thead>
                <tr class="text-slate-500 dark:text-slate-400 text-[10px] uppercase tracking-widest border-b border-slate-200 dark:border-white/5 font-bold">
                    <th class="py-4 px-6">{{ __('Work Day') }}</th>
                    <th class="py-4 px-6 text-center">{{ __('Bundles Received') }}</th>
                    <th class="py-4 px-6 text-center">{{ __('Returned') }}</th>
                    <th class="py-4 px-6 text-center text-amber-500">{{ __('Expected') }}</th>
                    <th class="py-4 px-6 text-center text-emerald-500">{{ __('Collected') }}</th>
                    <th class="py-4 px-6 text-center">{{ __('Difference') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-white/5 text-sm">
                @forelse($shifts as $shift)
                <tr class="hover:bg-emerald-500/5 transition-colors">
                    <td class="py-4 px-6">
                        <div class="font-bold text-slate-800 dark:text-slate-200">#{{ $shift->work_day_id }}</div>
                        <div class="text-[10px] text-slate-400 mt-0.5">{{ $shift->workDay?->start_time->translatedFormat('Y-m-d') }}</div>
                    </td>
                    <td class="py-4 px-6 text-center font-medium">{{ $shift->bundles_received }}</td>
                    <td class="py-4 px-6 text-center text-emerald-600">{{ $shift->bundles_returned }}</td>
                    <td class="py-4 px-6 text-center font-bold text-amber-600">
                        {{ number_format($shift->expected_cash, 0) }} <span class="text-[9px] opacity-70 ms-1 uppercase">SYP</span>
                    </td>
                    <td class="py-4 px-6 text-center font-black text-emerald-600">
                        {{ number_format($shift->cash_collected_base, 0) }} <span class="text-[9px] opacity-70 ms-1 uppercase">SYP</span>
                    </td>
                    <td class="py-4 px-6 text-center font-black {{ $shift->remaining_cash < 0 ? 'text-red-500' : ($shift->remaining_cash > 0 ? 'text-emerald-500' : 'text-slate-400') }}">
                        {{ $shift->remaining_cash > 0 ? '+' : '' }}{{ number_format($shift->remaining_cash, 0) }} <span class="text-[9px] opacity-70 ms-1 uppercase">SYP</span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="py-12 text-center text-slate-400 italic text-sm">
                        {{ __('No completed shifts found.') }}
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
