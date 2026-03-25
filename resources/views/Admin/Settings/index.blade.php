@extends('layouts.Admin.App')

@section('content')
<div x-data="{ activeTab: '{{ request('tab', 'general') }}' }" x-cloak>

{{-- Page Header --}}
<div class="mb-6">
    <h1 class="text-2xl font-bold text-slate-900 dark:text-white tracking-tight">{{ __('Settings') }}</h1>
    <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">{{ __('Configure bakery information, currencies, and material categories.') }}</p>
</div>

{{-- Flash Messages --}}
@if(session('success'))
<div class="mb-6 px-4 py-3 bg-emerald-500/10 border border-emerald-500/20 text-emerald-600 dark:text-emerald-400 rounded-xl text-sm">
    {{ session('success') }}
</div>
@endif
@if(session('error'))
<div class="mb-6 px-4 py-3 bg-red-500/10 border border-red-500/20 text-red-600 dark:text-red-400 rounded-xl text-sm">
    {{ session('error') }}
</div>
@endif

{{-- ── Tab Navigation ────────────────────────────────────────────── --}}
<div class="flex gap-1 mb-6 p-1 glass-panel rounded-2xl border border-slate-200 dark:border-white/5 w-fit">
    <button @click="activeTab = 'general'"
        :class="activeTab === 'general' ? 'bg-amber-500 text-white shadow-lg shadow-amber-500/20' : 'text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-white/5'"
        class="px-5 py-2.5 rounded-xl text-sm font-bold transition-all flex items-center gap-2">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
        {{ __('General Info') }}
    </button>
    <button @click="activeTab = 'currencies'"
        :class="activeTab === 'currencies' ? 'bg-amber-500 text-white shadow-lg shadow-amber-500/20' : 'text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-white/5'"
        class="px-5 py-2.5 rounded-xl text-sm font-bold transition-all flex items-center gap-2">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        {{ __('Currencies') }}
    </button>
    <button @click="activeTab = 'categories'"
        :class="activeTab === 'categories' ? 'bg-amber-500 text-white shadow-lg shadow-amber-500/20' : 'text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-white/5'"
        class="px-5 py-2.5 rounded-xl text-sm font-bold transition-all flex items-center gap-2">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A2 2 0 013 12V7a4 4 0 014-4z"/></svg>
        {{ __('Material Categories') }}
    </button>
</div>

