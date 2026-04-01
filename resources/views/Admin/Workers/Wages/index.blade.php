@extends('layouts.Admin.App')

@section('content')
<div class="mb-8">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-end w-full gap-4">
        <div>
            <h1 class="text-2xl lg:text-3xl font-bold text-slate-900 dark:text-white mb-1 tracking-tight">{{ __('Employee') }} <span class="text-transparent bg-clip-text bg-gradient-to-r from-emerald-400 to-teal-500">{{ __('Wages & Payments') }}</span></h1>
            <p class="text-sm text-slate-400">
                {{ __('Recording actual cash outflows for staff during Work Day:') }} 
                <span class="font-bold text-emerald-500">{{ $activeWorkDay->start_time->translatedFormat('Y-m-d h:i A') }}</span>
            </p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.attendance.index') }}" class="bg-white/5 hover:bg-white/10 text-white border border-white/10 px-4 py-2 rounded-xl text-sm font-bold transition-all flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                {{ __('Back to Attendance') }}
            </a>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <!-- Log Payment Form -->
    <div class="lg:col-span-1">
        @can('create wages')
        <div class="glass-panel p-6 rounded-3xl border border-white/5 sticky top-8"
             x-data="{ 
                selectedWorkerId: '',
                selectedCurrency: '',
                usdId: '{{ $currencies->where('code', 'USD')->first()->id ?? '' }}',
                usdRate: '{{ $currencies->where('code', 'USD')->first()->exchange_rate ?? '0' }}',
                workers: @js($workers)
             }">
            <h2 class="text-lg font-bold text-white mb-6 flex items-center gap-2">
                <svg class="w-5 h-5 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                {{ __('Record Payment') }}
            </h2>

            <form action="{{ route('admin.wages.store') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">{{ __('Select Employee') }}</label>
                    <select name="worker_id" x-model="selectedWorkerId" required class="block w-full px-4 py-3 bg-[#0f1115] border border-white/5 rounded-xl text-sm text-white focus:border-emerald-500/50 focus:ring-1 focus:ring-emerald-500/50 transition-all font-medium appearance-none">
                        <option value="">{{ __('Select Worker...') }}</option>
                        @foreach($workers as $worker)
                            <option value="{{ $worker->id }}">{{ $worker->first_name }} {{ $worker->last_name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">{{ __('Payment Type') }}</label>
                        <select name="type" required class="block w-full px-4 py-3 bg-[#0f1115] border border-white/5 rounded-xl text-sm text-white focus:border-emerald-500/50 focus:ring-1 focus:ring-emerald-500/50 transition-all font-medium appearance-none">
                            <option value="wage">{{ __('Wage') }}</option>
                            <option value="bonus">{{ __('Bonus') }}</option>
                            <option value="advance">{{ __('Advance') }}</option>
                            <option value="allowance">{{ __('Allowance') }}</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">{{ __('Currency') }}</label>
                        <select name="currency_id" x-model="selectedCurrency" required class="block w-full px-4 py-3 bg-[#0f1115] border border-white/5 rounded-xl text-sm text-white focus:border-emerald-500/50 focus:ring-1 focus:ring-emerald-500/50 transition-all font-medium appearance-none">
                            <option value="">{{ __('Select Currency...') }}</option>
                            @foreach($currencies as $curr)
                                <option value="{{ $curr->id }}">{{ $curr->code }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div x-show="selectedCurrency == usdId" x-cloak class="p-4 bg-emerald-500/5 border border-emerald-500/10 rounded-xl space-y-3">
                    <label class="block text-xs font-bold text-emerald-500 uppercase tracking-widest">{{ __('USD exchange rate') }}</label>
                    <div class="relative">
                        <input type="number" step="0.01" name="exchange_rate" :value="usdRate"
                            class="block w-full px-4 py-3 bg-[#0f1115] border border-emerald-500/20 rounded-xl text-sm text-white focus:outline-none focus:border-emerald-500/50 transition-all font-medium">
                        <div class="absolute inset-y-0 {{ app()->getLocale() == 'ar' ? 'left-0 pl-4' : 'right-0 pr-4' }} flex items-center pointer-events-none">
                            <span class="text-slate-500 text-[10px] font-bold uppercase">{{ __('SYP / 1 USD') }}</span>
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">{{ __('Amount') }}</label>
                    <div class="relative">
                        <input type="number" step="0.01" name="amount" required 
                            class="block w-full px-4 py-3 bg-[#0f1115] border border-white/5 rounded-xl text-sm text-white focus:outline-none focus:border-emerald-500/50 focus:ring-1 focus:ring-emerald-500/50 transition-all font-medium" 
                            placeholder="0.00">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">{{ __('Notes (Optional)') }}</label>
                    <textarea name="notes" rows="2" class="block w-full px-4 py-3 bg-[#0f1115] border border-white/5 rounded-xl text-sm text-white focus:border-emerald-500/50 focus:ring-1 focus:ring-emerald-500/50 transition-all font-medium"></textarea>
                </div>

                <button type="submit" class="w-full bg-emerald-500 hover:bg-emerald-400 text-black py-4 rounded-xl font-bold transition-all shadow-lg shadow-emerald-500/20 flex items-center justify-center gap-2">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                    {{ __('Record Payment') }}
                </button>
            </form>
        </div>
        @else
        <div class="p-6 bg-amber-500/10 border border-amber-500/20 rounded-3xl text-center">
            <svg class="w-12 h-12 mx-auto text-amber-500 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" /></svg>
            <p class="text-sm font-bold text-amber-500 uppercase tracking-widest">{{ __('Restricted Access') }}</p>
            <p class="text-[11px] text-slate-400 mt-2">{{ __('You do not have permission to record payments.') }}</p>
        </div>
        @endcan
    </div>

    <!-- Payment History Table -->
    <div class="lg:col-span-2">
        <div class="glass-panel overflow-hidden rounded-3xl border border-white/5 shadow-xl">
            @can('filter wages')
            <div class="p-6 bg-white/5 border-b border-white/5 space-y-4">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
                    <h3 class="text-sm font-bold text-white flex items-center gap-2">
                        <svg class="w-4 h-4 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        {{ $listTitle }}
                    </h3>
                    <a href="{{ route('admin.wages.index') }}" class="text-[10px] uppercase font-bold text-slate-500 hover:text-emerald-500 transition-all">{{ __('Show Today Only') }}</a>
                </div>

                <!-- Date Filter -->
                <form action="{{ route('admin.wages.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-2">
                    <div class="relative group">
                        <label class="block text-[10px] text-slate-500 uppercase font-bold mb-1 ml-1 tracking-widest">{{ __('From Date') }}</label>
                        <input type="date" name="date_from" value="{{ request('date_from') }}" 
                            class="w-full px-3 py-2 bg-[#0f1115] border border-white/10 rounded-xl text-xs text-slate-300 focus:outline-none focus:border-emerald-500/50 transition-all">
                    </div>
                    <div class="relative group">
                        <label class="block text-[10px] text-slate-500 uppercase font-bold mb-1 ml-1 tracking-widest">{{ __('To Date') }}</label>
                        <div class="flex gap-2">
                            <input type="date" name="date_to" value="{{ request('date_to') }}" 
                                class="flex-1 w-full px-3 py-2 bg-[#0f1115] border border-white/10 rounded-xl text-xs text-slate-300 focus:outline-none focus:border-emerald-500/50 transition-all">
                            <button type="submit" class="p-2 bg-emerald-500 hover:bg-emerald-400 text-black rounded-xl transition-all shadow-lg shadow-emerald-500/20">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
            @endcan
            
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-{{ app()->getLocale() == 'ar' ? 'right' : 'left' }} text-slate-300">
                    <thead class="text-xs text-slate-500 uppercase bg-white/5">
                        <tr>
                            <th class="px-6 py-4">{{ __('Employee') }}</th>
                            <th class="px-6 py-4">{{ __('Type') }}</th>
                            <th class="px-6 py-4">{{ __('Amount') }}</th>
                            <th class="px-6 py-4">{{ __('Local Value') }}</th>
                            <th class="px-6 py-4 text-center">{{ __('Note') }}</th>
                            <th class="px-6 py-4">{{ __('Time') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        @forelse($transactions as $t)
                        <tr class="hover:bg-white/5 transition-colors group">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-lg bg-emerald-500/10 text-emerald-500 flex items-center justify-center font-bold text-xs uppercase">
                                        {{ mb_substr($t->worker->first_name, 0, 1) }}
                                    </div>
                                    <span class="font-bold text-white">{{ $t->worker->first_name }} {{ $t->worker->last_name }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-0.5 rounded-md text-[10px] font-bold uppercase tracking-wider bg-emerald-500/10 text-emerald-500">
                                    {{ __(ucfirst($t->type)) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 font-black text-white">
                                {{ number_format($t->amount, 2) }} <span class="text-[10px] text-slate-500 font-medium ml-1">{{ $t->currency->code }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-emerald-400 font-bold">
                                    {{ number_format($t->amount * $t->exchange_rate, 0) }} <span class="text-[9px] uppercase">{{ __('SYP') }}</span>
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($t->notes)
                                    <span class="text-xs text-slate-400 font-medium italic truncate max-w-[150px] inline-block" title="{{ $t->notes }}">
                                        {{ $t->notes }}
                                    </span>
                                @else
                                    <span class="text-[10px] text-slate-600">—</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-slate-500 text-xs">
                                {{ $t->created_at->translatedFormat('h:i A') }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-slate-500 italic">
                                {{ __('No payments recorded for today.') }}
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if($transactions->hasPages())
                <div class="p-4 border-t border-white/5">
                    {{ $transactions->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
