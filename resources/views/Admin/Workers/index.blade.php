@extends('layouts.Admin.App')

@section('content')
<div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-end gap-4">
    <div>
        <h1 class="text-2xl font-bold text-slate-900 dark:text-white tracking-tight">{{ __('Personnel & Workers') }}</h1>
        <p class="text-sm text-slate-500 mt-1">{{ __('Manage bakery staff, titles, and daily wages.') }}</p>
    </div>
    <a href="{{ route('admin.workers.create') }}" class="bg-[#0ea5e9]/10 text-[#0ea5e9] border border-[#0ea5e9]/30 hover:bg-[#0ea5e9] hover:text-white transition-all px-4 py-2 rounded-xl text-sm font-bold flex items-center shadow-[0_0_15px_rgba(14,165,233,0.15)]">
        <svg class="w-4 h-4 {{ app()->getLocale() == 'ar' ? 'ml-2' : 'mr-2' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
        {{ __('Register Worker') }}
    </a>
</div>

@if(session('success'))
<div class="mb-6 px-4 py-3 bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 rounded-xl">
    {{ session('success') }}
</div>
@endif

<div class="glass-panel p-6 rounded-2xl border border-slate-200 dark:border-white/5">
    <div class="overflow-x-auto custom-scrollbar">
        <table class="w-full text-left border-collapse" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
            <thead>
                <tr class="text-slate-500 dark:text-slate-400 text-xs uppercase tracking-wider border-b border-slate-200 dark:border-white/5">
                    <th class="py-4 px-4 font-medium">{{ __('Name & Title') }}</th>
                    <th class="py-4 px-4 font-medium">{{ __('Base Daily Wage') }}</th>
                    <th class="py-4 px-4 font-medium">{{ __('Mobile Contacts') }}</th>
                    <th class="py-4 px-4 font-medium {{ app()->getLocale() == 'ar' ? 'text-left' : 'text-right' }}">{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200 dark:divide-white/5 text-sm text-slate-600 dark:text-slate-300">
                @forelse($workers as $worker)
                <tr class="hover:bg-slate-50 dark:hover:bg-white/5 transition-colors">
                    <td class="py-3 px-4">
                        <div class="font-medium text-slate-900 dark:text-white">{{ $worker->first_name }} {{ $worker->last_name }}</div>
                        <div class="text-[11px] text-slate-500 mt-1 uppercase">{{ $worker->title ?: __('Worker') }}</div>
                    </td>
                    <td class="py-3 px-4">
                        <span class="px-2 py-1 bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 text-xs rounded-md border border-emerald-500/20 font-bold whitespace-nowrap">
                            {{ number_format($worker->daily_wage, 2) }} {{ $worker->currency?->symbol ?? '' }}
                        </span>
                    </td>
                    <td class="py-3 px-4">
                        @foreach($worker->mobiles as $mobile)
                            <div class="text-xs text-slate-500">{{ $mobile->mobile_number }}</div>
                        @endforeach
                    </td>
                    <td class="py-3 px-4 {{ app()->getLocale() == 'ar' ? 'text-left' : 'text-right' }}">
                        <a href="{{ route('admin.workers.edit', $worker->id) }}" class="text-[#0ea5e9] hover:text-[#38bdf8] transition-colors font-medium {{ app()->getLocale() == 'ar' ? 'ml-3' : 'mr-3' }}">{{ __('Edit') }}</a>
                        <form action="{{ route('admin.workers.destroy', $worker->id) }}" method="POST" class="inline-block" onsubmit="return confirm('{{ __('Are you sure you want to terminate this worker profile?') }}');">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-red-500 hover:text-red-400 transition-colors font-medium">{{ __('Remove') }}</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="py-8 text-center text-slate-500 italic">{{ __('No personnel records found.') }}</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
