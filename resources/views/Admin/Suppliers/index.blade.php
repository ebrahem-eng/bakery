@extends('layouts.Admin.App')

@section('content')
<div x-data="tableFilter()" x-cloak>

<div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-end gap-4">
    <div>
        <h1 class="text-2xl font-bold text-slate-900 dark:text-white tracking-tight">{{ __('Suppliers') }}</h1>
        <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">{{ __('Manage vendors mapping to raw material categories.') }}</p>
    </div>
    @can('create suppliers')
    <a href="{{ route('admin.suppliers.create') }}" class="bg-[#eab308]/10 text-[#eab308] border border-[#eab308]/30 hover:bg-[#eab308] hover:text-[#451a03] transition-all px-4 py-2 rounded-xl text-sm font-bold flex items-center shadow-[0_0_15px_rgba(234,179,8,0.15)]">
        <svg class="w-4 h-4 {{ app()->getLocale() == 'ar' ? 'ml-2' : 'mr-2' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
        {{ __('Add Supplier') }}
    </a>
    @endcan
</div>

@if(session('success'))
<div class="mb-6 px-4 py-3 bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 rounded-xl">
    {{ session('success') }}
</div>
@endif

<!-- Filter Bar -->
<div class="mb-6 glass-panel rounded-2xl border border-slate-200 dark:border-white/5 overflow-hidden">
    <div class="p-4 bg-slate-50 dark:bg-black/20 border-b border-slate-200 dark:border-white/5 flex justify-between items-center">
        <h3 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
            <svg class="w-4 h-4 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" /></svg>
            {{ __('Filter Suppliers') }}
        </h3>
        <button @click="search = ''; currentPage = 1" class="text-[10px] uppercase tracking-widest font-bold text-slate-500 hover:text-amber-500 transition-colors">{{ __('Reset All') }}</button>
    </div>
    <div class="p-4 grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
            <label class="block text-[10px] uppercase tracking-widest text-slate-500 font-bold mb-2">{{ __('Search Name') }}</label>
            <div class="relative">
                <svg class="w-4 h-4 text-slate-400 absolute top-1/2 -translate-y-1/2 {{ app()->getLocale() == 'ar' ? 'right-3' : 'left-3' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                <input type="text" x-model="search" @input="currentPage = 1" placeholder="{{ __('e.g. Ahmad, Ali...') }}" class="block w-full {{ app()->getLocale() == 'ar' ? 'pr-10 pl-4' : 'pl-10 pr-4' }} py-2.5 bg-white dark:bg-[#0f1115] border border-slate-200 dark:border-white/5 rounded-xl text-sm placeholder-slate-400 text-slate-900 dark:text-white focus:outline-none focus:border-amber-500/50 focus:ring-1 focus:ring-amber-500/50 transition-all">
            </div>
        </div>
        <div class="flex items-end">
            <div class="text-[10px] uppercase tracking-widest font-bold text-slate-500">
                {{ __('Results:') }} <span class="text-amber-500" x-text="filteredRows().length"></span> / {{ count($suppliers) }}
            </div>
        </div>
    </div>
</div>

<div class="glass-panel rounded-2xl border border-slate-200 dark:border-white/5 overflow-hidden">
    <div class="overflow-x-auto custom-scrollbar">
        <table class="w-full text-left border-collapse" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
            <thead>
                <tr class="text-slate-500 dark:text-slate-400 text-xs uppercase tracking-wider border-b border-slate-200 dark:border-white/5">
                    <th class="py-4 px-4 font-medium">{{ __('Name') }}</th>
                    <th class="py-4 px-4 font-medium">{{ __('Supplying Categories') }}</th>
                    <th class="py-4 px-4 font-medium">{{ __('Mobile Contacts') }}</th>
                    <th class="py-4 px-4 font-medium {{ app()->getLocale() == 'ar' ? 'text-left' : 'text-right' }}">{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200 dark:divide-white/5 text-sm text-slate-600 dark:text-slate-300">
                @forelse($suppliers as $i => $supplier)
                <tr class="hover:bg-slate-50 dark:hover:bg-white/5 transition-colors table-row-item"
                    data-search="{{ mb_strtolower($supplier->first_name . ' ' . $supplier->last_name . ' ' . $supplier->title) }}"
                    x-show="isVisible($el, {{ $i }})" x-transition>
                    <td class="py-3 px-4">
                        <div class="font-medium text-slate-900 dark:text-white">{{ $supplier->first_name }} {{ $supplier->last_name }}</div>
                        <div class="text-[11px] text-slate-500">{{ $supplier->title }}</div>
                    </td>
                    <td class="py-3 px-4">
                        <div class="flex flex-wrap gap-2">
                            @foreach($supplier->categories as $category)
                                <span class="px-2 py-1 bg-[#38bdf8]/10 text-[#38bdf8] text-xs rounded-md border border-[#38bdf8]/20">{{ __($category->name) }}</span>
                            @endforeach
                        </div>
                    </td>
                    <td class="py-3 px-4">
                        @foreach($supplier->mobiles as $mobile)
                            <div class="text-xs text-slate-500 dark:text-slate-400">{{ $mobile->mobile_number }}</div>
                        @endforeach
                    </td>
                    <td class="py-3 px-4 {{ app()->getLocale() == 'ar' ? 'text-left' : 'text-right' }}">
                        <a href="{{ route('admin.suppliers.show', $supplier->id) }}" class="text-[#38bdf8] hover:text-white transition-colors {{ app()->getLocale() == 'ar' ? 'ml-3' : 'mr-3' }}">{{ __('View Profile') }}</a>
                        @can('edit suppliers')
                        <a href="{{ route('admin.suppliers.edit', $supplier->id) }}" class="text-[#eab308] hover:text-white transition-colors {{ app()->getLocale() == 'ar' ? 'ml-3' : 'mr-3' }}">{{ __('Edit') }}</a>
                        @endcan
                        @can('delete suppliers')
                        <form action="{{ route('admin.suppliers.destroy', $supplier->id) }}" method="POST" class="inline-block" onsubmit="return confirm('{{ __('Delete Supplier completely?') }}');">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-red-400 hover:text-white transition-colors">{{ __('Delete') }}</button>
                        </form>
                        @endcan
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="py-8 text-center text-slate-500 italic">{{ __('No suppliers registered.') }}</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <!-- Pagination -->
    @if(count($suppliers) > 0)
    <div class="p-4 border-t border-slate-200 dark:border-white/5 bg-slate-50 dark:bg-black/20 flex flex-col sm:flex-row justify-between items-center gap-3">
        <div class="text-[10px] uppercase tracking-widest font-bold text-slate-500">
            {{ __('Page') }} <span x-text="currentPage"></span> / <span x-text="totalPages()"></span>
        </div>
        <div class="flex items-center gap-2">
            <button @click="prevPage()" :disabled="currentPage === 1" class="px-3 py-1.5 bg-white dark:bg-white/5 border border-slate-200 dark:border-white/10 rounded-lg text-xs font-bold text-slate-600 dark:text-slate-300 disabled:opacity-30 hover:border-amber-500/50 transition-all">{{ __('Previous') }}</button>
            <template x-for="p in totalPages()" :key="p">
                <button @click="currentPage = p" :class="currentPage === p ? 'bg-amber-500 text-white border-amber-500' : 'bg-white dark:bg-white/5 text-slate-600 dark:text-slate-300 border-slate-200 dark:border-white/10 hover:border-amber-500/50'" class="w-8 h-8 rounded-lg text-xs font-bold border transition-all" x-text="p"></button>
            </template>
            <button @click="nextPage()" :disabled="currentPage === totalPages()" class="px-3 py-1.5 bg-white dark:bg-white/5 border border-slate-200 dark:border-white/10 rounded-lg text-xs font-bold text-slate-600 dark:text-slate-300 disabled:opacity-30 hover:border-amber-500/50 transition-all">{{ __('Next') }}</button>
        </div>
    </div>
    @endif
</div>

</div>

<script>
function tableFilter() {
    return {
        search: '',
        currentPage: 1,
        perPage: 10,
        filteredRows() {
            const rows = document.querySelectorAll('.table-row-item');
            return [...rows].filter(el => {
                const s = el.dataset.search || '';
                return !this.search || s.includes(this.search.toLowerCase());
            });
        },
        totalPages() { return Math.max(1, Math.ceil(this.filteredRows().length / this.perPage)); },
        isVisible(el, index) {
            const s = el.dataset.search || '';
            if (this.search && !s.includes(this.search.toLowerCase())) return false;
            const filtered = this.filteredRows();
            const idx = filtered.indexOf(el);
            if (idx === -1) return false;
            const start = (this.currentPage - 1) * this.perPage;
            return idx >= start && idx < start + this.perPage;
        },
        prevPage() { if (this.currentPage > 1) this.currentPage--; },
        nextPage() { if (this.currentPage < this.totalPages()) this.currentPage++; }
    }
}
</script>
@endsection
