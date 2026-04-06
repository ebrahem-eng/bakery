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
        @can('create attendance')
        <a href="{{ route('admin.attendance.presence') }}" class="bg-emerald-500 hover:bg-emerald-400 text-black px-6 py-2.5 rounded-xl text-sm font-bold transition-all flex items-center shadow-lg shadow-emerald-500/20">
            <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
            {{ __('Manage Daily Presence') }}
        </a>
        @endcan
    </div>

    <br>
    <!-- Alerts -->
    <div class="mt-8 space-y-4">
        @if(session('success_message'))
        <div class="mb-6 p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-sm flex items-center shadow-lg shadow-emerald-500/5">
            <svg class="w-5 h-5 {{ app()->getLocale() == 'ar' ? 'ml-3' : 'mr-3' }} flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            {{ session('success_message') }}
        </div>
    @endif
    @if(session('error_message'))
        <div class="mb-6 p-4 rounded-xl bg-red-500/10 border border-red-500/20 text-red-400 text-sm flex items-center shadow-lg shadow-red-500/5">
            <svg class="w-5 h-5 {{ app()->getLocale() == 'ar' ? 'ml-3' : 'mr-3' }} flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            {{ session('error_message') }}
        </div>
    @endif
    @if($errors->any())
        <div class="mb-6 p-4 rounded-xl bg-red-500/10 border border-red-500/20 text-red-400 text-sm shadow-lg shadow-red-500/5">
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif
    </div>

@php 
    $pendingWorkers = $pendingWorkersOverall;
@endphp
<br>
@if($pendingWorkers->isNotEmpty())
    <!-- Pending Shifts Alert -->
    <div class="mt-8 mb-8 p-4 bg-amber-500/10 border border-amber-500/20 rounded-2xl flex items-center justify-between gap-4 shadow-lg shadow-amber-500/5">
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

