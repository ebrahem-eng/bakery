@extends('layouts.Admin.App')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-white tracking-tight">{{ __('Create Custom Role') }}</h1>
    <p class="text-sm text-slate-400 mt-1">{{ __('Define a new authorization profile and assign capability thresholds.') }}</p>
</div>

<form action="{{ route('admin.roles.store') }}" method="POST" class="glass-panel p-6 rounded-2xl border border-white/5 max-w-4xl">
    @csrf
    
    <div class="mb-8">
        <label class="block text-sm font-medium text-slate-300 mb-2">{{ __('Role Name') }}</label>
        <input type="text" name="name" required class="glass-input block w-full px-4 py-3 border-transparent rounded-xl leading-5 bg-black/30 text-white placeholder-slate-500 focus:outline-none focus:bg-black/50 focus:border-[#eab308] focus:ring-1 focus:ring-[#eab308] transition-all" placeholder="{{ __('e.g. Supervisor, Manager, Cashier') }}">
        @error('name') <span class="text-red-400 text-xs mt-1 block">{{ $message }}</span> @enderror
    </div>

    <!-- Permissions Grid -->
    <div class="mb-8">
        <h3 class="text-lg font-semibold text-white mb-4">{{ __('Assign Capabilities') }}</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @forelse($permissions as $permission)
            <label class="flex items-center space-x-3 {{ app()->getLocale() == 'ar' ? 'space-x-reverse' : '' }} group cursor-pointer p-3 rounded-xl border border-white/5 hover:bg-white/5 transition-colors">
                <input type="checkbox" name="permissions[]" value="{{ $permission->name }}" class="form-checkbox h-5 w-5 text-[#eab308] rounded border-white/10 bg-black/30 focus:ring-[#eab308] focus:ring-offset-slate-900 transition-colors">
                <span class="text-sm text-slate-300 group-hover:text-white transition-colors">{{ __($permission->name) }}</span>
            </label>
            @empty
            <div class="col-span-full text-slate-500 italic text-sm">{{ __('No permissions registered in the system yet. Please run the Permissions Seeder.') }}</div>
            @endforelse
        </div>
        @error('permissions') <span class="text-red-400 text-xs mt-1 block">{{ $message }}</span> @enderror
    </div>

    <div class="flex justify-end gap-4 border-t border-white/5 pt-6">
        <a href="{{ route('admin.roles.index') }}" class="px-6 py-2.5 rounded-xl text-sm font-medium text-slate-300 hover:text-white hover:bg-white/5 transition-colors">{{ __('Cancel') }}</a>
        <button type="submit" class="bg-[#eab308]/10 text-[#eab308] border border-[#eab308]/30 hover:bg-[#eab308] hover:text-[#451a03] transition-all px-6 py-2.5 rounded-xl text-sm font-bold shadow-[0_0_15px_rgba(234,179,8,0.15)]">{{ __('Save Role') }}</button>
    </div>
</form>
@endsection
