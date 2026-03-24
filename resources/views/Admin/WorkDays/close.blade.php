@extends('layouts.Admin.App')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-white tracking-tight">{{ __('Close Work Day') }} #{{ $workDay->id }}</h1>
    <p class="text-sm text-slate-400 mt-1">{{ __('Finalize all operations and calculate carry-overs.') }}</p>
</div>

<form action="{{ route('admin.work_days.close', $workDay->id) }}" method="POST" class="glass-panel p-6 rounded-2xl border border-white/5 max-w-4xl">
    @csrf
    
    <div class="p-4 rounded-xl border border-red-500/20 bg-red-500/5 mb-8">
        <h3 class="text-red-400 font-bold mb-2 flex items-center">
            <svg class="w-5 h-5 {{ app()->getLocale() == 'ar' ? 'ml-2' : 'mr-2' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            {{ __('Warning: Irreversible Action') }}
        </h3>
        <p class="text-sm text-red-300">
            {{ __('By closing this Work Day, you assert that all inputs (supplier payments, worker wages, distributions) are finalized. The carried over bundles will logically transfer their exact capital equivalent to the next Work Day balance.') }}
        </p>
    </div>

    <!-- Carry over bundles calculation -->
    <div class="mb-8">
        <h3 class="text-lg font-semibold text-white mb-4">{{ __('Carry Over Bundles & Cash Reconciliation') }}</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-slate-300 mb-2">{{ __('Total Leftover Bundles (Not Sold)') }}</label>
                <input type="number" name="carried_over_bundles" required value="0" class="glass-input block w-full px-4 py-3 border-transparent rounded-xl leading-5 bg-black/30 text-white focus:outline-none focus:bg-black/50 focus:border-[#eab308] focus:ring-1 focus:ring-[#eab308] transition-all">
                @error('carried_over_bundles') <span class="text-red-400 text-xs mt-1 block">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-300 mb-2">{{ __('Carried Over Equivalent Cash Value') }}</label>
                <input type="number" step="0.01" name="carried_over_money" required value="0.00" class="glass-input block w-full px-4 py-3 border-transparent rounded-xl leading-5 bg-black/30 text-white focus:outline-none focus:bg-black/50 focus:border-[#eab308] focus:ring-1 focus:ring-[#eab308] transition-all">
                @error('carried_over_money') <span class="text-red-400 text-xs mt-1 block">{{ $message }}</span> @enderror
            </div>
        </div>
    </div>

    <div class="flex justify-end gap-4 border-t border-white/5 pt-6">
        <a href="{{ route('admin.work_days.index') }}" class="px-6 py-2.5 rounded-xl text-sm font-medium text-slate-300 hover:text-white hover:bg-white/5 transition-colors">{{ __('Cancel') }}</a>
        <button type="submit" onclick="return confirm('{{ __('Are you absolutely sure you want to end this Work Day?') }}');" class="bg-red-500/10 text-red-500 border border-red-500/30 hover:bg-red-500 hover:text-white transition-all px-6 py-2.5 rounded-xl text-sm font-bold shadow-[0_0_15px_rgba(239,68,68,0.15)]">{{ __('Conclude Work Day') }}</button>
    </div>
</form>
@endsection
