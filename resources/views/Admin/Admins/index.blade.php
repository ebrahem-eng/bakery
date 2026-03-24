@extends('layouts.Admin.App')

@section('content')
<div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-end gap-4">
    <div>
        <h1 class="text-2xl font-bold text-white tracking-tight">{{ __('Admin Accounts') }}</h1>
        <p class="text-sm text-slate-400 mt-1">{{ __('Manage system personnel and their active roles.') }}</p>
    </div>
    <a href="{{ route('admin.manage_admins.create') }}" class="bg-[#eab308]/10 text-[#eab308] border border-[#eab308]/30 hover:bg-[#eab308] hover:text-[#451a03] transition-all px-4 py-2 rounded-xl text-sm font-bold flex items-center shadow-[0_0_15px_rgba(234,179,8,0.15)]">
        <svg class="w-4 h-4 {{ app()->getLocale() == 'ar' ? 'ml-2' : 'mr-2' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
        </svg>
        {{ __('Add Admin') }}
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
                    <th class="py-4 px-4 font-medium">{{ __('Name') }}</th>
                    <th class="py-4 px-4 font-medium">{{ __('Email') }}</th>
                    <th class="py-4 px-4 font-medium">{{ __('Role') }}</th>
                    <th class="py-4 px-4 font-medium">{{ __('Mobiles') }}</th>
                    <th class="py-4 px-4 font-medium">{{ __('Status') }}</th>
                    <th class="py-4 px-4 font-medium {{ app()->getLocale() == 'ar' ? 'text-left' : 'text-right' }}">{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-white/5 text-sm text-slate-300">
                @forelse($admins as $admin)
                <tr class="hover:bg-white/5 transition-colors">
                    <td class="py-3 px-4">
                        <div class="font-medium text-white">{{ $admin->first_name }} {{ $admin->last_name }}</div>
                        <div class="text-[11px] text-slate-500">{{ $admin->title }}</div>
                    </td>
                    <td class="py-3 px-4">{{ $admin->email }}</td>
                    <td class="py-3 px-4">
                        @foreach($admin->roles as $role)
                            <span class="px-2 py-1 bg-indigo-500/20 text-indigo-400 text-xs rounded-md border border-indigo-500/20">{{ $role->name }}</span>
                        @endforeach
                    </td>
                    <td class="py-3 px-4">
                        @foreach($admin->mobiles as $mobile)
                            <div class="text-xs text-slate-400">{{ $mobile->mobile_number }}</div>
                        @endforeach
                    </td>
                    <td class="py-3 px-4">
                        @if($admin->status == 'active')
                            <span class="px-2 py-1 bg-emerald-500/20 text-emerald-400 text-xs rounded-md border border-emerald-500/20">{{ __('Active') }}</span>
                        @else
                            <span class="px-2 py-1 bg-red-500/20 text-red-400 text-xs rounded-md border border-red-500/20">{{ __('Inactive') }}</span>
                        @endif
                    </td>
                    <td class="py-3 px-4 {{ app()->getLocale() == 'ar' ? 'text-left' : 'text-right' }}">
                        <a href="{{ route('admin.manage_admins.edit', $admin->id) }}" class="text-[#38bdf8] hover:text-white transition-colors {{ app()->getLocale() == 'ar' ? 'ml-3' : 'mr-3' }}">{{ __('Edit') }}</a>
                        <form action="{{ route('admin.manage_admins.destroy', $admin->id) }}" method="POST" class="inline-block" onsubmit="return confirm('{{ __('Are you sure?') }}');">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-red-400 hover:text-white transition-colors">{{ __('Delete') }}</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="py-8 text-center text-slate-500 italic">{{ __('No admins found.') }}</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
