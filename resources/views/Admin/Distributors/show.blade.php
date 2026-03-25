@extends('layouts.Admin.App')

@section('content')
<div class="mb-8 flex flex-col sm:flex-row justify-between items-start sm:items-end gap-4">
    <div>
        <div class="flex items-center gap-3 mb-1">
            <a href="{{ route('admin.distributors.index') }}" class="p-2 bg-white dark:bg-white/5 border border-slate-200 dark:border-white/10 rounded-xl text-slate-500 hover:text-amber-500 transition-colors">
                <svg class="w-5 h-5 {{ app()->getLocale() == 'ar' ? 'rotate-180' : '' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
            </a>
            <h1 class="text-2xl lg:text-3xl font-bold text-slate-900 dark:text-white tracking-tight">{{ __('Distributor Details') }}</h1>
        </div>
        <p class="text-sm text-slate-500 dark:text-slate-400">
            {{ __('In-depth profile and transaction history for:') }} 
            <span class="font-bold text-amber-600 dark:text-amber-500">{{ $distributor->first_name }} {{ $distributor->last_name }}</span>
        </p>
    </div>
    <div class="flex gap-3">
        <a href="{{ route('admin.distributors.edit', $distributor) }}" class="bg-white dark:bg-white/5 border border-slate-200 dark:border-white/10 text-slate-700 dark:text-white px-4 py-2 rounded-xl text-sm font-bold flex items-center transition-all hover:border-amber-500/50">
            <svg class="w-4 h-4 {{ app()->getLocale() == 'ar' ? 'ml-2' : 'mr-2' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
            </svg>
            {{ __('Edit Profile') }}
        </a>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <!-- Left Column: Primary Info -->
    <div class="lg:col-span-1 space-y-6">
        <div class="glass-panel p-6 rounded-2xl border border-slate-200 dark:border-white/5 relative overflow-hidden">
            <div class="absolute top-0 {{ app()->getLocale() == 'ar' ? 'left-0' : 'right-0' }} p-4 opacity-10">
                <svg class="w-20 h-20 text-amber-500" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                </svg>
            </div>
            
            <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-6 flex items-center gap-2">
                <span class="w-1.5 h-5 bg-amber-500 rounded-full"></span>
                {{ __('Profile Information') }}
            </h3>

            <div class="space-y-6">
                <div>
                    <label class="block text-[10px] uppercase tracking-widest text-slate-500 font-bold mb-1">{{ __('Full Name') }}</label>
                    <p class="text-slate-900 dark:text-white font-medium">{{ $distributor->first_name }} {{ $distributor->last_name }}</p>
                </div>

                <div>
                    <label class="block text-[10px] uppercase tracking-widest text-slate-500 font-bold mb-1">{{ __('Title / Role') }}</label>
                    <p class="text-slate-900 dark:text-white font-medium">{{ $distributor->title ?? __('No Title') }}</p>
                </div>

                <div>
                    <label class="block text-[10px] uppercase tracking-widest text-slate-500 font-bold mb-1">{{ __('Preferred Currency') }}</label>
                    <span class="px-2.5 py-1 bg-amber-500/10 text-amber-500 border border-amber-500/20 rounded-lg text-xs font-bold uppercase">
                        {{ $distributor->currency->code }} - {{ $distributor->currency->name }}
                    </span>
                </div>

                <div>
                    <label class="block text-[10px] uppercase tracking-widest text-slate-500 font-bold mb-1">{{ __('Contact Numbers') }}</label>
                    <div class="flex flex-wrap gap-2 mt-2">
                        @forelse($distributor->mobiles as $mobile)
                            <div class="flex items-center gap-2 px-3 py-1.5 bg-slate-100 dark:bg-white/5 border border-slate-200 dark:border-white/10 rounded-xl text-sm text-slate-700 dark:text-slate-300">
                                <svg class="w-3.5 h-3.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                </svg>
                                {{ $mobile->number }}
                            </div>
                        @empty
                            <p class="text-sm text-slate-500 italic">{{ __('No contact numbers registered.') }}</p>
                        @endforelse
                    </div>
                </div>

                @if($distributor->notes)
                <div>
                    <label class="block text-[10px] uppercase tracking-widest text-slate-500 font-bold mb-1">{{ __('Internal Notes') }}</label>
                    <div class="p-3 bg-slate-50 dark:bg-black/20 rounded-xl border border-slate-200 dark:border-white/5 text-sm text-slate-600 dark:text-slate-400 italic">
                        "{{ $distributor->notes }}"
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Right Column: Financial Summary & Activity -->
    <div class="lg:col-span-2 space-y-6">
        <!-- Stats Widgets -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="glass-panel p-5 rounded-2xl border border-blue-500/20 bg-blue-500/5">
                <p class="text-[10px] uppercase tracking-widest text-blue-600 dark:text-blue-400 font-bold mb-1">{{ __('Total Sales') }}</p>
                <h4 class="text-2xl font-black text-slate-900 dark:text-white">{{ number_format($distributor->distributions->sum('total_price'), 2) }} <span class="text-xs">{{ $distributor->currency->code }}</span></h4>
            </div>
            <div class="glass-panel p-5 rounded-2xl border border-emerald-500/20 bg-emerald-500/5">
                <p class="text-[10px] uppercase tracking-widest text-emerald-600 dark:text-emerald-400 font-bold mb-1">{{ __('Total Paid') }}</p>
                <h4 class="text-2xl font-black text-slate-900 dark:text-white">{{ number_format($distributor->transactions->where('type', 'payment')->sum('amount'), 2) }} <span class="text-xs">{{ $distributor->currency->code }}</span></h4>
            </div>
            <div class="glass-panel p-5 rounded-2xl border border-red-500/20 bg-red-500/5">
                <p class="text-[10px] uppercase tracking-widest text-red-600 dark:text-red-400 font-bold mb-1">{{ __('Outstanding Balance') }}</p>
                @php
                    $balance = $distributor->distributions->sum('total_price') 
                             - $distributor->returns->sum('total_refund')
                             - $distributor->transactions->where('type', 'payment')->sum('amount')
                             - $distributor->transactions->where('type', 'discount')->sum('amount');
                @endphp
                <h4 class="text-2xl font-black {{ $balance > 0 ? 'text-red-500' : 'text-slate-900 dark:text-white' }}">{{ number_format($balance, 2) }} <span class="text-xs">{{ $distributor->currency->code }}</span></h4>
            </div>
        </div>

        <!-- Recent Activity Table -->
        <div class="glass-panel rounded-2xl border border-slate-200 dark:border-white/5 overflow-hidden">
            <div class="p-6 border-b border-slate-200 dark:border-white/5 flex justify-between items-center bg-slate-50 dark:bg-black/20">
                <h3 class="text-lg font-bold text-slate-900 dark:text-white flex items-center gap-2">
                    <span class="w-1.5 h-5 bg-amber-500 rounded-full"></span>
                    {{ __('Recent Ledger Activity') }}
                </h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50/50 dark:bg-black/10 border-b border-slate-200 dark:border-white/5 text-[10px] uppercase tracking-wider text-slate-500 dark:text-slate-400 font-bold">
                            <th class="p-4">{{ __('Date') }}</th>
                            <th class="p-4">{{ __('Type') }}</th>
                            <th class="p-4">{{ __('Reference') }}</th>
                            <th class="p-4 text-right">{{ __('Amount') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 dark:divide-white/5">
                        @php
                            $activities = collect();
                            foreach($distributor->distributions as $d) {
                                $activities->push([
                                    'date' => $d->created_at,
                                    'type' => 'Sale',
                                    'ref' => '#'.$d->id . ' - ' . $d->bundle_count . ' ' . __('Bundles'),
                                    'amount' => $d->total_price,
                                    'color' => 'blue'
                                ]);
                            }
                            foreach($distributor->returns as $r) {
                                $activities->push([
                                    'date' => $r->created_at,
                                    'type' => 'Return',
                                    'ref' => '#'.$r->id . ' - ' . $r->bundle_count . ' ' . __('Bundles'),
                                    'amount' => -$r->total_refund,
                                    'color' => 'amber'
                                ]);
                            }
                            foreach($distributor->transactions as $t) {
                                $activities->push([
                                    'date' => $t->created_at,
                                    'type' => ucfirst($t->type),
                                    'ref' => $t->notes ?? __('Direct Transaction'),
                                    'amount' => -$t->amount,
                                    'color' => $t->type == 'payment' ? 'emerald' : 'rose'
                                ]);
                            }
                            $activities = $activities->sortByDesc('date')->take(10);
                        @endphp

                        @forelse($activities as $activity)
                        <tr class="hover:bg-slate-50 dark:hover:bg-white/5 transition-colors">
                            <td class="p-4 text-sm text-slate-500">{{ $activity['date']->format('Y-m-d H:i') }}</td>
                            <td class="p-4">
                                <span class="px-2 py-0.5 bg-{{ $activity['color'] }}-500/10 text-{{ $activity['color'] }}-500 border border-{{ $activity['color'] }}-500/20 rounded-md text-[10px] font-bold uppercase">
                                    {{ __($activity['type']) }}
                                </span>
                            </td>
                            <td class="p-4 text-sm text-slate-700 dark:text-slate-300">{{ $activity['ref'] }}</td>
                            <td class="p-4 text-right font-bold {{ $activity['amount'] > 0 ? 'text-slate-900 dark:text-white' : 'text-emerald-500' }}">
                                {{ number_format(abs($activity['amount']), 2) }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="p-8 text-center text-slate-500 italic text-sm">
                                {{ __('No transactions found for this distributor.') }}
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($activities->count() >= 10)
            <div class="p-4 bg-slate-50/50 dark:bg-black/10 text-center border-t border-slate-200 dark:border-white/5">
                <p class="text-[10px] text-slate-500 uppercase tracking-widest font-bold">{{ __('Showing last 10 activities') }}</p>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