{{-- ════════════════════════════════════════════════════════════════ --}}
{{-- TAB 1 — General Bakery Info                                     --}}
{{-- ════════════════════════════════════════════════════════════════ --}}
<div x-show="activeTab === 'general'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0">
    <form action="{{ route('admin.settings.bakeryInfo') }}" method="POST" class="glass-panel rounded-2xl border border-slate-200 dark:border-white/5 overflow-hidden">
        @csrf
        <div class="p-4 bg-slate-50 dark:bg-black/20 border-b border-slate-200 dark:border-white/5">
            <h3 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
                <svg class="w-4 h-4 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                {{ __('Bakery Information') }}
            </h3>
        </div>
        <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
            {{-- Bakery Name --}}
            <div>
                <label class="block text-[10px] uppercase tracking-widest text-slate-500 font-bold mb-2">{{ __('Bakery Name') }}</label>
                <input type="text" name="bakery_name" value="{{ old('bakery_name', $bakeryName) }}"
                    placeholder="{{ __('e.g. Al-Sham Bakery') }}"
                    class="block w-full px-4 py-3 bg-white dark:bg-[#0f1115] border border-slate-200 dark:border-white/5 rounded-xl text-sm text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:border-amber-500/50 focus:ring-1 focus:ring-amber-500/50 transition-all">
                @error('bakery_name') <span class="text-red-400 text-xs mt-1 block">{{ $message }}</span> @enderror
            </div>
            {{-- Phone --}}
            <div>
                <label class="block text-[10px] uppercase tracking-widest text-slate-500 font-bold mb-2">{{ __('Phone Number') }}</label>
                <input type="text" name="bakery_phone" value="{{ old('bakery_phone', $bakeryPhone) }}"
                    placeholder="{{ __('e.g. +963 xxx xxx xxxx') }}"
                    class="block w-full px-4 py-3 bg-white dark:bg-[#0f1115] border border-slate-200 dark:border-white/5 rounded-xl text-sm text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:border-amber-500/50 focus:ring-1 focus:ring-amber-500/50 transition-all">
                @error('bakery_phone') <span class="text-red-400 text-xs mt-1 block">{{ $message }}</span> @enderror
            </div>
            {{-- Address --}}
            <div class="md:col-span-2">
                <label class="block text-[10px] uppercase tracking-widest text-slate-500 font-bold mb-2">{{ __('Address') }}</label>
                <textarea name="bakery_address" rows="3"
                    placeholder="{{ __('Full bakery address...') }}"
                    class="block w-full px-4 py-3 bg-white dark:bg-[#0f1115] border border-slate-200 dark:border-white/5 rounded-xl text-sm text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:border-amber-500/50 focus:ring-1 focus:ring-amber-500/50 transition-all resize-none">{{ old('bakery_address', $bakeryAddress) }}</textarea>
                @error('bakery_address') <span class="text-red-400 text-xs mt-1 block">{{ $message }}</span> @enderror
            </div>
            {{-- Default Language --}}
            <div>
                <label class="block text-[10px] uppercase tracking-widest text-slate-500 font-bold mb-2">{{ __('Default Language') }}</label>
                <select name="default_language"
                    class="block w-full px-4 py-3 bg-white dark:bg-[#0f1115] border border-slate-200 dark:border-white/5 rounded-xl text-sm text-slate-900 dark:text-white focus:outline-none focus:border-amber-500/50 focus:ring-1 focus:ring-amber-500/50 transition-all">
                    <option value="ar" {{ old('default_language', $defaultLang) == 'ar' ? 'selected' : '' }}>العربية</option>
                    <option value="en" {{ old('default_language', $defaultLang) == 'en' ? 'selected' : '' }}>English</option>
                </select>
            </div>
        </div>
        <div class="p-4 border-t border-slate-200 dark:border-white/5 bg-slate-50 dark:bg-black/20 flex justify-end">
            <button type="submit" class="bg-[#eab308]/10 text-[#eab308] border border-[#eab308]/30 hover:bg-[#eab308] hover:text-[#451a03] transition-all px-6 py-2.5 rounded-xl text-sm font-bold shadow-[0_0_15px_rgba(234,179,8,0.15)]">
                {{ __('Save Configuration') }}
            </button>
        </div>
    </form>
</div>

