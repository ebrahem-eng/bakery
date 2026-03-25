@extends('layouts.Admin.App')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-slate-900 dark:text-white tracking-tight">{{ __('Register Raw Material Supply') }}</h1>
    <p class="text-sm text-slate-500 mt-1">{{ __('Dynamically build complex batch invoices mapped instantly.') }}</p>
</div>

<form action="{{ route('admin.supplies.store') }}" method="POST" x-data="supplyInvoice()">
    @csrf
    
    <!-- Top Level Invoice Data -->
    <div class="glass-panel p-6 rounded-2xl mb-6">
        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">{{ __('Select Supplier') }}</label>
        <select name="supplier_id" x-model="supplier_id" required class="glass-input block w-full lg:w-1/2 px-4 py-3 rounded-xl focus:ring-[#eab308]">
            <option value="">{{ __('Select Supplier...') }}</option>
            @foreach($suppliers as $supplier)
                <option value="{{ $supplier->id }}">{{ $supplier->first_name }} {{ $supplier->last_name }}</option>
            @endforeach
        </select>
    </div>

    <!-- Items Array -->
    <div class="space-y-6">
        <template x-for="(item, index) in items" :key="item.id">
            <div class="glass-panel p-6 rounded-2xl border border-slate-200 dark:border-white/10 relative overflow-hidden transition-all hover:border-amber-500/30 dark:hover:border-white/20">
                <!-- Delete Button -->
                <button type="button" @click="removeItem(index)" class="absolute top-4 {{ app()->getLocale() == 'ar' ? 'left-4' : 'right-4' }} text-red-400 hover:text-red-300" title="{{ __('Remove Item') }}">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                </button>
                
                <h3 class="text-lg font-bold text-amber-500 mb-4">{{ __('Supply Line') }} <span x-text="index + 1"></span></h3>

                <!-- Core Matrix -->
                <div class="grid grid-cols-1 lg:grid-cols-4 gap-4 mb-4">
                    <div class="lg:col-span-1">
                        <label class="block text-xs text-slate-400 mb-1">{{ __('Category') }}</label>
                        <select x-bind:name="`supplies[${index}][category_id]`" x-model="item.category_id" @change="updateCategory(item)" required class="glass-input w-full px-3 py-2 rounded-lg text-sm">
                            <option value="">{{ __('Select...') }}</option>
                            <template x-for="cat in categories" :key="cat.id">
                                <option :value="cat.id" x-text="cat.name"></option>
                            </template>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs text-slate-400 mb-1">{{ __('Currency') }}</label>
                        <select x-bind:name="`supplies[${index}][currency_id]`" x-model="item.currency_id" @change="updateExchangeRate(item)" required class="glass-input w-full px-3 py-2 rounded-lg text-sm">
                            <option value="">{{ __('Select...') }}</option>
                            <template x-for="cur in currencies" :key="cur.id">
                                <option :value="cur.id" x-text="cur.name"></option>
                            </template>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs text-slate-400 mb-1">{{ __('Exchange Rate') }}</label>
                        <input type="number" step="0.01" x-bind:name="`supplies[${index}][exchange_rate]`" x-model="item.exchange_rate" required class="glass-input w-full px-3 py-2 rounded-lg text-sm">
                    </div>
                    <div>
                        <label class="block text-xs text-emerald-400 mb-1">{{ __('Unit Price') }}</label>
                        <input type="number" step="0.01" x-bind:name="`supplies[${index}][unit_price]`" x-model="item.unit_price" required class="glass-input w-full px-3 py-2 rounded-lg text-sm">
                    </div>
                </div>

                <!-- Dynamic Polymorphic Fields -->
                <div class="p-4 bg-slate-100/50 dark:bg-black/20 rounded-xl mb-4 border border-slate-200 dark:border-white/5">
                    <!-- FLOUR LOGIC -->
                    <template x-if="item.category_name === 'طحين'">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs text-slate-600 dark:text-slate-300 mb-1">{{ __('Weight in Tons (1000kg)') }}</label>
                                <input type="number" step="0.001" x-bind:name="`supplies[${index}][quantity]`" x-model="item.quantity" class="glass-input w-full px-3 py-2 rounded-lg text-sm">
                            </div>
                            <div>
                                <label class="block text-xs text-slate-600 dark:text-slate-300 mb-1">{{ __('Flour Type (e.g Zero, Number 1)') }}</label>
                                <input type="text" x-bind:name="`supplies[${index}][material_type_name]`" x-model="item.material_type_name" class="glass-input w-full px-3 py-2 rounded-lg text-sm">
                            </div>
                        </div>
                    </template>

                    <!-- YEAST LOGIC -->
                    <template x-if="item.category_name === 'خميرة'">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-xs text-slate-600 dark:text-slate-300 mb-1">{{ __('Boxes Count') }}</label>
                                <input type="number" step="1" x-bind:name="`supplies[${index}][boxes_count]`" x-model="item.boxes_count" class="glass-input w-full px-3 py-2 rounded-lg text-sm">
                            </div>
                            <div>
                                <label class="block text-xs text-slate-600 dark:text-slate-300 mb-1">{{ __('Weight per Box (kg)') }}</label>
                                <input type="number" step="0.01" x-bind:name="`supplies[${index}][box_weight]`" x-model="item.box_weight" class="glass-input w-full px-3 py-2 rounded-lg text-sm">
                            </div>
                            <div class="flex flex-col justify-end">
                                <span class="text-xs text-slate-500 mb-1">{{ __('Computed Native Weight') }}</span>
                                <div class="font-mono text-amber-400 font-bold" x-text="(item.boxes_count * item.box_weight).toFixed(2) + ' kg'"></div>
                                <input type="hidden" x-bind:name="`supplies[${index}][quantity]`" x-bind:value="item.boxes_count * item.box_weight">
                            </div>
                        </div>
                    </template>

                    <!-- DIESEL / SALT LOGIC -->
                    <!-- DIESEL / SALT LOGIC -->
                    <template x-if="item.category_name === 'مازوت' || item.category_name === 'ملح'">
                        <div>
                            <label class="block text-xs text-slate-600 dark:text-slate-300 mb-1" x-text="item.category_name === 'مازوت' ? '{{ __('Total Liters') }}' : '{{ __('Total Kilos') }}'"></label>
                            <input type="number" step="0.01" x-bind:name="`supplies[${index}][quantity]`" x-model="item.quantity" class="glass-input w-full md:w-1/2 px-3 py-2 rounded-lg text-sm">
                        </div>
                    </template>
                    
                    <template x-if="item.category_name === ''">
                        <div class="text-xs text-slate-500 italic">{{ __('Select a category to map explicit variables.') }}</div>
                    </template>
                </div>

                <!-- Financials & Unloading -->
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-4 items-start">
                    <div class="lg:col-span-3">
                        <label class="block text-xs text-slate-400 mb-1">{{ __('Notes') }}</label>
                        <textarea x-bind:name="`supplies[${index}][notes]`" x-model="item.notes" rows="2" class="glass-input w-full px-3 py-2 rounded-lg text-sm"></textarea>
                    </div>

                    <div class="lg:col-span-12 bg-slate-100 dark:bg-white/5 p-4 rounded-xl border border-slate-200 dark:border-white/10">
                        <div class="text-xs font-bold text-slate-700 dark:text-slate-300 mb-2">{{ __('Unloading Specifications') }}</div>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                            <div>
                                <label class="block text-[10px] text-slate-400 mb-1">{{ __('Fee Paid By') }}</label>
                                <select x-bind:name="`supplies[${index}][unloading_fee_payer]`" x-model="item.unloading_fee_payer" class="glass-input w-full px-2 py-1.5 rounded text-xs bg-black/40 text-slate-200 border-none outline-none">
                                    <option value="bakery">{{ __('Bakery (Added to Daily Expenses)') }}</option>
                                    <option value="supplier">{{ __('Supplier (Tracked without Expense)') }}</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-[10px] text-slate-400 mb-1">{{ __('Fee Amount') }}</label>
                                <div class="flex gap-1">
                                    <input type="number" step="0.01" x-bind:name="`supplies[${index}][unloading_fee]`" x-model="item.unloading_fee" class="glass-input w-2/3 px-2 py-1.5 rounded text-xs bg-black/40 text-slate-200 border-none outline-none">
                                    <select x-bind:name="`supplies[${index}][unloading_fee_currency_id]`" x-model="item.unloading_fee_currency_id" @change="updateFeeExchangeRate(item)" class="glass-input w-1/3 px-1 py-1.5 rounded text-[10px] bg-black/40 text-slate-200 border-none outline-none">
                                        <template x-for="c in currencies" :key="c.id">
                                            <option :value="c.id" x-text="c.name"></option>
                                        </template>
                                    </select>
                                </div>
                            </div>
                            <div>
                                <label class="block text-[10px] text-slate-400 mb-1">{{ __('Fee Ex-Rate') }}</label>
                                <input type="number" step="0.01" x-bind:name="`supplies[${index}][unloading_fee_exchange_rate]`" x-model="item.unloading_fee_exchange_rate" class="glass-input w-full px-2 py-1.5 rounded text-xs bg-black/40 text-slate-200 border-none outline-none" title="Exchange rate for unloading fee">
                            </div>
                        </div>
                    </div>

                    <div class="lg:col-span-4 flex flex-col justify-between h-full">
                        <div class="text-right">
                            <div class="text-[10px] text-slate-500 dark:text-slate-400 uppercase tracking-wider">{{ __('Total Native Cost') }}</div>
                            <div class="text-xl font-bold font-mono text-slate-900 dark:text-white" x-text="item.total_cost.toLocaleString(undefined, {minimumFractionDigits: 2})"></div>
                        </div>
                        <div class="mt-2">
                            <label class="block text-[10px] text-emerald-400 mb-1 {{ app()->getLocale() == 'ar' ? 'text-right' : 'text-left' }}">{{ __('Amount Paid From Register') }}</label>
                            <div class="relative">
                                <input type="number" step="0.01" x-bind:name="`supplies[${index}][paid_amount]`" x-model="item.paid_amount" 
                                    class="glass-input block w-full {{ app()->getLocale() == 'ar' ? 'pl-20 pr-4' : 'pr-20 pl-4' }} py-2 rounded-lg text-sm font-bold text-emerald-300 {{ app()->getLocale() == 'ar' ? 'text-right' : 'text-left' }}">
                                <div class="absolute inset-y-0 {{ app()->getLocale() == 'ar' ? 'left-0 pl-4' : 'right-0 pr-4' }} flex items-center pointer-events-none">
                                    <span class="text-emerald-400/50 text-xs font-bold" x-text="currencies.find(c => c.id == item.currency_id)?.symbol || ''"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </template>
    </div>

    <!-- Actions -->
    <div class="mt-6 flex flex-col sm:flex-row justify-between items-center bg-slate-100 dark:bg-black/20 p-4 rounded-2xl border border-slate-200 dark:border-white/5">
        <button type="button" @click="addItem()" class="bg-white/50 dark:bg-white/5 hover:bg-white dark:hover:bg-white/10 text-slate-900 dark:text-white transition-all px-4 py-2 rounded-xl text-sm font-medium flex items-center mb-4 sm:mb-0 border border-slate-200 dark:border-white/10">
            <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
            {{ __('Add Another Material') }}
        </button>
        
        <button type="submit" class="w-full sm:w-auto bg-[#eab308]/10 text-[#eab308] border border-[#eab308]/30 hover:bg-[#eab308] hover:text-[#451a03] transition-all px-8 py-3 rounded-xl text-lg font-bold shadow-[0_0_15px_rgba(234,179,8,0.15)] flex items-center justify-center">
            <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            {{ __('Finalize & Post Invoice') }}
        </button>
    </div>
