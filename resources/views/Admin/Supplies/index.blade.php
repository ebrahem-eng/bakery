@extends('layouts.Admin.App')

@section('content')
<div x-data="supplyFilter()" x-cloak>

<div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-end gap-4">
    <div>
        <h1 class="text-2xl font-bold text-slate-900 dark:text-white tracking-tight">{{ __('Supply Records') }}</h1>
        <p class="text-sm text-slate-500 mt-1">{{ __('Chronological ledger of raw material purchases.') }}</p>
    </div>
    @can('create supplies')
    <a href="{{ route('admin.supplies.create') }}" class="bg-[#0ea5e9]/10 text-[#0ea5e9] border border-[#0ea5e9]/30 hover:bg-[#0ea5e9] hover:text-white transition-all px-4 py-2 rounded-xl text-sm font-bold flex items-center shadow-[0_0_15px_rgba(14,165,233,0.15)]">
        <svg class="w-4 h-4 {{ app()->getLocale() == 'ar' ? 'ml-2' : 'mr-2' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
        {{ __('Register New Supply') }}
    </a>
    @endcan
</div>

@if(session('success'))
<div class="mb-6 px-4 py-3 bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 rounded-xl">{{ session('success') }}</div>
@endif

<!-- Filter Bar -->
<div class="mb-6 glass-panel rounded-2xl border border-slate-200 dark:border-white/5 overflow-hidden">
    <div class="p-4 bg-slate-50 dark:bg-black/20 border-b border-slate-200 dark:border-white/5 flex justify-between items-center">
        <h3 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
            <svg class="w-4 h-4 text-sky-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" /></svg>
            {{ __('Filter Supplies') }}
        </h3>
        <button @click="search = ''; filterPaid = ''; currentPage = 1" class="text-[10px] uppercase tracking-widest font-bold text-slate-500 hover:text-amber-500 transition-colors">{{ __('Reset All') }}</button>
    </div>
    <div class="p-4 grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div>
            <label class="block text-[10px] uppercase tracking-widest text-slate-500 font-bold mb-2">{{ __('Search Supplier') }}</label>
            <div class="relative">
                <svg class="w-4 h-4 text-slate-400 absolute top-1/2 -translate-y-1/2 {{ app()->getLocale() == 'ar' ? 'right-3' : 'left-3' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                <input type="text" x-model="search" @input="currentPage = 1" placeholder="{{ __('e.g. Ahmad, Ali...') }}" class="block w-full {{ app()->getLocale() == 'ar' ? 'pr-10 pl-4' : 'pl-10 pr-4' }} py-2.5 bg-white dark:bg-[#0f1115] border border-slate-200 dark:border-white/5 rounded-xl text-sm placeholder-slate-400 text-slate-900 dark:text-white focus:outline-none focus:border-sky-500/50 focus:ring-1 focus:ring-sky-500/50 transition-all">
            </div>
        </div>
        <div>
            <label class="block text-[10px] uppercase tracking-widest text-slate-500 font-bold mb-2">{{ __('Payment Status') }}</label>
            <select x-model="filterPaid" @change="currentPage = 1" class="block w-full px-4 py-2.5 bg-white dark:bg-[#0f1115] border border-slate-200 dark:border-white/5 rounded-xl text-sm text-slate-900 dark:text-white focus:outline-none focus:border-sky-500/50 focus:ring-1 focus:ring-sky-500/50 transition-all appearance-none">
                <option value="">{{ __('All') }}</option>
                <option value="paid">{{ __('Fully Paid') }}</option>
                <option value="unpaid">{{ __('Has Unpaid Amount') }}</option>
            </select>
        </div>
        <div class="flex items-end">
            <div class="text-[10px] uppercase tracking-widest font-bold text-slate-500">
                {{ __('Results:') }} <span class="text-sky-500" x-text="filteredRows().length"></span> / {{ count($supplies) }}
            </div>
        </div>
    </div>
</div>

<div class="glass-panel rounded-2xl border border-slate-200 dark:border-white/5 overflow-hidden">
    <div class="overflow-x-auto custom-scrollbar">
        <table class="w-full text-left border-collapse" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
            <thead>
                <tr class="text-slate-500 dark:text-slate-400 text-xs uppercase tracking-wider border-b border-slate-200 dark:border-white/5">
                    <th class="py-4 px-4 font-medium">{{ __('Date & Work Day') }}</th>
                    <th class="py-4 px-4 font-medium">{{ __('Supplier') }}</th>
                    <th class="py-4 px-4 font-medium">{{ __('Category Info') }}</th>
                    <th class="py-4 px-4 font-medium">{{ __('Metrics') }}</th>
                    <th class="py-4 px-4 font-medium text-right">{{ __('Finances') }}</th>
                    <th class="py-4 px-4 font-medium text-center">{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200 dark:divide-white/5 text-sm text-slate-600 dark:text-slate-300">
                @forelse($supplies as $i => $supply)
                <tr class="hover:bg-slate-50 dark:hover:bg-white/5 transition-colors table-row-item"
                    data-search="{{ mb_strtolower($supply->supplier->first_name . ' ' . $supply->supplier->last_name . ' ' . ($supply->category->name ?? '')) }}"
                    data-paid="{{ $supply->is_fully_paid ? 'paid' : 'unpaid' }}"
                    x-show="isVisible($el, {{ $i }})" x-transition>
                    <td class="py-3 px-4">
                        <div class="font-medium text-slate-900 dark:text-white">{{ $supply->created_at->translatedFormat('M d, Y h:i A') }}</div>
                        <div class="text-[11px] text-emerald-600 dark:text-emerald-400 font-bold tracking-wider uppercase mt-1">{{ __('Day ID:') }} {{ $supply->work_day_id }}</div>
                    </td>
                    <td class="py-3 px-4">
                        <div class="font-medium text-slate-900 dark:text-white">{{ $supply->supplier->first_name }} {{ $supply->supplier->last_name }}</div>
                        <div class="text-xs text-slate-500 mt-1">{{ $supply->supplier->title }}</div>
                    </td>
                    <td class="py-3 px-4">
                        <span class="px-2 py-1 bg-[#38bdf8]/10 text-[#0ea5e9] dark:text-[#38bdf8] text-xs rounded-md border border-[#0ea5e9]/20 font-bold shadow-sm whitespace-nowrap">{{ __($supply->category->name) }}</span>
                        @if($supply->material_type_name)
                            <div class="text-[10px] text-amber-500 mt-1 font-bold">{{ $supply->material_type_name }}</div>
                        @endif
                    </td>
                    <td class="py-3 px-4">
                        @if($supply->category->input_mode === 'bags_weight')
                            <div class="text-sm font-mono text-slate-900 dark:text-white font-bold">{{ $supply->bags_count }} {{ __('bags') }} × {{ $supply->bag_weight }} {{ __('kg') }}</div>
                            <div class="text-[10px] text-emerald-500 mt-1 font-mono font-bold">= {{ number_format($supply->quantity, 2) }} {{ __('kg') }}</div>
                            @if($supply->material_type_name)
                                <div class="text-[10px] text-amber-500 mt-1 font-bold">{{ $supply->material_type_name }}</div>
                            @endif
                        @elseif($supply->category->input_mode === 'cartons_molds')
                            <div class="text-sm font-mono text-slate-900 dark:text-white font-bold">{{ $supply->boxes_count }} {{ __('cartons') }} × {{ $supply->molds_per_carton }} {{ __('molds') }}</div>
                            <div class="text-[10px] text-emerald-500 mt-1 font-mono font-bold">= {{ number_format($supply->quantity, 0) }} {{ __('molds') }}</div>
                        @else
                            <div class="text-sm font-mono text-slate-900 dark:text-white font-bold">{{ number_format($supply->quantity, 2) }} {{ __($supply->category->unit ?? 'Units') }}</div>
                            @if($supply->bag_type)
                                <div class="text-[10px] text-amber-500 mt-1 font-bold">{{ $supply->bag_type }}</div>
                            @endif
                        @endif
                        <div class="text-[11px] text-slate-500 mt-1">
                            {{ __('At') }} <span class="font-bold text-slate-700 dark:text-slate-300">{{ number_format($supply->unit_price, 2) }} {{ $supply->currency->code }}</span> / {{ __($supply->category->input_mode === 'bags_weight' ? 'ton' : ($supply->category->input_mode === 'cartons_molds' ? 'carton' : ($supply->category->unit ?? 'unit'))) }}
                        </div>
                    </td>
                    <td class="py-3 px-4 text-right">
                        {{-- Supply Native Total --}}
                        <div class="text-sm font-bold text-slate-900 dark:text-white font-mono">
                            {{ number_format($supply->total_cost, 2) }} {{ $supply->currency->code }}
                        </div>
                        
                        {{-- System Currency Conversion (if different) --}}
                        @if($supply->currency->code !== $currencyCode)
                        <div class="text-[10px] text-slate-400 mt-0.5 font-mono">
                            (&approx; {{ number_format(\App\Models\Currency::convertAmount($supply->total_cost, $supply->exchange_rate), 0) }} {{ $currencyCode }})
                        </div>
                        @endif

                        {{-- Paid Status --}}
                        @if(!$supply->is_fully_paid)
                            <div class="text-[10px] text-red-500 mt-2 font-bold tracking-wider">
                                {{ __('Unpaid:') }} {{ number_format($supply->unpaid_amount, 2) }} {{ $supply->currency->code }}
                            </div>
                        @else
                            <div class="text-[10px] text-emerald-500 mt-2 font-bold tracking-wider">{{ __('Fully Paid') }}</div>
                        @endif
                        @if($supply->unloading_fee > 0)
                            <div class="text-[10px] text-amber-600 dark:text-amber-500 mt-1">{{ __('+ Unloading:') }} {{ number_format($supply->unloading_fee, 2) }} {{ $supply->unloadingFeeCurrency->code ?? '' }}</div>
                        @endif
                    </td>
                    <td class="py-3 px-4 text-center">
                        <a href="{{ route('admin.supplies.show', $supply->id) }}" class="inline-flex items-center justify-center p-2 bg-slate-100 hover:bg-slate-200 dark:bg-white/5 dark:hover:bg-white/10 text-[#0ea5e9] dark:text-[#38bdf8] rounded-lg transition-colors border border-slate-200 dark:border-white/5" title="{{ __('View Details') }}">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="py-8 text-center text-slate-500 italic">{{ __('No raw materials recorded in the system yet.') }}</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if(count($supplies) > 0)
    <div class="p-4 border-t border-slate-200 dark:border-white/5 bg-slate-50 dark:bg-black/20 flex flex-col sm:flex-row justify-between items-center gap-3">
        <div class="text-[10px] uppercase tracking-widest font-bold text-slate-500">
            {{ __('Page') }} <span x-text="currentPage"></span> / <span x-text="totalPages()"></span>
        </div>
        <div class="flex items-center gap-2">
            <button @click="prevPage()" :disabled="currentPage === 1" class="px-3 py-1.5 bg-white dark:bg-white/5 border border-slate-200 dark:border-white/10 rounded-lg text-xs font-bold text-slate-600 dark:text-slate-300 disabled:opacity-30 hover:border-sky-500/50 transition-all">{{ __('Previous') }}</button>
            <template x-for="p in totalPages()" :key="p">
                <button @click="currentPage = p" :class="currentPage === p ? 'bg-sky-500 text-white border-sky-500' : 'bg-white dark:bg-white/5 text-slate-600 dark:text-slate-300 border-slate-200 dark:border-white/10 hover:border-sky-500/50'" class="w-8 h-8 rounded-lg text-xs font-bold border transition-all" x-text="p"></button>
            </template>
            <button @click="nextPage()" :disabled="currentPage === totalPages()" class="px-3 py-1.5 bg-white dark:bg-white/5 border border-slate-200 dark:border-white/10 rounded-lg text-xs font-bold text-slate-600 dark:text-slate-300 disabled:opacity-30 hover:border-sky-500/50 transition-all">{{ __('Next') }}</button>
        </div>
    </div>
    @endif
</div>

</div>

<script>
function supplyFilter() {
    return {
        search: '', filterPaid: '', currentPage: 1, perPage: 10,
        filteredRows() {
            return [...document.querySelectorAll('.table-row-item')].filter(el => {
                const s = el.dataset.search || '';
                const paid = el.dataset.paid || '';
                if (this.search && !s.includes(this.search.toLowerCase())) return false;
                if (this.filterPaid && paid !== this.filterPaid) return false;
                return true;
            });
        },
        totalPages() { return Math.max(1, Math.ceil(this.filteredRows().length / this.perPage)); },
        isVisible(el, index) {
            const s = el.dataset.search || '';
            const paid = el.dataset.paid || '';
            if (this.search && !s.includes(this.search.toLowerCase())) return false;
            if (this.filterPaid && paid !== this.filterPaid) return false;
            const idx = this.filteredRows().indexOf(el);
            const start = (this.currentPage - 1) * this.perPage;
            return idx >= start && idx < start + this.perPage;
        },
        prevPage() { if (this.currentPage > 1) this.currentPage--; },
        nextPage() { if (this.currentPage < this.totalPages()) this.currentPage++; }
    }
}
</script>
@endsection
