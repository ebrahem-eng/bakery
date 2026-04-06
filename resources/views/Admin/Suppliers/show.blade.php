@extends('layouts.Admin.App')

@section('content')
<div class="mb-8 flex items-center justify-between">
    <div>
        <h1 class="text-2xl font-bold text-white mb-1">{{ __('Supplier Profile') }} <span class="text-transparent bg-clip-text bg-gradient-to-r from-amber-400 to-orange-500">#{{ $supplier->id }}</span></h1>
        <p class="text-sm text-slate-400">{{ $supplier->first_name }} {{ $supplier->last_name }} - {{ $supplier->title ?? __('Overview') }}</p>
    </div>
    <a href="{{ route('admin.suppliers.index') }}" class="text-slate-400 hover:text-white transition-colors text-sm font-medium flex items-center">
        <svg class="w-4 h-4 {{ app()->getLocale() == 'ar' ? 'ml-1 rotate-180' : 'mr-1' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
        </svg>
        {{ __('Back') }}
    </a>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Profile Quick Stats -->
    <div class="lg:col-span-1 space-y-6">
        <div class="glass-panel rounded-2xl p-6 border border-white/5">
            <h3 class="text-sm font-semibold text-white uppercase tracking-wider mb-4">{{ __('Contact Numbers') }}</h3>
            <div class="space-y-3">
                @forelse($supplier->mobiles as $mobile)
                    <div class="flex items-center gap-3 text-slate-300">
                        <svg class="w-5 h-5 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                        <span class="font-mono">{{ $mobile->mobile_number }}</span>
                    </div>
                @empty
                    <p class="text-xs text-slate-500 italic">{{ __('No Contacts') }}</p>
                @endforelse
            </div>

            <h3 class="text-sm font-semibold text-white uppercase tracking-wider mt-6 mb-4">{{ __('Supplying Categories') }}</h3>
            <div class="flex flex-wrap gap-2">
                @forelse($supplier->categories as $category)
                    <span class="px-3 py-1 bg-amber-500/10 text-amber-400 text-xs rounded-lg border border-amber-500/20">{{ __($category->name) }}</span>
                @empty
                    <span class="text-xs text-slate-500 italic">{{ __('None mapped') }}</span>
                @endforelse
            </div>
        </div>

        <!-- Macro Financials -->
        <div class="glass-panel rounded-2xl p-6 border border-emerald-500/10">
            <div class="text-sm font-semibold text-emerald-400 uppercase tracking-wider mb-2">{{ __('Total Supplied Value') }}</div>
            <div class="text-3xl font-bold text-white">{{ number_format($supplier->supplies->sum('total_cost'), 2) }}</div>
            <div class="text-xs text-slate-500 mt-2">{{ __('Aggregate volume injected into the bakery.') }}</div>
        </div>
        
        <div class="glass-panel rounded-2xl p-6 border border-purple-500/10">
            <div class="text-sm font-semibold text-purple-400 uppercase tracking-wider mb-2">{{ __('Unloading Deductions') }}</div>
            <div class="text-3xl font-bold text-white">{{ number_format($supplier->supplies->sum('unloading_fee'), 2) }}</div>
        </div>
    </div>

    <!-- Recent Supply Ledgers -->
    <div class="lg:col-span-2 space-y-6">
        <!-- Deliveries Table -->
        <div class="glass-panel rounded-2xl border border-white/5 overflow-hidden">
            <div class="p-6 border-b border-white/5 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <h2 class="text-lg font-bold text-white">{{ __('Recent Deliveries') }}</h2>
                
                {{-- Deliveries Filter --}}
                <form action="{{ route('admin.suppliers.show', $supplier) }}" method="GET" class="flex flex-wrap items-center gap-2">
                    <input type="hidden" name="payment_start" value="{{ $paymentStart }}">
                    <input type="hidden" name="payment_end" value="{{ $paymentEnd }}">
                    
                    <div class="flex items-center gap-1">
                        <input type="date" name="delivery_start" value="{{ $deliveryStart }}" class="bg-black/20 border border-white/10 rounded-lg px-2 py-1 text-[10px] text-white focus:border-amber-500/50 outline-none">
                        <span class="text-slate-500 text-[10px]">-</span>
                        <input type="date" name="delivery_end" value="{{ $deliveryEnd }}" class="bg-black/20 border border-white/10 rounded-lg px-2 py-1 text-[10px] text-white focus:border-amber-500/50 outline-none">
                    </div>
                    <button type="submit" class="p-1 px-3 bg-amber-500 rounded-lg text-white text-[10px] font-bold hover:bg-amber-600 transition-colors">
                        {{ __('Filter') }}
                    </button>
                    @if($deliveryStart || $deliveryEnd)
                        <a href="{{ route('admin.suppliers.show', ['supplier' => $supplier, 'payment_start' => $paymentStart, 'payment_end' => $paymentEnd]) }}" class="text-[10px] text-slate-500 hover:text-white underline">{{ __('Clear') }}</a>
                    @endif
                </form>
            </div>
            <div class="overflow-x-auto custom-scrollbar">
                <table class="w-full text-left border-collapse" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
                    <thead>
                        <tr class="text-slate-400 text-xs uppercase tracking-wider border-b border-white/5">
                            <th class="py-4 px-4 font-medium">{{ __('Date & Work Day') }}</th>
                            <th class="py-4 px-4 font-medium">{{ __('Material Category') }}</th>
                            <th class="py-4 px-4 font-medium">{{ __('Quantity') }}</th>
                            <th class="py-4 px-4 font-medium">{{ __('Cost') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5 text-sm text-slate-300">
                        @forelse($supplies as $supply)
                        <tr class="hover:bg-white/5 transition-colors">
                            <td class="py-3 px-4">
                                <div class="font-medium text-white">{{ $supply->created_at->translatedFormat('Y-m-d H:i') }}</div>
                                <div class="text-xs text-slate-500">{{ __('Day ID:') }} {{ $supply->work_day_id }}</div>
                            </td>
                            <td class="py-3 px-4 text-emerald-400">
                                {{ __($supply->category->name ?? '--') }}
                            </td>
                            <td class="py-3 px-4">
                                <div class="text-xs font-mono text-white font-bold">{{ number_format($supply->quantity, 2) }} {{ __('Units') }}</div>
                                @if($supply->boxes_count)
                                    <div class="text-[10px] text-slate-500 mt-1 font-mono">{{ $supply->boxes_count }} {{ __('Boxes') }}</div>
                                @endif
                                @if($supply->material_type_name)
                                    <div class="text-[10px] text-amber-500 mt-1 font-bold">{{ $supply->material_type_name }}</div>
                                @endif
                            </td>
                            <td class="py-3 px-4 font-bold text-white">
                                {{ number_format($supply->total_cost, 2) }}
                                <span class="text-[10px] text-slate-500">{{ $supply->currency->code ?? '' }}</span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="py-8 text-center text-slate-500 italic">{{ __('No raw materials found matching the filters.') }}</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($supplies->hasPages())
                <div class="p-4 border-t border-white/5">
                    {{ $supplies->links() }}
                </div>
            @endif
        </div>

        <!-- Payment History Table -->
        <div class="glass-panel rounded-2xl border border-white/5 overflow-hidden">
            <div class="p-6 border-b border-white/5 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <h2 class="text-lg font-bold text-white">{{ __('Payment History') }}</h2>
                
                {{-- Payments Filter --}}
                <form action="{{ route('admin.suppliers.show', $supplier) }}" method="GET" class="flex flex-wrap items-center gap-2">
                    <input type="hidden" name="delivery_start" value="{{ $deliveryStart }}">
                    <input type="hidden" name="delivery_end" value="{{ $deliveryEnd }}">
                    <input type="hidden" name="deliveries_page" value="{{ request('deliveries_page') }}">

                    <div class="flex items-center gap-1">
                        <input type="date" name="payment_start" value="{{ $paymentStart }}" class="bg-black/20 border border-white/10 rounded-lg px-2 py-1 text-[10px] text-white focus:border-purple-500/50 outline-none">
                        <span class="text-slate-500 text-[10px]">-</span>
                        <input type="date" name="payment_end" value="{{ $paymentEnd }}" class="bg-black/20 border border-white/10 rounded-lg px-2 py-1 text-[10px] text-white focus:border-purple-500/50 outline-none">
                    </div>
                    <button type="submit" class="p-1 px-3 bg-purple-600 rounded-lg text-white text-[10px] font-bold hover:bg-purple-700 transition-colors">
                        {{ __('Filter') }}
                    </button>
                    @if($paymentStart || $paymentEnd)
                        <a href="{{ route('admin.suppliers.show', ['supplier' => $supplier, 'delivery_start' => $deliveryStart, 'delivery_end' => $deliveryEnd, 'deliveries_page' => request('deliveries_page')]) }}" class="text-[10px] text-slate-500 hover:text-white underline">{{ __('Clear') }}</a>
                    @endif
                </form>
            </div>
            <div class="overflow-x-auto custom-scrollbar">
                <table class="w-full text-left border-collapse" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
                    <thead>
                        <tr class="text-slate-400 text-xs uppercase tracking-wider border-b border-white/5">
                            <th class="py-4 px-4 font-medium">{{ __('Date') }}</th>
                            <th class="py-4 px-4 font-medium">{{ __('Type') }}</th>
                            <th class="py-4 px-4 font-medium">{{ __('Amount') }}</th>
                            <th class="py-4 px-4 font-medium">{{ __('Base Equiv.') }}</th>
                            <th class="py-4 px-4 font-medium">{{ __('Admin') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5 text-sm text-slate-300">
                        @forelse($allPayments as $payment)
                        <tr class="hover:bg-white/5 transition-colors">
                            <td class="py-3 px-4">
                                <div class="font-medium text-white">{{ $payment->date->translatedFormat('Y-m-d H:i') }}</div>
                                <a href="{{ route('admin.supplies.show', $payment->supply_id) }}" class="text-[10px] text-amber-500 hover:underline">#{{ $payment->supply_id }}</a>
                            </td>
                            <td class="py-3 px-4">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold {{ $payment->type === 'initial_payment' ? 'bg-blue-500/10 text-blue-400' : 'bg-purple-500/10 text-purple-400' }}">
                                    {{ $payment->type === 'initial_payment' ? __('Initial Payment') : __('Debt Settlement') }}
                                </span>
                            </td>
                            <td class="py-3 px-4">
                                <span class="font-bold text-white">{{ number_format($payment->amount, 2) }}</span>
                                <span class="text-[10px] text-slate-500 uppercase">{{ $payment->currency->code ?? '' }}</span>
                            </td>
                            <td class="py-3 px-4 text-emerald-400 font-bold">
                                {{ number_format($payment->base_amount, 2) }}
                            </td>
                            <td class="py-3 px-4 text-xs">
                                {{ $payment->admin_name }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="py-12 text-center text-slate-500 italic">
                                <svg class="w-12 h-12 text-slate-700 mx-auto mb-3 opacity-20" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                {{ __('No payments recorded matching the filters.') }}
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($allPayments->hasPages())
                <div class="p-4 border-t border-white/5">
                    {{ $allPayments->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
