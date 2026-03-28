@extends('layouts.Admin.App')

@section('content')
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-end w-full gap-4">
        <div>
            <h1 class="text-2xl lg:text-3xl font-bold text-slate-900 dark:text-white mb-1 tracking-tight">{{ __('Worker') }} <span class="text-transparent bg-clip-text bg-gradient-to-r from-amber-400 to-orange-500">{{ __('Attendance & Shifts') }}</span></h1>
            <p class="text-sm text-slate-400">
                {{ __('Managing shifts for Active Work Day:') }} 
                <span class="font-bold text-emerald-500">{{ $activeWorkDay->start_time->translatedFormat('Y-m-d h:i A') }}</span>
            </p>
        </div>
        <a href="{{ route('admin.attendance.presence') }}" class="bg-emerald-500 hover:bg-emerald-400 text-black px-6 py-2.5 rounded-xl text-sm font-bold transition-all flex items-center shadow-lg shadow-emerald-500/20">
            <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
            {{ __('Manage Daily Presence') }}
        </a>
    </div>
</div>

@php 
    $pendingWorkers = $workers->filter(function($w) use ($activeWorkDay) {
        return $w->attendances->isNotEmpty() && 
               !$w->shifts->where('work_day_id', $activeWorkDay->id)->whereNull('check_out')->first();
    });
@endphp

@if($pendingWorkers->isNotEmpty())
    <!-- Pending Shifts Alert -->
    <div class="mb-8 p-4 bg-amber-500/10 border border-amber-500/20 rounded-2xl flex items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            <div class="w-10 h-10 rounded-full bg-amber-500/20 flex items-center justify-center text-amber-500">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
            </div>
            <div>
                <h3 class="text-sm font-bold text-amber-500">{{ __('Staff Ready for Shift') }}</h3>
                <p class="text-[11px] text-slate-400">{{ $pendingWorkers->count() }} {{ __('workers are marked as present but haven\'t started a shift yet.') }}</p>
            </div>
        </div>
        <div class="flex -space-x-2">
            @foreach($pendingWorkers->take(5) as $pw)
                <div class="w-8 h-8 rounded-full border-2 border-[#0f1115] bg-slate-800 flex items-center justify-center text-[10px] font-bold text-white shadow-lg" title="{{ $pw->first_name }}">
                    {{ mb_substr($pw->first_name, 0, 1) }}
                </div>
            @endforeach
            @if($pendingWorkers->count() > 5)
                <div class="w-8 h-8 rounded-full border-2 border-[#0f1115] bg-slate-700 flex items-center justify-center text-[10px] font-bold text-slate-400 shadow-lg">
                    +{{ $pendingWorkers->count() - 5 }}
                </div>
            @endif
        </div>
    </div>
@endif

<!-- Alerts -->
@if(session('success_message'))
    <div class="mb-6 p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-sm flex items-center">
        <svg class="w-5 h-5 {{ app()->getLocale() == 'ar' ? 'ml-3' : 'mr-3' }} flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        {{ session('success_message') }}
    </div>
@endif
@if(session('error_message'))
    <div class="mb-6 p-4 rounded-xl bg-red-500/10 border border-red-500/20 text-red-400 text-sm flex items-center">
        <svg class="w-5 h-5 {{ app()->getLocale() == 'ar' ? 'ml-3' : 'mr-3' }} flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        {{ session('error_message') }}
    </div>
@endif
@if($errors->any())
    <div class="mb-6 p-4 rounded-xl bg-red-500/10 border border-red-500/20 text-red-400 text-sm">
        <ul class="list-disc list-inside">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<!-- Cards Grid -->
