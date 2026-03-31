@extends('layouts.Admin.App')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-black text-slate-900 dark:text-white flex items-center gap-3">
                <span class="w-10 h-10 rounded-2xl bg-emerald-500/10 flex items-center justify-center text-emerald-500">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                </span>
                {{ __('Settle Invoice') }}
            </h2>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-2">
                {{ __('Registering payment for') }} <span class="font-bold text-slate-700 dark:text-slate-200">#{{ $supply->id }}</span> - {{ $supply->category->name }}
            </p>
        </div>
        <a href="{{ route('admin.accounts.debts') }}" class="glass-btn px-4 py-2 rounded-xl text-sm font-medium flex items-center text-slate-600 dark:text-slate-400 hover:text-red-500 transition-colors">
            <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
            {{ __('Cancel & Back') }}
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Sidebar Info -->
        <div class="lg:col-span-1 space-y-6">
            <div class="glass-panel p-5 rounded-2xl border border-slate-200 dark:border-white/5 space-y-4">
                <div class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">{{ __('Invoice Details') }}</div>
                
                <div>
                    <div class="text-[10px] text-slate-500 uppercase">{{ __('Supplier') }}</div>
                    <div class="font-bold text-slate-900 dark:text-white">{{ $supply->supplier->first_name }} {{ $supply->supplier->last_name }}</div>
                </div>

                <div>
                    <div class="text-[10px] text-slate-500 uppercase">{{ __('Material') }}</div>
                    <div class="font-bold text-slate-900 dark:text-white">{{ $supply->category->name }}</div>
                </div>

                <div>
                    <div class="text-[10px] text-slate-500 uppercase">{{ __('Date registered') }}</div>
                    <div class="font-bold text-slate-900 dark:text-white">{{ $supply->created_at->format('Y/m/d') }}</div>
                </div>

                @if($supply->due_date)
                <div class="pt-4 border-t border-slate-200 dark:border-white/5">
                    <div class="text-[10px] text-red-500 uppercase font-bold">{{ __('Due Date') }}</div>
                    <div class="font-black text-rose-600 dark:text-rose-400">{{ $supply->due_date->format('Y/m/d') }}</div>
                </div>
                @endif
            </div>

            <div class="glass-panel p-5 rounded-2xl border border-red-500/20 bg-red-500/5 space-y-2">
                <div class="text-[10px] font-bold text-red-500 uppercase tracking-widest">{{ __('Outstanding Balance') }}</div>
                <div class="text-3xl font-black text-red-600 dark:text-red-400 font-mono">
                    {{ number_format($supply->unpaid_amount, 2) }}
                    <span class="text-xs uppercase">{{ $supply->currency->code }}</span>
                </div>
                @if($supply->currency->code !== $currencyCode)
                    <div class="text-[10px] text-red-500/70 font-bold border-t border-red-500/10 pt-2">
                        &approx; {{ number_format(\App\Models\Currency::convertAmount($supply->unpaid_amount, $supply->exchange_rate), 0) }} {{ $currencyCode }}
                    </div>
                @endif
            </div>
        </div>

        <!-- Main Form -->
        <div class="lg:col-span-2">
            <div class="glass-panel p-6 rounded-2xl border border-slate-200 dark:border-white/5" x-data="paymentForm()">
                <form action="{{ route('admin.supplies.pay.submit', $supply->id) }}" method="POST" class="space-y-6">
                    @csrf
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-3">{{ __('Amount to Pay') }}</label>
                            <div class="flex gap-3">
                                <div class="relative flex-1">
                                    <input type="number" step="0.01" name="payment_amount" x-model="paymentAmount" required 
                                        class="glass-input block w-full px-5 py-4 rounded-2xl text-2xl font-black text-slate-900 dark:text-emerald-400 border-2 border-slate-200 dark:border-white/10 bg-white dark:bg-black/40 focus:ring-emerald-500 focus:border-emerald-500"
                                        placeholder="0.00">
                                </div>
                                <div class="w-1/3">
                                    <select name="payment_currency_id" x-model="paymentCurrencyId" @change="updateExchangeRate()" 
                                        class="glass-input block w-full px-4 py-4 rounded-2xl text-lg font-bold border-2 border-slate-200 dark:border-white/10 bg-white dark:bg-black/40 focus:ring-emerald-500">
                                        @foreach($currencies as $currency)
                                            <option value="{{ $currency->id }}">{{ $currency->code }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="mt-3 flex items-center justify-between px-2">
                                <button type="button" @click="payFull()" class="text-xs font-bold text-emerald-500 hover:text-emerald-400 transition-colors underline decoration-dotted">
                                    {{ __('Pay full remaining debt') }}
                                </button>
                            </div>
                        </div>

                        <!-- Exchange Rate (Shown if not SYP or custom) -->
                        <div x-show="!isSYP()" x-transition class="p-4 rounded-xl bg-slate-50 dark:bg-white/5 border border-slate-200 dark:border-white/10">
                            <label class="block text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest mb-2">{{ __('Manual Exchange Rate') }}</label>
                            <div class="flex items-center gap-4">
                                <input type="number" step="0.01" name="payment_exchange_rate" x-model="paymentExchangeRate" 
                                    class="glass-input block w-1/2 px-4 py-2 rounded-lg text-sm bg-white dark:bg-black/20 border-slate-200 dark:border-white/10">
                                <div class="text-[10px] text-slate-400 italic">
                                    {{ __('Rate for 1 unit of chosen currency relative to system base.') }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="pt-6 border-t border-slate-200 dark:border-white/5">
                        <button type="submit" class="w-full bg-emerald-500 hover:bg-emerald-400 text-emerald-950 font-black py-4 px-6 rounded-2xl transition-all shadow-lg shadow-emerald-500/20 flex items-center justify-center gap-3 text-lg">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            {{ __('Complete Payment') }}
                        </button>
                        <p class="text-center text-[10px] text-slate-500 mt-4 uppercase tracking-widest">
                            {{ __('This action will update the supplier balance and the daily ledger.') }}
                        </p>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    function paymentForm() {
        return {
            paymentAmount: '',
            paymentCurrencyId: '{{ $supply->currency_id }}',
            paymentExchangeRate: 1,
            currencies: @json($currencies),
            sypIds: [],

            init() {
                this.sypIds = this.currencies.filter(c => c.code.includes('SYP')).map(c => c.id);
                this.updateExchangeRate();
            },

            updateExchangeRate() {
                let cur = this.currencies.find(c => c.id == this.paymentCurrencyId);
                if (cur) {
                    this.paymentExchangeRate = cur.exchange_rate;
                }
            },

            isSYP() {
                return this.sypIds.includes(parseInt(this.paymentCurrencyId));
            },

            payFull() {
                this.paymentCurrencyId = '{{ $supply->currency_id }}';
                this.updateExchangeRate();
                this.paymentAmount = '{{ $supply->unpaid_amount }}';
            }
        }
    }
</script>
@endsection
