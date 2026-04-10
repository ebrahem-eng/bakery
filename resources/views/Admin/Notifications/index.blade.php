@extends('layouts.Admin.App')

@section('content')
<div class="mb-6 flex flex-col sm:flex-row sm:items-end justify-between gap-4">
    <div>
        <h1 class="text-2xl font-bold text-slate-900 dark:text-white tracking-tight">{{ __('Notification Center') }}</h1>
        <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">{{ __('View and manage all your system alerts.') }}</p>
    </div>
    
    @can('manage notifications')
    <div class="flex items-center gap-3">
        <button type="button" onclick="markAllNotificationsReadAdmin()" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 dark:bg-white/5 dark:hover:bg-white/10 text-slate-700 dark:text-white rounded-xl text-sm font-semibold transition-colors flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
            {{ __('Mark All as Read') }}
        </button>

        <form action="{{ route('admin.notifications.deleteAll') }}" method="POST" class="inline-block" data-confirm data-confirm-title="{{ __('Delete All Notifications') }}" data-confirm-message="{{ __('Are you sure you want to delete all notifications from your record?') }}">
            @csrf @method('DELETE')
            <button type="submit" class="px-4 py-2 bg-red-500/10 hover:bg-red-500 text-red-500 hover:text-white border border-red-500/20 rounded-xl text-sm font-semibold transition-all flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                {{ __('Delete All Notifications') }}
            </button>
        </form>
    </div>
    @endcan
</div>

@if(session('success'))
<div class="mb-6 px-4 py-3 bg-emerald-500/10 border border-emerald-500/20 text-emerald-600 dark:text-emerald-400 rounded-xl text-sm flex items-center gap-2">
    <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
    {{ session('success') }}
</div>
@endif

<div class="glass-panel rounded-2xl border border-slate-200 dark:border-white/5 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
            <thead>
                <tr class="text-slate-500 dark:text-slate-400 text-xs uppercase tracking-wider border-b border-slate-200 dark:border-white/5">
                    <th class="py-4 px-6 font-medium">{{ __('Alert') }}</th>
                    <th class="py-4 px-6 font-medium">{{ __('Details') }}</th>
                    <th class="py-4 px-6 font-medium">{{ __('Received') }}</th>
                    <th class="py-4 px-6 font-medium">{{ __('Status') }}</th>
                    <th class="py-4 px-6 font-medium text-center">{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200 dark:divide-white/5 text-sm text-slate-600 dark:text-slate-300">
                @forelse($notifications as $notif)
                <tr class="hover:bg-slate-50 dark:hover:bg-white/5 transition-colors {{ is_null($notif->read_at) ? 'bg-amber-50/50 dark:bg-amber-500/5' : '' }}">
                    <td class="py-4 px-6">
                        <div class="font-medium text-slate-900 dark:text-white {{ is_null($notif->read_at) ? 'font-bold' : '' }}">
                            {{ $notif->data['title'] ?? __('System Alert') }}
                        </div>
                    </td>
                    <td class="py-4 px-6">
                        <div class="max-w-md truncate text-slate-700 dark:text-slate-300 {{ is_null($notif->read_at) ? 'font-medium' : '' }}">
                            {{ collect($notif->data)->get('message', __('No additional details provided.')) }}
                        </div>
                    </td>
                    <td class="py-4 px-6 whitespace-nowrap text-slate-500">
                        <span title="{{ $notif->created_at->format('Y-m-d H:i') }}">{{ $notif->created_at->diffForHumans() }}</span>
                    </td>
                    <td class="py-4 px-6 whitespace-nowrap">
                        @if(!is_null($notif->read_at))
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-slate-100 text-slate-800 dark:bg-slate-800 dark:text-slate-300">
                                {{ __('Read') }}
                            </span>
                        @else
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold bg-amber-100 text-amber-800 dark:bg-amber-500/20 dark:text-amber-400 shadow-[0_0_8px_rgba(245,158,11,0.2)]">
                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500 mr-1.5 {{ app()->getLocale() == 'ar' ? 'ml-1.5 mr-0' : '' }}"></span>
                                {{ __('Unread') }}
                            </span>
                        @endif
                    </td>
                    <td class="py-4 px-6 text-center">
                        <div class="flex items-center justify-center gap-3">
                            <a href="{{ $notif->data['url'] ?? '#' }}" onclick="{{ is_null($notif->read_at) ? 'markNotificationReadAdmin(\''.$notif->id.'\', event, \''.($notif->data['url'] ?? '#').'\')' : '' }}" class="text-[#38bdf8] hover:text-blue-500 transition-colors font-semibold tooltip-trigger" data-tooltip="{{ __('View') }}">
                                <svg class="w-5 h-5 mx-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                            </a>
                            
                            @can('manage notifications')
                            <form action="{{ route('admin.notifications.destroy', $notif->id) }}" method="POST" class="inline-block" data-confirm data-confirm-title="{{ __('Delete Notification') }}" data-confirm-message="{{ __('Are you sure you want to delete this notification?') }}">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-red-400 hover:text-red-500 transition-colors font-semibold tooltip-trigger" data-tooltip="{{ __('Delete permanently') }}">
                                    <svg class="w-5 h-5 mx-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                </button>
                            </form>
                            @endcan
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="py-12 text-center">
                        <svg class="w-12 h-12 text-slate-400 mx-auto mb-3 opacity-50" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" /></svg>
                        <p class="text-slate-500 dark:text-slate-400 text-sm">{{ __('No notifications found.') }}</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if($notifications->hasPages())
    <div class="p-4 border-t border-slate-200 dark:border-white/5 bg-slate-50 dark:bg-black/20">
        {{ $notifications->links() }}
    </div>
    @endif
</div>
@endsection
