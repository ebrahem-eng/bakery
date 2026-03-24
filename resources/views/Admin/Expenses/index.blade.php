@extends('layouts.Admin.App')

@section('content')
<div class="mb-8 flex flex-col sm:flex-row justify-between items-start sm:items-end gap-4">
    <div>
        <h1 class="text-2xl lg:text-3xl font-bold text-slate-900 dark:text-white mb-1 tracking-tight">{{ __('Daily') }} <span class="text-transparent bg-clip-text bg-gradient-to-r from-red-400 to-rose-500">{{ __('Expenses') }}</span></h1>
        <p class="text-sm text-slate-400">
            {{ __('Track operational outgoings for Active Work Day:') }} 
            <span class="font-bold text-amber-500">{{ $activeWorkDay->start_time->format('Y-m-d h:i A') }}</span>
        </p>
    </div>
    <button @click="$dispatch('open-expense-modal')" class="bg-gradient-to-r from-red-500 to-rose-500 hover:from-red-400 hover:to-rose-400 text-white px-4 py-2 rounded-xl text-sm font-bold flex items-center shadow-[0_0_15px_rgba(244,63,94,0.3)] transition-all">
        <svg class="w-4 h-4 {{ app()->getLocale() == 'ar' ? 'ml-2' : 'mr-2' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
        </svg>
        {{ __('Log Expense') }}
    </button>
</div>

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

<!-- Stats Row -->
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
    @php
        $stats = [
            'operating' => $expenses->where('category', 'operating')->sum('amount'),
            'logistics' => $expenses->where('category', 'logistics')->sum('amount'),
            'personal' => $expenses->where('category', 'personal')->sum('amount'),
            'other' => $expenses->where('category', 'other')->sum('amount'),
        ];
        $labels = [
            'operating' => __('Operating Costs'),
            'logistics' => __('Logistics / Patrols'),
            'personal' => __('Personal Drawings'),
            'other' => __('Other Expenses'),
        ];
        $colors = [
            'operating' => 'blue',
            'logistics' => 'emerald',
            'personal' => 'amber',
            'other' => 'slate',
        ];
    @endphp

    @foreach($stats as $key => $total)
    <div class="glass-panel p-4 rounded-2xl border border-{{ $colors[$key] }}-500/10 bg-{{ $colors[$key] }}-500/5">
        <p class="text-xs text-{{ $colors[$key] }}-400 uppercase tracking-wider mb-1 font-semibold">{{ $labels[$key] }}</p>
        <p class="text-xl font-bold text-white">{{ number_format($total, 2) }}</p>
    </div>
    @endforeach
</div>