{{-- ════════════════════════════════════════════════════════════════ --}}
{{-- TAB 2 — Currencies                                             --}}
{{-- ════════════════════════════════════════════════════════════════ --}}
<div x-show="activeTab === 'currencies'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0">

    {{-- Add Currency Form --}}
    <div class="glass-panel rounded-2xl border border-slate-200 dark:border-white/5 overflow-hidden mb-6">
        <div class="p-4 bg-slate-50 dark:bg-black/20 border-b border-slate-200 dark:border-white/5">
            <h3 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
                <svg class="w-4 h-4 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                {{ __('Add Currency') }}
            </h3>
        </div>
        <form action="{{ route('admin.settings.currencies.store') }}" method="POST" class="p-4 grid grid-cols-1 sm:grid-cols-4 gap-4 items-end">
            @csrf
            <div>
                <label class="block text-[10px] uppercase tracking-widest text-slate-500 font-bold mb-2">{{ __('Currency Name') }}</label>
                <input type="text" name="name" required placeholder="{{ __('e.g. US Dollar') }}"
                    class="block w-full px-4 py-2.5 bg-white dark:bg-[#0f1115] border border-slate-200 dark:border-white/5 rounded-xl text-sm text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:border-amber-500/50 focus:ring-1 focus:ring-amber-500/50 transition-all">
            </div>
            <div>
                <label class="block text-[10px] uppercase tracking-widest text-slate-500 font-bold mb-2">{{ __('Currency Code') }}</label>
                <input type="text" name="code" required placeholder="{{ __('e.g. USD') }}"
                    class="block w-full px-4 py-2.5 bg-white dark:bg-[#0f1115] border border-slate-200 dark:border-white/5 rounded-xl text-sm text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:border-amber-500/50 focus:ring-1 focus:ring-amber-500/50 transition-all">
            </div>
            <div>
                <label class="block text-[10px] uppercase tracking-widest text-slate-500 font-bold mb-2">{{ __('Exchange Rate') }}</label>
                <input type="number" step="0.01" name="exchange_rate" required placeholder="1.00"
                    class="block w-full px-4 py-2.5 bg-white dark:bg-[#0f1115] border border-slate-200 dark:border-white/5 rounded-xl text-sm text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:border-amber-500/50 focus:ring-1 focus:ring-amber-500/50 transition-all">
            </div>
            <div>
                <button type="submit" class="w-full bg-[#eab308]/10 text-[#eab308] border border-[#eab308]/30 hover:bg-[#eab308] hover:text-[#451a03] transition-all px-4 py-2.5 rounded-xl text-sm font-bold shadow-[0_0_15px_rgba(234,179,8,0.15)]">
                    {{ __('Add') }}
                </button>
            </div>
        </form>
    </div>

    {{-- Currencies Table --}}
    <div class="glass-panel rounded-2xl border border-slate-200 dark:border-white/5 overflow-hidden">
        <div class="p-4 bg-slate-50 dark:bg-black/20 border-b border-slate-200 dark:border-white/5">
            <h3 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
                <svg class="w-4 h-4 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                {{ __('Currencies Management') }}
            </h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
                <thead>
                    <tr class="text-slate-500 dark:text-slate-400 text-xs uppercase tracking-wider border-b border-slate-200 dark:border-white/5">
                        <th class="py-4 px-4 font-medium">{{ __('Currency Name') }}</th>
                        <th class="py-4 px-4 font-medium">{{ __('Currency Code') }}</th>
                        <th class="py-4 px-4 font-medium">{{ __('Exchange Rate') }}</th>
                        <th class="py-4 px-4 font-medium">{{ __('Status') }}</th>
                        <th class="py-4 px-4 font-medium {{ app()->getLocale() == 'ar' ? 'text-left' : 'text-right' }}">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-white/5 text-sm text-slate-600 dark:text-slate-300">
                    @forelse($currencies as $currency)
                    <tr x-data="{ editing: false }" class="hover:bg-slate-50 dark:hover:bg-white/5 transition-colors">
                        {{-- Read Mode --}}
                        <template x-if="!editing">
                            <td class="py-3 px-4 font-medium text-slate-900 dark:text-white">{{ $currency->name }}</td>
                        </template>
                        <template x-if="!editing">
                            <td class="py-3 px-4">
                                <span class="px-2 py-1 bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 text-xs rounded border border-slate-200 dark:border-white/10 font-mono">{{ $currency->code }}</span>
                            </td>
                        </template>
                        <template x-if="!editing">
                            <td class="py-3 px-4 font-mono">{{ number_format($currency->exchange_rate, 2) }}</td>
                        </template>
                        <template x-if="!editing">
                            <td class="py-3 px-4">
                                @if($currency->is_default)
                                    <span class="px-2.5 py-1 bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 text-[10px] uppercase tracking-widest font-bold rounded-full border border-emerald-500/20">{{ __('Default') }}</span>
                                @else
                                    <span class="text-slate-400 text-xs">—</span>
                                @endif
                            </td>
                        </template>
                        <template x-if="!editing">
                            <td class="py-3 px-4 {{ app()->getLocale() == 'ar' ? 'text-left' : 'text-right' }}">
                                <button @click="editing = true" class="text-[#38bdf8] hover:text-blue-300 transition-colors text-xs font-bold {{ app()->getLocale() == 'ar' ? 'ml-3' : 'mr-3' }}">{{ __('Edit') }}</button>
                                @if(!$currency->is_default)
                                <form action="{{ route('admin.settings.currencies.destroy', $currency->id) }}" method="POST" class="inline-block" onsubmit="return confirm('{{ __('Are you sure?') }}');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-red-400 hover:text-red-300 transition-colors text-xs font-bold">{{ __('Delete') }}</button>
                                </form>
                                @endif
                            </td>
                        </template>

                        {{-- Edit Mode --}}
                        <template x-if="editing">
                            <td colspan="5" class="py-3 px-4">
                                <form action="{{ route('admin.settings.currencies.update', $currency->id) }}" method="POST" class="flex flex-wrap items-center gap-3">
                                    @csrf @method('PUT')
                                    <input type="text" name="name" value="{{ $currency->name }}" required class="px-3 py-2 bg-white dark:bg-[#0f1115] border border-slate-200 dark:border-white/10 rounded-lg text-sm text-slate-900 dark:text-white w-40 focus:outline-none focus:border-amber-500/50 focus:ring-1 focus:ring-amber-500/50">
                                    <input type="text" name="code" value="{{ $currency->code }}" required class="px-3 py-2 bg-white dark:bg-[#0f1115] border border-slate-200 dark:border-white/10 rounded-lg text-sm text-slate-900 dark:text-white w-24 font-mono focus:outline-none focus:border-amber-500/50 focus:ring-1 focus:ring-amber-500/50">
                                    <input type="number" step="0.01" name="exchange_rate" value="{{ $currency->exchange_rate }}" required class="px-3 py-2 bg-white dark:bg-[#0f1115] border border-slate-200 dark:border-white/10 rounded-lg text-sm text-slate-900 dark:text-white w-32 font-mono focus:outline-none focus:border-amber-500/50 focus:ring-1 focus:ring-amber-500/50">
                                    <label class="flex items-center gap-2 text-xs text-slate-400">
                                        <input type="checkbox" name="is_default" value="1" {{ $currency->is_default ? 'checked' : '' }} class="form-checkbox h-4 w-4 text-amber-500 rounded border-slate-300 dark:border-white/10 bg-white dark:bg-[#0f1115]">
                                        {{ __('Set as Default') }}
                                    </label>
                                    <button type="submit" class="px-4 py-2 bg-emerald-500/10 text-emerald-500 border border-emerald-500/30 rounded-lg text-xs font-bold hover:bg-emerald-500 hover:text-white transition-all">{{ __('Save') }}</button>
                                    <button type="button" @click="editing = false" class="px-4 py-2 text-slate-400 hover:text-white text-xs font-bold transition-colors">{{ __('Cancel') }}</button>
                                </form>
                            </td>
                        </template>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="py-8 text-center text-slate-500 italic">{{ __('No currencies registered.') }}</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- ════════════════════════════════════════════════════════════════ --}}
{{-- TAB 3 — Material Categories                                     --}}
{{-- ════════════════════════════════════════════════════════════════ --}}
<div x-show="activeTab === 'categories'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0">

    {{-- Add Category Form --}}
    <div class="glass-panel rounded-2xl border border-slate-200 dark:border-white/5 overflow-hidden mb-6">
        <div class="p-4 bg-slate-50 dark:bg-black/20 border-b border-slate-200 dark:border-white/5">
            <h3 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
                <svg class="w-4 h-4 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                {{ __('Add Category') }}
            </h3>
        </div>
        <form action="{{ route('admin.settings.categories.store') }}" method="POST" class="p-4 flex flex-wrap items-end gap-4">
            @csrf
            <div class="flex-1 min-w-[200px]">
                <label class="block text-[10px] uppercase tracking-widest text-slate-500 font-bold mb-2">{{ __('Category Name') }}</label>
                <input type="text" name="name" required placeholder="{{ __('e.g. Flour, Diesel, Yeast...') }}"
                    class="block w-full px-4 py-2.5 bg-white dark:bg-[#0f1115] border border-slate-200 dark:border-white/5 rounded-xl text-sm text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:border-amber-500/50 focus:ring-1 focus:ring-amber-500/50 transition-all">
            </div>
            <div>
                <button type="submit" class="bg-[#eab308]/10 text-[#eab308] border border-[#eab308]/30 hover:bg-[#eab308] hover:text-[#451a03] transition-all px-6 py-2.5 rounded-xl text-sm font-bold shadow-[0_0_15px_rgba(234,179,8,0.15)]">
                    {{ __('Add') }}
                </button>
            </div>
        </form>
    </div>

    {{-- Categories Table --}}
    <div class="glass-panel rounded-2xl border border-slate-200 dark:border-white/5 overflow-hidden">
        <div class="p-4 bg-slate-50 dark:bg-black/20 border-b border-slate-200 dark:border-white/5">
            <h3 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
                <svg class="w-4 h-4 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A2 2 0 013 12V7a4 4 0 014-4z"/></svg>
                {{ __('Material Categories') }}
            </h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
                <thead>
                    <tr class="text-slate-500 dark:text-slate-400 text-xs uppercase tracking-wider border-b border-slate-200 dark:border-white/5">
                        <th class="py-4 px-4 font-medium">#</th>
                        <th class="py-4 px-4 font-medium">{{ __('Category Name') }}</th>
                        <th class="py-4 px-4 font-medium">{{ __('Status') }}</th>
                        <th class="py-4 px-4 font-medium">{{ __('Suppliers') }}</th>
                        <th class="py-4 px-4 font-medium {{ app()->getLocale() == 'ar' ? 'text-left' : 'text-right' }}">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-white/5 text-sm text-slate-600 dark:text-slate-300">
                    @forelse($categories as $i => $category)
                    <tr x-data="{ editing: false }" class="hover:bg-slate-50 dark:hover:bg-white/5 transition-colors">
                        {{-- Read Mode --}}
                        <template x-if="!editing">
                            <td class="py-3 px-4 text-slate-400 text-xs">{{ $i + 1 }}</td>
                        </template>
                        <template x-if="!editing">
                            <td class="py-3 px-4 font-medium text-slate-900 dark:text-white">{{ $category->name }}</td>
                        </template>
                        <template x-if="!editing">
                            <td class="py-3 px-4">
                                @if($category->is_active)
                                    <span class="px-2.5 py-1 bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 text-[10px] uppercase tracking-widest font-bold rounded-full border border-emerald-500/20">{{ __('Active') }}</span>
                                @else
                                    <span class="px-2.5 py-1 bg-red-500/10 text-red-600 dark:text-red-400 text-[10px] uppercase tracking-widest font-bold rounded-full border border-red-500/20">{{ __('Inactive') }}</span>
                                @endif
                            </td>
                        </template>
                        <template x-if="!editing">
                            <td class="py-3 px-4">
                                <span class="text-slate-400 text-xs">{{ $category->suppliers()->count() }} {{ __('Suppliers') }}</span>
                            </td>
                        </template>
                        <template x-if="!editing">
                            <td class="py-3 px-4 {{ app()->getLocale() == 'ar' ? 'text-left' : 'text-right' }}">
                                <button @click="editing = true" class="text-[#38bdf8] hover:text-blue-300 transition-colors text-xs font-bold {{ app()->getLocale() == 'ar' ? 'ml-3' : 'mr-3' }}">{{ __('Edit') }}</button>
                                <form action="{{ route('admin.settings.categories.destroy', $category->id) }}" method="POST" class="inline-block" onsubmit="return confirm('{{ __('Are you sure?') }}');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-red-400 hover:text-red-300 transition-colors text-xs font-bold">{{ __('Delete') }}</button>
                                </form>
                            </td>
                        </template>

                        {{-- Edit Mode --}}
                        <template x-if="editing">
                            <td colspan="5" class="py-3 px-4">
                                <form action="{{ route('admin.settings.categories.update', $category->id) }}" method="POST" class="flex flex-wrap items-center gap-3">
                                    @csrf @method('PUT')
                                    <input type="text" name="name" value="{{ $category->name }}" required class="px-3 py-2 bg-white dark:bg-[#0f1115] border border-slate-200 dark:border-white/10 rounded-lg text-sm text-slate-900 dark:text-white w-48 focus:outline-none focus:border-amber-500/50 focus:ring-1 focus:ring-amber-500/50">
                                    <label class="flex items-center gap-2 text-xs text-slate-500 dark:text-slate-400">
                                        <input type="checkbox" name="is_active" value="1" {{ $category->is_active ? 'checked' : '' }} class="form-checkbox h-4 w-4 text-amber-500 rounded border-slate-300 dark:border-white/10 bg-white dark:bg-[#0f1115]">
                                        {{ __('Active') }}
                                    </label>
                                    <button type="submit" class="px-4 py-2 bg-emerald-500/10 text-emerald-500 border border-emerald-500/30 rounded-lg text-xs font-bold hover:bg-emerald-500 hover:text-white transition-all">{{ __('Save') }}</button>
                                    <button type="button" @click="editing = false" class="px-4 py-2 text-slate-400 hover:text-white text-xs font-bold transition-colors">{{ __('Cancel') }}</button>
                                </form>
                            </td>
                        </template>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="py-8 text-center text-slate-500 italic">{{ __('No categories registered.') }}</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

</div>
@endsection
