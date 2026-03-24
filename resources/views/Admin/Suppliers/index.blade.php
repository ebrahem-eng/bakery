@extends('layouts.Admin.App')

@section('content')
<div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-end gap-4">
    <div>
        <h1 class="text-2xl font-bold text-white tracking-tight">{{ __('Suppliers') }}</h1>
        <p class="text-sm text-slate-400 mt-1">{{ __('Manage vendors mapping to raw material categories.') }}</p>
    </div>
    <a href="{{ route('admin.suppliers.create') }}" class="bg-[#eab308]/10 text-[#eab308] border border-[#eab308]/30 hover:bg-[#eab308] hover:text-[#451a03] transition-all px-4 py-2 rounded-xl text-sm font-bold flex items-center shadow-[0_0_15px_rgba(234,179,8,0.15)]">
        <svg class="w-4 h-4 {{ app()->getLocale() == 'ar' ? 'ml-2' : 'mr-2' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
        {{ __('Add Supplier') }}
    </a>
</div>

@if(session('success'))
<div class="mb-6 px-4 py-3 bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 rounded-xl">
    {{ session('success') }}
</div>
@endif

<div class="glass-panel p-6 rounded-2xl border border-white/5">
    <div class="overflow-x-auto custom-scrollbar">
        <table class="w-full text-left border-collapse" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
            <thead>
                <tr class="text-slate-400 text-xs uppercase tracking-wider border-b border-white/5">
                    <th class="py-4 px-4 font-medium">{{ __('Name') }}</th>
                    <th class="py-4 px-4 font-medium">{{ __('Supplying Categories') }}</th>
                    <th class="py-4 px-4 font-medium">{{ __('Mobile Contacts') }}</th>
                    <th class="py-4 px-4 font-medium {{ app()->getLocale() == 'ar' ? 'text-left' : 'text-right' }}">{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-white/5 text-sm text-slate-300">
                @forelse($suppliers as $supplier)
                <tr class="hover:bg-white/5 transition-colors">
                    <td class="py-3 px-4">
                        <div class="font-medium text-white">{{ $supplier->first_name }} {{ $supplier->last_name }}</div>
                        <div class="text-[11px] text-slate-500">{{ $supplier->title }}</div>
                    </td>
                    <td class="py-3 px-4">
                        <div class="flex flex-wrap gap-2">
                            @foreach($supplier->categories as $category)
                                <span class="px-2 py-1 bg-[#38bdf8]/10 text-[#38bdf8] text-xs rounded-md border border-[#38bdf8]/20">{{ __($category->name) }}</span>
                            @endforeach
                        </div>
                    </td>
                    <td class="py-3 px-4">
                        @foreach($supplier->mobiles as $mobile)
                            <div class="text-xs text-slate-400">{{ $mobile->mobile_number }}</div>
                        @endforeach
                    </td>
                    <td class="py-3 px-4 {{ app()->getLocale() == 'ar' ? 'text-left' : 'text-right' }}">
                        <a href="{{ route('admin.suppliers.edit', $supplier->id) }}" class="text-[#eab308] hover:text-white transition-colors {{ app()->getLocale() == 'ar' ? 'ml-3' : 'mr-3' }}">{{ __('Edit') }}</a>
                        <form action="{{ route('admin.suppliers.destroy', $supplier->id) }}" method="POST" class="inline-block" onsubmit="return confirm('{{ __('Delete Supplier completely?') }}');">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-red-400 hover:text-white transition-colors">{{ __('Delete') }}</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="py-8 text-center text-slate-500 italic">{{ __('No suppliers registered.') }}</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
