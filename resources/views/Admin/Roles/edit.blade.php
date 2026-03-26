@extends('layouts.Admin.App')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-white tracking-tight">{{ __('Edit Role') }}: {{ __($role->name) }}</h1>
</div>

<form action="{{ route('admin.roles.update', $role->id) }}" method="POST" class="glass-panel p-6 rounded-2xl border border-white/5 max-w-4xl">
    @csrf @method('PUT')
    
    <div class="mb-8">
        <label class="block text-sm font-medium text-slate-300 mb-2">{{ __('Role Name') }}</label>
        <input type="text" name="name" value="{{ old('name', $role->name) }}" required class="glass-input block w-full px-4 py-3 border-transparent rounded-xl leading-5 bg-black/30 text-white placeholder-slate-500 focus:outline-none focus:bg-black/50 focus:border-[#38bdf8] focus:ring-1 focus:ring-[#38bdf8] transition-all">
        @error('name') <span class="text-red-400 text-xs mt-1 block">{{ $message }}</span> @enderror
    </div>

    <!-- Permissions Grid -->
    <div class="mb-8">
        <h3 class="text-lg font-semibold text-white mb-4">{{ __('Assign Capabilities') }}</h3>
        
        <div class="space-y-6">
            @foreach($permissionGroups as $group => $permissions)
            <div class="p-4 rounded-xl border border-white/5 bg-black/10">
                <h4 class="text-md font-bold text-[#38bdf8] mb-3 capitalize border-b border-white/5 pb-2">{{ __($group) }} {{ __('Permissions') }}</h4>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    @foreach($permissions as $permission)
                    <label class="flex items-center space-x-3 {{ app()->getLocale() == 'ar' ? 'space-x-reverse' : '' }} group cursor-pointer p-2 rounded-lg hover:bg-white/5 transition-colors">
                        <input type="checkbox" name="permissions[]" value="{{ $permission->name }}" {{ in_array($permission->name, $rolePermissions) ? 'checked' : '' }} class="form-checkbox h-4 w-4 text-[#38bdf8] rounded border-white/10 bg-black/30 focus:ring-[#38bdf8] focus:ring-offset-slate-900 transition-colors">
                        <span class="text-xs text-slate-300 group-hover:text-white transition-colors">{{ __($permission->name) }}</span>
                    </label>
                    @endforeach
                </div>
            </div>
            @endforeach
        </div>
        @error('permissions') <span class="text-red-400 text-xs mt-1 block">{{ $message }}</span> @enderror
    </div>

    <div class="flex justify-end gap-4 border-t border-white/5 pt-6">
        <a href="{{ route('admin.roles.index') }}" class="px-6 py-2.5 rounded-xl text-sm font-medium text-slate-300 hover:text-white hover:bg-white/5 transition-colors">{{ __('Cancel') }}</a>
        <button type="submit" class="bg-[#38bdf8]/10 text-[#38bdf8] border border-[#38bdf8]/30 hover:bg-[#38bdf8] hover:text-[#0f172a] transition-all px-6 py-2.5 rounded-xl text-sm font-bold shadow-[0_0_15px_rgba(56,189,248,0.15)]">{{ __('Update Role') }}</button>
    </div>
</form>
@endsection