<!-- Table -->
<div class="glass-panel rounded-2xl border border-white/5 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-black/20 border-b border-white/5 text-xs uppercase tracking-wider text-slate-400">
                    <th class="p-4 font-semibold w-16">#</th>
                    <th class="p-4 font-semibold">{{ __('Title') }}</th>
                    <th class="p-4 font-semibold">{{ __('Category') }}</th>
                    <th class="p-4 font-semibold">{{ __('Amount') }}</th>
                    <th class="p-4 font-semibold">{{ __('Notes') }}</th>
                    <th class="p-4 font-semibold text-center">{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-white/5">
                @forelse($expenses as $expense)
                <tr class="hover:bg-white/5 transition-colors">
                    <td class="p-4 text-sm text-slate-500 font-medium">{{ $expense->id }}</td>
                    <td class="p-4">
                        <span class="font-bold text-white">{{ $expense->title }}</span>
                        <p class="text-[10px] text-slate-500 uppercase tracking-widest mt-1">{{ $expense->created_at->format('h:i A') }}</p>
                    </td>
                    <td class="p-4">
                        <span class="px-2.5 py-1 bg-white/5 text-slate-300 border border-white/10 rounded-md text-[10px] uppercase tracking-wider font-bold">
                            {{ $labels[$expense->category] ?? __('Other') }}
                        </span>
                    </td>
                    <td class="p-4">
                        <div class="flex items-center gap-2">
                            <span class="text-red-400 font-bold">{{ number_format($expense->amount, 2) }}</span>
                            <span class="text-xs text-slate-500 font-medium uppercase">{{ $expense->currency->code }}</span>
                        </div>
                    </td>
                    <td class="p-4">
                        <span class="text-xs text-slate-400 line-clamp-2 w-48">{{ $expense->notes ?? '-' }}</span>
                    </td>
                    <td class="p-4 text-center">
                        <form action="{{ route('admin.expenses.destroy', $expense) }}" method="POST" class="inline-block" onsubmit="return confirm('{{ __('Are you sure you want to delete this expense?') }}');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="p-2 text-slate-400 hover:text-red-400 hover:bg-red-400/10 rounded-lg transition-colors">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="p-8 text-center text-slate-500">
                        {{ __('No expenses logged today.') }}
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Add Expense Modal -->
<div x-data="{ open: false }"
     @open-expense-modal.window="open = true" 
     x-show="open" 
     class="fixed inset-0 z-[100] overflow-y-auto" style="display: none;">
    
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
        <div x-show="open" x-transition.opacity class="fixed inset-0 transition-opacity bg-black/60 backdrop-blur-sm" @click="open = false"></div>

        <div x-show="open" x-transition 
             class="relative inline-block w-full max-w-md p-6 overflow-hidden text-left align-middle transition-all transform glass-panel rounded-2xl shadow-xl border border-white/10"
             {{ app()->getLocale() == 'ar' ? 'dir="rtl"' : 'dir="ltr"' }}>
            
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-xl font-bold text-white">{{ __('Log Day Expense') }}</h3>
                <button @click="open = false" class="text-slate-400 hover:text-white transition-colors">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form action="{{ route('admin.expenses.store') }}" method="POST" class="space-y-4">
                @csrf
                
                <div>
                    <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">{{ __('Category') }}</label>
                    <select name="category" required class="block w-full px-4 py-3 bg-[#0f1115] border border-white/5 rounded-xl text-sm text-white focus:outline-none focus:border-red-500/50 focus:ring-1 focus:ring-red-500/50 transition-all font-medium appearance-none">
                        <option value="operating">{{ __('Operating Costs (تشغيلية)') }}</option>
                        <option value="logistics">{{ __('Logistics & Patrols (تموين / دوريات)') }}</option>
                        <option value="personal">{{ __('Personal Drawings (سحب شخصي)') }}</option>
                        <option value="other">{{ __('Other (أخرى)') }}</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">{{ __('Title / Description') }}</label>
                    <input type="text" name="title" required 
                        class="block w-full px-4 py-3 bg-[#0f1115] border border-white/5 rounded-xl text-sm placeholder-slate-600 text-white focus:outline-none focus:border-red-500/50 focus:ring-1 focus:ring-red-500/50 transition-all font-medium" 
                        placeholder="{{ __('e.g. Fixing Generator') }}">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">{{ __('Amount') }}</label>
                        <input type="number" step="0.01" name="amount" required 
                            class="block w-full px-4 py-3 bg-[#0f1115] border border-white/5 rounded-xl text-sm placeholder-slate-600 text-white focus:outline-none focus:border-red-500/50 focus:ring-1 focus:ring-red-500/50 transition-all font-medium" 
                            placeholder="0.00">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">{{ __('Currency') }}</label>
                        <select name="currency_id" required class="block w-full px-4 py-3 bg-[#0f1115] border border-white/5 rounded-xl text-sm text-white focus:outline-none focus:border-red-500/50 focus:ring-1 focus:ring-red-500/50 transition-all font-medium appearance-none">
                            @foreach($currencies as $currency)
                                <option value="{{ $currency->id }}">{{ $currency->code }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">{{ __('Notes (Optional)') }}</label>
                    <input type="text" name="notes" class="block w-full px-4 py-3 bg-[#0f1115] border border-white/5 rounded-xl text-sm text-white focus:outline-none focus:border-red-500/50 focus:ring-1 focus:ring-red-500/50 transition-all font-medium" placeholder="" >
                </div>

                <div class="pt-4 flex gap-3">
                    <button type="button" @click="open = false" class="flex-1 bg-transparent hover:bg-white/5 text-white border border-white/10 px-4 py-3 rounded-xl text-sm font-bold transition-colors">
                        {{ __('Cancel') }}
                    </button>
                    <button type="submit" class="flex-1 bg-red-500 hover:bg-red-400 text-white px-4 py-3 rounded-xl text-sm font-bold transition-colors shadow-[0_0_15px_rgba(239,68,68,0.3)]">
                        {{ __('Save Expense') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