</form>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('supplyInvoice', () => ({
        supplier_id: '',
        suppliers: @json($suppliers),
        categories: @json($categories),
        currencies: @json($currencies),
        items: [],
        init() {
            this.addItem();
        },
        addItem() {
            this.items.push({
                id: Date.now() + Math.random(),
                category_id: '',
                category_name: '',
                currency_id: this.items.length > 0 ? this.items[this.items.length-1].currency_id : '',
                exchange_rate: this.items.length > 0 ? this.items[this.items.length-1].exchange_rate : 1,
                material_type_name: '',
                quantity: null,
                boxes_count: null,
                box_weight: null,
                unit_price: null,
                paid_amount: 0,
                unloading_fee: 0,
                unloading_fee_payer: 'bakery',
                unloading_fee_currency_id: this.currencies.length > 0 ? this.currencies[0].id : '',
                unloading_fee_exchange_rate: this.currencies.length > 0 ? this.currencies[0].default_exchange_rate : 1,
                notes: '',
                get total_cost() {
                    const q = this.category_name === 'خميرة' ? (this.boxes_count * this.box_weight) : this.quantity;
                    return (q || 0) * (this.unit_price || 0);
                }
            });
        },
        removeItem(index) {
            this.items.splice(index, 1);
            if(this.items.length === 0) {
                this.addItem();
            }
        },
        updateCategory(item) {
            let cat = this.categories.find(c => c.id == item.category_id);
            item.category_name = cat ? cat.name : '';
        },
        updateExchangeRate(item) {
            let cur = this.currencies.find(c => c.id == item.currency_id);
            if(cur) item.exchange_rate = cur.default_exchange_rate;
        },
        updateFeeExchangeRate(item) {
            let cur = this.currencies.find(c => c.id == item.unloading_fee_currency_id);
            if(cur) item.unloading_fee_exchange_rate = cur.default_exchange_rate;
        }
    }))
})
</script>
@endsection
