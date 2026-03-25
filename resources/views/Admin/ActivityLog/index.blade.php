@extends('layouts.Admin.App')

@section('content')
<div class="space-y-6">

    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-end gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white tracking-tight flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-violet-600 to-purple-500 flex items-center justify-center shadow-[0_0_15px_rgba(139,92,246,0.3)]">
                    <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" /></svg>
                </div>
                {{ __('Activity Log') }}
            </h1>
            <p class="text-sm text-slate-500 mt-1">{{ __('Monitor all system activities, changes, and operations in real-time.') }}</p>
        </div>
        <form action="{{ route('admin.activity-log.clear') }}" method="POST" onsubmit="return confirm('{{ __('Are you sure? This will permanently delete ALL activity log entries.') }}');">
            @csrf @method('DELETE')
            <button type="submit" class="bg-red-500/10 text-red-500 border border-red-500/30 hover:bg-red-500 hover:text-white transition-all px-4 py-2 rounded-xl text-sm font-bold flex items-center gap-2 shadow-[0_0_15px_rgba(239,68,68,0.15)]">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                {{ __('Clear All Logs') }}
            </button>
        </form>
    </div>

    @if(session('success_message'))
    <div class="px-4 py-3 bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 rounded-xl">
        {{ session('success_message') }}
    </div>
    @endif

    <!-- Stats -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        @php
            $totalLogs = \Spatie\Activitylog\Models\Activity::count();
            $todayLogs = \Spatie\Activitylog\Models\Activity::whereDate('created_at', today())->count();
            $createEvents = \Spatie\Activitylog\Models\Activity::where('event', 'created')->count();
            $uniqueCausers = \Spatie\Activitylog\Models\Activity::distinct('causer_id')->count('causer_id');
        @endphp
        <div class="glass-panel rounded-2xl border border-slate-200 dark:border-white/5 p-4">
            <div class="text-[10px] uppercase tracking-widest text-slate-500 font-bold">{{ __('Total Entries') }}</div>
            <div class="text-2xl font-black text-slate-900 dark:text-white mt-1">{{ number_format($totalLogs) }}</div>
        </div>
        <div class="glass-panel rounded-2xl border border-slate-200 dark:border-white/5 p-4">
            <div class="text-[10px] uppercase tracking-widest text-slate-500 font-bold">{{ __('Today') }}</div>
            <div class="text-2xl font-black text-violet-500 mt-1">{{ number_format($todayLogs) }}</div>
        </div>
        <div class="glass-panel rounded-2xl border border-slate-200 dark:border-white/5 p-4">
            <div class="text-[10px] uppercase tracking-widest text-slate-500 font-bold">{{ __('Create Events') }}</div>
            <div class="text-2xl font-black text-emerald-500 mt-1">{{ number_format($createEvents) }}</div>
        </div>
        <div class="glass-panel rounded-2xl border border-slate-200 dark:border-white/5 p-4">
            <div class="text-[10px] uppercase tracking-widest text-slate-500 font-bold">{{ __('Active Admins') }}</div>
            <div class="text-2xl font-black text-amber-500 mt-1">{{ $uniqueCausers }}</div>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="glass-panel rounded-2xl border border-slate-200 dark:border-white/5 overflow-hidden">
        <div class="p-4 bg-slate-50 dark:bg-black/20 border-b border-slate-200 dark:border-white/5 flex justify-between items-center">
            <h3 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
                <svg class="w-4 h-4 text-violet-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" /></svg>
                {{ __('Filter Activity Logs') }}
            </h3>
            <a href="{{ route('admin.activity-log.index') }}" class="text-[10px] uppercase tracking-widest font-bold text-slate-500 hover:text-violet-500 transition-colors">{{ __('Reset All') }}</a>
        </div>
        <form method="GET" action="{{ route('admin.activity-log.index') }}" class="p-4 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
            <div>
                <label class="block text-[10px] uppercase tracking-widest text-slate-500 font-bold mb-2">{{ __('Search Description') }}</label>
                <div class="relative">
                    <svg class="w-4 h-4 text-slate-400 absolute top-1/2 -translate-y-1/2 {{ app()->getLocale() == 'ar' ? 'right-3' : 'left-3' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ __('e.g. created, updated...') }}" class="block w-full {{ app()->getLocale() == 'ar' ? 'pr-10 pl-4' : 'pl-10 pr-4' }} py-2.5 bg-white dark:bg-[#0f1115] border border-slate-200 dark:border-white/5 rounded-xl text-sm placeholder-slate-400 text-slate-900 dark:text-white focus:outline-none focus:border-violet-500/50 focus:ring-1 focus:ring-violet-500/50 transition-all">
                </div>
            </div>
            <div>
                <label class="block text-[10px] uppercase tracking-widest text-slate-500 font-bold mb-2">{{ __('Event Type') }}</label>
                <select name="event" class="block w-full px-4 py-2.5 bg-white dark:bg-[#0f1115] border border-slate-200 dark:border-white/5 rounded-xl text-sm text-slate-900 dark:text-white focus:outline-none focus:border-violet-500/50 focus:ring-1 focus:ring-violet-500/50 transition-all appearance-none">
                    <option value="">{{ __('All Events') }}</option>
                    @foreach($events as $event)
                    <option value="{{ $event }}" {{ request('event') == $event ? 'selected' : '' }}>{{ __(ucfirst($event)) }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-[10px] uppercase tracking-widest text-slate-500 font-bold mb-2">{{ __('Model / Subject') }}</label>
                <select name="subject_type" class="block w-full px-4 py-2.5 bg-white dark:bg-[#0f1115] border border-slate-200 dark:border-white/5 rounded-xl text-sm text-slate-900 dark:text-white focus:outline-none focus:border-violet-500/50 focus:ring-1 focus:ring-violet-500/50 transition-all appearance-none">
                    <option value="">{{ __('All Models') }}</option>
                    @foreach($subjectTypes as $type)
                    <option value="App\Models\{{ $type }}" {{ request('subject_type') == "App\Models\\$type" ? 'selected' : '' }}>{{ __($type) }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-[10px] uppercase tracking-widest text-slate-500 font-bold mb-2">{{ __('Performed By') }}</label>
                <select name="causer_id" class="block w-full px-4 py-2.5 bg-white dark:bg-[#0f1115] border border-slate-200 dark:border-white/5 rounded-xl text-sm text-slate-900 dark:text-white focus:outline-none focus:border-violet-500/50 focus:ring-1 focus:ring-violet-500/50 transition-all appearance-none">
                    <option value="">{{ __('All Admins') }}</option>
                    @foreach($admins as $admin)
                    <option value="{{ $admin->id }}" {{ request('causer_id') == $admin->id ? 'selected' : '' }}>{{ $admin->first_name }} {{ $admin->last_name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-end">
                <button type="submit" class="w-full bg-violet-500/10 text-violet-500 border border-violet-500/30 hover:bg-violet-500 hover:text-white transition-all px-4 py-2.5 rounded-xl text-sm font-bold flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                    {{ __('Apply Filters') }}
                </button>
            </div>
        </form>
    </div>

    <!-- Activity Table -->
    <div class="glass-panel rounded-2xl border border-slate-200 dark:border-white/5 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
                <thead>
                    <tr class="bg-slate-50 dark:bg-black/20 border-b border-slate-200 dark:border-white/5 text-xs uppercase tracking-wider text-slate-500 dark:text-slate-400">
                        <th class="p-4 font-semibold w-16">#</th>
                        <th class="p-4 font-semibold">{{ __('Timestamp') }}</th>
                        <th class="p-4 font-semibold">{{ __('Event') }}</th>
                        <th class="p-4 font-semibold">{{ __('Model') }}</th>
                        <th class="p-4 font-semibold">{{ __('Description') }}</th>
                        <th class="p-4 font-semibold">{{ __('Performed By') }}</th>
                        <th class="p-4 font-semibold text-center">{{ __('Details') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-white/5">
                    @forelse($activities as $activity)
                    <tr class="hover:bg-slate-50 dark:hover:bg-white/5 transition-colors">
                        <td class="p-4 text-xs text-slate-400 font-mono">{{ $activity->id }}</td>
                        <td class="p-4 whitespace-nowrap">
                            <div class="text-xs font-medium text-slate-900 dark:text-white">{{ $activity->created_at->format('Y-m-d') }}</div>
                            <div class="text-[10px] text-slate-500 mt-0.5">{{ $activity->created_at->format('h:i:s A') }}</div>
                        </td>
                        <td class="p-4">
                            @php
                                $eventColors = ['created' => 'emerald', 'updated' => 'amber', 'deleted' => 'red'];
                                $color = $eventColors[$activity->event] ?? 'slate';
                            @endphp
                            <span class="px-2 py-0.5 bg-{{ $color }}-500/10 text-{{ $color }}-500 border border-{{ $color }}-500/20 rounded-md text-[10px] font-bold uppercase">
                                {{ __( ucfirst($activity->event ?? 'system') ) }}
                            </span>
                        </td>
                        <td class="p-4">
                            <span class="px-2 py-0.5 bg-violet-500/10 text-violet-500 border border-violet-500/20 rounded-md text-[10px] font-bold uppercase whitespace-nowrap">
                                {{ __(class_basename($activity->subject_type ?? 'Unknown')) }}
                            </span>
                            @if($activity->subject_id)
                            <span class="text-[10px] text-slate-400 font-mono {{ app()->getLocale() == 'ar' ? 'mr-1' : 'ml-1' }}">#{{ $activity->subject_id }}</span>
                            @endif
                        </td>
                        <td class="p-4">
                            <span class="text-xs text-slate-700 dark:text-slate-300 line-clamp-2">{{ $activity->description }}</span>
                        </td>
                        <td class="p-4">
                            @if($activity->causer)
                            <div class="flex items-center gap-2">
                                <div class="w-6 h-6 rounded-full bg-gradient-to-tr from-violet-500 to-purple-500 flex items-center justify-center text-white text-[10px] font-bold">
                                    {{ substr($activity->causer->first_name ?? 'S', 0, 1) }}
                                </div>
                                <span class="text-xs font-medium text-slate-700 dark:text-slate-300 whitespace-nowrap">{{ $activity->causer->first_name ?? __('System') }}</span>
                            </div>
                            @else
                            <span class="text-xs text-slate-400 italic">{{ __('System') }}</span>
                            @endif
                        </td>
                        <td class="p-4 text-center">
                            <a href="{{ route('admin.activity-log.show', $activity) }}" class="inline-flex p-2 text-slate-400 hover:text-violet-500 hover:bg-violet-500/10 rounded-lg transition-colors" title="{{ __('View Details') }}">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="p-12 text-center">
                            <svg class="w-12 h-12 mx-auto text-slate-300 dark:text-slate-700 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" /></svg>
                            <p class="text-slate-500 dark:text-slate-400 font-medium">{{ __('No activity logs recorded yet.') }}</p>
                            <p class="text-xs text-slate-400 dark:text-slate-500 mt-1">{{ __('Activities will appear here as users interact with the system.') }}</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($activities->hasPages())
        <div class="p-4 border-t border-slate-200 dark:border-white/5 bg-slate-50 dark:bg-black/20">
            {{ $activities->withQueryString()->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