<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
    @foreach($workers as $worker)
        @php
            $activeShift = $worker->shifts->whereNull('check_out')->first();
            $completedShifts = $worker->shifts->whereNotNull('check_out');
            
            $advances = $worker->transactions->where('type', 'advance')->sum('amount');
            $allowances = $worker->transactions->where('type', 'allowance')->sum('amount');
            $deductions = $worker->transactions->where('type', 'deduction')->sum('amount');
            
            $earnedWage = $completedShifts->sum('snapshot_daily_wage');
            $netAccrued = $earnedWage + $allowances - $deductions - $advances;
        @endphp

        <div class="glass-panel rounded-2xl border border-white/5 overflow-hidden flex flex-col group relative">
            
            <!-- Header -->
            <div class="p-5 border-b border-white/5 bg-black/10 flex justify-between items-start">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-gradient-to-tr from-amber-500 to-orange-500 p-[1px]">
                        <div class="w-full h-full bg-[#121419] rounded-xl flex items-center justify-center text-amber-500 font-bold text-lg">
                            {{ mb_substr($worker->first_name, 0, 1) }}
                        </div>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-white">{{ $worker->first_name }} {{ $worker->last_name }}</h3>
                        <div class="flex items-center gap-2 mt-0.5">
                            <p class="text-xs text-slate-400 uppercase tracking-widest">{{ $worker->title }}</p>
                            @if($completedShifts->count() > 0)
                                <span class="w-1 h-1 rounded-full bg-slate-600"></span>
                                <span class="text-[10px] text-emerald-500 font-bold uppercase tracking-tighter bg-emerald-500/10 px-1.5 py-0.5 rounded-md border border-emerald-500/10">{{ $completedShifts->count() }} {{ __('Shifts Done') }}</span>
                            @endif
                        </div>
                    </div>
                </div>
                <!-- Status Badge -->
                @php $isPresent = $worker->attendances->count() > 0; @endphp
                @if($activeShift)
                    <span class="px-3 py-1 bg-emerald-500/20 text-emerald-400 text-[10px] uppercase font-bold tracking-wider rounded-md border border-emerald-500/20 shadow-[0_0_10px_rgba(16,185,129,0.2)] animate-pulse">{{ __('On Shift') }}</span>
                @elseif($isPresent)
                    <span class="px-3 py-1 bg-amber-500/20 text-amber-500 text-[10px] uppercase font-bold tracking-wider rounded-md border border-amber-500/20">{{ __('Between Shifts') }}</span>
                @else
                    <span class="px-3 py-1 bg-red-500/20 text-red-500 text-[10px] uppercase font-bold tracking-wider rounded-md border border-red-500/20">{{ __('NOT PRESENT') }}</span>
                @endif
            </div>

            <!-- Stats Body -->
            <div class="p-5 flex-1 space-y-4">
                <div class="flex justify-between items-center text-sm">
                    <span class="text-slate-400">{{ __('Base Daily Wage:') }}</span>
                    <span class="text-white font-medium">{{ number_format($worker->daily_wage, 2) }} {{ $worker->currency->code }}</span>
                </div>
                
                <!-- Transactions Summary -->
                <div class="grid grid-cols-2 gap-3 pt-4 border-t border-white/5 text-xs">
                    <div class="glass-panel p-3 rounded-xl border border-white/5">
                        <p class="text-slate-500 mb-1">{{ __('Advances') }}</p>
                        <p class="text-red-400 font-bold">{{ number_format($advances, 2) }} <span class="text-[10px] text-red-400/70">{{ $worker->currency->code }}</span></p>
                    </div>
                    <div class="glass-panel p-3 rounded-xl border border-white/5">
                        <p class="text-slate-500 mb-1">{{ __('Allowances / Ded') }}</p>
                        <p class="text-slate-200 font-bold">+{{ number_format($allowances, 2) }} / -{{ number_format($deductions, 2) }} <span class="text-[10px] text-slate-500">{{ $worker->currency->code }}</span></p>
                    </div>
                </div>
                
                <!-- Net Earnings -->
                <div class="flex justify-between items-center bg-amber-500/5 p-3 rounded-xl border border-amber-500/10 text-sm">
                    <span class="text-amber-500 font-medium">{{ __('Net Earned Today') }}</span>
                    <span class="text-amber-400 font-bold text-lg">{{ number_format($netAccrued, 2) }} {{ $worker->currency->code }}</span>
                </div>
            </div>

            <!-- Actions Footer -->
            <div class="p-4 border-t border-white/5 bg-black/20 flex gap-2">
                @if(!$activeShift)
                        @if(!$isPresent)
                            <div class="p-4 bg-red-500/10 border border-red-500/20 rounded-xl text-center">
                                <p class="text-[10px] font-bold text-red-400 uppercase tracking-widest">{{ __('Worker is not present today') }}</p>
                                <p class="text-[9px] text-red-400/60 mt-1">{{ __('You must mark attendance above first.') }}</p>
                            </div>
                        @else
                            <form action="{{ route('admin.attendance.clock_in') }}" method="POST" class="flex-1 space-y-3">
                                @csrf
                                <input type="hidden" name="worker_id" value="{{ $worker->id }}">
                                
                                <div class="relative group/time">
                                    <label class="block text-[10px] text-slate-500 uppercase font-bold mb-1 ml-1">{{ __('Shift Start Time') }}</label>
                                    <input type="datetime-local" name="check_in" value="{{ now()->format('Y-m-d\TH:i') }}" 
                                        class="w-full px-3 py-2 bg-white/5 dark:bg-black/20 border border-slate-200 dark:border-white/5 rounded-lg text-xs text-slate-700 dark:text-slate-300 focus:outline-none focus:border-emerald-500/50 transition-all font-medium">
                                </div>

                                <div>
                                    <label class="block text-[10px] text-slate-500 uppercase font-bold mb-1 ml-1">{{ __('Bundles Received') }}</label>
                                    <input type="number" name="bundles_received" value="0" min="0" 
                                        class="w-full px-3 py-2 bg-white/5 dark:bg-black/20 border border-slate-200 dark:border-white/5 rounded-lg text-xs text-slate-700 dark:text-slate-300 focus:outline-none focus:border-emerald-500/50 transition-all font-medium"
                                        placeholder="0">
                                </div>

                                <button type="submit" class="w-full py-2.5 bg-emerald-500/10 hover:bg-emerald-500/20 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20 rounded-xl font-bold text-xs uppercase tracking-wider transition-colors focus:ring-2 focus:ring-emerald-500">
                                    {{ $completedShifts->count() > 0 ? __('Start New Shift') : __('Clock In') }}
                                </button>
                            </form>
                        @endif
                @else
                    <!-- Clock OUT & Transact -->
                    <button type="button" x-data="" @click="$dispatch('open-transaction-modal', { id: {{ $worker->id }}, name: '{{ $worker->first_name }} {{ $worker->last_name }}', currency: '{{ $worker->currency->code }}' })" class="flex-1 py-2.5 bg-slate-800 hover:bg-slate-700 text-slate-300 border border-white/10 rounded-xl font-bold text-xs uppercase tracking-wider transition-colors">
                        {{ __('Add Transaction') }}
                    </button>
                    
                    <form action="{{ route('admin.attendance.clock_out', $activeShift->id) }}" method="POST" class="flex-1 space-y-2"
                          x-data="{ 
                              cashCurrencyId: '', 
                              isLocal: true,
                              exchangeRate: 1,
                              setCurrency(id) {
                                  this.cashCurrencyId = id;
                                  const curr = @js($currencies->map(fn($c) => ['id' => $c->id, 'is_default' => $c->is_default, 'exchange_rate' => $c->exchange_rate]));
                                  const found = curr.find(c => c.id == id);
                                  this.isLocal = found ? found.is_default : true;
                                  this.exchangeRate = found ? found.exchange_rate : 1;
                              }
                          }">
                        @csrf
                        <div>
                            <label class="block text-[10px] text-slate-500 uppercase font-bold mb-1 ml-1">{{ __('Shift End Time') }}</label>
                            <input type="datetime-local" name="check_out" value="{{ now()->format('Y-m-d\TH:i') }}" 
                                class="w-full px-3 py-2 bg-white/5 dark:bg-black/20 border border-slate-200 dark:border-white/5 rounded-lg text-xs text-slate-700 dark:text-slate-300 focus:outline-none focus:border-amber-500/50 transition-all font-medium">
                        </div>

                        <div>
                            <label class="block text-[10px] text-slate-500 uppercase font-bold mb-1 ml-1">{{ __('Bundles Returned') }}</label>
                            <input type="number" name="bundles_returned" value="0" min="0" 
                                class="w-full px-3 py-2 bg-white/5 dark:bg-black/20 border border-slate-200 dark:border-white/5 rounded-lg text-xs text-slate-700 dark:text-slate-300 focus:outline-none focus:border-amber-500/50 transition-all font-medium"
                                placeholder="0">
                        </div>

                        {{-- Cash Collected --}}
                        <div>
                            <label class="block text-[10px] text-slate-500 uppercase font-bold mb-1 ml-1">{{ __('Cash Collected') }}</label>
                            <input type="number" step="0.01" name="cash_collected" value="0" min="0" 
                                class="w-full px-3 py-2 bg-white/5 dark:bg-black/20 border border-slate-200 dark:border-white/5 rounded-lg text-xs text-slate-700 dark:text-slate-300 focus:outline-none focus:border-amber-500/50 transition-all font-medium"
                                placeholder="0.00">
                        </div>

                        {{-- Cash Currency --}}
                        <div>
                            <label class="block text-[10px] text-slate-500 uppercase font-bold mb-1 ml-1">{{ __('Currency') }}</label>
                            <select name="cash_currency_id" x-model="cashCurrencyId" @change="setCurrency($event.target.value)"
                                class="w-full px-3 py-2 bg-white/5 dark:bg-black/20 border border-slate-200 dark:border-white/5 rounded-lg text-xs text-slate-700 dark:text-slate-300 focus:outline-none focus:border-amber-500/50 transition-all font-medium appearance-none">
                                <option value="">{{ __('Select Currency...') }}</option>
                                @foreach($currencies as $curr)
                                    <option value="{{ $curr->id }}" {{ $curr->is_default ? 'selected' : '' }}>{{ $curr->code }} - {{ $curr->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Exchange Rate (only for non-local) --}}
                        <div x-show="!isLocal" x-transition>
                            <label class="block text-[10px] text-slate-500 uppercase font-bold mb-1 ml-1">{{ __('Exchange Rate') }}</label>
                            <input type="number" step="0.01" name="cash_exchange_rate" :value="exchangeRate" min="0"
                                class="w-full px-3 py-2 bg-white/5 dark:bg-black/20 border border-slate-200 dark:border-white/5 rounded-lg text-xs text-slate-700 dark:text-slate-300 focus:outline-none focus:border-amber-500/50 transition-all font-medium"
                                placeholder="1.00">
                        </div>

                        <button type="submit" class="w-full py-2.5 bg-amber-500/20 hover:bg-amber-500/30 text-amber-600 dark:text-amber-500 border border-amber-500/20 rounded-xl font-bold text-xs uppercase tracking-wider transition-colors focus:ring-2 focus:ring-amber-500">
                            {{ __('Clock Out') }}
                        </button>
                    </form>
                @endif
            </div>

        </div>
    @endforeach
