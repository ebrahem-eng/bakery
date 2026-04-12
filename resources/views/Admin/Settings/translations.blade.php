@extends('layouts.Admin.App')

@section('content')
<div x-data="translationManager" x-cloak>
    {{-- Page Header --}}
    <div class="mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white tracking-tight">{{ __('Translations Management') }}</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">{{ __('Dynamically update system localized text and add new keys.') }}</p>
        </div>
        <div class="flex items-center gap-3">
            @can('edit settings translations')
            <button @click="showAddModal = true" class="bg-amber-500 hover:bg-amber-600 text-white px-5 py-2.5 rounded-xl text-sm font-bold transition-all flex items-center gap-2 shadow-lg shadow-amber-500/20">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                {{ __('Add New Key') }}
            </button>
            @endcan
            <a href="{{ route('admin.settings.index') }}" class="px-5 py-2.5 bg-slate-100 dark:bg-white/5 text-slate-600 dark:text-slate-300 rounded-xl text-sm font-bold border border-slate-200 dark:border-white/5 hover:bg-slate-200 dark:hover:bg-white/10 transition-all">
                {{ __('Back to Settings') }}
            </a>
        </div>
    </div>

    {{-- Search & UI Controls --}}
    <div class="mb-6 glass-panel p-4 rounded-2xl border border-slate-200 dark:border-white/5 flex flex-col md:flex-row gap-4">
        <div class="relative flex-1">
            <form action="{{ route('admin.settings.translations.index') }}" method="GET">
                <input type="text" name="search" value="{{ $search }}" placeholder="{{ __('Search keys or translations...') }}"
                    class="block w-full pl-10 pr-4 py-3 bg-white dark:bg-[#0f1115] border border-slate-200 dark:border-white/5 rounded-xl text-sm text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:border-amber-500/50 focus:ring-1 focus:ring-amber-500/50 transition-all">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
                @if($search)
                <a href="{{ route('admin.settings.translations.index') }}" class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-red-500">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </a>
                @endif
            </form>
        </div>
    </div>

    {{-- Translations Table --}}
    <div class="glass-panel rounded-2xl border border-slate-200 dark:border-white/5 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse" dir="ltr">
                <thead>
                    <tr class="text-slate-500 dark:text-slate-400 text-xs uppercase tracking-wider border-b border-slate-200 dark:border-white/5 bg-slate-50/50 dark:bg-black/20">
                        <th class="py-4 px-6 font-bold w-1/3">{{ __('English Key') }}</th>
                        <th class="py-4 px-6 font-bold">{{ __('Arabic Translation') }}</th>
                        <th class="py-4 px-6 font-bold text-right">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-white/5 text-sm text-slate-600 dark:text-slate-300">
                    @foreach($translations as $key => $value)
                    <tr class="hover:bg-slate-50 dark:hover:bg-white/5 transition-colors group" x-data="{ editing: false, currentValue: '{{ addslashes($value) }}', loading: false }">
                        <td class="py-4 px-6 font-mono text-xs text-slate-900 dark:text-slate-200 break-all select-all">
                            {{ $key }}
                        </td>
                        <td class="py-4 px-6" dir="rtl">
                            <template x-if="!editing">
                                <span class="text-slate-900 dark:text-white text-base tracking-wide" x-text="currentValue"></span>
                            </template>
                            <template x-if="editing">
                                <div class="flex gap-2">
                                    <input type="text" x-model="currentValue" 
                                        class="block w-full px-4 py-2 bg-white dark:bg-[#0f1115] border border-amber-500/50 rounded-xl text-base text-slate-900 dark:text-white focus:outline-none focus:ring-1 focus:ring-amber-500/50 transition-all font-bold">
                                </div>
                            </template>
                        </td>
                        <td class="py-4 px-6 text-right">
                            @can('edit settings translations')
                            <div class="flex items-center justify-end gap-2">
                                <button x-show="!editing" @click="editing = true" class="p-2 text-slate-400 hover:text-amber-500 transition-colors">
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </button>
                                
                                <button x-show="!editing" @click="remove('{{ addslashes($key) }}')" class="p-2 text-slate-400 hover:text-red-500 transition-colors">
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                </button>
                                
                                <button x-show="editing" @click="save('{{ addslashes($key) }}', currentValue, $data)" :disabled="loading"
                                    class="bg-emerald-500 hover:bg-emerald-600 text-white px-3 py-1.5 rounded-lg text-xs font-bold transition-all flex items-center gap-1">
                                    <template x-if="!loading">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    </template>
                                    <template x-if="loading">
                                        <svg class="animate-spin h-4 w-4" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                    </template>
                                    {{ __('Save') }}
                                </button>
                                
                                <button x-show="editing" @click="editing = false; currentValue = '{{ addslashes($value) }}'" :disabled="loading"
                                    class="text-slate-400 hover:text-slate-600 dark:hover:text-white px-2 py-1.5 text-xs font-bold">
                                    {{ __('Cancel') }}
                                </button>
                            </div>
                            @endcan
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        {{-- Pagination --}}
        @if($translations->hasPages())
        <div class="p-6 border-t border-slate-200 dark:border-white/5 bg-slate-50/50 dark:bg-black/20">
            {{ $translations->links() }}
        </div>
        @endif
    </div>

    {{-- Add Modal --}}
    <div x-show="showAddModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm"
        x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
        
        <div @click.away="showAddModal = false" class="bg-white dark:bg-[#1a1d21] rounded-3xl w-full max-w-md overflow-hidden shadow-2xl border border-slate-200 dark:border-white/10">
            <div class="px-6 py-4 border-b border-slate-200 dark:border-white/5 flex items-center justify-between">
                <h2 class="text-lg font-bold text-slate-900 dark:text-white">{{ __('Add New Translation Key') }}</h2>
                <button @click="showAddModal = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-white transition-colors">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            
            <form action="{{ route('admin.settings.translations.store') }}" method="POST" class="p-6 space-y-4">
                @csrf
                <div>
                    <label class="block text-[10px] uppercase tracking-widest text-slate-500 font-bold mb-2">{{ __('English Key') }}</label>
                    <input type="text" name="key" required placeholder="{{ __('e.g. Confirm Order') }}"
                        class="block w-full px-4 py-3 bg-slate-50 dark:bg-[#0f1115] border border-slate-200 dark:border-white/5 rounded-xl text-sm text-slate-900 dark:text-white focus:outline-none focus:border-amber-500/50 focus:ring-1 focus:ring-amber-500/50">
                </div>
                <div>
                    <label class="block text-[10px] uppercase tracking-widest text-slate-500 font-bold mb-2">{{ __('Arabic Value') }}</label>
                    <input type="text" name="value" required placeholder="{{ __('تأكيد الطلب') }}"
                        class="block w-full px-4 py-3 bg-slate-50 dark:bg-[#0f1115] border border-slate-200 dark:border-white/5 rounded-xl text-sm text-slate-900 dark:text-white focus:outline-none focus:border-amber-500/50 focus:ring-1 focus:ring-amber-500/50 font-bold" dir="rtl">
                </div>
                <div class="pt-2">
                    <button type="submit" class="w-full bg-amber-500 hover:bg-amber-600 text-white font-bold py-3 rounded-xl transition-all shadow-lg shadow-amber-500/20">
                        {{ __('Add Key') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('translationManager', () => ({
        showAddModal: false,
        async save(key, value, rowScope) {
            rowScope.loading = true;
            try {
                const response = await fetch("{{ route('admin.settings.translations.update') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ key, value })
                });
                
                const data = await response.json();
                if (data.success) {
                    rowScope.editing = false;
                    // Toast message if available
                    if (window.showToast) window.showToast(data.message, 'success');
                } else {
                    alert(data.message || 'Error updating translation');
                }
            } catch (error) {
                console.error(error);
                alert('Connection error. Please try again.');
            } finally {
                rowScope.loading = false;
            }
        },
        async remove(key) {
            window.dispatchEvent(new CustomEvent('confirm-action', {
                detail: {
                    title: "{{ __('Delete Translation Key') }}",
                    message: "{{ __('Are you sure you want to delete this key?') }}",
                    type: 'danger',
                    confirmText: "{{ __('Confirm') }}",
                    cancelText: "{{ __('Cancel') }}",
                    onConfirm: async () => {
                        try {
                            const response = await fetch("{{ route('admin.settings.translations.destroy') }}", {
                                method: 'DELETE',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                },
                                body: JSON.stringify({ key })
                            });
                            
                            const data = await response.json();
                            if (data.success) {
                                window.location.reload();
                            } else {
                                alert(data.message || 'Error deleting translation');
                            }
                        } catch (error) {
                            console.error(error);
                            alert('Connection error. Please try again.');
                        }
                    }
                }
            }));
        }
    }));
});
</script>
@endsection
