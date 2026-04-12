{{-- Global Confirmation Modal --}}
<div x-data="confirmModal()" x-cloak
     @confirm-action.window="open($event.detail)"
     class="fixed inset-0 z-[999]"
     x-show="show"
     style="display: none;">

    {{-- Backdrop --}}
    <div x-show="show"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-black/60 backdrop-blur-sm"></div>

    {{-- Modal Panel --}}
    <div class="fixed inset-0 flex items-center justify-center p-4">
        <div x-show="show"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-90 translate-y-4"
             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 scale-100 translate-y-0"
             x-transition:leave-end="opacity-0 scale-90 translate-y-4"
             @click.away="cancel()"
             class="relative w-full max-w-md bg-white/95 dark:bg-[#1a1d23]/95 border border-slate-200 dark:border-white/10 rounded-2xl shadow-2xl dark:shadow-red-900/10 overflow-hidden"
             style="backdrop-filter: blur(24px); -webkit-backdrop-filter: blur(24px);">

            {{-- Animated Top Bar --}}
            <div class="h-1 w-full" 
                 :class="{
                    'bg-gradient-to-r from-red-500 via-orange-500 to-red-500': type === 'danger',
                    'bg-gradient-to-r from-emerald-500 via-teal-400 to-emerald-500': type === 'success',
                    'bg-gradient-to-r from-amber-500 via-orange-400 to-amber-500': type !== 'danger' && type !== 'success'
                 }"
                 style="background-size: 200% 100%; animation: shimmer 2s linear infinite;">
            </div>

            <div class="p-6">
                {{-- Icon --}}
                <div class="mx-auto mb-5 w-16 h-16 rounded-2xl flex items-center justify-center"
                     :class="{
                        'bg-gradient-to-br from-red-500/20 to-orange-500/20 dark:from-red-500/10 dark:to-orange-500/10 border border-red-500/20': type === 'danger',
                        'bg-gradient-to-br from-emerald-500/20 to-teal-500/20 dark:from-emerald-500/10 dark:to-teal-500/10 border border-emerald-500/20': type === 'success',
                        'bg-gradient-to-br from-amber-500/20 to-orange-500/20 dark:from-amber-500/10 dark:to-orange-500/10 border border-amber-500/20': type !== 'danger' && type !== 'success'
                     }"
                     style="animation: pulse-icon 2.5s ease-in-out infinite;">
                    {{-- Danger Icon --}}
                    <template x-if="type === 'danger'">
                        <svg class="w-8 h-8 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                        </svg>
                    </template>
                    {{-- Warning Icon --}}
                    <template x-if="type !== 'danger' && type !== 'success'">
                        <svg class="w-8 h-8 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9 5.25h.008v.008H12v-.008z" />
                        </svg>
                    </template>
                    {{-- Success Icon --}}
                    <template x-if="type === 'success'">
                        <svg class="w-8 h-8 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                        </svg>
                    </template>
                </div>

                {{-- Title --}}
                <h3 class="text-lg font-bold text-center text-slate-800 dark:text-white mb-2" x-text="title"></h3>

                {{-- Message --}}
                <p class="text-sm text-center text-slate-500 dark:text-slate-400 leading-relaxed mb-6 whitespace-pre-line" x-text="message"></p>

                {{-- Actions --}}
                <div class="flex items-center gap-3">
                    <button @click="cancel()" type="button" x-show="!hideCancel"
                            class="flex-1 px-4 py-2.5 text-sm font-semibold rounded-xl border border-slate-200 dark:border-white/10 text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-white/5 transition-all">
                        <span x-text="cancelText"></span>
                    </button>
                    <button @click="confirm()" type="button"
                            class="flex-1 px-4 py-2.5 text-sm font-bold rounded-xl transition-all shadow-lg"
                            :class="{
                                'bg-gradient-to-r from-red-500 to-red-600 text-white shadow-red-500/25 hover:shadow-red-500/40 hover:from-red-600 hover:to-red-700': type === 'danger',
                                'bg-gradient-to-r from-emerald-500 to-teal-600 text-white shadow-emerald-500/25 hover:shadow-emerald-500/40 hover:from-emerald-600 hover:to-teal-700': type === 'success',
                                'bg-gradient-to-r from-amber-500 to-orange-500 text-white shadow-amber-500/25 hover:shadow-amber-500/40 hover:from-amber-600 hover:to-orange-600': type !== 'danger' && type !== 'success'
                            }">
                        <span x-text="confirmText"></span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    @keyframes shimmer {
        0%   { background-position: 200% 0; }
        100% { background-position: -200% 0; }
    }
    @keyframes pulse-icon {
        0%, 100% { transform: scale(1); }
        50%      { transform: scale(1.05); }
    }
    [x-cloak] { display: none !important; }
</style>

<script>
    function confirmModal() {
        return {
            show: false,
            title: '',
            message: '',
            type: 'danger',
            confirmText: '{{ __("Confirm") }}',
            hideCancel: false,
            formEl: null,
            onConfirmCallback: null,

            open(detail) {
                this.title = detail.title || '{{ __("Are you sure?") }}';
                this.message = detail.message || '';
                this.type = detail.type || 'danger';
                this.confirmText = detail.confirmText || '{{ __("Confirm") }}';
                this.cancelText = detail.cancelText || '{{ __("Cancel") }}';
                this.hideCancel = detail.hideCancel || false;
                this.formEl = detail.form || null;
                this.onConfirmCallback = detail.onConfirm || null;
                this.show = true;
            },

            confirm() {
                this.show = false;
                if (this.onConfirmCallback) {
                    this.onConfirmCallback();
                } else if (this.formEl) {
                    this.formEl.submit();
                }
            },

            cancel() {
                this.show = false;
                this.hideCancel = false;
                this.formEl = null;
                this.onConfirmCallback = null;
            }
        }
    }

    /**
     * Helper: attach to any form with data-confirm attributes.
     * Usage: <form data-confirm data-confirm-title="..." data-confirm-message="..." data-confirm-type="danger">
     */
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('form[data-confirm]').forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                window.dispatchEvent(new CustomEvent('confirm-action', {
                    detail: {
                        title: form.dataset.confirmTitle || '{{ __("Are you sure?") }}',
                        message: form.dataset.confirmMessage || '',
                        type: form.dataset.confirmType || 'danger',
                        confirmText: form.dataset.confirmBtnText || '{{ __("Confirm") }}',
                        cancelText: form.dataset.confirmCancelText || '{{ __("Cancel") }}',
                        form: form
                    }
                }));
            });
        });
    });
</script>
