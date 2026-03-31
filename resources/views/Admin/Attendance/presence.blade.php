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
    <div x-data="{ 
            selectedWorkers: [],
            allWorkerIds: {{ json_encode($workers->pluck('id')) }},
            workerNames: {{ json_encode($workers->mapWithKeys(fn($w) => [$w->id => $w->first_name . ' ' . $w->last_name])) }},
            toggleWorker(id) {
                if (this.selectedWorkers.includes(id)) {
                    this.selectedWorkers = this.selectedWorkers.filter(w => w !== id);
                } else {
                    this.selectedWorkers.push(id);
                }
            },
            selectAll() {
                if (this.selectedWorkers.length === this.allWorkerIds.length) {
                    this.selectedWorkers = [];
                } else {
                    this.selectedWorkers = [...this.allWorkerIds];
                }
            },
            getSelectedArrivable() {
                // Workers with no attendance record
                const presentIds = [
                    @foreach($workers as $w)
                        @if($w->attendances->first()) {{ $w->id }}, @endif
                    @endforeach
                ];
                return this.selectedWorkers.filter(id => !presentIds.includes(id));
            },
            getSelectedDepartable() {
                // Workers with attendance but no departure
                const departableIds = [
                    @foreach($workers as $w)
                        @php $attn = $w->attendances->first(); @endphp
                        @if($attn && !$attn->departure_time) {{ $w->id }}, @endif
                    @endforeach
                ];
                return this.selectedWorkers.filter(id => departableIds.includes(id));
            }
         }" 
         class="p-8 glass-panel rounded-3xl border border-white/5 relative">
        <div class="flex flex-col md:flex-row md:items-center justify-between mb-8 gap-4">
            <div>
                <h2 class="text-xl font-bold text-white flex items-center gap-3">
                    <span class="w-3 h-3 rounded-full bg-emerald-500 animate-pulse"></span>
                    {{ __('Mark Daily Attendance') }}
                </h2>
                <p class="text-sm text-slate-500 mt-1">{{ __('Select workers who have arrived and log their initial entry time.') }}</p>
            </div>
            <div class="flex items-center gap-3">
                <button type="button" @click="selectAll()" class="px-4 py-2 bg-white/5 hover:bg-white/10 text-slate-300 border border-white/5 rounded-xl text-xs font-bold transition-all">
                    <span x-text="selectedWorkers.length === allWorkerIds.length ? '{{ __('Deselect All') }}' : '{{ __('Select All') }}'"></span>
                </button>
                <button type="button" @click="$dispatch('open-attendance-modal')" class="px-6 py-3 bg-white/5 hover:bg-white/10 text-white border border-white/10 rounded-2xl font-bold flex items-center gap-2 transition-all group">
                    <span class="w-6 h-6 rounded-full bg-emerald-500 text-black flex items-center justify-center group-hover:scale-110 transition-transform">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                    </span>
                    {{ __('Log Arrival') }}
                </button>
            </div>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6 gap-4">
            @forelse($workers as $worker)
                @php $attendance = $worker->attendances->first(); @endphp
                <div @click="toggleWorker({{ $worker->id }})" 
                     :class="selectedWorkers.includes({{ $worker->id }}) ? 'border-amber-500 ring-1 ring-amber-500/50' : 'border-white/5'"
                     class="group/card relative p-4 rounded-2xl border transition-all cursor-pointer select-none {{ $attendance ? 'bg-emerald-500/5' : 'bg-white/5' }}">
                    
                    <div class="absolute top-4 {{ app()->getLocale() == 'ar' ? 'left-4' : 'right-4' }} z-20">
                        <div @click.stop="toggleWorker({{ $worker->id }})" 
                             :class="selectedWorkers.includes({{ $worker->id }}) ? 'bg-amber-500 border-amber-500 shadow-[0_0_15px_rgba(245,158,11,0.4)] scale-110' : 'bg-black/40 border-white/10 hover:border-white/20'"
                             class="w-6 h-6 rounded-lg border flex items-center justify-center transition-all duration-300 cursor-pointer overflow-hidden group/check">
                            <span x-show="selectedWorkers.includes({{ $worker->id }})" 
                                  x-transition:enter="transition ease-out duration-200"
                                  x-transition:enter-start="scale-0 opacity-0"
                                  x-transition:enter-end="scale-100 opacity-100"
                                  class="text-black">
                                <svg class="w-4 h-4" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                </svg>
                            </span>
                        </div>
                    </div>

                    <div class="flex items-center gap-3 mb-4 {{ !$attendance ? 'grayscale opacity-60 group-hover/card:grayscale-0 group-hover/card:opacity-100 transition-all' : '' }}">
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center font-bold {{ $attendance ? 'bg-emerald-500 text-black shadow-[0_0_15px_rgba(16,185,129,0.3)]' : 'bg-slate-700 text-slate-400' }}">
                            {{ mb_substr($worker->first_name, 0, 1) }}
                        </div>
                        <div class="min-w-0">
                            <p class="text-sm font-bold text-white truncate">{{ $worker->first_name }}</p>
                            <p class="text-[10px] text-slate-500 uppercase tracking-widest">{{ $worker->title }}</p>
                        </div>
                    </div>
                    
                    @if($attendance)
                        <div class="pt-3 border-t border-emerald-500/10 space-y-2">
                            <div class="flex items-center justify-between text-[10px]">
                                <span class="text-emerald-500/60 uppercase font-bold">{{ __('ARRIVED') }}</span>
                                <span class="text-emerald-400 font-bold bg-emerald-400/10 px-1.5 py-0.5 rounded-md">{{ $attendance->arrival_time->translatedFormat('h:i A') }}</span>
                            </div>
                            @if($attendance->departure_time)
                                <div class="flex items-center justify-between text-[10px]">
                                    <span class="text-amber-500/60 uppercase font-bold">{{ __('DEPARTED') }}</span>
                                    <span class="text-amber-400 font-bold bg-amber-400/10 px-1.5 py-0.5 rounded-md">{{ $attendance->departure_time->translatedFormat('h:i A') }}</span>
                                </div>
                            @else
                                <button type="button" 
                                    @click.stop="$dispatch('open-departure-modal', { 
                                        id: {{ $attendance->id }}, 
                                        name: '{{ $worker->first_name }} {{ $worker->last_name }}',
                                        arrival: '{{ $attendance->arrival_time->translatedFormat('Y-m-d\TH:i') }}'
                                    })"
                                    class="w-full mt-2 py-1.5 bg-amber-500/20 hover:bg-amber-500/30 text-amber-500 text-[10px] font-bold rounded-lg border border-amber-500/20 transition-all pointer-events-auto grayscale-0 opacity-100">
                                    {{ __('Sign Out') }}
                                </button>
                            @endif
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

        <!-- Sticky Bulk Actions Bar -->
        <div x-show="selectedWorkers.length > 0" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-10"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 translate-y-10"
             class="fixed bottom-8 left-1/2 -translate-x-1/2 z-[90] w-fit min-w-[300px] max-w-[90vw] glass-panel rounded-full border border-white/10 shadow-2xl p-2 px-6 flex items-center justify-between gap-8 animate-in fade-in slide-in-from-bottom-5">
            <div class="flex items-center gap-3">
                <span class="flex items-center justify-center w-6 h-6 rounded-full bg-amber-500 text-black text-[10px] font-bold" x-text="selectedWorkers.length"></span>
                <span class="text-xs font-bold text-white uppercase tracking-widest">{{ __('Selected') }}</span>
            </div>
            
            <div class="h-8 w-px bg-white/10"></div>
            
            <div class="flex items-center gap-2">
                <button x-show="getSelectedArrivable().length > 0" 
                        @click="$dispatch('open-bulk-arrival-modal', { ids: getSelectedArrivable() })"
                        class="px-4 py-2 bg-emerald-500 hover:bg-emerald-400 text-black rounded-full text-[10px] font-bold transition-all shadow-lg flex items-center gap-2">
                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                    {{ __('Group Arrival') }} (<span x-text="getSelectedArrivable().length"></span>)
                </button>
                
                <button x-show="getSelectedDepartable().length > 0" 
                        @click="$dispatch('open-bulk-departure-modal', { ids: getSelectedDepartable() })"
                        class="px-4 py-2 bg-amber-500 hover:bg-amber-400 text-black rounded-full text-[10px] font-bold transition-all shadow-lg flex items-center gap-2">
                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    {{ __('Group Departure') }} (<span x-text="getSelectedDepartable().length"></span>)
                </button>
                
                <button @click="selectedWorkers = []" class="p-2 text-slate-400 hover:text-white transition-colors">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        </div>
    </div>
