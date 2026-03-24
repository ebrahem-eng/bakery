@extends('layouts.Admin.App')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-slate-900 dark:text-white tracking-tight">{{ __('Register Raw Material Supply') }}</h1>
    <p class="text-sm text-slate-500 mt-1">{{ __('Link new supply batches to the active Work Day ledger.') }}</p>
</div>

<form action="{{ route('admin.supplies.store') }}" method="POST" class="glass-panel p-6 rounded-2xl max-w-5xl" 
      x-data="supplyCalculator()">
    @csrf
    
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        
        <!-- Vendor / Supplier Selection -->
        <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">{{ __('Supplier') }}</label>
            <select name="supplier_id" x-model="selectedSupplier" @change="filterCategories()" required class="glass-input block w-full px-4 py-3 rounded-xl focus:outline-none focus:ring-1 focus:ring-[#0ea5e9]">
                <option value="">{{ __('Select Supplier...') }}</option>
                @foreach($suppliers as $supplier)
                    <option value="{{ $supplier->id }}">{{ $supplier->first_name }} {{ $supplier->last_name }}</option>
                @endforeach
            </select>
        </div>

        <!-- Mapped Categories -->
        <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">{{ __('Material Category') }}</label>
            <select name="category_id" x-model="selectedCategory" required class="glass-input block w-full px-4 py-3 rounded-xl focus:outline-none focus:ring-1 focus:ring-[#0ea5e9]">
                <option value="">{{ __('Select Material...') }}</option>
                <template x-for="cat in availableCategories" :key="cat.id">
                    <option :value="cat.id" x-text="cat.name"></option>
                </template>
            </select>
        </div>

        <!-- Currency -->
        <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">{{ __('Buying Currency') }}</label>
            <select name="currency_id" x-model="selectedCurrency" @change="updateExchangeRate()" required class="glass-input block w-full px-4 py-3 rounded-xl focus:outline-none focus:ring-1 focus:ring-[#0ea5e9]">
                <option value="">{{ __('Select Currency...') }}</option>
                @foreach($currencies as $currency)
                    <option value="{{ $currency->id }}" data-rate="{{ $currency->default_exchange_rate }}">{{ $currency->name }} ({{ $currency->symbol }})</option>
                @endforeach
            </select>
        </div>

        <!-- Live Exchange Rate Override -->
        <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">{{ __('Exchange Rate Recorded') }}</label>
            <input type="number" step="0.01" name="exchange_rate" x-model="exchangeRate" required class="glass-input block w-full px-4 py-3 rounded-xl focus:outline-none focus:ring-1 focus:ring-[#0ea5e9]">
            <p class="text-[10px] text-slate-500 mt-1">{{ __('Used to lock historical value.') }}</p>
        </div>
    </div>

    <div class="border-t border-slate-200 dark:border-white/5 my-8"></div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <!-- Quantity & Metrics -->
        <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">{{ __('Quantity') }}</label>
            <input type="number" step="0.01" name="quantity" x-model="quantity" @input="calculateTotal()" required class="glass-input text-2xl font-bold font-mono block w-full px-4 py-3 rounded-xl focus:outline-none focus:ring-1 focus:ring-[#0ea5e9]">
            <p class="text-[10px] text-slate-500 mt-1">{{ __('Enter weight/volume amount.') }}</p>
        </div>

        <!-- Unit Price -->
        <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">{{ __('Unit Price') }}</label>
            <input type="number" step="0.01" name="unit_price" x-model="unitPrice" @input="calculateTotal()" required class="glass-input text-2xl font-bold font-mono block w-full px-4 py-3 rounded-xl focus:outline-none focus:ring-1 focus:ring-[#0ea5e9]">
        </div>
        
        <!-- Extracted Subtotal (Read Only UI) -->
        <div class="bg-blue-50 dark:bg-blue-500/10 border border-blue-200 dark:border-blue-500/20 rounded-xl p-4 flex flex-col justify-center">
            <span class="text-xs font-semibold text-blue-600 dark:text-blue-400 uppercase tracking-wider">{{ __('Total Invoice Cost') }}</span>
            <div class="text-3xl font-bold text-slate-900 dark:text-white mt-1" x-text="formatCurrency(totalCost)"></div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        <!-- Paid Partial Amount -->
        <div>
            <label class="block text-sm font-medium text-emerald-600 dark:text-emerald-400 mb-2">{{ __('Amount Paid Right Now') }}</label>
            <input type="number" step="0.01" name="paid_amount" x-model="paidAmount" required class="glass-input block w-full px-4 py-3 rounded-xl focus:outline-none focus:ring-1 focus:ring-emerald-500 border border-emerald-200 dark:border-emerald-500/30">
            <div class="flex justify-between items-center mt-2">
                <p class="text-[10px] text-slate-500">{{ __('Leave 0 if full debt.') }}</p>
                <div class="text-xs font-bold text-red-500">Unpaid Debt Tracked: <span x-text="formatCurrency(remainingDebt)"></span></div>
            </div>
        </div>
        
        <!-- Unloading Fees -->
        <div>
            <label class="block text-sm font-medium text-amber-600 dark:text-amber-400 mb-2">{{ __('Unloading / Porterage Fee') }}</label>
            <input type="number" step="0.01" name="unloading_fee" x-model="unloadingFee" required class="glass-input block w-full px-4 py-3 rounded-xl focus:outline-none focus:ring-1 focus:ring-amber-500 border border-amber-200 dark:border-amber-500/30">
            <p class="text-[10px] text-slate-500 mt-1">{{ __('This registers as a separate daily operational expense automagically.') }}</p>
        </div>
    </div>
    
    <div>
        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">{{ __('Reference Notes (Optional)') }}</label>
        <textarea name="notes" rows="2" class="glass-input block w-full px-4 py-3 rounded-xl focus:outline-none focus:ring-1 focus:ring-[#0ea5e9]"></textarea>
    </div>

    <div class="flex justify-end gap-4 border-t border-slate-200 dark:border-white/5 mt-8 pt-6">
        <a href="{{ route('admin.supplies.index') }}" class="px-6 py-2.5 rounded-xl text-sm font-medium text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white transition-colors">{{ __('Cancel') }}</a>
        <button type="submit" class="bg-gradient-to-r from-[#0ea5e9] to-[#3b82f6] hover:from-[#38bdf8] hover:to-[#60a5fa] text-white transition-all px-8 py-3 rounded-xl text-sm font-bold shadow-[0_0_20px_rgba(14,165,233,0.3)]">{{ __('Finalize Supply Process') }}</button>
    </div>
