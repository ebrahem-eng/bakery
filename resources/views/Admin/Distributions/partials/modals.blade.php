<!-- Sell Bundles Modal -->
<div x-data="{ open: false, distId: '', distName: '', currency: '' }"
     @open-sale-modal.window="open = true; distId = $event.detail.id; distName = $event.detail.name; currency = $event.detail.currency" 
     x-show="open" 
     class="fixed inset-0 z-[100] overflow-y-auto" style="display: none;">
    
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
        <div x-show="open" x-transition.opacity class="fixed inset-0 transition-opacity bg-black/60 backdrop-blur-sm" @click="open = false"></div>

        <div x-show="open" x-transition 
             class="relative inline-block w-full max-w-md p-6 overflow-hidden text-left align-middle transition-all transform glass-panel rounded-2xl shadow-xl border border-white/10"
             {{ app()->getLocale() == 'ar' ? 'dir="rtl"' : 'dir="ltr"' }}>
            
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-xl font-bold text-white">{{ __('Sell Bread Bundles') }}</h3>
                <button @click="open = false" class="text-slate-400 hover:text-white transition-colors">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form action="{{ route('admin.distributions.store') }}" method="POST" class="space-y-4">
                @csrf
                <input type="hidden" name="distributor_id" :value="distId">
                
                <div class="p-3 bg-emerald-500/10 border border-emerald-500/20 rounded-xl text-emerald-400 text-sm mb-4">
                    {{ __('Registering sale for:') }} <span class="font-bold" x-text="distName"></span>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">{{ __('Bundle Count') }}</label>
                        <input type="number" name="bundle_count" required 
                            class="block w-full px-4 py-3 bg-[#0f1115] border border-white/5 rounded-xl text-sm placeholder-slate-600 text-white focus:outline-none focus:border-emerald-500/50 focus:ring-1 focus:ring-emerald-500/50 transition-all font-medium" 
                            placeholder="e.g. 100">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">{{ __('Unit Price') }}</label>
                        <div class="relative">
                            <input type="number" step="0.01" name="price_per_bundle" required 
                                class="block w-full {{ app()->getLocale() == 'ar' ? 'pl-16 pr-4' : 'pr-16 pl-4' }} py-3 bg-[#0f1115] border border-white/5 rounded-xl text-sm placeholder-slate-600 text-white focus:outline-none focus:border-emerald-500/50 focus:ring-1 focus:ring-emerald-500/50 transition-all font-medium" 
                                placeholder="0.00">
                            <div class="absolute inset-y-0 {{ app()->getLocale() == 'ar' ? 'left-0 pl-4' : 'right-0 pr-4' }} flex items-center pointer-events-none">
                                <span class="text-slate-400 text-sm font-bold" x-text="currency"></span>
                            </div>
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">{{ __('Amount Paid (Now)') }}</label>
                    <div class="relative">
                        <input type="number" step="0.01" name="amount_paid" required value="0.00"
                            class="block w-full {{ app()->getLocale() == 'ar' ? 'pl-16 pr-4' : 'pr-16 pl-4' }} py-3 bg-[#0f1115] border border-white/5 rounded-xl text-sm placeholder-slate-600 text-white focus:outline-none focus:border-emerald-500/50 focus:ring-1 focus:ring-emerald-500/50 transition-all font-medium">
                        <div class="absolute inset-y-0 {{ app()->getLocale() == 'ar' ? 'left-0 pl-4' : 'right-0 pr-4' }} flex items-center pointer-events-none">
                            <span class="text-slate-400 text-sm font-bold" x-text="currency"></span>
                        </div>
                    </div>
                    <p class="mt-1 text-xs text-slate-500">{{ __('If full debt, leave as 0.00') }}</p>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">{{ __('Notes (Optional)') }}</label>
                    <input type="text" name="notes" class="block w-full px-4 py-3 bg-[#0f1115] border border-white/5 rounded-xl text-sm text-white focus:outline-none focus:border-emerald-500/50 focus:ring-1 focus:ring-emerald-500/50 transition-all font-medium" placeholder="" >
                </div>

                <div class="pt-4 flex gap-3">
                    <button type="button" @click="open = false" class="flex-1 bg-transparent hover:bg-white/5 text-white border border-white/10 px-4 py-3 rounded-xl text-sm font-bold transition-colors">
                        {{ __('Cancel') }}
                    </button>
                    <button type="submit" class="flex-1 bg-emerald-500 hover:bg-emerald-400 text-[#0f1115] px-4 py-3 rounded-xl text-sm font-bold transition-colors shadow-[0_0_15px_rgba(16,185,129,0.3)]">
                        {{ __('Log Sale') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Returns Modal -->
<div x-data="{ open: false, distId: '', distName: '', currency: '' }"
     @open-return-modal.window="open = true; distId = $event.detail.id; distName = $event.detail.name; currency = $event.detail.currency" 
     x-show="open" 
     class="fixed inset-0 z-[100] overflow-y-auto" style="display: none;">
    
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
        <div x-show="open" x-transition.opacity class="fixed inset-0 transition-opacity bg-black/60 backdrop-blur-sm" @click="open = false"></div>

        <div x-show="open" x-transition 
             class="relative inline-block w-full max-w-md p-6 overflow-hidden text-left align-middle transition-all transform glass-panel rounded-2xl shadow-xl border border-white/10"
             {{ app()->getLocale() == 'ar' ? 'dir="rtl"' : 'dir="ltr"' }}>
            
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-xl font-bold text-white">{{ __('Return Bundles') }}</h3>
                <button @click="open = false" class="text-slate-400 hover:text-white transition-colors">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form action="{{ route('admin.distributions.return') }}" method="POST" class="space-y-4">
                @csrf
                <input type="hidden" name="distributor_id" :value="distId">
                
                <div class="p-3 bg-red-500/10 border border-red-500/20 rounded-xl text-red-400 text-sm mb-4">
                    {{ __('Refusing/Returning for:') }} <span class="font-bold" x-text="distName"></span>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">{{ __('Bundles Returned') }}</label>
                        <input type="number" name="bundle_count" required 
                            class="block w-full px-4 py-3 bg-[#0f1115] border border-white/5 rounded-xl text-sm placeholder-slate-600 text-white focus:outline-none focus:border-red-500/50 focus:ring-1 focus:ring-red-500/50 transition-all font-medium" 
                            placeholder="e.g. 5">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">{{ __('Refund per Unit') }}</label>
                        <div class="relative">
                            <input type="number" step="0.01" name="refund_per_bundle" required 
                                class="block w-full {{ app()->getLocale() == 'ar' ? 'pl-16 pr-4' : 'pr-16 pl-4' }} py-3 bg-[#0f1115] border border-white/5 rounded-xl text-sm placeholder-slate-600 text-white focus:outline-none focus:border-red-500/50 focus:ring-1 focus:ring-red-500/50 transition-all font-medium" 
                                placeholder="0.00">
                            <div class="absolute inset-y-0 {{ app()->getLocale() == 'ar' ? 'left-0 pl-4' : 'right-0 pr-4' }} flex items-center pointer-events-none">
                                <span class="text-slate-400 text-sm font-bold" x-text="currency"></span>
                            </div>
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">{{ __('Notes (Optional)') }}</label>
                    <input type="text" name="notes" class="block w-full px-4 py-3 bg-[#0f1115] border border-white/5 rounded-xl text-sm text-white focus:outline-none focus:border-red-500/50 focus:ring-1 focus:ring-red-500/50 transition-all font-medium" placeholder="" >
                </div>

                <div class="pt-4 flex gap-3">
                    <button type="button" @click="open = false" class="flex-1 bg-transparent hover:bg-white/5 text-white border border-white/10 px-4 py-3 rounded-xl text-sm font-bold transition-colors">
                        {{ __('Cancel') }}
                    </button>
                    <button type="submit" class="flex-1 bg-red-500 hover:bg-red-400 text-[#0f1115] px-4 py-3 rounded-xl text-sm font-bold transition-colors shadow-[0_0_15px_rgba(239,68,68,0.3)]">
                        {{ __('Log Return') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Payment Modal -->
<div x-data="{ open: false, distId: '', distName: '', currency: '' }"
     @open-pay-modal.window="open = true; distId = $event.detail.id; distName = $event.detail.name; currency = $event.detail.currency" 
     x-show="open" 
     class="fixed inset-0 z-[100] overflow-y-auto" style="display: none;">
    
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
        <div x-show="open" x-transition.opacity class="fixed inset-0 transition-opacity bg-black/60 backdrop-blur-sm" @click="open = false"></div>

        <div x-show="open" x-transition 
             class="relative inline-block w-full max-w-md p-6 overflow-hidden text-left align-middle transition-all transform glass-panel rounded-2xl shadow-xl border border-white/10"
             {{ app()->getLocale() == 'ar' ? 'dir="rtl"' : 'dir="ltr"' }}>
            
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-xl font-bold text-white">{{ __('Receive Payment') }}</h3>
                <button @click="open = false" class="text-slate-400 hover:text-white transition-colors">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form action="{{ route('admin.distributions.transaction') }}" method="POST" class="space-y-4">
                @csrf
                <input type="hidden" name="distributor_id" :value="distId">
                
                <div class="p-3 bg-amber-500/10 border border-amber-500/20 rounded-xl text-amber-400 text-sm mb-4">
                    {{ __('Applying credit against debt for:') }} <span class="font-bold" x-text="distName"></span>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">{{ __('Transaction Type') }}</label>
                    <select name="type" required class="block w-full px-4 py-3 bg-[#0f1115] border border-white/5 rounded-xl text-sm text-white focus:outline-none focus:border-amber-500/50 focus:ring-1 focus:ring-amber-500/50 transition-all appearance-none font-medium">
                        <option value="payment">{{ __('Cash Payment (دفعة)') }}</option>
                        <option value="discount">{{ __('Discount / Forgive (حسم)') }}</option>
                        <option value="other_credit">{{ __('Other Credit') }}</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">{{ __('Amount') }}</label>
                    <div class="relative">
                        <input type="number" step="0.01" name="amount" required 
                            class="block w-full {{ app()->getLocale() == 'ar' ? 'pl-16 pr-4' : 'pr-16 pl-4' }} py-3 bg-[#0f1115] border border-white/5 rounded-xl text-sm placeholder-slate-600 text-white focus:outline-none focus:border-amber-500/50 focus:ring-1 focus:ring-amber-500/50 transition-all font-medium" 
                            placeholder="0.00">
                        <div class="absolute inset-y-0 {{ app()->getLocale() == 'ar' ? 'left-0 pl-4' : 'right-0 pr-4' }} flex items-center pointer-events-none">
                            <span class="text-slate-400 text-sm font-bold" x-text="currency"></span>
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">{{ __('Notes (Optional)') }}</label>
                    <input type="text" name="notes" class="block w-full px-4 py-3 bg-[#0f1115] border border-white/5 rounded-xl text-sm text-white focus:outline-none focus:border-amber-500/50 focus:ring-1 focus:ring-amber-500/50 transition-all font-medium" placeholder="" >
                </div>

                <div class="pt-4 flex gap-3">
                    <button type="button" @click="open = false" class="flex-1 bg-transparent hover:bg-white/5 text-white border border-white/10 px-4 py-3 rounded-xl text-sm font-bold transition-colors">
                        {{ __('Cancel') }}
                    </button>
                    <button type="submit" class="flex-1 bg-amber-500 hover:bg-amber-400 text-[#0f1115] px-4 py-3 rounded-xl text-sm font-bold transition-colors shadow-[0_0_15px_rgba(245,158,11,0.3)]">
                        {{ __('Save Ledger Entry') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
