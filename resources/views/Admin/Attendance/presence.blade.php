@extends('layouts.Admin.App')

@section('content')
<div class="mb-8">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-end w-full gap-4">
        <div>
            <h1 class="text-2xl lg:text-3xl font-bold text-slate-900 dark:text-white mb-1 tracking-tight">{{ __('Daily Staff Presence') }}</h1>
            <p class="text-sm text-slate-400">
                {{ __('Marking staff as present for Active Work Day:') }} 
                @if($activeWorkDay)
                    <span class="font-bold text-emerald-500">{{ $activeWorkDay->start_time->translatedFormat('Y-m-d h:i A') }}</span>
                @else
                    <span class="text-red-400 font-bold italic">{{ __('No Active Work Day') }}</span>
                @endif
            </p>
        </div>
        <div class="flex gap-2">
            <button type="button" @click="$dispatch('open-history-modal')" class="bg-white/5 shadow-sm hover:bg-white/10 text-white px-4 py-2 rounded-xl text-sm font-medium border border-white/10 flex items-center transition-all">
                <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                {{ __('Presence History') }}
            </button>
            <a href="{{ route('admin.attendance.index') }}" class="bg-amber-500 hover:bg-amber-400 text-black px-4 py-2 rounded-xl text-sm font-bold transition-all flex items-center shadow-lg">
                <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" /></svg>
                {{ __('Attendance Dashboard') }}
            </a>
        </div>
    </div>
</div>

@if(!$activeWorkDay)
    <div class="glass-panel p-12 rounded-3xl border border-white/5 text-center">
        <div class="w-20 h-20 bg-amber-500/10 rounded-full flex items-center justify-center mx-auto mb-6">
            <svg class="w-10 h-10 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
        </div>
        <h2 class="text-2xl font-bold text-white mb-2">{{ __('No Active Work Day') }}</h2>
        <p class="text-slate-400 mb-8 max-w-md mx-auto">{{ __('You must start a new Work Day before you can mark staff as present.') }}</p>
        <a href="{{ route('admin.work-days.index') }}" class="inline-flex items-center px-6 py-3 bg-amber-500 hover:bg-amber-400 text-black font-bold rounded-xl transition-all shadow-xl">
             {{ __('Go to Work Days') }}
        </a>
    </div>
@else
    <!-- Presence Management Section -->
    <div class="p-8 glass-panel rounded-3xl border border-white/5">
        <div class="flex items-center justify-between mb-8">
            <div>
                <h2 class="text-xl font-bold text-white flex items-center gap-3">
                    <span class="w-3 h-3 rounded-full bg-emerald-500 animate-pulse"></span>
                    {{ __('Mark Daily Attendance') }}
                </h2>
                <p class="text-sm text-slate-500 mt-1">{{ __('Select workers who have arrived and log their initial entry time.') }}</p>
            </div>
            <button type="button" @click="$dispatch('open-attendance-modal')" class="px-6 py-3 bg-white/5 hover:bg-white/10 text-white border border-white/10 rounded-2xl font-bold flex items-center gap-2 transition-all group">
                <span class="w-6 h-6 rounded-full bg-emerald-500 text-black flex items-center justify-center group-hover:scale-110 transition-transform">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                </span>
                {{ __('Log Arrival') }}
            </button>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6 gap-4">
            @forelse($workers as $worker)
                @php $attendance = $worker->attendances->first(); @endphp
                <div class="p-4 rounded-2xl border transition-all {{ $attendance ? 'bg-emerald-500/5 border-emerald-500/20' : 'bg-white/5 border-white/5 grayscale pointer-events-none opacity-60' }}">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center font-bold {{ $attendance ? 'bg-emerald-500 text-black shadow-[0_0_15px_rgba(16,185,129,0.3)]' : 'bg-slate-700 text-slate-400' }}">
                            {{ mb_substr($worker->first_name, 0, 1) }}
                        </div>
                        <div class="min-w-0">
                            <p class="text-sm font-bold text-white truncate">{{ $worker->first_name }}</p>
                            <p class="text-[10px] text-slate-500 uppercase tracking-widest">{{ $worker->title }}</p>
                        </div>
                    </div>
                    
                    @if($attendance)
                        <div class="pt-3 border-t border-emerald-500/10">
                            <div class="flex items-center justify-between text-[10px]">
                                <span class="text-emerald-500/60 uppercase font-bold">{{ __('ARRIVED') }}</span>
                                <span class="text-emerald-400 font-bold bg-emerald-400/10 px-1.5 py-0.5 rounded-md">{{ $attendance->arrival_time->translatedFormat('h:i A') }}</span>
                            </div>
                        </div>
                    @else
                        <div class="pt-3 border-t border-white/5 text-center">
                            <span class="text-[10px] text-slate-600 font-bold uppercase">{{ __('WAITING...') }}</span>
                        </div>
                    @endif
                </div>
            @empty
                <div class="col-span-full py-12 text-center">
                    <p class="text-slate-500 italic">{{ __('No personnel records found.') }}</p>
                </div>
            @endforelse
        </div>
    </div>
