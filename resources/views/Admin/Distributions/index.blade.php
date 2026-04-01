@extends('layouts.Admin.App')

@section('content')
<div x-data="distributionFilters()" x-cloak>

<div class="mb-8 flex flex-col sm:flex-row justify-between items-start sm:items-end gap-4">
    <div>
        <h1 class="text-2xl lg:text-3xl font-bold text-slate-900 dark:text-white mb-1 tracking-tight">{{ __('Sales &') }} <span class="text-transparent bg-clip-text bg-gradient-to-r from-emerald-400 to-teal-500">{{ __('Distributions') }}</span></h1>
        <p class="text-sm text-slate-400">
            {{ __('Managing outbound supply for Active Work Day:') }} 
            <span class="font-bold text-amber-500">{{ $activeWorkDay->start_time->translatedFormat('Y-m-d h:i A') }}</span>
        </p>
    </div>
</div>

<!-- Alerts -->
@if(session('success_message'))
    <div class="mb-6 p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-sm flex items-center">
        <svg class="w-5 h-5 {{ app()->getLocale() == 'ar' ? 'ml-3' : 'mr-3' }} flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        {{ session('success_message') }}
    </div>
@endif
@if(session('error_message'))
    <div class="mb-6 p-4 rounded-xl bg-red-500/10 border border-red-500/20 text-red-400 text-sm flex items-center">
        <svg class="w-5 h-5 {{ app()->getLocale() == 'ar' ? 'ml-3' : 'mr-3' }} flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        {{ session('error_message') }}
    </div>
@endif
@if($errors->any())
    <div class="mb-6 p-4 rounded-xl bg-red-500/10 border border-red-500/20 text-red-400 text-sm">
        <ul class="list-disc list-inside">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<!-- ═══════════════ PROFESSIONAL FILTER BAR ═══════════════ -->
