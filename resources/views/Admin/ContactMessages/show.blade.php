@extends('layouts.Admin.App')

@section('content')
<div class="mb-6 flex items-center justify-between">
    <div class="flex items-center gap-4">
        <a href="{{ route('admin.contact-messages.index') }}" class="p-2 rounded-full hover:bg-slate-200 dark:hover:bg-white/10 text-slate-500 hover:text-slate-900 dark:text-slate-400 dark:hover:text-white transition-colors">
            <svg class="w-6 h-6 {{ app()->getLocale() == 'ar' ? 'rotate-180' : '' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white tracking-tight">{{ __('Message Details') }}</h1>
        </div>
    </div>
    
    @can('delete contact messages')
    <form action="{{ route('admin.contact-messages.destroy', $contactMessage->id) }}" method="POST" data-confirm data-confirm-title="{{ __('Delete Message') }}" data-confirm-message="{{ __('Are you sure you want to delete this message? This action cannot be undone.') }}">
        @csrf @method('DELETE')
        <button type="submit" class="px-4 py-2 bg-red-500/10 text-red-600 dark:text-red-400 border border-red-500/20 hover:bg-red-500 hover:text-white rounded-xl text-sm font-semibold transition-all shadow-[0_0_15px_rgba(239,68,68,0.15)] flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
            {{ __('Delete Message') }}
        </button>
    </form>
    @endcan
</div>

<div class="glass-panel rounded-2xl border border-slate-200 dark:border-white/5 overflow-hidden">
    <!-- Header -->
    <div class="p-6 bg-slate-50 dark:bg-black/20 border-b border-slate-200 dark:border-white/5 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-full bg-gradient-to-br from-amber-400 to-amber-600 flex items-center justify-center text-white font-bold text-xl shadow-lg">
                {{ mb_substr($contactMessage->name, 0, 1) }}
            </div>
            <div>
                <h2 class="text-lg font-bold text-slate-900 dark:text-white">{{ $contactMessage->name }}</h2>
                <a href="mailto:{{ $contactMessage->email }}" class="text-sm text-[#38bdf8] hover:underline">{{ $contactMessage->email }}</a>
            </div>
        </div>
        <div class="text-slate-500 text-sm font-medium flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            {{ $contactMessage->created_at->format('Y-m-d H:i A') }} 
            <span class="text-xs text-slate-400 ml-1">({{ $contactMessage->created_at->diffForHumans() }})</span>
        </div>
    </div>

    <!-- Message Body -->
    <div class="p-8">
        <label class="block text-xs uppercase tracking-widest text-slate-400 font-bold mb-4">{{ __('Message Content') }}</label>
        <div class="prose prose-slate dark:prose-invert max-w-none text-slate-700 dark:text-slate-300 whitespace-pre-wrap leading-loose">
            {{ $contactMessage->message }}
        </div>
    </div>
</div>
@endsection
