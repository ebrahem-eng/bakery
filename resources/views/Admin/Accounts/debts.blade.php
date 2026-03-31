<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="text-2xl font-black bg-gradient-to-r from-red-600 to-red-400 bg-clip-text text-transparent flex items-center gap-3">
                    <svg class="w-8 h-8 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    {{ __('Outstanding Debts (Payables)') }}
                </h2>
                <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">{{ __('Monitor & manage unpaid supplier balances') }}</p>
            </div>
            
            <div>
                <a href="{{ route('admin.accounts.index') }}" class="glass-btn px-4 py-2 rounded-xl text-sm font-medium flex items-center text-slate-700 dark:text-slate-300 hover:text-red-500 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                    {{ __('Back to Accounts Dashboard') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="space-y-6" x-data="debtManager()">
        {{-- Summaries --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="glass-panel rounded-2xl border border-red-500/30 p-5 relative overflow-hidden flex flex-col justify-center">
                <div class="absolute top-0 right-0 w-32 h-32 bg-red-500/10 rounded-full blur-3xl -translate-y-1/2"></div>
                <div class="relative z-10">
                    <div class="text-[10px] text-red-500 dark:text-red-400 font-bold uppercase tracking-wider mb-1">{{ __('Total Outstanding') }}</div>
                    <div class="text-3xl font-black text-red-600 dark:text-red-400">{{ number_format($totalOutstanding, 0) }} <span class="text-sm">{{ $currencyCode }}</span></div>
                </div>
            </div>
            
            <div class="glass-panel rounded-2xl border border-rose-600/30 p-5 relative overflow-hidden flex flex-col justify-center bg-rose-500/5">
                <div class="absolute top-0 right-0 w-32 h-32 bg-rose-600/10 rounded-full blur-3xl -translate-y-1/2"></div>
                <div class="relative z-10">
                    <div class="text-[10px] text-rose-600 dark:text-rose-400 font-bold uppercase tracking-wider mb-1 flex items-center gap-1">
                        <svg class="w-3 h-3 animate-pulse" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                        {{ __('Overdue Debts') }}
                    </div>
                    <div class="text-2xl font-black text-rose-700 dark:text-rose-500">{{ number_format($overdueAmount, 0) }} <span class="text-sm text-rose-400">{{ $currencyCode }}</span></div>
                </div>
            </div>

            <div class="glass-panel rounded-2xl border border-amber-500/30 p-5 relative overflow-hidden flex flex-col justify-center">
                <div class="absolute top-0 right-0 w-32 h-32 bg-amber-500/10 rounded-full blur-3xl -translate-y-1/2"></div>
                <div class="relative z-10">
                    <div class="text-[10px] text-amber-600 dark:text-amber-500 font-bold uppercase tracking-wider mb-1">{{ __('Due within 7 Days') }}</div>
                    <div class="text-2xl font-black text-amber-600 dark:text-amber-500">{{ number_format($dueSoonAmount, 0) }} <span class="text-sm">{{ $currencyCode }}</span></div>
                </div>
            </div>
        </div>

        {{-- Table --}}
        <div class="glass-panel rounded-2xl border border-slate-200 dark:border-white/5 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left whitespace-nowrap">
                    <thead>
                        <tr class="bg-slate-50 dark:bg-black/40 border-b border-slate-200 dark:border-white/5">
                            <th class="py-3 px-4 text-[10px] font-bold text-slate-600 dark:text-slate-400 uppercase tracking-wider">{{ __('Supplier') }}</th>
                            <th class="py-3 px-4 text-[10px] font-bold text-slate-600 dark:text-slate-400 uppercase tracking-wider">{{ __('Material / Invoice') }}</th>
                            <th class="py-3 px-4 text-[10px] font-bold text-slate-600 dark:text-slate-400 uppercase tracking-wider text-right">{{ __('Invoice Total') }}</th>
                            <th class="py-3 px-4 text-[10px] font-bold text-slate-600 dark:text-slate-400 uppercase tracking-wider text-right">{{ __('Unpaid Balance') }}</th>
                            <th class="py-3 px-4 text-[10px] font-bold text-slate-600 dark:text-slate-400 uppercase tracking-wider text-center">{{ __('Due Date') }}</th>
                            <th class="py-3 px-4 text-[10px] font-bold text-slate-600 dark:text-slate-400 uppercase tracking-wider text-center">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-white/5">
                        @forelse($supplies as $supply)
                            @php
                                $isOverdue = $supply->due_date && $supply->due_date->startOfDay()->isPast();
                                $isDueSoon = $supply->due_date && !$isOverdue && $supply->due_date->startOfDay()->diffInDays(now()->startOfDay()) <= 7;
                            @endphp
                            <tr class="hover:bg-slate-50/50 dark:hover:bg-white/5 transition-colors">
                                <td class="py-3 px-4">
                                    <div class="font-bold text-slate-900 dark:text-white">{{ $supply->supplier->first_name }} {{ $supply->supplier->last_name }}</div>
                                    <div class="text-[10px] text-slate-500">{{ $supply->created_at->format('Y/m/d H:i') }}</div>
                                </td>
                                <td class="py-3 px-4">
                                    <div class="text-sm font-semibold text-slate-700 dark:text-slate-300">
                                        {{ $supply->category->name }}
                                        @if($supply->material_type_name)
                                            <span class="text-xs text-slate-500">({{ $supply->material_type_name }})</span>
                                        @endif
                                    </div>
                                    <div class="text-[10px] text-slate-400 font-mono">Invoice #{{ $supply->id }}</div>
                                </td>
                                <td class="py-3 px-4 text-right">
                                    <div class="font-bold text-slate-900 dark:text-white font-mono">{{ number_format($supply->total_cost, 2) }} <span class="text-xs">{{ $supply->currency->code }}</span></div>
                                    @if($supply->paid_amount > 0)
                                        <div class="text-[10px] text-emerald-500 tracking-wider">{{ __('Paid:') }} {{ number_format($supply->paid_amount, 2) }} {{ $supply->paidCurrency->code ?? '' }}</div>
                                    @endif
                                </td>
                                <td class="py-3 px-4 text-right">
                                    <div class="font-black text-red-600 dark:text-red-400 font-mono text-base">
                                        {{ number_format($supply->unpaid_amount, 2) }} <span class="text-xs">{{ $supply->currency->code }}</span>
                                    </div>
                                    @if($supply->currency->code !== $currencyCode)
                                        <div class="text-[9px] text-red-500/70">(&approx; {{ number_format(\App\Models\Currency::convertAmount($supply->unpaid_amount, $supply->exchange_rate), 0) }} {{ $currencyCode }})</div>
                                    @endif
                                </td>
                                <td class="py-3 px-4 text-center">
                                    @if($supply->due_date)
                                        <div class="inline-flex items-center px-2 py-1 rounded text-xs font-bold {{ $isOverdue ? 'bg-red-500/10 text-red-600 border border-red-500/20' : ($isDueSoon ? 'bg-amber-500/10 text-amber-600 border border-amber-500/20' : 'bg-slate-100 dark:bg-white/5 text-slate-600 dark:text-slate-400') }}">
                                            @if($isOverdue)
                                                <svg class="w-3 h-3 mr-1 animate-pulse" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                            @endif
                                            {{ $supply->due_date->format('Y/m/d') }}
                                        </div>
                                    @else
                                        <span class="text-xs text-slate-400 italic">{{ __('None') }}</span>
                                    @endif
                                </td>
                                <td class="py-3 px-4 text-center">
                                    <button @click="openPaymentModal({{ $supply->id }}, '{{ number_format($supply->unpaid_amount, 2, '.', '') }}', {{ $supply->currency_id }})" class="inline-flex items-center justify-center p-2 bg-emerald-500/10 hover:bg-emerald-500/20 text-emerald-600 dark:text-emerald-400 rounded-lg transition-colors border border-emerald-500/20" title="{{ __('Make Payment') }}">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-12 text-center text-slate-500 text-sm">
                                    <div class="flex flex-col items-center justify-center opacity-50">
                                        <svg class="w-12 h-12 mb-3 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                        {{ __('No outstanding debts found. Everything is fully paid!') }}
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Payment Modal --}}
        <div x-show="isPaymentModalOpen" 
            class="fixed inset-0 z-50 flex items-center justify-center overflow-y-auto overflow-x-hidden bg-black/50 backdrop-blur-sm"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            x-cloak>
            
            <div class="relative w-full max-w-md p-4" @click.away="closePaymentModal()">
                <div class="relative bg-white dark:bg-[#0f172a] rounded-2xl shadow-xl border border-slate-200 dark:border-white/10" 
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave="transition ease-in duration-200"
                    x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
                    
                    <div class="flex items-center justify-between p-5 border-b border-slate-200 dark:border-white/5">
                        <h3 class="text-lg font-bold text-slate-900 dark:text-white flex items-center gap-2">
                            <span class="w-8 h-8 rounded-full bg-emerald-500/10 flex items-center justify-center text-emerald-500">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            </span>
                            {{ __('Register Debt Payment') }}
                        </h3>
                        <button type="button" @click="closePaymentModal()" class="text-slate-400 hover:text-slate-500 dark:hover:text-white transition-colors">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                        </button>
                    </div>

                    <form :action="paymentUrl" method="POST" class="p-5">
                        @csrf
                        <div class="space-y-4">
                            <div>
                                <label class="block text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">{{ __('Payment Amount') }}</label>
                                <div class="flex gap-2">
                                    <input type="number" step="0.01" name="payment_amount" x-model="paymentAmount" required class="glass-input block w-full px-4 py-2.5 rounded-xl text-lg font-black text-slate-900 dark:text-emerald-400 border border-slate-200 dark:border-white/10 bg-white/50 dark:bg-black/40 focus:ring-emerald-500">
                                    <select name="payment_currency_id" x-model="paymentCurrencyId" @change="updateExchangeRate()" class="glass-input w-1/3 px-3 py-2.5 rounded-xl text-sm font-bold border border-slate-200 dark:border-white/10 bg-white/50 dark:bg-black/40">
                                        <template x-for="c in currencies" :key="c.id">
                                            <option :value="c.id" x-text="c.code"></option>
                                        </template>
                                    </select>
                                </div>
                                <div class="text-[10px] text-slate-400 mt-1 flex justify-between">
                                    <span>{{ __('Remaining Debt:') }} <span class="font-bold text-slate-500 dark:text-slate-300" x-text="targetDebtAmount"></span></span>
                                    <button type="button" @click="payFullAmount" class="font-bold text-emerald-500 hover:underline">{{ __('Pay Full') }}</button>
                                </div>
                            </div>

                            <div x-show="!sypIds.includes(parseInt(paymentCurrencyId))" x-transition>
                                <label class="block text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">{{ __('Exchange Rate') }}</label>
                                <input type="number" step="0.01" name="payment_exchange_rate" x-model="paymentExchangeRate" class="glass-input block w-full px-4 py-2.5 rounded-xl text-sm border border-slate-200 dark:border-white/10 bg-white/50 dark:bg-black/40">
                            </div>
                        </div>

                        <div class="mt-6 flex justify-end gap-3">
                            <button type="button" @click="closePaymentModal()" class="px-5 py-2.5 text-sm font-medium text-slate-700 dark:text-slate-300 bg-slate-100 dark:bg-white/5 hover:bg-slate-200 dark:hover:bg-white/10 rounded-xl transition-colors">{{ __('Cancel') }}</button>
                            <button type="submit" class="px-5 py-2.5 text-sm font-bold text-emerald-900 bg-emerald-400 hover:bg-emerald-300 rounded-xl transition-all shadow-lg shadow-emerald-500/20 flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                                {{ __('Confirm Payment') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('debtManager', () => ({
                isPaymentModalOpen: false,
                paymentUrl: '',
                paymentAmount: '',
                paymentCurrencyId: '',
                paymentExchangeRate: 1,
                targetDebtAmount: 0,
                targetCurrencyId: null,
                
                currencies: @json($currencies),
                sypIds: [],

                init() {
                    this.sypIds = this.currencies.filter(c => c.code.includes('SYP')).map(c => c.id);
                },

                openPaymentModal(supplyId, unpaidAmount, supplyCurrencyId) {
                    this.paymentUrl = `{{ url('admin/supplies') }}/${supplyId}/pay`;
                    this.targetDebtAmount = unpaidAmount;
                    this.targetCurrencyId = supplyCurrencyId;
                    
                    // Default payment to the supply's native currency
                    this.paymentCurrencyId = supplyCurrencyId;
                    this.updateExchangeRate();
                    this.paymentAmount = '';
                    
                    this.isPaymentModalOpen = true;
                },

                closePaymentModal() {
                    this.isPaymentModalOpen = false;
                },

                updateExchangeRate() {
                    let cur = this.currencies.find(c => c.id == this.paymentCurrencyId);
                    if (cur) {
                        this.paymentExchangeRate = cur.exchange_rate;
                    }
                },

                payFullAmount() {
                    this.paymentCurrencyId = this.targetCurrencyId;
                    this.updateExchangeRate();
                    this.paymentAmount = this.targetDebtAmount;
                }
            }))
        })
    </script>
</x-app-layout>
