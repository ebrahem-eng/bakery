@extends('layouts.Admin.App')

@section('content')
<div class="mb-8 flex justify-between items-end">
    <div>
        <h1 class="text-2xl lg:text-3xl font-bold text-slate-900 dark:text-white mb-1 tracking-tight">{{ __('Manage') }} <span class="text-transparent bg-clip-text bg-gradient-to-r from-amber-400 to-orange-500">{{ __('Distributors') }}</span></h1>
        <p class="text-sm text-slate-500 dark:text-slate-400">{{ __('Register and monitor all external sales distribution contacts.') }}</p>
    </div>
    @can('create distributors')
    <a href="{{ route('admin.distributors.create') }}" class="bg-gradient-to-r from-amber-500 to-orange-500 hover:from-amber-400 hover:to-orange-400 text-[#0f1115] px-4 py-2 rounded-xl text-sm font-bold flex items-center shadow-[0_0_15px_rgba(245,158,11,0.3)] transition-all">
        <svg class="w-4 h-4 {{ app()->getLocale() == 'ar' ? 'ml-2' : 'mr-2' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
        </svg>
        {{ __('Add Distributor') }}
    </a>
    @endcan
</div>

@if(session('success_message'))
    <div class="mb-6 p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-sm flex items-center">
        <svg class="w-5 h-5 {{ app()->getLocale() == 'ar' ? 'ml-3' : 'mr-3' }} flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        {{ session('success_message') }}
    </div>
@endif

<div class="glass-panel p-6 rounded-2xl border border-slate-200 dark:border-white/5 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50 dark:bg-black/20 border-b border-slate-200 dark:border-white/5 text-xs uppercase tracking-wider text-slate-500 dark:text-slate-400">
                    <th class="p-4 font-semibold">{{ __('Name') }}</th>
                    <th class="p-4 font-semibold">{{ __('Title') }}</th>
                    <th class="p-4 font-semibold">{{ __('Contact Numbers') }}</th>
                    <th class="p-4 font-semibold">{{ __('Default Currency') }}</th>
                    <th class="p-4 font-semibold text-center">{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200 dark:divide-white/5">
                @forelse($distributors as $distributor)
                <tr class="hover:bg-slate-50 dark:hover:bg-white/5 transition-colors">
                    <td class="p-4">
                        <div class="font-bold text-slate-900 dark:text-white">{{ $distributor->first_name }} {{ $distributor->last_name }}</div>
                    </td>
                    <td class="p-4">
                        <span class="text-sm text-slate-600 dark:text-slate-300">{{ $distributor->title ?? '-' }}</span>
                    </td>
                    <td class="p-4">
                        <div class="flex flex-wrap gap-1">
                            @forelse($distributor->mobiles as $mobile)
                                <span class="px-2 py-1 bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-white/10 rounded-md text-xs text-slate-600 dark:text-slate-300">
                                    {{ $mobile->number }}
                                </span>
                            @empty
                                <span class="text-xs text-slate-500">{{ __('No Contacts') }}</span>
                            @endforelse
                        </div>
                    </td>
                    <td class="p-4">
                        <span class="px-2 py-1 bg-amber-500/10 text-amber-500 border border-amber-500/20 rounded-md text-xs font-bold">
                            {{ $distributor->currency->code }}
                        </span>
                    </td>
                    <td class="p-4 flex justify-center gap-2">
                        <a href="{{ route('admin.distributors.show', $distributor) }}" class="p-2 text-slate-400 hover:text-blue-500 hover:bg-blue-500/10 rounded-lg transition-colors">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                        </a>
                        @can('edit distributors')
                        <a href="{{ route('admin.distributors.edit', $distributor) }}" class="p-2 text-slate-400 hover:text-amber-400 hover:bg-amber-400/10 rounded-lg transition-colors">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                        </a>
                        @endcan
                        @can('delete distributors')
                        <form action="{{ route('admin.distributors.destroy', $distributor) }}" method="POST" class="inline-block" onsubmit="return confirm('{{ __('Are you sure you want to delete this distributor entirely?') }}');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="p-2 text-slate-400 hover:text-red-400 hover:bg-red-400/10 rounded-lg transition-colors">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>
                        </form>
                        @endcan
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="p-8 text-center text-slate-500">
                        {{ __('No distributors registered yet.') }}
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
