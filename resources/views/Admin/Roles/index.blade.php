@extends('layouts.Admin.App')

@section('content')
<div x-data="tableFilter()" x-cloak>

<div class="mb-6 flex justify-between items-end">
    <div>
        <h1 class="text-2xl font-bold text-slate-900 dark:text-white tracking-tight">{{ __('System Roles') }}</h1>
        <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">{{ __('Manage authorization thresholds and custom capability scopes.') }}</p>
    </div>
    <a href="{{ route('admin.roles.create') }}" class="bg-[#eab308]/10 text-[#eab308] border border-[#eab308]/30 hover:bg-[#eab308] hover:text-[#451a03] transition-all px-4 py-2 rounded-xl text-sm font-bold flex items-center shadow-[0_0_15px_rgba(234,179,8,0.15)]">
        <svg class="w-4 h-4 {{ app()->getLocale() == 'ar' ? 'ml-2' : 'mr-2' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
        {{ __('Create Role') }}
    </a>
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
            {{ __('Filter') }}
        </h3>
        <button @click="search = ''; date_from = ''; date_to = ''; currentPage = 1" class="text-[10px] uppercase tracking-widest font-bold text-slate-500 hover:text-amber-500 transition-colors">{{ __('Reset All') }}</button>
    </div>
    <div class="p-4 grid grid-cols-1 sm:grid-cols-4 gap-4">
        <div>
            <label class="block text-[10px] uppercase tracking-widest text-slate-500 font-bold mb-2">{{ __('Search Role Name') }}</label>
            <div class="relative">
                <svg class="w-4 h-4 text-slate-400 absolute top-1/2 -translate-y-1/2 {{ app()->getLocale() == 'ar' ? 'right-3' : 'left-3' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                <input type="text" x-model="search" @input="currentPage = 1" placeholder="{{ __('e.g. Super Admin...') }}" class="block w-full {{ app()->getLocale() == 'ar' ? 'pr-10 pl-4' : 'pl-10 pr-4' }} py-2.5 bg-white dark:bg-[#0f1115] border border-slate-200 dark:border-white/5 rounded-xl text-sm placeholder-slate-400 text-slate-900 dark:text-white focus:outline-none focus:border-amber-500/50 focus:ring-1 focus:ring-amber-500/50 transition-all">
            </div>
        </div>
        <div class="sm:col-span-2">
            <label class="block text-[10px] uppercase tracking-widest text-slate-500 font-bold mb-2">{{ __('Date Range') }}</label>
            <div class="flex items-center gap-2">
                <input type="date" x-model="date_from" @change="currentPage = 1" class="block w-full px-3 py-2 bg-white dark:bg-[#0f1115] border border-slate-200 dark:border-white/5 rounded-xl text-xs text-slate-900 dark:text-white focus:outline-none focus:border-amber-500/50 transition-all">
                <span class="text-slate-400 text-xs">-</span>
                <input type="date" x-model="date_to" @change="currentPage = 1" class="block w-full px-3 py-2 bg-white dark:bg-[#0f1115] border border-slate-200 dark:border-white/5 rounded-xl text-xs text-slate-900 dark:text-white focus:outline-none focus:border-amber-500/50 transition-all">
            </div>
        </div>
        <div class="flex items-end justify-end">
            <div class="text-[10px] uppercase tracking-widest font-bold text-slate-500">
                {{ __('Results:') }} <span class="text-amber-500" x-text="filteredRows().length"></span> / {{ count($roles) }}
            </div>
        </div>
    </div>
</div>

<div class="glass-panel rounded-2xl border border-slate-200 dark:border-white/5 overflow-hidden">
    <div class="overflow-x-auto custom-scrollbar">
        <table class="w-full text-left border-collapse" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
            <thead>
                <tr class="text-slate-500 dark:text-slate-400 text-xs uppercase tracking-wider border-b border-slate-200 dark:border-white/5">
                    <th class="py-4 px-4 font-medium">{{ __('Role Name') }}</th>
                    <th class="py-4 px-4 font-medium">{{ __('Assigned Permissions') }}</th>
                    <th class="py-4 px-4 font-medium {{ app()->getLocale() == 'ar' ? 'text-left' : 'text-right' }}">{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200 dark:divide-white/5 text-sm text-slate-600 dark:text-slate-300">
                @forelse($roles as $i => $role)
                <tr class="hover:bg-slate-50 dark:hover:bg-white/5 transition-colors table-row-item"
                    data-search="{{ mb_strtolower($role->name) }}"
                    data-date="{{ $role->created_at->format('Y-m-d') }}"
                    x-show="isVisible($el, {{ $i }})" x-transition>
                    <td class="py-3 px-4 font-medium text-slate-900 dark:text-white">{{ __($role->name) }}</td>
                    <td class="py-3 px-4">
                        <div class="flex flex-wrap gap-2">
                            @foreach($role->permissions->take(5) as $perm)
                                <span class="px-2 py-1 bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 text-[10px] rounded border border-slate-200 dark:border-white/10">{{ __($perm->name) }}</span>
                            @endforeach
                            @if($role->permissions->count() > 5)
                                <span class="px-2 py-1 bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400 text-[10px] rounded border border-slate-200 dark:border-white/5">+{{ $role->permissions->count() - 5 }} {{ __('more') }}</span>
                            @endif
                        </div>
                    </td>
                    <td class="py-3 px-4 {{ app()->getLocale() == 'ar' ? 'text-left' : 'text-right' }} w-32">
                        <a href="{{ route('admin.roles.edit', $role->id) }}" class="text-[#38bdf8] hover:text-white transition-colors {{ app()->getLocale() == 'ar' ? 'ml-3' : 'mr-3' }}">{{ __('Edit') }}</a>
                        <form action="{{ route('admin.roles.destroy', $role->id) }}" method="POST" class="inline-block" onsubmit="return confirm('{{ __('Are you sure?') }}');">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-red-400 hover:text-white transition-colors">{{ __('Delete') }}</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="3" class="py-8 text-center text-slate-500 italic">{{ __('No custom roles found.') }}</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if(count($roles) > 0)
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
        date_from: '',
        date_to: '',
        currentPage: 1,
        perPage: 10,
        filteredRows() {
            const rows = document.querySelectorAll('.table-row-item');
            return [...rows].filter(el => {
                const s = el.dataset.search || '';
                const date = el.dataset.date || '';

                if (this.search && !s.includes(this.search.toLowerCase())) return false;
                if (this.date_from && date < this.date_from) return false;
                if (this.date_to && date > this.date_to) return false;
                return true;
            });
        },
        totalPages() { return Math.max(1, Math.ceil(this.filteredRows().length / this.perPage)); },
        isVisible(el, index) {
            const s = el.dataset.search || '';
            const date = el.dataset.date || '';

            if (this.search && !s.includes(this.search.toLowerCase())) return false;
            if (this.date_from && date < this.date_from) return false;
            if (this.date_to && date > this.date_to) return false;

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
