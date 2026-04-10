@extends('layouts.Admin.App')

@section('content')
<div class="mb-6 flex flex-col sm:flex-row sm:items-end justify-between gap-4">
    <div>
        <h1 class="text-2xl font-bold text-slate-900 dark:text-white tracking-tight">{{ __('Contact Messages') }}</h1>
        <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">{{ __('Manage and respond to messages sent from the public website.') }}</p>
    </div>
</div>

@if(session('success'))
<div class="mb-6 px-4 py-3 bg-emerald-500/10 border border-emerald-500/20 text-emerald-600 dark:text-emerald-400 rounded-xl text-sm flex items-center gap-2">
    <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
    {{ session('success') }}
</div>
@endif

<div class="glass-panel rounded-2xl border border-slate-200 dark:border-white/5 overflow-hidden">
    <div class="p-4 bg-slate-50 dark:bg-black/20 border-b border-slate-200 dark:border-white/5 flex flex-col sm:flex-row gap-4 justify-between items-center">
        <form action="{{ route('admin.contact-messages.index') }}" method="GET" class="flex w-full sm:w-auto items-center gap-3">
            <div class="relative w-full sm:w-64">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ __('Search by name or email...') }}" class="w-full pl-10 pr-4 py-2 border border-slate-200 dark:border-white/10 rounded-xl bg-white dark:bg-[#0f1115] text-sm text-slate-900 dark:text-white placeholder-slate-400 focus:border-amber-500 focus:ring-1 focus:ring-amber-500 transition-colors">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
            </div>
            
            <select name="status" class="w-full sm:w-auto px-4 py-2 border border-slate-200 dark:border-white/10 rounded-xl bg-white dark:bg-[#0f1115] text-sm text-slate-900 dark:text-white focus:border-amber-500 focus:ring-1 focus:ring-amber-500 transition-colors">
                <option value="all" {{ request('status') == 'all' ? 'selected' : '' }}>{{ __('All Messages') }}</option>
                <option value="unread" {{ request('status') == 'unread' ? 'selected' : '' }}>{{ __('Unread') }}</option>
                <option value="read" {{ request('status') == 'read' ? 'selected' : '' }}>{{ __('Read') }}</option>
            </select>

            <button type="submit" class="px-4 py-2 bg-slate-100 dark:bg-white/5 text-slate-700 dark:text-white rounded-xl text-sm font-semibold hover:bg-slate-200 dark:hover:bg-white/10 transition-colors">{{ __('Filter') }}</button>
        </form>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
            <thead>
                <tr class="text-slate-500 dark:text-slate-400 text-xs uppercase tracking-wider border-b border-slate-200 dark:border-white/5">
                    <th class="py-4 px-6 font-medium">{{ __('Sender') }}</th>
                    <th class="py-4 px-6 font-medium">{{ __('Message Snippet') }}</th>
                    <th class="py-4 px-6 font-medium">{{ __('Date Received') }}</th>
                    <th class="py-4 px-6 font-medium">{{ __('Status') }}</th>
                    <th class="py-4 px-6 font-medium text-center">{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200 dark:divide-white/5 text-sm text-slate-600 dark:text-slate-300">
                @forelse($messages as $msg)
                <tr class="hover:bg-slate-50 dark:hover:bg-white/5 transition-colors {{ !$msg->is_read ? 'bg-amber-50/50 dark:bg-amber-500/5' : '' }}">
                    <td class="py-4 px-6">
                        <div class="font-medium text-slate-900 dark:text-white {{ !$msg->is_read ? 'font-bold' : '' }}">{{ $msg->name }}</div>
                        <div class="text-xs text-slate-500 dark:text-slate-400">{{ $msg->email }}</div>
                    </td>
                    <td class="py-4 px-6">
                        <div class="max-w-md truncate text-slate-700 dark:text-slate-300 {{ !$msg->is_read ? 'font-medium' : '' }}">
                            {{ Str::limit($msg->message, 50) }}
                        </div>
                    </td>
                    <td class="py-4 px-6 whitespace-nowrap text-slate-500 {{ !$msg->is_read ? 'font-medium text-slate-700 dark:text-slate-300' : '' }}">
                        <span title="{{ $msg->created_at->format('Y-m-d H:i') }}">{{ $msg->created_at->diffForHumans() }}</span>
                    </td>
                    <td class="py-4 px-6 whitespace-nowrap">
                        @if($msg->is_read)
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
                            <a href="{{ route('admin.contact-messages.show', $msg->id) }}" class="text-[#38bdf8] hover:text-blue-500 transition-colors font-semibold tooltip-trigger" data-tooltip="{{ __('View full message') }}">
                                <svg class="w-5 h-5 mx-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                            </a>
                            
                            @can('delete contact messages')
                            <form action="{{ route('admin.contact-messages.destroy', $msg->id) }}" method="POST" class="inline-block" data-confirm data-confirm-title="{{ __('Delete Message') }}" data-confirm-message="{{ __('Are you sure you want to delete this message? This action cannot be undone.') }}">
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
                        <svg class="w-12 h-12 text-slate-400 mx-auto mb-3 opacity-50" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
                        <p class="text-slate-500 dark:text-slate-400 text-sm">{{ __('No messages found matching your criteria.') }}</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if($messages->hasPages())
    <div class="p-4 border-t border-slate-200 dark:border-white/5 bg-slate-50 dark:bg-black/20">
        {{ $messages->links() }}
    </div>
    @endif
</div>
@endsection
