@extends('layouts.Admin.App')

@section('content')
<div class="mb-8 flex flex-col sm:flex-row justify-between items-start sm:items-end gap-4">
    <div>
        <div class="flex items-center gap-3 mb-1">
            <a href="{{ route('admin.distributors.show', $distributor) }}" class="p-2 bg-white dark:bg-white/5 border border-slate-200 dark:border-white/10 rounded-xl text-slate-500 hover:text-amber-500 transition-colors">
                <svg class="w-5 h-5 {{ app()->getLocale() == 'ar' ? 'rotate-180' : '' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
            </a>
            <h1 class="text-2xl lg:text-3xl font-bold text-slate-900 dark:text-white tracking-tight">{{ __('Record Transaction') }}</h1>
        </div>
        <p class="text-sm text-slate-500 dark:text-slate-400">
            {{ __('Add a direct payment or discount for:') }} 
            <span class="font-bold text-amber-600 dark:text-amber-500">{{ $distributor->first_name }} {{ $distributor->last_name }}</span>
        </p>
    </div>
</div>

<div class="max-w-3xl mx-auto">
    <div class="glass-panel p-8 rounded-3xl border border-slate-200 dark:border-white/5 shadow-2xl relative overflow-hidden bg-white dark:bg-slate-900">
        <!-- Decoration -->
        <div class="absolute top-0 right-0 p-6 opacity-5 pointer-events-none">
            <svg class="w-32 h-32 text-emerald-500" fill="currentColor" viewBox="0 0 24 24">
                <path d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
        </div>

        <!-- Balance Insight -->
        <div class="mb-8 p-6 rounded-2xl bg-slate-50 dark:bg-white/5 border border-slate-200 dark:border-white/10 flex flex-col sm:flex-row justify-between items-center gap-4">
            <div>
                <p class="text-[10px] uppercase tracking-widest text-slate-500 font-bold mb-1">{{ __('Current Outstanding Balance') }}</p>
                <h4 class="text-3xl font-black {{ $balance > 0 ? 'text-red-500' : 'text-slate-900 dark:text-white' }}">
                    {{ number_format($balance, 2) }} 
                    <span class="text-sm font-bold text-slate-400">{{ $distributor->currency->code }}</span>
                </h4>
            </div>
            <div class="px-4 py-2 bg-amber-500/10 text-amber-600 dark:text-amber-500 border border-amber-500/20 rounded-xl text-xs font-bold uppercase tracking-wider">
                {{ $distributor->currency->name }}
            </div>
        </div>

        <form action="{{ route('admin.distributors.transaction.store', $distributor) }}" method="POST" class="space-y-6">
            @csrf
            
            <div x-data="{ type: 'payment' }">
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-3">{{ __('Transaction Type') }}</label>
                <div class="grid grid-cols-2 gap-3 p-1.5 bg-slate-200/50 dark:bg-slate-800/50 rounded-2xl border border-slate-300 dark:border-white/10">
                    <button type="button" @click="type = 'payment'" 
                            :class="type === 'payment' ? 'bg-emerald-500 text-white shadow-lg' : 'text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200'"
                            class="py-3 px-4 rounded-xl text-sm font-bold transition-all flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        {{ __('Payment') }}
                    </button>
                    <button type="button" @click="type = 'discount'"
                            :class="type === 'discount' ? 'bg-emerald-500 text-white shadow-lg' : 'text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200'"
                            class="py-3 px-4 rounded-xl text-sm font-bold transition-all flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" /></svg>
                        {{ __('Discount') }}
                    </button>
                </div>
                <input type="hidden" name="type" :value="type">
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-3">{{ __('Amount') }}</label>
                    <div class="relative group">
                        <input type="number" name="amount" step="0.01" required
                               class="block w-full px-5 py-4 bg-slate-50 dark:bg-white/5 border border-slate-200 dark:border-white/10 rounded-2xl text-slate-900 dark:text-white font-medium focus:ring-4 focus:ring-amber-500/20 focus:border-amber-500 transition-all outline-none" 
                               placeholder="0.00">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-3">{{ __('Payment Currency') }}</label>
                    <div class="relative group">
                        <select name="currency_id" required
                                class="block w-full px-5 py-4 bg-slate-50 dark:bg-white/5 border border-slate-200 dark:border-white/10 rounded-2xl text-slate-900 dark:text-white font-medium focus:ring-4 focus:ring-amber-500/20 focus:border-amber-500 transition-all outline-none appearance-none">
                            @foreach($currencies as $currency)
                                <option value="{{ $currency->id }}" {{ $currency->id == $distributor->preferred_currency_id ? 'selected' : '' }}>
                                    {{ $currency->code }} - {{ $currency->name }}
                                </option>
                            @endforeach
                        </select>
                        <div class="absolute inset-y-0 {{ app()->getLocale() == 'ar' ? 'left-4' : 'right-4' }} flex items-center pointer-events-none">
                            <svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                        </div>
                    </div>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-3">{{ __('Internal Notes') }}</label>
                <textarea name="notes" rows="3"
                          class="block w-full px-5 py-4 bg-slate-50 dark:bg-white/5 border border-slate-200 dark:border-white/10 rounded-2xl text-slate-900 dark:text-white font-medium focus:ring-4 focus:ring-amber-500/20 focus:border-amber-500 transition-all outline-none resize-none"
                          placeholder="{{ __('e.g. Debt settlement for March supplies...') }}"></textarea>
            </div>

            <div class="pt-4 flex flex-col sm:flex-row gap-4">
                <button type="submit" 
                        class="flex-1 py-4 bg-gradient-to-r from-amber-500 to-orange-500 hover:from-amber-400 hover:to-orange-400 text-black rounded-2xl font-bold shadow-lg shadow-amber-500/20 transition-all flex items-center justify-center gap-2">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                    {{ __('Save Transaction') }}
                </button>
                <a href="{{ route('admin.distributors.show', $distributor) }}" 
                   class="py-4 px-8 bg-slate-100 dark:bg-white/5 border border-slate-200 dark:border-white/10 text-slate-600 dark:text-slate-400 rounded-2xl font-bold transition-all hover:bg-slate-200 dark:hover:bg-white/10 text-center">
                    {{ __('Cancel') }}
                </a>
            </div>
        </form>
    </div>

    <!-- Security Information -->
    <div class="mt-8 flex items-center justify-center gap-2 text-slate-400 dark:text-slate-500">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04M12 21.472a11.953 11.953 0 008.618-3.04A12.02 12.02 0 0012 2.944a12.02 12.02 0 00-8.618 3.04A11.952 11.952 0 003.382 18.432a11.954 11.954 0 008.618 3.04z" /></svg>
        <span class="text-xs font-medium tracking-wide uppercase">{{ __('Transaction will be linked to current active work day') }}</span>
    </div>
</div>
@endsection