@endif

<!-- Bulk Arrival Modal -->
<div x-data="{ open: false, workerIds: [] }"
     @open-bulk-arrival-modal.window="open = true; workerIds = $event.detail.ids" 
     x-show="open" 
     class="fixed inset-0 z-[100] overflow-y-auto" style="display: none;">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
        <div x-show="open" x-transition.opacity class="fixed inset-0 transition-opacity bg-black/60 backdrop-blur-sm" @click="open = false"></div>
        <div x-show="open" x-transition 
             class="relative inline-block w-full max-w-md p-6 overflow-hidden text-left align-middle transition-all transform glass-panel rounded-2xl shadow-xl border border-white/10"
             {{ app()->getLocale() == 'ar' ? 'dir="rtl"' : 'dir="ltr"' }}>
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-xl font-bold text-white">{{ __('Group Arrival') }}</h3>
                <button @click="open = false" class="text-slate-400 hover:text-white transition-colors">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <form action="{{ route('admin.attendance.bulk_mark_attendance') }}" method="POST" class="space-y-4">
                @csrf
                <template x-for="id in workerIds" :key="id">
                    <input type="hidden" name="worker_ids[]" :value="id">
                </template>

                <div class="mb-6 p-4 bg-emerald-500/5 border border-emerald-500/10 rounded-2xl">
                    <p class="text-[10px] text-slate-400 uppercase tracking-widest mb-2 font-bold">{{ __('Marking attendance for:') }}</p>
                    <div class="flex flex-wrap gap-2">
                        <template x-for="id in workerIds" :key="id">
                            <span class="px-2 py-1 bg-emerald-500/10 text-emerald-400 rounded-md text-[10px] font-bold border border-emerald-500/10"
                                  x-text="workerNames[id]"></span>
                        </template>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">{{ __('Arrival Time') }}</label>
                    <input type="datetime-local" name="arrival_time" value="{{ now()->translatedFormat('Y-m-d\TH:i') }}" 
                        min="{{ $activeWorkDay->start_time->translatedFormat('Y-m-d\TH:i') }}"
                        required 
                        class="block w-full px-4 py-3 bg-[#0f1115] border border-white/5 rounded-xl text-sm text-white focus:outline-none focus:border-amber-500/50 focus:ring-1 focus:ring-amber-500/50 transition-all font-medium">
                    <p class="text-[10px] text-slate-500 mt-2 italic px-1">
                        {{ __('Must be after') }} {{ $activeWorkDay->start_time->translatedFormat('Y-m-d h:i A') }}
                    </p>
                </div>
                <div class="pt-4">
                    <button type="submit" class="w-full bg-emerald-500 hover:bg-emerald-400 text-black px-4 py-3 rounded-xl text-sm font-bold transition-colors shadow-lg">
                        {{ __('Save Group Arrival') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Bulk Departure Modal -->
<div x-data="{ open: false, workerIds: [] }"
     @open-bulk-departure-modal.window="open = true; workerIds = $event.detail.ids" 
     x-show="open" 
     class="fixed inset-0 z-[100] overflow-y-auto" style="display: none;">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
        <div x-show="open" x-transition.opacity class="fixed inset-0 transition-opacity bg-black/60 backdrop-blur-sm" @click="open = false"></div>
        <div x-show="open" x-transition 
             class="relative inline-block w-full max-w-md p-6 overflow-hidden text-left align-middle transition-all transform glass-panel rounded-2xl shadow-xl border border-white/10"
             {{ app()->getLocale() == 'ar' ? 'dir="rtl"' : 'dir="ltr"' }}>
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-xl font-bold text-white">{{ __('Group Departure') }}</h3>
                <button @click="open = false" class="text-slate-400 hover:text-white transition-colors">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <form action="{{ route('admin.attendance.bulk_mark_departure') }}" method="POST" class="space-y-4">
                @csrf
                <template x-for="id in workerIds" :key="id">
                    <input type="hidden" name="worker_ids[]" :value="id">
                </template>

                <div class="mb-6 p-4 bg-amber-500/5 border border-amber-500/10 rounded-2xl">
                    <p class="text-[10px] text-slate-400 uppercase tracking-widest mb-2 font-bold">{{ __('Signing out:') }}</p>
                    <div class="flex flex-wrap gap-2">
                        <template x-for="id in workerIds" :key="id">
                            <span class="px-2 py-1 bg-amber-500/10 text-amber-400 rounded-md text-[10px] font-bold border border-amber-500/10"
                                  x-text="workerNames[id]"></span>
                        </template>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">{{ __('Departure Time') }}</label>
                    <input type="datetime-local" name="departure_time" value="{{ now()->translatedFormat('Y-m-d\TH:i') }}" 
                        min="{{ $activeWorkDay->start_time->translatedFormat('Y-m-d\TH:i') }}"
                        required 
                        class="block w-full px-4 py-3 bg-[#0f1115] border border-white/5 rounded-xl text-sm text-white focus:outline-none focus:border-amber-500/50 focus:ring-1 focus:ring-amber-500/50 transition-all font-medium">
                    <p class="text-[10px] text-slate-500 mt-2 italic px-1">
                        {{ __('Must be after arrival time.') }}
                    </p>
                </div>
                <div class="pt-4">
                    <button type="submit" class="w-full bg-amber-500 hover:bg-amber-400 text-black px-4 py-3 rounded-xl text-sm font-bold transition-colors shadow-lg">
                        {{ __('Save Group Departure') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>


<!-- Departure Modal -->
<div x-data="{ open: false, attendanceId: '', workerName: '', arrivalTime: '' }"
     @open-departure-modal.window="open = true; attendanceId = $event.detail.id; workerName = $event.detail.name; arrivalTime = $event.detail.arrival" 
     x-show="open" 
     class="fixed inset-0 z-[100] overflow-y-auto" style="display: none;">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
        <div x-show="open" x-transition.opacity class="fixed inset-0 transition-opacity bg-black/60 backdrop-blur-sm" @click="open = false"></div>
        <div x-show="open" x-transition 
             class="relative inline-block w-full max-w-md p-6 overflow-hidden text-left align-middle transition-all transform glass-panel rounded-2xl shadow-xl border border-white/10"
             {{ app()->getLocale() == 'ar' ? 'dir="rtl"' : 'dir="ltr"' }}>
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-xl font-bold text-white">{{ __('Log Departure') }}</h3>
                <button @click="open = false" class="text-slate-400 hover:text-white transition-colors">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="mb-6 p-4 bg-amber-500/5 border border-amber-500/10 rounded-2xl">
                <p class="text-xs text-slate-400 uppercase tracking-widest mb-1">{{ __('Worker') }}</p>
                <p class="text-lg font-bold text-white" x-text="workerName"></p>
                <div class="flex items-center gap-2 mt-2 text-[10px] text-emerald-500 font-bold bg-emerald-500/10 w-fit px-2 py-1 rounded-md">
                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    {{ __('Arrived at:') }} <span x-text="new Date(arrivalTime).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})"></span>
                </div>
            </div>
            <form :action="`/admin/attendance/${attendanceId}/mark-departure`" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">{{ __('Departure Time') }}</label>
                    <input type="datetime-local" name="departure_time" value="{{ now()->translatedFormat('Y-m-d\TH:i') }}" 
                        :min="arrivalTime"
                        required 
                        class="block w-full px-4 py-3 bg-[#0f1115] border border-white/5 rounded-xl text-sm text-white focus:outline-none focus:border-amber-500/50 focus:ring-1 focus:ring-amber-500/50 transition-all font-medium">
                    <p class="text-[10px] text-slate-500 mt-2 italic px-1">
                        {{ __('Must be after arrival time.') }}
                    </p>
                </div>
                <div class="pt-4">
                    <button type="submit" class="w-full bg-amber-500 hover:bg-amber-400 text-[#0f1115] px-4 py-3 rounded-xl text-sm font-bold transition-colors shadow-[0_0_15px_rgba(245,158,11,0.3)]">
                        {{ __('Save Departure') }}
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
                                    ->groupBy(fn($a) => $a->arrival_time->translatedFormat('Y-m-d'));
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

<!-- Attendance Modal -->
<div x-data="{ 
        open: false, 
        search: '',
        selectedIds: [],
        workers: {{ json_encode($workers->filter(fn($w) => !$w->attendances->first())->map(fn($w) => [
            'id' => $w->id,
            'name' => $w->first_name . ' ' . $w->last_name,
            'title' => $w->title
        ])->values()) }},
        get filteredWorkers() {
            return this.workers.filter(w => w.name.toLowerCase().includes(this.search.toLowerCase()));
        },
        toggleId(id) {
            if (this.selectedIds.includes(id)) {
                this.selectedIds = this.selectedIds.filter(i => i !== id);
            } else {
                this.selectedIds.push(id);
            }
        }
     }"
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
            
            <form action="{{ route('admin.attendance.bulk_mark_attendance') }}" method="POST" class="space-y-4">
                @csrf
                <template x-for="id in selectedIds" :key="id">
                    <input type="hidden" name="worker_ids[]" :value="id">
                </template>

                <div>
                    <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">{{ __('Search & Select Personnel') }}</label>
                    <div class="relative group mb-3">
                        <div class="absolute inset-y-0 {{ app()->getLocale() == 'ar' ? 'right-0 pr-3' : 'left-0 pl-3' }} flex items-center pointer-events-none">
                            <svg class="h-4 w-4 text-slate-500 group-focus-within:text-amber-500 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        </div>
                        <input type="text" x-model="search" placeholder="{{ __('Filter by name...') }}" 
                               class="block w-full {{ app()->getLocale() == 'ar' ? 'pr-10 pl-4' : 'pl-10 pr-4' }} py-2.5 bg-black/40 border border-white/10 rounded-xl text-sm text-white focus:outline-none focus:border-amber-500/50 transition-all">
                    </div>

                    <div class="max-h-[200px] overflow-y-auto pr-2 space-y-1 custom-scrollbar">
                        <template x-for="worker in filteredWorkers" :key="worker.id">
                            <div @click="toggleId(worker.id)" 
                                 :class="selectedIds.includes(worker.id) ? 'bg-amber-500/10 border-amber-500/30' : 'bg-white/5 border-white/5 hover:bg-white/10'"
                                 class="p-3 border rounded-xl cursor-pointer transition-all flex items-center justify-between group/item">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-lg bg-slate-800 flex items-center justify-center text-xs font-bold text-slate-400 group-hover/item:bg-amber-500 group-hover/item:text-black transition-colors" x-text="worker.name.charAt(0)"></div>
                                    <div class="min-w-0">
                                        <p class="text-sm font-bold text-white truncate" x-text="worker.name"></p>
                                        <p class="text-[10px] text-slate-500 uppercase tracking-widest" x-text="worker.title"></p>
                                    </div>
                                </div>
                                <div :class="selectedIds.includes(worker.id) ? 'bg-amber-500 scale-110' : 'bg-black/40 border-white/10'"
                                     class="w-5 h-5 rounded-md border flex items-center justify-center transition-all duration-300">
                                    <svg x-show="selectedIds.includes(worker.id)" class="w-3 h-3 text-black" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                            </div>
                        </template>
                        <template x-if="filteredWorkers.length === 0">
                            <div class="text-center py-4 text-slate-500 text-xs italic">{{ __('No personnel found matching search.') }}</div>
                        </template>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">{{ __('Arrival Time') }}</label>
                    <input type="datetime-local" name="arrival_time" value="{{ now()->translatedFormat('Y-m-d\TH:i') }}" 
                        min="{{ $activeWorkDay->start_time->translatedFormat('Y-m-d\TH:i') }}"
                        required 
                        class="block w-full px-4 py-3 bg-[#0f1115] border border-white/5 rounded-xl text-sm text-white focus:outline-none focus:border-amber-500/50 focus:ring-1 focus:ring-amber-500/50 transition-all font-medium">
                    <p class="text-[10px] text-slate-500 mt-2 italic px-1">
                        {{ __('Must be after') }} {{ $activeWorkDay->start_time->translatedFormat('Y-m-d h:i A') }}
                    </p>
                </div>

                <div class="pt-4">
                    <button type="submit" 
                            :disabled="selectedIds.length === 0"
                            :class="selectedIds.length > 0 ? 'bg-amber-500 hover:bg-amber-400 text-[#0f1115]' : 'bg-slate-700 text-slate-500 cursor-not-allowed grayscale'"
                            class="w-full px-4 py-3 rounded-xl text-sm font-bold transition-all shadow-[0_0_15px_rgba(245,158,11,0.3)] flex items-center justify-center gap-2">
                        {{ __('Save Arrival') }}
                        <span x-show="selectedIds.length > 0" class="px-2 py-0.5 bg-black/20 rounded text-[10px]" x-text="selectedIds.length"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>@endsection
