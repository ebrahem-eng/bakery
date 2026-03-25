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
    <div class="lg:col-span-2">
        <div class="glass-panel rounded-2xl border border-white/5 overflow-hidden">
            <div class="p-6 border-b border-white/5 flex justify-between items-center">
                <h2 class="text-lg font-bold text-white">{{ __('Recent Deliveries') }}</h2>
            </div>
            <div class="overflow-x-auto custom-scrollbar">
                <table class="w-full text-left border-collapse" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
                    <thead>
                        <tr class="text-slate-400 text-xs uppercase tracking-wider border-b border-white/5">
                            <th class="py-4 px-4 font-medium">{{ __('Date & Work Day') }}</th>
                            <th class="py-4 px-4 font-medium">{{ __('Material Category') }}</th>
                            <th class="py-4 px-4 font-medium">{{ __('Quantity') }}</th>
                            <th class="py-4 px-4 font-medium">{{ __('Total Invoice Cost') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5 text-sm text-slate-300">
                        @forelse($supplier->supplies->take(20) as $supply)
                        <tr class="hover:bg-white/5 transition-colors">
                            <td class="py-3 px-4">
                                <div class="font-medium text-white">{{ $supply->created_at->format('Y-m-d H:i') }}</div>
                                <div class="text-xs text-slate-500">{{ __('Day ID:') }} {{ $supply->work_day_id }}</div>
                            </td>
                            <td class="py-3 px-4 text-emerald-400">
                                {{ __($supply->category->name ?? '--') }}
                            </td>
                            <td class="py-3 px-4">
                                <div class="text-sm font-mono text-slate-900 dark:text-white font-bold">{{ number_format($supply->quantity, 2) }} {{ __('Units') }}</div>
                                @if($supply->boxes_count)
                                    <div class="text-[10px] text-slate-500 mt-1 font-mono">{{ $supply->boxes_count }} {{ __('Boxes') }} × {{ $supply->box_weight }} {{ __('KG') }}</div>
                                @endif
                                @if($supply->material_type_name)
                                    <div class="text-[10px] text-amber-500 mt-1 font-bold">{{ $supply->material_type_name }}</div>
                                @endif
                            </td>
                            <td class="py-3 px-4 font-bold">
                                {{ number_format($supply->total_cost, 2) }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="py-8 text-center text-slate-500 italic">{{ __('No raw materials recorded in the system yet.') }}</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