</form>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('supplyCalculator', () => ({
        suppliers: @json($suppliers),
        allCategories: @json($categories),
        currencies: @json($currencies),
        
        selectedSupplier: '',
        availableCategories: [],
        selectedCategory: '',
        
        selectedCurrency: '',
        exchangeRate: 1,
        
        quantity: '',
        unitPrice: '',
        totalCost: 0,
        paidAmount: '',
        unloadingFee: 0,
        
        get remainingDebt() {
            let pAmount = parseFloat(this.paidAmount) || 0;
            let debt = this.totalCost - pAmount;
            return debt > 0 ? debt : 0;
        },
        
        filterCategories() {
            this.selectedCategory = '';
            if(!this.selectedSupplier) {
                this.availableCategories = [];
                return;
            }
            let supplier = this.suppliers.find(s => s.id == this.selectedSupplier);
            if(supplier && supplier.categories) {
                this.availableCategories = supplier.categories;
            } else {
                this.availableCategories = [];
            }
        },
        
        updateExchangeRate() {
            if(!this.selectedCurrency) {
                this.exchangeRate = 1;
                return;
            }
            let currency = this.currencies.find(c => c.id == this.selectedCurrency);
            if(currency) {
                this.exchangeRate = currency.default_exchange_rate;
            }
        },
        
        calculateTotal() {
            this.totalCost = (parseFloat(this.quantity) || 0) * (parseFloat(this.unitPrice) || 0);
        },
        
        formatCurrency(num) {
            return Number(Math.round(num + 'e2') + 'e-2').toFixed(2);
        }
    }))
})
</script>
@endsection
