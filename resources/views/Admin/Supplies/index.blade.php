@extends('layouts.Admin.App')

@section('content')
<div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-end gap-4">
    <div>
        <h1 class="text-2xl font-bold text-slate-900 dark:text-white tracking-tight">{{ __('Supply Records') }}</h1>
        <p class="text-sm text-slate-500 mt-1">{{ __('Chronological ledger of raw material purchases.') }}</p>
    </div>
    <a href="{{ route('admin.supplies.create') }}" class="bg-[#0ea5e9]/10 text-[#0ea5e9] border border-[#0ea5e9]/30 hover:bg-[#0ea5e9] hover:text-white transition-all px-4 py-2 rounded-xl text-sm font-bold flex items-center shadow-[0_0_15px_rgba(14,165,233,0.15)]">
        <svg class="w-4 h-4 {{ app()->getLocale() == 'ar' ? 'ml-2' : 'mr-2' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
        {{ __('Register New Supply') }}
    </a>
</div>

@if(session('success'))
<div class="mb-6 px-4 py-3 bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 rounded-xl">
    {{ session('success') }}
</div>
@endif

<div class="glass-panel p-6 rounded-2xl border border-slate-200 dark:border-white/5">
    <div class="overflow-x-auto custom-scrollbar">
        <table class="w-full text-left border-collapse" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
            <thead>
                <tr class="text-slate-500 dark:text-slate-400 text-xs uppercase tracking-wider border-b border-slate-200 dark:border-white/5">
                    <th class="py-4 px-4 font-medium">{{ __('Date & Work Day') }}</th>
                    <th class="py-4 px-4 font-medium">{{ __('Supplier') }}</th>
                    <th class="py-4 px-4 font-medium">{{ __('Category Info') }}</th>
                    <th class="py-4 px-4 font-medium">{{ __('Metrics') }}</th>
                    <th class="py-4 px-4 font-medium text-right">{{ __('Finances') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200 dark:divide-white/5 text-sm text-slate-600 dark:text-slate-300">
                @forelse($supplies as $supply)
                <tr class="hover:bg-slate-50 dark:hover:bg-white/5 transition-colors">
                    <td class="py-3 px-4">
                        <div class="font-medium text-slate-900 dark:text-white">{{ $supply->created_at->format('M d, Y h:i A') }}</div>
                        <div class="text-[11px] text-emerald-600 dark:text-emerald-400 font-bold tracking-wider uppercase mt-1">{{ __('Day ID:') }} {{ $supply->work_day_id }}</div>
                    </td>
                    <td class="py-3 px-4">
                        <div class="font-medium text-slate-900 dark:text-white">{{ $supply->supplier->first_name }} {{ $supply->supplier->last_name }}</div>
                        <div class="text-xs text-slate-500 mt-1">{{ $supply->supplier->title }}</div>
                    </td>
                    <td class="py-3 px-4">
                        <span class="px-2 py-1 bg-[#38bdf8]/10 text-[#0ea5e9] dark:text-[#38bdf8] text-xs rounded-md border border-[#0ea5e9]/20 font-bold shadow-sm whitespace-nowrap">{{ __($supply->category->name) }}</span>
                    </td>
                    <td class="py-3 px-4">
                        <div class="text-sm font-mono text-slate-900 dark:text-white font-bold">{{ number_format($supply->quantity, 2) }} Units</div>
                        <div class="text-[11px] text-slate-500 mt-1">{{ __('At') }} {{ number_format($supply->unit_price, 2) }} {{ $supply->currency->symbol }} / {{ __('Unit') }}</div>
                    </td>
                    <td class="py-3 px-4 text-right">
                        <div class="text-sm font-bold text-slate-900 dark:text-white font-mono">
                            {{ number_format($supply->total_cost, 2) }} {{ $supply->currency->symbol }}
                        </div>
                        @if($supply->paid_amount < $supply->total_cost)
                            <div class="text-[10px] text-red-500 mt-1 font-bold tracking-wider">{{ __('Unpaid:') }} {{ number_format($supply->total_cost - $supply->paid_amount, 2) }}</div>
                        @else
                            <div class="text-[10px] text-emerald-500 mt-1 font-bold tracking-wider">{{ __('Fully Paid') }}</div>
                        @endif
                        @if($supply->unloading_fee > 0)
                            <div class="text-[10px] text-amber-600 dark:text-amber-500 mt-1">{{ __('+ Unloading:') }} {{ number_format($supply->unloading_fee, 2) }}</div>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="py-8 text-center text-slate-500 italic">{{ __('No raw materials recorded in the system yet.') }}</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