<div class="mb-8 glass-panel rounded-2xl border border-slate-200 dark:border-white/5 overflow-hidden">
    <div class="p-4 sm:p-5 bg-slate-50 dark:bg-black/20 border-b border-slate-200 dark:border-white/5 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
        <h3 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
            <svg class="w-4 h-4 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
            </svg>
            {{ __('Filter Distributors') }}
        </h3>
        <button @click="resetFilters()" class="text-[10px] uppercase tracking-widest font-bold text-slate-500 hover:text-amber-500 transition-colors">{{ __('Reset All') }}</button>
    </div>
    <div class="p-4 sm:p-5 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-4">
        <!-- Search by Name -->
        <div>
            <label class="block text-[10px] uppercase tracking-widest text-slate-500 font-bold mb-2">{{ __('Search Name') }}</label>
            <div class="relative">
                <svg class="w-4 h-4 text-slate-400 absolute top-1/2 -translate-y-1/2 {{ app()->getLocale() == 'ar' ? 'right-3' : 'left-3' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
                <input type="text" x-model="searchName" placeholder="{{ __('e.g. Ahmad, Ali...') }}" class="block w-full {{ app()->getLocale() == 'ar' ? 'pr-10 pl-4' : 'pl-10 pr-4' }} py-2.5 bg-white dark:bg-[#0f1115] border border-slate-200 dark:border-white/5 rounded-xl text-sm placeholder-slate-400 text-slate-900 dark:text-white focus:outline-none focus:border-emerald-500/50 focus:ring-1 focus:ring-emerald-500/50 transition-all">
            </div>
        </div>

        <!-- Currency Filter -->
        <div>
            <label class="block text-[10px] uppercase tracking-widest text-slate-500 font-bold mb-2">{{ __('Currency') }}</label>
            <select x-model="filterCurrency" class="block w-full px-4 py-2.5 bg-white dark:bg-[#0f1115] border border-slate-200 dark:border-white/5 rounded-xl text-sm text-slate-900 dark:text-white focus:outline-none focus:border-emerald-500/50 focus:ring-1 focus:ring-emerald-500/50 transition-all appearance-none">
                <option value="">{{ __('All Currencies') }}</option>
                @php $currencies = $distributors->pluck('currency.code')->unique(); @endphp
                @foreach($currencies as $code)
                    <option value="{{ $code }}">{{ $code }}</option>
                @endforeach
            </select>
        </div>

        <!-- Date Range -->
        <div>
            <label class="block text-[10px] uppercase tracking-widest text-slate-500 font-bold mb-2">{{ __('Date Range') }}</label>
            <div class="flex items-center gap-2">
                <input type="date" x-model="date_from" class="block w-full px-3 py-2 bg-white dark:bg-[#0f1115] border border-slate-200 dark:border-white/5 rounded-xl text-xs text-slate-900 dark:text-white focus:outline-none focus:border-emerald-500/50 transition-all">
                <span class="text-slate-400 text-xs">-</span>
                <input type="date" x-model="date_to" class="block w-full px-3 py-2 bg-white dark:bg-[#0f1115] border border-slate-200 dark:border-white/5 rounded-xl text-xs text-slate-900 dark:text-white focus:outline-none focus:border-emerald-500/50 transition-all">
            </div>
        </div>

        <!-- Balance Status -->
        <div>
            <label class="block text-[10px] uppercase tracking-widest text-slate-500 font-bold mb-2">{{ __('Balance Status') }}</label>
            <select x-model="filterBalance" class="block w-full px-4 py-2.5 bg-white dark:bg-[#0f1115] border border-slate-200 dark:border-white/5 rounded-xl text-sm text-slate-900 dark:text-white focus:outline-none focus:border-emerald-500/50 focus:ring-1 focus:ring-emerald-500/50 transition-all appearance-none">
                <option value="">{{ __('All') }}</option>
                <option value="has_debt">{{ __('Has Outstanding Debt') }}</option>
                <option value="clear">{{ __('Fully Settled') }}</option>
            </select>
        </div>

        <!-- Sort By -->
        <div>
            <label class="block text-[10px] uppercase tracking-widest text-slate-500 font-bold mb-2">{{ __('Sort By') }}</label>
            <select x-model="sortBy" class="block w-full px-4 py-2.5 bg-white dark:bg-[#0f1115] border border-slate-200 dark:border-white/5 rounded-xl text-sm text-slate-900 dark:text-white focus:outline-none focus:border-emerald-500/50 focus:ring-1 focus:ring-emerald-500/50 transition-all appearance-none">
                <option value="name_asc">{{ __('Name') }} (A → Z)</option>
                <option value="name_desc">{{ __('Name') }} (Z → A)</option>
                <option value="debt_desc">{{ __('Highest Debt') }}</option>
                <option value="debt_asc">{{ __('Lowest Debt') }}</option>
                <option value="sales_desc">{{ __('Most Active Today') }}</option>
            </select>
        </div>
    </div>
    <!-- Active Filters Summary -->
    <div class="px-4 sm:px-5 pb-4 sm:pb-5 flex flex-wrap items-center gap-3">
        <div class="text-[10px] uppercase tracking-widest font-bold text-slate-500">
            {{ __('Results:') }} <span class="text-emerald-500" x-text="filteredCount"></span> / <span x-text="totalCount"></span>
        </div>
        <template x-if="searchName">
            <span class="px-2.5 py-1 bg-emerald-500/10 text-emerald-500 border border-emerald-500/20 rounded-lg text-[10px] font-bold flex items-center gap-1">
                <span x-text="'{{ __('Name') }}: ' + searchName"></span>
                <button @click="searchName = ''" class="hover:text-white transition-colors">&times;</button>
            </span>
        </template>
        <template x-if="filterCurrency">
            <span class="px-2.5 py-1 bg-amber-500/10 text-amber-500 border border-amber-500/20 rounded-lg text-[10px] font-bold flex items-center gap-1">
                <span x-text="'{{ __('Currency') }}: ' + filterCurrency"></span>
                <button @click="filterCurrency = ''" class="hover:text-white transition-colors">&times;</button>
            </span>
        </template>
        <template x-if="filterBalance">
            <span class="px-2.5 py-1 bg-blue-500/10 text-blue-500 border border-blue-500/20 rounded-lg text-[10px] font-bold flex items-center gap-1">
                {{ __('Balance Status') }}
                <button @click="filterBalance = ''" class="hover:text-white transition-colors">&times;</button>
            </span>
        </template>
    </div>
