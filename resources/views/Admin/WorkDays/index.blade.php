@extends('layouts.Admin.App')

@section('content')
<div x-data="wdFilter()" x-cloak>

<div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-end gap-4">
    <div>
        <h1 class="text-2xl font-bold text-slate-900 dark:text-white tracking-tight">{{ __('Work Days Management') }}</h1>
        <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">{{ __('Manage and track chronological accounting periods.') }}</p>
    </div>
    @if(!$activeWorkDay)
    @can('create work days')
    <form action="{{ route('admin.work_days.store') }}" method="POST" class="inline-block">
        @csrf
        <button type="submit" class="bg-[#eab308]/10 text-[#eab308] border border-[#eab308]/30 hover:bg-[#eab308] hover:text-[#451a03] transition-all px-4 py-2 rounded-xl text-sm font-bold flex items-center shadow-[0_0_15px_rgba(234,179,8,0.15)]">
            <svg class="w-4 h-4 {{ app()->getLocale() == 'ar' ? 'ml-2' : 'mr-2' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
            {{ __('Start New Work Day') }}
        </button>
    </form>
    @endcan
    @else
    @can('edit work days')
    <a href="{{ route('admin.work_days.showCloseForm', $activeWorkDay->id) }}" class="bg-red-500/10 text-red-500 border border-red-500/30 hover:bg-red-500 hover:text-white transition-all px-4 py-2 rounded-xl text-sm font-medium flex items-center shadow-[0_0_15px_rgba(239,68,68,0.15)]">
        <svg class="w-4 h-4 {{ app()->getLocale() == 'ar' ? 'ml-2' : 'mr-2' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 10a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1h-4a1 1 0 01-1-1v-4z" />
        </svg>
        {{ __('Close Active Work Day') }}
    </a>
    @endcan
    @endif
</div>

@if(session('success'))
<div class="mb-6 px-4 py-3 bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 rounded-xl">{{ session('success') }}</div>
@endif
@if(session('error'))
<div class="mb-6 px-4 py-3 bg-red-500/10 border border-red-500/20 text-red-400 rounded-xl">{{ session('error') }}</div>
@endif

<!-- Filter Bar -->
<div class="mb-6 glass-panel rounded-2xl border border-slate-200 dark:border-white/5 overflow-hidden">
    <div class="p-4 bg-slate-50 dark:bg-black/20 border-b border-slate-200 dark:border-white/5 flex justify-between items-center">
        <h3 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
            <svg class="w-4 h-4 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" /></svg>
            {{ __('Filter Work Days') }}
        </h3>
        <button @click="filterStatus = ''; currentPage = 1" class="text-[10px] uppercase tracking-widest font-bold text-slate-500 hover:text-amber-500 transition-colors">{{ __('Reset All') }}</button>
    </div>
    <div class="p-4 grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
            <label class="block text-[10px] uppercase tracking-widest text-slate-500 font-bold mb-2">{{ __('Status') }}</label>
            <select x-model="filterStatus" @change="currentPage = 1" class="block w-full px-4 py-2.5 bg-white dark:bg-[#0f1115] border border-slate-200 dark:border-white/5 rounded-xl text-sm text-slate-900 dark:text-white focus:outline-none focus:border-amber-500/50 focus:ring-1 focus:ring-amber-500/50 transition-all appearance-none">
                <option value="">{{ __('All') }}</option>
                <option value="active">{{ __('Active') }}</option>
                <option value="closed">{{ __('Closed') }}</option>
            </select>
        </div>
        <div class="flex items-end">
            <div class="text-[10px] uppercase tracking-widest font-bold text-slate-500">
                {{ __('Results:') }} <span class="text-amber-500" x-text="filteredRows().length"></span> / {{ count($workDays) }}
            </div>
        </div>
    </div>
</div>

<div class="glass-panel rounded-2xl border border-slate-200 dark:border-white/5 overflow-hidden">
    <div class="overflow-x-auto custom-scrollbar">
        <table class="w-full text-left border-collapse" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
            <thead>
                <tr class="text-slate-500 dark:text-slate-400 text-xs uppercase tracking-wider border-b border-slate-200 dark:border-white/5">
                    <th class="py-4 px-4 font-medium">#</th>
                    <th class="py-4 px-4 font-medium">{{ __('Start Time') }}</th>
                    <th class="py-4 px-4 font-medium">{{ __('End Time') }}</th>
                    <th class="py-4 px-4 font-medium">{{ __('Status') }}</th>
                    <th class="py-4 px-4 font-medium">{{ __('Opened By') }}</th>
                    <th class="py-4 px-4 font-medium">{{ __('Closed By') }}</th>
                    <th class="py-4 px-4 font-medium {{ app()->getLocale() == 'ar' ? 'text-left' : 'text-right' }}">{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200 dark:divide-white/5 text-sm text-slate-600 dark:text-slate-300">
                @forelse($workDays as $i => $day)
                <tr class="hover:bg-slate-50 dark:hover:bg-white/5 transition-colors table-row-item"
                    data-status="{{ $day->status }}"
                    x-show="isVisible($el, {{ $i }})" x-transition>
                    <td class="py-3 px-4 font-medium text-slate-900 dark:text-white">{{ $day->id }}</td>
                    <td class="py-3 px-4">{{ $day->start_time->format('Y-m-d H:i') }}</td>
                    <td class="py-3 px-4">{{ $day->end_time ? $day->end_time->format('Y-m-d H:i') : '--' }}</td>
                    <td class="py-3 px-4">
                        @if($day->status == 'active')
                            <span class="px-2 py-1 bg-emerald-500/20 text-emerald-400 text-xs rounded-md border border-emerald-500/20 animate-pulse">{{ __('Active') }}</span>
                        @else
                            <span class="px-2 py-1 bg-slate-500/20 text-slate-400 text-xs rounded-md border border-slate-500/20">{{ __('Closed') }}</span>
                        @endif
                    </td>
                    <td class="py-3 px-4">{{ $day->openedBy->first_name ?? '--' }}</td>
                    <td class="py-3 px-4">{{ $day->closedBy->first_name ?? '--' }}</td>
                    <td class="py-3 px-4 {{ app()->getLocale() == 'ar' ? 'text-left' : 'text-right' }}">
                        <a href="{{ route('admin.work_days.show', $day->id) }}" class="text-[#0ea5e9] dark:text-[#38bdf8] hover:text-amber-600 dark:hover:text-white transition-colors font-medium">{{ __('View Log') }}</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="py-8 text-center text-slate-500 italic">{{ __('No work days found.') }}</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if(count($workDays) > 0)
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
function wdFilter() {
    return {
        filterStatus: '', currentPage: 1, perPage: 10,
        filteredRows() {
            return [...document.querySelectorAll('.table-row-item')].filter(el => !this.filterStatus || (el.dataset.status || '') === this.filterStatus);
        },
        totalPages() { return Math.max(1, Math.ceil(this.filteredRows().length / this.perPage)); },
        isVisible(el, index) {
            if (this.filterStatus && (el.dataset.status || '') !== this.filterStatus) return false;
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
