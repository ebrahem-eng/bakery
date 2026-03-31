@extends('layouts.Admin.App')

@section('content')
<div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
    <div>
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.manage_admins.index') }}" class="p-2 bg-slate-100 dark:bg-white/5 rounded-xl text-slate-500 hover:text-slate-900 dark:hover:text-white transition-colors">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
            </a>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white tracking-tight">{{ $admin->first_name }} {{ $admin->last_name }}</h1>
        </div>
        <p class="text-sm text-slate-500 mt-1 {{ app()->getLocale() == 'ar' ? 'mr-12' : 'ml-12' }}">{{ $admin->title ?: __('Administrator') }}</p>
    </div>
    <div class="flex gap-3">
        <a href="{{ route('admin.manage_admins.edit', $admin->id) }}" class="px-4 py-2 bg-slate-100 dark:bg-white/5 border border-slate-200 dark:border-white/10 rounded-xl text-sm font-bold text-slate-700 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-white/10 transition-all flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
            {{ __('Edit Profile') }}
        </a>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Main Info -->
    <div class="lg:col-span-2 space-y-6">
        <div class="glass-panel p-6 rounded-2xl border border-slate-200 dark:border-white/5 shadow-sm">
            <h3 class="text-sm font-bold text-slate-900 dark:text-white mb-6 flex items-center gap-2">
                <svg class="w-4 h-4 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                {{ __('Account Details') }}
            </h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div>
                    <label class="block text-[10px] uppercase tracking-widest text-slate-500 font-black mb-1.5">{{ __('Email Address') }}</label>
                    <div class="text-slate-900 dark:text-white font-medium flex items-center gap-2 text-sm md:text-base">
                        <svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
                        {{ $admin->email }}
                    </div>
                </div>
                <div>
                    <label class="block text-[10px] uppercase tracking-widest text-slate-500 font-black mb-1.5">{{ __('Active Roles') }}</label>
                    <div class="flex flex-wrap gap-2">
                        @forelse($admin->roles as $role)
                            <span class="px-2 py-1 bg-indigo-500/20 text-indigo-400 text-[10px] font-bold rounded-md border border-indigo-500/20 uppercase tracking-tighter">{{ $role->name }}</span>
                        @empty
                            <span class="text-xs text-slate-500 italic">{{ __('No roles assigned') }}</span>
                        @endforelse
                    </div>
                </div>
                <div>
                    <label class="block text-[10px] uppercase tracking-widest text-slate-500 font-black mb-1.5">{{ __('Gender / Age') }}</label>
                    <div class="text-slate-900 dark:text-white font-medium flex items-center gap-2">
                        <span class="px-2 py-0.5 bg-slate-100 dark:bg-white/5 rounded-md text-[10px] uppercase font-bold text-slate-500">{{ __($admin->gender) }}</span>
                        <span class="text-slate-300 dark:text-slate-700">•</span>
                        <span>{{ $admin->age ?: '-' }} {{ __('Years') }}</span>
                    </div>
                </div>
                <div>
                    <label class="block text-[10px] uppercase tracking-widest text-slate-500 font-black mb-1.5">{{ __('Status') }}</label>
                    @if($admin->status == 'active')
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-emerald-500/10 text-emerald-500 text-[10px] font-bold rounded-full border border-emerald-500/20 uppercase">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                            {{ __('Active') }}
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-red-500/10 text-red-500 text-[10px] font-bold rounded-full border border-red-500/20 uppercase">
                            <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>
                            {{ __('Inactive') }}
                        </span>
                    @endif
                </div>
            </div>
        </div>

        <div class="glass-panel p-6 rounded-2xl border border-slate-200 dark:border-white/5 shadow-sm">
            <h3 class="text-sm font-bold text-slate-900 dark:text-white mb-6 flex items-center gap-2">
                <svg class="w-4 h-4 text-sky-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                {{ __('Secondary Information') }}
            </h3>
            <div class="space-y-6">
                <div>
                    <label class="block text-[10px] uppercase tracking-widest text-slate-500 font-black mb-2">{{ __('Home Address') }}</label>
                    <div class="p-4 bg-slate-50 dark:bg-black/20 rounded-xl border border-slate-200 dark:border-white/5 text-sm text-slate-700 dark:text-slate-300 italic min-h-[60px]">
                        {{ $admin->address ?: __('No address registered') }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Sidebar Info -->
    <div class="space-y-6">
        <div class="glass-panel p-6 rounded-2xl border border-slate-200 dark:border-white/5 shadow-sm">
            <h3 class="text-sm font-bold text-slate-900 dark:text-white mb-6 flex items-center gap-2">
                <svg class="w-4 h-4 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" /></svg>
                {{ __('Mobile Numbers') }}
            </h3>
            <div class="space-y-3">
                @forelse($admin->mobiles as $mobile)
                    <div class="flex items-center justify-between p-3 bg-slate-50 dark:bg-black/20 rounded-xl border border-slate-200 dark:border-white/5 group">
                        <span class="text-sm font-bold text-slate-900 dark:text-white tracking-wider group-hover:text-emerald-500 transition-colors">{{ $mobile->mobile_number }}</span>
                        <a href="tel:{{ $mobile->mobile_number }}" class="p-1.5 bg-emerald-500/10 text-emerald-500 rounded-lg opacity-0 group-hover:opacity-100 transition-all">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" /></svg>
                        </a>
                    </div>
                @empty
                    <div class="text-center py-4 text-slate-500 italic text-xs">{{ __('No mobile numbers registered') }}</div>
                @endforelse
            </div>
        </div>

        <div class="glass-panel p-6 rounded-2xl border border-slate-200 dark:border-white/5 shadow-sm">
            <h3 class="text-[10px] font-black text-slate-500 uppercase tracking-widest mb-4 flex items-center gap-2">
                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                {{ __('Account History') }}
            </h3>
            <div class="space-y-4">
                <div class="flex justify-between items-center text-xs">
                    <span class="text-slate-500">{{ __('Created At') }}</span>
                    <span class="text-slate-900 dark:text-white font-medium">{{ $admin->created_at->translatedFormat('Y-m-d') }}</span>
                </div>
                <div class="flex justify-between items-center text-xs">
                    <span class="text-slate-500">{{ __('Last Updated') }}</span>
                    <span class="text-slate-900 dark:text-white font-medium">{{ $admin->updated_at->diffForHumans() }}</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
