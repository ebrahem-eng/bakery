@extends('layouts.Admin.App')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-slate-900 dark:text-white tracking-tight">{{ __('Register Worker') }}</h1>
    <p class="text-sm text-slate-500 mt-1">{{ __('Add a new staff member and establish their default compensation.') }}</p>
</div>

<form action="{{ route('admin.workers.store') }}" method="POST" class="glass-panel p-6 rounded-2xl max-w-4xl" 
      x-data="{ 
          mobiles: [''], 
          currencyId: '', 
          currencies: {{ $currencies->map(fn($c) => ['id' => $c->id, 'is_default' => $c->is_default, 'rate' => $c->exchange_rate])->toJson() }},
          showExchangeRate() {
              const c = this.currencies.find(i => i.id == this.currencyId);
              return c && !c.is_default;
          },
          getDefaultRate() {
              const c = this.currencies.find(i => i.id == this.currencyId);
              return c ? c.rate : 1;
          }
      }">
    @csrf
    
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">{{ __('First Name') }}</label>
            <input type="text" name="first_name" required class="glass-input block w-full px-4 py-3 rounded-xl focus:outline-none focus:ring-1 focus:ring-[#0ea5e9]">
            @error('first_name') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">{{ __('Last Name') }}</label>
            <input type="text" name="last_name" required class="glass-input block w-full px-4 py-3 rounded-xl focus:outline-none focus:ring-1 focus:ring-[#0ea5e9]">
            @error('last_name') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
        </div>
        <div class="md:col-span-2">
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">{{ __('Job Title / Role (Optional)') }}</label>
            <input type="text" name="title" class="glass-input block w-full px-4 py-3 rounded-xl focus:outline-none focus:ring-1 focus:ring-[#0ea5e9]" placeholder="{{ __('e.g. Baker, Distributor, Guard') }}">
        </div>
    </div>

    <!-- Compensation -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8 bg-slate-50 dark:bg-white/5 border border-slate-200 dark:border-white/5 p-4 rounded-xl">
        <div>
            <label class="block text-sm font-medium text-emerald-600 dark:text-emerald-400 mb-2">{{ __('Fixed Daily Wage') }}</label>
            <input type="number" step="0.01" name="daily_wage" required class="glass-input block w-full px-4 py-3 rounded-xl focus:outline-none focus:ring-1 focus:ring-emerald-500 border border-emerald-200 dark:border-emerald-500/30">
            @error('daily_wage') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
        </div>
        <div>
            <label class="block text-sm font-medium text-emerald-600 dark:text-emerald-400 mb-2">{{ __('Payment Currency') }}</label>
            <select name="currency_id" x-model="currencyId" required class="glass-input block w-full px-4 py-3 rounded-xl focus:outline-none focus:ring-1 focus:ring-emerald-500 border border-emerald-200 dark:border-emerald-500/30">
                <option value="">{{ __('Select Currency...') }}</option>
                @foreach($currencies as $currency)
                    <option value="{{ $currency->id }}">{{ $currency->name }} ({{ $currency->code }})</option>
                @endforeach
            </select>
            @error('currency_id') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
        </div>

        <!-- Exchange Rate Step (Conditional) -->
        <div x-show="showExchangeRate()" x-transition class="md:col-span-2 p-4 bg-amber-500/5 border border-amber-500/20 rounded-xl">
            <label class="block text-sm font-bold text-amber-600 dark:text-amber-400 mb-2">{{ __('Custom Exchange Rate') }} (1 <span x-text="currencies.find(c => c.id == currencyId)?.code || ''"></span> = ?? SYP)</label>
            <div class="flex items-center gap-4">
                <input type="number" step="0.01" name="exchange_rate" :value="getDefaultRate()" class="glass-input flex-1 px-4 py-3 rounded-xl focus:outline-none focus:ring-1 focus:ring-amber-500 border border-amber-500/30" placeholder="0.00">
                <p class="text-xs text-slate-500 max-w-[200px] leading-relaxed">
                    {{ __('Adjust the exchange rate if it differs from the system default for this specific wage agreement.') }}
                </p>
            </div>
        </div>
    </div>

    <!-- Dynamic Mobiles -->
    <div class="mb-8 p-4 rounded-xl border border-slate-200 dark:border-white/5 bg-slate-50 dark:bg-white/5">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-sm font-semibold text-slate-900 dark:text-white">{{ __('Mobile Numbers') }}</h3>
            <button type="button" @click="mobiles.push('')" class="text-xs text-[#0ea5e9] font-bold hover:text-[#38bdf8]">{{ __('+ Add Number') }}</button>
        </div>
        <template x-for="(mobile, index) in mobiles" :key="index">
            <div class="flex gap-3 mb-3">
                <input type="text" x-model="mobiles[index]" name="mobiles[]" class="flex-1 glass-input px-4 py-2 rounded-xl focus:outline-none focus:ring-1 focus:ring-[#0ea5e9]" placeholder="{{ __('Mobile Number') }}">
                <button type="button" @click="mobiles.length > 1 ? mobiles.splice(index, 1) : mobiles[0] = ''" class="px-3 py-2 text-slate-400 hover:text-red-500 hover:bg-slate-200 dark:hover:bg-white/10 rounded-xl transition-colors">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        </template>
    </div>

    <div class="flex justify-end gap-4 border-t border-slate-200 dark:border-white/5 pt-6">
        <a href="{{ route('admin.workers.index') }}" class="px-6 py-2.5 rounded-xl text-sm font-medium text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white transition-colors">{{ __('Cancel') }}</a>
        <button type="submit" class="bg-gradient-to-r from-[#0ea5e9] to-[#3b82f6] hover:from-[#38bdf8] hover:to-[#60a5fa] text-white transition-all px-8 py-2.5 rounded-xl text-sm font-bold shadow-[0_0_20px_rgba(14,165,233,0.3)]">{{ __('Save Worker Profile') }}</button>
    </div>
</form>
@endsection
