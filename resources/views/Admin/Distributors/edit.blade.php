@extends('layouts.Admin.App')

@section('content')
<div class="mb-8 flex items-center justify-between">
    <div>
        <h1 class="text-2xl font-bold text-white mb-1">{{ __('Edit') }} <span class="text-transparent bg-clip-text bg-gradient-to-r from-amber-400 to-orange-500">{{ __('Distributor') }}</span></h1>
        <p class="text-sm text-slate-400">{{ __('Modify existing sales contacts and debt currency logic.') }}</p>
    </div>
    <a href="{{ route('admin.distributors.index') }}" class="text-slate-400 hover:text-white transition-colors text-sm font-medium flex items-center">
        <svg class="w-4 h-4 {{ app()->getLocale() == 'ar' ? 'ml-1 rotate-180' : 'mr-1' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
        </svg>
        {{ __('Back') }}
    </a>
</div>

@if($errors->any())
    <div class="mb-6 p-4 rounded-xl bg-red-500/10 border border-red-500/20 text-red-400 text-sm">
        <ul class="list-disc list-inside">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form action="{{ route('admin.distributors.update', $distributor) }}" method="POST" class="glass-panel rounded-2xl p-6 border border-white/5">
    @csrf
    @method('PUT')
    
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        <!-- First Name -->
        <div>
            <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">{{ __('First Name') }}</label>
            <input type="text" name="first_name" value="{{ old('first_name', $distributor->first_name) }}" required class="block w-full px-4 py-3 bg-[#0f1115] border border-white/5 rounded-xl text-sm text-white focus:outline-none focus:border-amber-500/50 focus:ring-1 focus:ring-amber-500/50 transition-all">
        </div>

        <!-- Last Name -->
        <div>
            <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">{{ __('Last Name') }}</label>
            <input type="text" name="last_name" value="{{ old('last_name', $distributor->last_name) }}" required class="block w-full px-4 py-3 bg-[#0f1115] border border-white/5 rounded-xl text-sm text-white focus:outline-none focus:border-amber-500/50 focus:ring-1 focus:ring-amber-500/50 transition-all">
        </div>

        <!-- Title -->
        <div>
            <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">{{ __('Title (Optional)') }}</label>
            <input type="text" name="title" value="{{ old('title', $distributor->title) }}" class="block w-full px-4 py-3 bg-[#0f1115] border border-white/5 rounded-xl text-sm text-white focus:outline-none focus:border-amber-500/50 focus:ring-1 focus:ring-amber-500/50 transition-all">
        </div>

        <!-- Default Currency -->
        <div>
            <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">{{ __('Debt Currency') }}</label>
            <select name="preferred_currency_id" required class="block w-full px-4 py-3 bg-[#0f1115] border border-white/5 rounded-xl text-sm text-white focus:border-amber-500/50 focus:ring-1 focus:ring-amber-500/50 transition-all appearance-none">
                @foreach($currencies as $currency)
                    <option value="{{ $currency->id }}" {{ old('preferred_currency_id', $distributor->preferred_currency_id) == $currency->id ? 'selected' : '' }}>{{ $currency->name }} ({{ $currency->code }})</option>
                @endforeach
            </select>
        </div>
    </div>

    <!-- Mobile Numbers via Alpine.js -->
    @php
        $existingMobiles = $distributor->mobiles->pluck('number')->toArray();
        if(empty($existingMobiles)) $existingMobiles = [''];
    @endphp
    <div class="mb-8" x-data="{ mobiles: {{ json_encode($existingMobiles) }} }">
        <div class="flex items-center justify-between mb-2">
            <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider">{{ __('Contact Numbers') }}</label>
            <button type="button" @click="mobiles.push('')" class="text-xs font-semibold text-amber-500 hover:text-amber-400 flex items-center transition-colors">
                <svg class="w-4 h-4 {{ app()->getLocale() == 'ar' ? 'ml-1' : 'mr-1' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                {{ __('Add New Contact') }}
            </button>
        </div>
        <template x-for="(mobile, index) in mobiles" :key="index">
            <div class="flex items-center gap-3 mb-3">
                <input type="text" x-model="mobiles[index]" :name="`mobiles[]`" class="flex-1 px-4 py-3 bg-[#0f1115] border border-white/5 rounded-xl text-sm text-white focus:outline-none focus:border-amber-500/50 focus:ring-1 focus:ring-amber-500/50 transition-all" placeholder="{{ __('Enter mobile number') }}">
                <button type="button" @click="mobiles.splice(index, 1)" x-show="mobiles.length > 1" class="p-3 bg-red-400/10 text-red-400 hover:bg-red-400/20 rounded-xl transition-colors">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        </template>
    </div>

    <!-- Notes -->
    <div class="mb-8">
        <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">{{ __('Notes (Optional)') }}</label>
        <textarea name="notes" rows="3" class="block w-full px-4 py-3 bg-[#0f1115] border border-white/5 rounded-xl text-sm text-white focus:outline-none focus:border-amber-500/50 focus:ring-1 focus:ring-amber-500/50 transition-all">{{ old('notes', $distributor->notes) }}</textarea>
    </div>

    <div class="pt-6 border-t border-white/5 flex justify-end gap-3">
        <a href="{{ route('admin.distributors.index') }}" class="px-6 py-2.5 rounded-xl border border-white/10 text-slate-300 hover:bg-white/5 text-sm font-bold transition-colors">{{ __('Cancel') }}</a>
        <button type="submit" class="bg-gradient-to-r from-amber-500 to-orange-500 hover:from-amber-400 hover:to-orange-400 text-[#0f1115] px-6 py-2.5 rounded-xl text-sm font-bold shadow-[0_0_15px_rgba(245,158,11,0.3)] transition-all">
            {{ __('Update Configuration') }}
        </button>
    </div>
</form>
@endsection
