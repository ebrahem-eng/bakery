@extends('layouts.Admin.App')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-white tracking-tight">{{ __('Edit Supplier') }}</h1>
    <p class="text-sm text-slate-400 mt-1">{{ $supplier->first_name }} {{ $supplier->last_name }}</p>
</div>

<form action="{{ route('admin.suppliers.update', $supplier->id) }}" method="POST" class="glass-panel p-6 rounded-2xl border border-white/5 max-w-4xl" x-data="{ mobiles: {{ $supplier->mobiles->count() ? collect($supplier->mobiles->pluck('mobile_number'))->toJson() : '[\'\']' }} }">
    @csrf @method('PUT')
    
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        <div>
            <label class="block text-sm font-medium text-slate-300 mb-2">{{ __('First Name') }}</label>
            <input type="text" name="first_name" value="{{ old('first_name', $supplier->first_name) }}" required class="glass-input block w-full px-4 py-3 rounded-xl bg-black/30 text-white focus:outline-none focus:ring-1 focus:ring-[#38bdf8] transition-all">
            @error('first_name') <span class="text-red-400 text-xs mt-1 block">{{ $message }}</span> @enderror
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-300 mb-2">{{ __('Last Name') }}</label>
            <input type="text" name="last_name" value="{{ old('last_name', $supplier->last_name) }}" required class="glass-input block w-full px-4 py-3 rounded-xl bg-black/30 text-white focus:outline-none focus:ring-1 focus:ring-[#38bdf8] transition-all">
            @error('last_name') <span class="text-red-400 text-xs mt-1 block">{{ $message }}</span> @enderror
        </div>
        <div class="md:col-span-2">
            <label class="block text-sm font-medium text-slate-300 mb-2">{{ __('Company / Title (Optional)') }}</label>
            <input type="text" name="title" value="{{ old('title', $supplier->title) }}" class="glass-input block w-full px-4 py-3 rounded-xl bg-black/30 text-white focus:outline-none focus:ring-1 focus:ring-[#38bdf8] transition-all">
        </div>
    </div>

    <!-- Mapped Categories -->
    <div class="mb-8">
        <h3 class="text-sm font-semibold text-white mb-4">{{ __('Provided Materials') }}</h3>
        <div class="flex flex-wrap gap-4">
            @foreach($categories as $category)
            <label class="flex items-center space-x-2 {{ app()->getLocale() == 'ar' ? 'space-x-reverse' : '' }} cursor-pointer">
                <input type="checkbox" name="categories[]" value="{{ $category->id }}" {{ in_array($category->id, $supplier->categories->pluck('id')->toArray()) ? 'checked' : '' }} class="form-checkbox h-4 w-4 text-[#38bdf8] rounded border-white/10 bg-black/30 focus:ring-[#38bdf8] focus:ring-offset-slate-900 transition-colors">
                <span class="text-sm text-slate-300">{{ __($category->name) }}</span>
            </label>
            @endforeach
        </div>
        @error('categories') <span class="text-red-400 text-xs mt-1 block">{{ $message }}</span> @enderror
    </div>

    <!-- Dynamic Mobiles -->
    <div class="mb-8 p-4 rounded-xl border border-white/5 bg-white/5">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-sm font-semibold text-white">{{ __('Mobile Numbers') }}</h3>
            <button type="button" @click="mobiles.push('')" class="text-xs text-[#38bdf8] hover:text-[#7dd3fc]">{{ __('+ Add Number') }}</button>
        </div>
        <template x-for="(mobile, index) in mobiles" :key="index">
            <div class="flex gap-3 mb-3">
                <input type="text" x-model="mobiles[index]" name="mobiles[]" class="flex-1 glass-input px-4 py-2 rounded-xl bg-black/30 text-white focus:outline-none focus:ring-1 focus:ring-[#38bdf8] transition-all" placeholder="{{ __('Mobile Number') }}">
                <button type="button" @click="mobiles.length > 1 ? mobiles.splice(index, 1) : mobiles[0] = ''" class="px-3 py-2 text-slate-400 hover:text-red-400 bg-black/30 rounded-xl">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        </template>
    </div>

    <div class="flex justify-end gap-4 border-t border-white/5 pt-6">
        <a href="{{ route('admin.suppliers.index') }}" class="px-6 py-2.5 rounded-xl text-sm font-medium text-slate-300 hover:text-white hover:bg-white/5 transition-colors">{{ __('Cancel') }}</a>
        <button type="submit" class="bg-[#38bdf8]/10 text-[#38bdf8] border border-[#38bdf8]/30 hover:bg-[#38bdf8] hover:text-[#0f172a] transition-all px-6 py-2.5 rounded-xl text-sm font-bold shadow-[0_0_15px_rgba(56,189,248,0.15)]">{{ __('Update Supplier') }}</button>
    </div>
</form>
@endsection