</div>

<!-- ═══════════════ CARDS GRID ═══════════════ -->
<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
    @foreach($distributors as $distributor)
        @php
            $totalBilled = $distributor->distributions->sum('total_price');
            $totalPaidDistributions = $distributor->distributions->sum('amount_paid');
            $totalRefunds = $distributor->returns->sum('total_refund');
            $totalGeneralPayments = $distributor->transactions->where('type', 'payment')->sum('amount');
            $totalDiscounts = $distributor->transactions->where('type', 'discount')->sum('amount');
            $netDebt = $totalBilled - ($totalPaidDistributions + $totalRefunds + $totalGeneralPayments + $totalDiscounts);
            $todaySales = $distributor->distributions->where('work_day_id', $activeWorkDay->id)->sum('bundle_count');
        @endphp

        <div class="glass-panel rounded-2xl border border-white/5 overflow-hidden flex flex-col group relative distributor-card"
             data-name="{{ mb_strtolower($distributor->first_name . ' ' . $distributor->last_name) }}"
             data-currency="{{ $distributor->currency->code }}"
             data-debt="{{ $netDebt }}"
             data-today-sales="{{ $todaySales }}"
             data-date="{{ $distributor->created_at->format('Y-m-d') }}"
             x-show="shouldShow($el)"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100">
            
            <!-- Header -->
            <div class="p-5 border-b border-white/5 bg-black/10 flex justify-between items-start">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-gradient-to-tr from-emerald-500 to-teal-500 p-[1px]">
                        <div class="w-full h-full bg-[#121419] rounded-xl flex items-center justify-center text-emerald-500 font-bold text-lg">
                            {{ mb_substr($distributor->first_name, 0, 1) }}
                        </div>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-white">{{ $distributor->first_name }} {{ $distributor->last_name }}</h3>
                        <p class="text-xs text-slate-400 uppercase tracking-widest mt-0.5">{{ $distributor->title ?? __('Distributor') }}</p>
                    </div>
                </div>
                @if($netDebt > 0)
                <span class="px-2 py-0.5 bg-red-500/10 text-red-400 border border-red-500/20 rounded-md text-[9px] font-bold uppercase tracking-widest">{{ __('Owing') }}</span>
                @else
                <span class="px-2 py-0.5 bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 rounded-md text-[9px] font-bold uppercase tracking-widest">{{ __('Clear') }}</span>
                @endif
            </div>

            <!-- Stats Body -->
            <div class="p-5 flex-1 space-y-4">
                
                <div class="grid grid-cols-3 gap-3 pb-4 border-b border-white/5">
                    <div>
                        <p class="text-xs text-slate-500 uppercase tracking-wider mb-1">{{ __('Bundles Today') }}</p>
                        <p class="text-xl font-bold text-white">{{ number_format($todaySales) }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-slate-500 uppercase tracking-wider mb-1">{{ __('Total Billed') }}</p>
                        <p class="text-sm font-bold text-white">{{ number_format($totalBilled, 2) }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-slate-500 uppercase tracking-wider mb-1">{{ __('Currency') }}</p>
                        <p class="text-sm font-bold text-amber-500">{{ $distributor->currency->code }}</p>
                    </div>
                </div>

                <!-- Net Debt -->
                <div class="flex justify-between items-center bg-{{ $netDebt > 0 ? 'red' : 'emerald' }}-500/5 p-3 rounded-xl border border-{{ $netDebt > 0 ? 'red' : 'emerald' }}-500/10 text-sm">
                    <span class="text-{{ $netDebt > 0 ? 'red' : 'emerald' }}-400 font-medium">{{ __('Total Ledger Balance') }}</span>
                    <span class="text-{{ $netDebt > 0 ? 'red' : 'emerald' }}-400 font-bold text-lg">
                        {{ $netDebt > 0 ? '-' : '+' }}{{ number_format(abs($netDebt), 2) }} {{ $distributor->currency->code }}
                    </span>
                </div>
            </div>

            <!-- Actions Footer -->
            <div class="p-4 border-t border-white/5 bg-black/20 grid grid-cols-4 gap-2">
                <a href="{{ route('admin.distributors.show', $distributor) }}" class="py-2 bg-blue-500/20 hover:bg-blue-500/30 text-blue-400 border border-blue-500/20 rounded-xl font-bold text-[10px] uppercase tracking-wider transition-colors text-center">
                    {{ __('Profile') }}
                </a>
                @can('create distributions')
                <button type="button" x-data="" @click="$dispatch('open-sale-modal', { id: {{ $distributor->id }}, name: '{{ $distributor->first_name }} {{ $distributor->last_name }}', currency: '{{ $distributor->currency->code }}' })" class="py-2 bg-emerald-500/20 hover:bg-emerald-500/30 text-emerald-400 border border-emerald-500/20 rounded-xl font-bold text-[10px] uppercase tracking-wider transition-colors">
                    {{ __('Sell') }}
                </button>
                @endcan

                @can('edit returns')
                <button type="button" x-data="" @click="$dispatch('open-return-modal', { id: {{ $distributor->id }}, name: '{{ $distributor->first_name }} {{ $distributor->last_name }}', currency: '{{ $distributor->currency->code }}' })" class="py-2 bg-red-500/20 hover:bg-red-500/30 text-red-400 border border-red-500/20 rounded-xl font-bold text-[10px] uppercase tracking-wider transition-colors">
                    {{ __('Return') }}
                </button>
                @endcan

                @can('create accounts')
                <button type="button" x-data="" @click="$dispatch('open-pay-modal', { id: {{ $distributor->id }}, name: '{{ $distributor->first_name }} {{ $distributor->last_name }}', currency: '{{ $distributor->currency->code }}' })" class="py-2 bg-amber-500/20 hover:bg-amber-500/30 text-amber-500 border border-amber-500/20 rounded-xl font-bold text-[10px] uppercase tracking-wider transition-colors">
                    {{ __('Pay') }}
                </button>
                @endcan
            </div>
        </div>
    @endforeach
</div>

<!-- Empty state when filters exclude everything -->
<div x-show="filteredCount === 0" class="mt-8 text-center py-12 glass-panel rounded-2xl border border-slate-200 dark:border-white/5">
    <svg class="w-12 h-12 mx-auto text-slate-500 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
    </svg>
    <p class="text-slate-500 text-sm font-medium">{{ __('No distributors matched the active filters.') }}</p>
    <button @click="resetFilters()" class="mt-3 text-xs text-amber-500 font-bold hover:text-amber-400 transition-colors">{{ __('Reset All') }}</button>
</div>

</div>

<!-- Modals Wrapper -->
@include('Admin.Distributions.partials.modals')

<script>
function distributionFilters() {
    return {
        searchName: '',
        filterCurrency: '',
        filterBalance: '',
        date_from: '',
        date_to: '',
        sortBy: 'name_asc',
        totalCount: document.querySelectorAll('.distributor-card').length,
        
        get filteredCount() {
            return document.querySelectorAll('.distributor-card').length - 
                   document.querySelectorAll('.distributor-card[style*="display: none"]').length;
        },

        shouldShow(el) {
            const name = el.dataset.name || '';
            const currency = el.dataset.currency || '';
            const debt = parseFloat(el.dataset.debt || 0);
            const date = el.dataset.date || '';

            // Name filter
            if (this.searchName && !name.includes(this.searchName.toLowerCase())) return false;

            // Currency filter
            if (this.filterCurrency && currency !== this.filterCurrency) return false;

            // Balance filter
            if (this.filterBalance === 'has_debt' && debt <= 0) return false;
            if (this.filterBalance === 'clear' && debt > 0) return false;

            // Date filter
            if (this.date_from && date < this.date_from) return false;
            if (this.date_to && date > this.date_to) return false;

            return true;
        },

        resetFilters() {
            this.searchName = '';
            this.filterCurrency = '';
            this.filterBalance = '';
            this.date_from = '';
            this.date_to = '';
            this.sortBy = 'name_asc';
        }
    }
}
</script>

@endsection