<!-- Cards Grid -->
<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6 mt-10">
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
                @can('create attendance')
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
                                    <input type="datetime-local" name="check_in" value="{{ now()->translatedFormat('Y-m-d\TH:i') }}" 
                                        class="w-full px-3 py-2 bg-white/5 dark:bg-black/20 border border-slate-200 dark:border-white/5 rounded-lg text-xs text-slate-700 dark:text-slate-300 focus:outline-none focus:border-emerald-500/50 transition-all font-medium">
                                </div>

                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-[10px] text-emerald-500 uppercase font-extrabold mb-1 ml-1 leading-tight">{{ __('Fresh from Oven') }}</label>
                                        <input type="number" name="bundles_from_oven" value="0" min="0" 
                                            class="w-full px-3 py-2 bg-emerald-500/[0.03] dark:bg-emerald-500/5 border border-emerald-500/20 rounded-lg text-xs text-slate-700 dark:text-emerald-400 focus:outline-none focus:border-emerald-500/50 transition-all font-bold placeholder-emerald-500/30"
                                            placeholder="0">
                                    </div>
                                    <div>
                                        <label class="block text-[10px] text-blue-500 uppercase font-extrabold mb-1 ml-1 leading-tight">{{ __('Bakery Stock') }}</label>
                                        <input type="number" name="bundles_from_bakery" value="{{ $defaultBundles ?? 0 }}" min="0" 
                                            class="w-full px-3 py-2 bg-blue-500/[0.03] dark:bg-blue-500/5 border border-blue-500/20 rounded-lg text-xs text-slate-700 dark:text-blue-400 focus:outline-none focus:border-blue-500/50 transition-all font-bold placeholder-blue-500/30"
                                            placeholder="0">
                                    </div>
                                </div>

                                <button type="submit" class="w-full py-2.5 bg-emerald-500/10 hover:bg-emerald-500/20 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20 rounded-xl font-bold text-xs uppercase tracking-wider transition-colors focus:ring-2 focus:ring-emerald-500">
                                    {{ $completedShifts->count() > 0 ? __('Start New Shift') : __('Clock In') }}
                                </button>
                            </form>
                        @endif
                @else
                    <!-- Clock OUT -->
                    
                    <form action="{{ route('admin.attendance.clock_out', $activeShift->id) }}" method="POST" class="flex-1 space-y-2"
                          x-data="{ 
                              bundlesReceived: {{ $activeShift->bundles_received }},
                              bundlesReturned: 0,
                              pricePerBundle: {{ $defaultPricePerBundle ?? 0 }},
                              cashCollected: 0,
                              cashCurrencyId: '', 
                              isLocal: true,
                              exchangeRate: 1,
                              get bundlesSold() { return Math.max(0, this.bundlesReceived - (parseInt(this.bundlesReturned) || 0)); },
                              get expectedCash() { return (this.bundlesSold * (parseFloat(this.pricePerBundle) || 0)).toFixed(2); },
                              get cashDiff() { return ((parseFloat(this.cashCollected) || 0) - parseFloat(this.expectedCash)).toFixed(2); },
                              get hasMismatch() { return parseFloat(this.pricePerBundle) > 0 && Math.abs(parseFloat(this.cashDiff)) > 0.01; },
                              setCurrency(id) {
                                  this.cashCurrencyId = id;
                                  const curr = @js($currencies->map(fn($c) => ['id' => $c->id, 'is_default' => $c->is_default, 'exchange_rate' => $c->exchange_rate]));
                                  const found = curr.find(c => c.id == id);
                                  this.isLocal = found ? found.is_default : true;
                                  this.exchangeRate = found ? found.exchange_rate : 1;
                               },
                               confirmSubmit(e) {
                                   if (this.hasMismatch) {
                                       const diff = parseFloat(this.cashDiff);
                                       const type = diff > 0 ? '{{ __("Surplus") }}' : '{{ __("Deficit") }}';
                                       const msg = '{{ __("The cash collected does not match the expected amount.") }}\n' + type + ': ' + Math.abs(diff).toFixed(2) + '\n\n{{ __("Do you want to proceed?") }}';
                                       e.preventDefault();
                                       const form = e.target;
                                       window.dispatchEvent(new CustomEvent('confirm-action', {
                                           detail: {
                                               title: '{{ __("Cash Mismatch") }}',
                                               message: msg,
                                               type: 'warning',
                                               confirmText: '{{ __("Proceed Anyway") }}',
                                               onConfirm: () => form.submit()
                                           }
                                       }));
                                   }
                               }
                          }"
                          @submit="confirmSubmit($event)">
                        @csrf

                        {{-- Bundles received (read-only info) --}}
                        <div class="p-3 bg-blue-500/5 border border-blue-500/10 rounded-xl flex justify-between items-center">
                            <span class="text-[10px] text-blue-500 uppercase font-bold tracking-wider">{{ __('Bundles Delivered') }}</span>
                            <span class="text-sm font-black text-blue-400">{{ $activeShift->bundles_received }}</span>
                        </div>

                        <div>
                            <label class="block text-[10px] text-slate-500 uppercase font-bold mb-1 ml-1">{{ __('Shift End Time') }}</label>
                            <input type="datetime-local" name="check_out" value="{{ now()->translatedFormat('Y-m-d\TH:i') }}" 
                                class="w-full px-3 py-2 bg-white/5 dark:bg-black/20 border border-slate-200 dark:border-white/5 rounded-lg text-xs text-slate-700 dark:text-slate-300 focus:outline-none focus:border-amber-500/50 transition-all font-medium">
                        </div>

                        <div>
                            <label class="block text-[10px] text-slate-500 uppercase font-bold mb-1 ml-1">{{ __('Bundles Returned') }}</label>
                            <input type="number" name="bundles_returned" x-model="bundlesReturned" value="0" min="0" :max="bundlesReceived"
                                class="w-full px-3 py-2 bg-white/5 dark:bg-black/20 border border-slate-200 dark:border-white/5 rounded-lg text-xs text-slate-700 dark:text-slate-300 focus:outline-none focus:border-amber-500/50 transition-all font-medium"
                                placeholder="0">
                        </div>

                        <div>
                            <label class="block text-[10px] text-slate-500 uppercase font-bold mb-1 ml-1">{{ __('Price per Bundle') }}</label>
                            <input type="number" step="0.01" name="price_per_bundle" x-model="pricePerBundle" min="0"
                                class="w-full px-3 py-2 bg-white/5 dark:bg-black/20 border border-slate-200 dark:border-white/5 rounded-lg text-xs text-slate-700 dark:text-slate-300 focus:outline-none focus:border-amber-500/50 transition-all font-medium"
                                placeholder="0.00">
                            <p class="text-[9px] text-slate-500 mt-0.5 {{ app()->getLocale() == 'ar' ? 'mr-1' : 'ml-1' }}">{{ __('From settings. Adjustable per shift.') }}</p>
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

                        {{-- Auto-calculated summary --}}
                        <div x-show="pricePerBundle > 0" x-transition class="p-3 rounded-xl space-y-2 border"
                             :class="hasMismatch ? 'bg-red-500/5 border-red-500/15' : 'bg-emerald-500/5 border-emerald-500/15'">
                            <div class="flex justify-between items-center">
                                <span class="text-[10px] uppercase font-bold tracking-wider" :class="hasMismatch ? 'text-red-400' : 'text-emerald-400'">{{ __('Bundles Sold') }}</span>
                                <span class="text-sm font-black" :class="hasMismatch ? 'text-red-400' : 'text-emerald-400'" x-text="bundlesSold"></span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-[10px] uppercase font-bold tracking-wider" :class="hasMismatch ? 'text-red-400' : 'text-emerald-400'">{{ __('Expected Cash') }}</span>
                                <span class="text-sm font-black" :class="hasMismatch ? 'text-red-400' : 'text-emerald-400'" x-text="expectedCash"></span>
                            </div>
                            {{-- Mismatch warning --}}
                            <template x-if="hasMismatch">
                                <div class="pt-2 border-t border-red-500/20">
                                    <div class="flex justify-between items-center">
                                        <span class="text-[10px] uppercase font-bold tracking-wider text-red-400" x-text="parseFloat(cashDiff) > 0 ? '{{ __("Surplus") }}' : '{{ __("Deficit") }}'"></span>
                                        <span class="text-sm font-black text-red-400" x-text="Math.abs(parseFloat(cashDiff)).toFixed(2)"></span>
                                    </div>
                                    <p class="text-[9px] text-red-400/70 mt-1">{{ __('Cash does not match expected. You will be asked to confirm.') }}</p>
                                </div>
                            </template>
                            {{-- Match indicator --}}
                            <template x-if="!hasMismatch && parseFloat(cashCollected) > 0">
                                <div class="pt-2 border-t border-emerald-500/20 flex items-center gap-1.5">
                                    <svg class="w-3.5 h-3.5 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    <span class="text-[10px] text-emerald-400 font-bold uppercase tracking-wider">{{ __('Cash Matches') }}</span>
                                </div>
                            </template>
                        </div>

                        {{-- Cash Collected --}}
                        <div>
                            <label class="block text-[10px] text-slate-500 uppercase font-bold mb-1 ml-1">{{ __('Cash Collected') }}</label>
                            <input type="number" step="0.01" name="cash_collected" x-model="cashCollected" value="0" min="0" 
                                class="w-full px-3 py-2 bg-white/5 dark:bg-black/20 border border-slate-200 dark:border-white/5 rounded-lg text-xs text-slate-700 dark:text-slate-300 focus:outline-none focus:border-amber-500/50 transition-all font-medium"
                                placeholder="0.00">
                        </div>

                        <button type="submit" class="w-full py-2.5 bg-amber-500/20 hover:bg-amber-500/30 text-amber-600 dark:text-amber-500 border border-amber-500/20 rounded-xl font-bold text-xs uppercase tracking-wider transition-colors focus:ring-2 focus:ring-amber-500">
                            {{ __('Clock Out') }}
                        </button>
                    </form>
                @endif
                @endcan
            </div>
            </div>

        </div>
    @endforeach
</div>
<div class="mt-8">
    {{ $workers->links() }}
</div>
@endsection
