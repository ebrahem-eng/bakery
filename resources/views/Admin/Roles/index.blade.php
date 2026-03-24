@extends('layouts.Admin.App')

@section('content')
<div class="mb-6 flex justify-between items-end">
    <div>
        <h1 class="text-2xl font-bold text-white tracking-tight">{{ __('System Roles') }}</h1>
        <p class="text-sm text-slate-400 mt-1">{{ __('Manage authorization thresholds and custom capability scopes.') }}</p>
    </div>
    <a href="{{ route('admin.roles.create') }}" class="bg-[#eab308]/10 text-[#eab308] border border-[#eab308]/30 hover:bg-[#eab308] hover:text-[#451a03] transition-all px-4 py-2 rounded-xl text-sm font-bold flex items-center shadow-[0_0_15px_rgba(234,179,8,0.15)]">
        <svg class="w-4 h-4 {{ app()->getLocale() == 'ar' ? 'ml-2' : 'mr-2' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
        </svg>
        {{ __('Create Role') }}
    </a>
</div>

@if(session('success'))
<div class="mb-6 px-4 py-3 bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 rounded-xl">
    {{ session('success') }}
</div>
@endif

<div class="glass-panel p-6 rounded-2xl border border-white/5">
    <div class="overflow-x-auto custom-scrollbar">
        <table class="w-full text-left border-collapse" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
            <thead>
                <tr class="text-slate-400 text-xs uppercase tracking-wider border-b border-white/5">
                    <th class="py-4 px-4 font-medium">{{ __('Role Name') }}</th>
                    <th class="py-4 px-4 font-medium">{{ __('Assigned Permissions') }}</th>
                    <th class="py-4 px-4 font-medium {{ app()->getLocale() == 'ar' ? 'text-left' : 'text-right' }}">{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-white/5 text-sm text-slate-300">
                @forelse($roles as $role)
                <tr class="hover:bg-white/5 transition-colors">
                    <td class="py-3 px-4 font-medium text-white">{{ $role->name }}</td>
                    <td class="py-3 px-4">
                        <div class="flex flex-wrap gap-2">
                            @foreach($role->permissions->take(5) as $perm)
                                <span class="px-2 py-1 bg-slate-800 text-slate-300 text-[10px] rounded border border-white/10">{{ $perm->name }}</span>
                            @endforeach
                            @if($role->permissions->count() > 5)
                                <span class="px-2 py-1 bg-slate-800 text-slate-400 text-[10px] rounded border border-white/5">+{{ $role->permissions->count() - 5 }} {{ __('more') }}</span>
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
</div>
@endsection