</div>

<!-- Transaction Modal -->
<div x-data="{ open: false, workerId: '', workerName: '', currency: '' }"
     @open-transaction-modal.window="open = true; workerId = $event.detail.id; workerName = $event.detail.name; currency = $event.detail.currency" 
     x-show="open" 
     class="fixed inset-0 z-[100] overflow-y-auto" style="display: none;">
    
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
        <div x-show="open" x-transition.opacity class="fixed inset-0 transition-opacity bg-black/60 backdrop-blur-sm" @click="open = false"></div>

        <div x-show="open" x-transition 
             class="relative inline-block w-full max-w-md p-6 overflow-hidden text-left align-middle transition-all transform glass-panel rounded-2xl shadow-xl border border-white/10"
             {{ app()->getLocale() == 'ar' ? 'dir="rtl"' : 'dir="ltr"' }}>
            
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-xl font-bold text-white">{{ __('Add Transaction') }}</h3>
                <button @click="open = false" class="text-slate-400 hover:text-white transition-colors">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form action="{{ route('admin.attendance.transaction') }}" method="POST" class="space-y-4">
                @csrf
                <input type="hidden" name="worker_id" :value="workerId">
                
                <div class="p-3 bg-amber-500/10 border border-amber-500/20 rounded-xl text-amber-400 text-sm mb-4">
                    {{ __('Issuing transaction directly for worker:') }} <span class="font-bold" x-text="workerName"></span>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">{{ __('Transaction Type') }}</label>
                    <select name="type" required class="block w-full px-4 py-3 bg-[#0f1115] border border-white/5 rounded-xl text-sm text-white focus:border-amber-500/50 focus:ring-1 focus:ring-amber-500/50 transition-all font-medium appearance-none">
                        <option value="advance">{{ __('Advance') }}</option>
                        <option value="allowance">{{ __('Allowance') }}</option>
                        <option value="deduction">{{ __('Deduction') }}</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">{{ __('Amount') }}</label>
                    <div class="relative">
                        <input type="number" step="0.01" name="amount" required 
                            class="block w-full {{ app()->getLocale() == 'ar' ? 'pl-16 pr-4' : 'pr-16 pl-4' }} py-3 bg-[#0f1115] border border-white/5 rounded-xl text-sm placeholder-slate-600 text-white focus:outline-none focus:border-amber-500/50 focus:ring-1 focus:ring-amber-500/50 transition-all font-medium" 
                            placeholder="0.00">
                        <div class="absolute inset-y-0 {{ app()->getLocale() == 'ar' ? 'left-0 pl-4' : 'right-0 pr-4' }} flex items-center pointer-events-none">
                            <span class="text-slate-400 text-sm font-bold" x-text="currency"></span>
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">{{ __('Notes (Optional)') }}</label>
                    <input type="text" name="notes" class="block w-full px-4 py-3 bg-[#0f1115] border border-white/5 rounded-xl text-sm text-white focus:border-amber-500/50 focus:ring-1 focus:ring-amber-500/50 transition-all font-medium" placeholder="" >
                </div>

                <div class="pt-4 flex gap-3">
                    <button type="button" @click="open = false" class="flex-1 bg-transparent hover:bg-white/5 text-white border border-white/10 px-4 py-3 rounded-xl text-sm font-bold transition-colors">
                        {{ __('Cancel') }}
                    </button>
                    <button type="submit" class="flex-1 bg-amber-500 hover:bg-amber-400 text-[#0f1115] px-4 py-3 rounded-xl text-sm font-bold transition-colors shadow-[0_0_15px_rgba(245,158,11,0.3)]">
                        {{ __('Save Transaction') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
