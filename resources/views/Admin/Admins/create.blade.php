@extends('layouts.Admin.App')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-white tracking-tight">{{ __('Add Admin Account') }}</h1>
</div>

<form action="{{ route('admin.manage_admins.store') }}" method="POST" enctype="multipart/form-data" class="glass-panel p-6 rounded-2xl border border-white/5 max-w-5xl" x-data="{ mobiles: [''] }">
    @csrf
    
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        <div>
            <label class="block text-sm font-medium text-slate-300 mb-2">{{ __('First Name') }}</label>
            <input type="text" name="first_name" required class="glass-input block w-full px-4 py-3 rounded-xl bg-black/30 text-white focus:outline-none focus:ring-1 focus:ring-[#eab308] transition-all">
            @error('first_name') <span class="text-red-400 text-xs mt-1 block">{{ $message }}</span> @enderror
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-300 mb-2">{{ __('Last Name') }}</label>
            <input type="text" name="last_name" required class="glass-input block w-full px-4 py-3 rounded-xl bg-black/30 text-white focus:outline-none focus:ring-1 focus:ring-[#eab308] transition-all">
            @error('last_name') <span class="text-red-400 text-xs mt-1 block">{{ $message }}</span> @enderror
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-300 mb-2">{{ __('Title') }}</label>
            <input type="text" name="title" class="glass-input block w-full px-4 py-3 rounded-xl bg-black/30 text-white focus:outline-none focus:ring-1 focus:ring-[#eab308] transition-all">
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-300 mb-2">{{ __('Email Address') }}</label>
            <input type="email" name="email" required class="glass-input block w-full px-4 py-3 rounded-xl bg-black/30 text-white focus:outline-none focus:ring-1 focus:ring-[#eab308] transition-all">
            @error('email') <span class="text-red-400 text-xs mt-1 block">{{ $message }}</span> @enderror
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-300 mb-2">{{ __('Password') }}</label>
            <input type="password" name="password" required minlength="6" class="glass-input block w-full px-4 py-3 rounded-xl bg-black/30 text-white focus:outline-none focus:ring-1 focus:ring-[#eab308] transition-all">
            @error('password') <span class="text-red-400 text-xs mt-1 block">{{ $message }}</span> @enderror
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-300 mb-2">{{ __('Gender') }}</label>
            <select name="gender" class="glass-input block w-full px-4 py-3 rounded-xl bg-[#0f172a] text-white focus:outline-none focus:ring-1 focus:ring-[#eab308] transition-all">
                <option value="male">{{ __('Male') }}</option>
                <option value="female">{{ __('Female') }}</option>
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-300 mb-2">{{ __('Age') }}</label>
            <input type="number" name="age" class="glass-input block w-full px-4 py-3 rounded-xl bg-black/30 text-white focus:outline-none focus:ring-1 focus:ring-[#eab308] transition-all">
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-300 mb-2">{{ __('Address') }}</label>
            <input type="text" name="address" class="glass-input block w-full px-4 py-3 rounded-xl bg-black/30 text-white focus:outline-none focus:ring-1 focus:ring-[#eab308] transition-all">
        </div>
        <div class="md:col-span-2 mt-4">
            <label class="block text-sm font-medium text-slate-300 mb-2">{{ __('Profile Picture') }}</label>
            <input type="file" name="img" accept="image/*" class="glass-input block w-full px-4 py-3 rounded-xl bg-black/30 text-white focus:outline-none focus:ring-1 focus:ring-[#eab308] transition-all file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-[#eab308] file:text-black hover:file:bg-[#fde047]">
            @error('img') <span class="text-red-400 text-xs mt-1 block">{{ $message }}</span> @enderror
        </div>
    </div>

    <!-- Dynamic Mobiles -->
    <div class="mb-8 p-4 rounded-xl border border-white/5 bg-white/5">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-sm font-semibold text-white">{{ __('Mobile Numbers') }}</h3>
            <button type="button" @click="mobiles.push('')" class="text-xs text-[#eab308] hover:text-[#fde047]">{{ __('+ Add Number') }}</button>
        </div>
        <template x-for="(mobile, index) in mobiles" :key="index">
            <div class="flex gap-3 mb-3">
                <input type="text" x-model="mobiles[index]" name="mobiles[]" class="flex-1 glass-input px-4 py-2 rounded-xl bg-black/30 text-white focus:outline-none focus:ring-1 focus:ring-[#eab308] transition-all" placeholder="{{ __('Mobile Number') }}">
                <button type="button" @click="mobiles.length > 1 ? mobiles.splice(index, 1) : mobiles[0] = ''" class="px-3 py-2 text-slate-400 hover:text-red-400 bg-black/30 rounded-xl">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        </template>
    </div>

    <!-- Assigned Roles -->
    <div class="mb-8">
        <h3 class="text-sm font-semibold text-white mb-4">{{ __('Assign Role') }}</h3>
        <div class="flex flex-wrap gap-4">
            @foreach($roles as $role)
            <label class="flex items-center space-x-2 {{ app()->getLocale() == 'ar' ? 'space-x-reverse' : '' }} cursor-pointer">
                <input type="checkbox" name="roles[]" value="{{ $role->name }}" class="form-checkbox h-4 w-4 text-[#eab308] rounded border-white/10 bg-black/30 focus:ring-[#eab308] focus:ring-offset-slate-900 transition-colors">
                <span class="text-sm text-slate-300">{{ $role->name }}</span>
            </label>
            @endforeach
        </div>
    </div>

    <div class="flex justify-end gap-4 border-t border-white/5 pt-6">
        <a href="{{ route('admin.manage_admins.index') }}" class="px-6 py-2.5 rounded-xl text-sm font-medium text-slate-300 hover:text-white hover:bg-white/5 transition-colors">{{ __('Cancel') }}</a>
        <button type="submit" class="bg-[#eab308]/10 text-[#eab308] border border-[#eab308]/30 hover:bg-[#eab308] hover:text-[#451a03] transition-all px-6 py-2.5 rounded-xl text-sm font-bold shadow-[0_0_15px_rgba(234,179,8,0.15)]">{{ __('Create Account') }}</button>
    </div>
</form>
@endsection