@endif

<!-- Attendance Modal -->
<div x-data="{ open: false }"
     @open-attendance-modal.window="open = true" 
     x-show="open" 
     class="fixed inset-0 z-[100] overflow-y-auto" style="display: none;">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
        <div x-show="open" x-transition.opacity class="fixed inset-0 transition-opacity bg-black/60 backdrop-blur-sm" @click="open = false"></div>
        <div x-show="open" x-transition 
             class="relative inline-block w-full max-w-md p-6 overflow-hidden text-left align-middle transition-all transform glass-panel rounded-2xl shadow-xl border border-white/10"
             {{ app()->getLocale() == 'ar' ? 'dir="rtl"' : 'dir="ltr"' }}>
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-xl font-bold text-white">{{ __('Log Arrival') }}</h3>
                <button @click="open = false" class="text-slate-400 hover:text-white transition-colors">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <form action="{{ route('admin.attendance.mark_attendance') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">{{ __('Select Worker') }}</label>
                    <select name="worker_id" required class="block w-full px-4 py-3 bg-[#0f1115] border border-white/5 rounded-xl text-sm text-white focus:border-amber-500/50 focus:ring-1 focus:ring-amber-500/50 transition-all font-medium appearance-none">
                        <option value="">{{ __('Select Worker...') }}</option>
                        @foreach($workers as $worker)
                            <option value="{{ $worker->id }}">{{ $worker->first_name }} {{ $worker->last_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">{{ __('Arrival Time') }}</label>
                    <input type="datetime-local" name="arrival_time" value="{{ now()->format('Y-m-d\TH:i') }}" required 
                        class="block w-full px-4 py-3 bg-[#0f1115] border border-white/5 rounded-xl text-sm text-white focus:outline-none focus:border-amber-500/50 focus:ring-1 focus:ring-amber-500/50 transition-all font-medium">
                </div>
                <div class="pt-4">
                    <button type="submit" class="w-full bg-amber-500 hover:bg-amber-400 text-[#0f1115] px-4 py-3 rounded-xl text-sm font-bold transition-colors shadow-[0_0_15px_rgba(245,158,11,0.3)]">
                        {{ __('Save Arrival') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Presence History Modal -->
<div x-data="{ open: false }"
     @open-history-modal.window="open = true" 
     x-show="open" 
     class="fixed inset-0 z-[100] overflow-y-auto" style="display: none;">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
        <div x-show="open" x-transition.opacity class="fixed inset-0 transition-opacity bg-black/60 backdrop-blur-sm" @click="open = false"></div>
        <div x-show="open" x-transition 
             class="relative inline-block w-full max-w-4xl p-6 overflow-hidden text-left align-middle transition-all transform glass-panel rounded-2xl shadow-xl border border-white/10"
             {{ app()->getLocale() == 'ar' ? 'dir="rtl"' : 'dir="ltr"' }}>
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-xl font-bold text-white">{{ __('Presence History') }}</h3>
                <button @click="open = false" class="text-slate-400 hover:text-white transition-colors">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-{{ app()->getLocale() == 'ar' ? 'right' : 'left' }} text-slate-300">
                    <thead class="text-xs text-slate-500 uppercase bg-white/5">
                        <tr>
                            <th class="px-6 py-3">{{ __('Worker') }}</th>
                            <th class="px-6 py-3">{{ __('Days Present') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        @foreach($workers as $w)
                            @php
                                $presenceDates = \App\Models\WorkerAttendance::where('worker_id', $w->id)
                                    ->latest('arrival_time')
                                    ->get()
                                    ->groupBy(fn($a) => $a->arrival_time->format('Y-m-d'));
                            @endphp
                            <tr>
                                <td class="px-6 py-4 font-bold text-white">{{ $w->first_name }} {{ $w->last_name }}</td>
                                <td class="px-6 py-4">
                                    <div class="flex flex-wrap gap-2">
                                        @forelse($presenceDates as $date => $logs)
                                            <span class="px-2 py-1 bg-emerald-500/10 text-emerald-400 rounded-md text-[10px] border border-emerald-500/10" title="{{ $logs->first()->arrival_time->translatedFormat('h:i A') }}">
                                                {{ \Carbon\Carbon::parse($date)->translatedFormat('M d, Y') }}
                                            </span>
                                        @empty
                                            <span class="text-slate-600 italic">{{ __('No history') }}</span>
                                        @endforelse
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
