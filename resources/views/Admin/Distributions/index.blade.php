@extends('layouts.Admin.App')

@section('content')
<div class="mb-8 flex flex-col sm:flex-row justify-between items-start sm:items-end gap-4">
    <div>
        <h1 class="text-2xl lg:text-3xl font-bold text-slate-900 dark:text-white mb-1 tracking-tight">{{ __('Sales &') }} <span class="text-transparent bg-clip-text bg-gradient-to-r from-emerald-400 to-teal-500">{{ __('Distributions') }}</span></h1>
        <p class="text-sm text-slate-400">
            {{ __('Managing outbound supply for Active Work Day:') }} 
            <span class="font-bold text-amber-500">{{ $activeWorkDay->start_time->format('Y-m-d h:i A') }}</span>
        </p>
    </div>
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

<!-- Cards Grid -->
<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
    @foreach($distributors as $distributor)
        @php
            $totalBilled = $distributor->distributions->sum('total_price');
            $totalPaidDistributions = $distributor->distributions->sum('amount_paid');
            $totalRefunds = $distributor->returns->sum('total_refund');
            $totalGeneralPayments = $distributor->transactions->where('type', 'payment')->sum('amount');
            $totalDiscounts = $distributor->transactions->where('type', 'discount')->sum('amount');
            
            // Debt = Total Billed - (Payments at sales + Refunds + Ledger Payments + Discounts)
            $netDebt = $totalBilled - ($totalPaidDistributions + $totalRefunds + $totalGeneralPayments + $totalDiscounts);

            // Today's specific metrics
            $todaySales = $distributor->distributions->where('work_day_id', $activeWorkDay->id)->sum('bundle_count');
        @endphp

        <div class="glass-panel rounded-2xl border border-white/5 overflow-hidden flex flex-col group relative">
            
            <!-- Header -->
            <div class="p-5 border-b border-white/5 bg-black/10 flex justify-between items-start">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-gradient-to-tr from-emerald-500 to-teal-500 p-[1px]">
                        <div class="w-full h-full bg-[#121419] rounded-xl flex items-center justify-center text-emerald-500 font-bold text-lg">
                            {{ mb_substr($distributor->first_name, 0, 1) }}
                        </div>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-white">{{ $distributor->first_name }} {{ $distributor->last_name }}</h3>
                        <p class="text-xs text-slate-400 uppercase tracking-widest mt-0.5">{{ $distributor->title ?? __('Distributor') }}</p>
                    </div>
                </div>
            </div>

            <!-- Stats Body -->
            <div class="p-5 flex-1 space-y-4">
                
                <div class="grid grid-cols-2 gap-3 pb-4 border-b border-white/5">
                    <div>
                        <p class="text-xs text-slate-500 uppercase tracking-wider mb-1">{{ __('Bundles Today') }}</p>
                        <p class="text-xl font-bold text-white">{{ number_format($todaySales) }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-slate-500 uppercase tracking-wider mb-1">{{ __('Currency') }}</p>
                        <p class="text-sm font-bold text-amber-500">{{ $distributor->currency->code }}</p>
                    </div>
                </div>

                <!-- Net Debt -->
                <div class="flex justify-between items-center bg-{{ $netDebt > 0 ? 'red' : 'emerald' }}-500/5 p-3 rounded-xl border border-{{ $netDebt > 0 ? 'red' : 'emerald' }}-500/10 text-sm">
                    <span class="text-{{ $netDebt > 0 ? 'red' : 'emerald' }}-400 font-medium">{{ __('Total Ledger Balance') }}</span>
                    <span class="text-{{ $netDebt > 0 ? 'red' : 'emerald' }}-400 font-bold text-lg">
                        {{ $netDebt > 0 ? '-' : '+' }}{{ number_format(abs($netDebt), 2) }} {{ $distributor->currency->code }}
                    </span>
                </div>
            </div>

            <!-- Actions Footer -->
            <div class="p-4 border-t border-white/5 bg-black/20 grid grid-cols-3 gap-2">
                <button type="button" x-data="" @click="$dispatch('open-sale-modal', { id: {{ $distributor->id }}, name: '{{ $distributor->first_name }} {{ $distributor->last_name }}', currency: '{{ $distributor->currency->code }}' })" class="py-2 bg-emerald-500/20 hover:bg-emerald-500/30 text-emerald-400 border border-emerald-500/20 rounded-xl font-bold text-[10px] uppercase tracking-wider transition-colors">
                    {{ __('Sell Bundles') }}
                </button>

                <button type="button" x-data="" @click="$dispatch('open-return-modal', { id: {{ $distributor->id }}, name: '{{ $distributor->first_name }} {{ $distributor->last_name }}', currency: '{{ $distributor->currency->code }}' })" class="py-2 bg-red-500/20 hover:bg-red-500/30 text-red-400 border border-red-500/20 rounded-xl font-bold text-[10px] uppercase tracking-wider transition-colors">
                    {{ __('Return') }}
                </button>

                <button type="button" x-data="" @click="$dispatch('open-pay-modal', { id: {{ $distributor->id }}, name: '{{ $distributor->first_name }} {{ $distributor->last_name }}', currency: '{{ $distributor->currency->code }}' })" class="py-2 bg-amber-500/20 hover:bg-amber-500/30 text-amber-500 border border-amber-500/20 rounded-xl font-bold text-[10px] uppercase tracking-wider transition-colors">
                    {{ __('Receive Pay') }}
                </button>
            </div>
        </div>
    @endforeach
</div>

<!-- Modals Wrapper injected to prevent duplicate listeners -->
@include('Admin.Distributions.partials.modals')

@endsection
