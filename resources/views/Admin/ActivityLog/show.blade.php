@extends('layouts.Admin.App')

@section('content')
<div class="space-y-6">

    <div class="flex items-center gap-4 mb-6">
        <a href="{{ route('admin.activity-log.index') }}" class="p-2 glass-card rounded-xl border border-slate-200 dark:border-white/5 text-slate-500 hover:text-violet-500 transition-colors">
            <svg class="w-5 h-5 {{ app()->getLocale() == 'ar' ? 'rotate-180' : '' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white tracking-tight">{{ __('Activity Detail') }} #{{ $activity->id }}</h1>
            <p class="text-sm text-slate-500 mt-1">{{ $activity->created_at->translatedFormat('Y-m-d h:i:s A') }} — {{ $activity->created_at->diffForHumans() }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        <!-- Info Panel -->
        <div class="glass-panel rounded-2xl border border-slate-200 dark:border-white/5 overflow-hidden">
            <div class="p-4 bg-slate-50 dark:bg-black/20 border-b border-slate-200 dark:border-white/5">
                <h3 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
                    <svg class="w-4 h-4 text-violet-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    {{ __('Event Information') }}
                </h3>
            </div>
            <div class="p-6 space-y-4">
                <div class="flex justify-between items-center py-2 border-b border-slate-100 dark:border-white/5">
                    <span class="text-[10px] uppercase tracking-widest text-slate-500 font-bold">{{ __('Event') }}</span>
                    @php
                        $eventColors = ['created' => 'emerald', 'updated' => 'amber', 'deleted' => 'red'];
                        $color = $eventColors[$activity->event] ?? 'slate';
                    @endphp
                    <span class="px-3 py-1 bg-{{ $color }}-500/10 text-{{ $color }}-500 border border-{{ $color }}-500/20 rounded-lg text-xs font-bold uppercase">
                        {{ __(ucfirst($activity->event ?? 'system')) }}
                    </span>
                </div>
                <div class="flex justify-between items-center py-2 border-b border-slate-100 dark:border-white/5">
                    <span class="text-[10px] uppercase tracking-widest text-slate-500 font-bold">{{ __('Model') }}</span>
                    <span class="px-3 py-1 bg-violet-500/10 text-violet-500 border border-violet-500/20 rounded-lg text-xs font-bold">
                        {{ __(class_basename($activity->subject_type ?? 'Unknown')) }} #{{ $activity->subject_id }}
                    </span>
                </div>
                <div class="flex justify-between items-center py-2 border-b border-slate-100 dark:border-white/5">
                    <span class="text-[10px] uppercase tracking-widest text-slate-500 font-bold">{{ __('Description') }}</span>
                    <span class="text-sm text-slate-700 dark:text-slate-300">{{ __($activity->description) }}</span>
                </div>
                <div class="flex justify-between items-center py-2 border-b border-slate-100 dark:border-white/5">
                    <span class="text-[10px] uppercase tracking-widest text-slate-500 font-bold">{{ __('Log Name') }}</span>
                    <span class="text-sm text-slate-700 dark:text-slate-300">{{ $activity->log_name ?? 'default' }}</span>
                </div>
                <div class="flex justify-between items-center py-2">
                    <span class="text-[10px] uppercase tracking-widest text-slate-500 font-bold">{{ __('Performed By') }}</span>
                    @if($activity->causer)
                    <div class="flex items-center gap-2">
                        <div class="w-6 h-6 rounded-full bg-gradient-to-tr from-violet-500 to-purple-500 flex items-center justify-center text-white text-[10px] font-bold">
                            {{ substr($activity->causer->first_name ?? 'S', 0, 1) }}
                        </div>
                        <span class="text-sm font-medium text-slate-700 dark:text-slate-300">{{ $activity->causer->first_name }} {{ $activity->causer->last_name }}</span>
                    </div>
                    @else
                    <span class="text-sm text-slate-400 italic">{{ __('System') }}</span>
                    @endif
                </div>
            </div>
        </div>

        <!-- Changes Panel -->
        <div class="glass-panel rounded-2xl border border-slate-200 dark:border-white/5 overflow-hidden">
            <div class="p-4 bg-slate-50 dark:bg-black/20 border-b border-slate-200 dark:border-white/5">
                <h3 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
                    <svg class="w-4 h-4 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg>
                    {{ __('Data Changes') }}
                </h3>
            </div>
            <div class="p-6">
                @if($activity->properties && ($activity->properties->has('old') || $activity->properties->has('attributes')))
                    @if($activity->properties->has('old'))
                    <div class="mb-4">
                        <h4 class="text-[10px] uppercase tracking-widest text-red-500 font-bold mb-2 flex items-center gap-1">
                            <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" /></svg>
                            {{ __('Old Values') }}
                        </h4>
                        <div class="bg-red-500/5 border border-red-500/10 rounded-xl p-4 overflow-x-auto">
                            <pre class="text-xs text-slate-700 dark:text-slate-300 font-mono whitespace-pre-wrap" dir="ltr">{{ json_encode($activity->properties['old'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                        </div>
                    </div>
                    @endif
                    @if($activity->properties->has('attributes'))
                    <div>
                        <h4 class="text-[10px] uppercase tracking-widest text-emerald-500 font-bold mb-2 flex items-center gap-1">
                            <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" /></svg>
                            {{ __('New Values') }}
                        </h4>
                        <div class="bg-emerald-500/5 border border-emerald-500/10 rounded-xl p-4 overflow-x-auto">
                            <pre class="text-xs text-slate-700 dark:text-slate-300 font-mono whitespace-pre-wrap" dir="ltr">{{ json_encode($activity->properties['attributes'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                        </div>
                    </div>
                    @endif
                @else
                <div class="text-center py-8">
                    <svg class="w-10 h-10 mx-auto text-slate-300 dark:text-slate-600 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" /></svg>
                    <p class="text-sm text-slate-500">{{ __('No property changes recorded for this event.') }}</p>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Delete -->
    <div class="flex justify-end">
        <form action="{{ route('admin.activity-log.destroy', $activity) }}" method="POST" data-confirm data-confirm-title="{{ __('Delete Log Entry') }}" data-confirm-message="{{ __('Are you sure you want to delete this log entry?') }}">
            @csrf @method('DELETE')
            <button type="submit" class="text-xs text-red-400 hover:text-red-600 transition-colors font-bold uppercase tracking-widest flex items-center gap-1">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                {{ __('Delete This Entry') }}
            </button>
        </form>
    </div>
</div>
@endsection
