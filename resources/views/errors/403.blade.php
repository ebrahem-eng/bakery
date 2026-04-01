<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('Access Denied') }} — {{ __('Bakery Accounting System') }}</title>

    <script>
        if (localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>

    @include('layouts.Admin.Links')

    <style>
        .error-card {
            backdrop-filter: blur(24px);
            -webkit-backdrop-filter: blur(24px);
        }
        .shield-icon {
            animation: pulse-glow 2.5s ease-in-out infinite;
        }
        @keyframes pulse-glow {
            0%, 100% { filter: drop-shadow(0 0 12px rgba(239, 68, 68, 0.3)); }
            50%      { filter: drop-shadow(0 0 28px rgba(239, 68, 68, 0.6)); }
        }
        .error-code {
            background: linear-gradient(135deg, #ef4444, #f97316, #ef4444);
            background-size: 200% 200%;
            animation: gradient-shift 4s ease infinite;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        @keyframes gradient-shift {
            0%   { background-position: 0% 50%; }
            50%  { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }
        .line-decoration {
            background: linear-gradient(90deg, transparent, rgba(239,68,68,0.4), transparent);
        }
    </style>
</head>
<body class="antialiased font-sans text-slate-900 dark:text-slate-300">

    <!-- Background & Glow -->
    <div class="fixed inset-0 z-[-2] bg-slate-50 dark:bg-[#0f1115]">
        <div class="hidden dark:block absolute top-[-10%] left-[-10%] w-[40%] h-[40%] rounded-full bg-red-500/10 blur-[120px] pointer-events-none"></div>
        <div class="hidden dark:block absolute bottom-[-10%] right-[-10%] w-[40%] h-[40%] rounded-full bg-orange-600/10 blur-[120px] pointer-events-none"></div>
    </div>

    <!-- Grid Overlay -->
    <div class="fixed inset-0 z-[-1] opacity-[0.05] dark:opacity-[0.02] pointer-events-none" style="background-image: linear-gradient(#94a3b8 1px, transparent 1px), linear-gradient(90deg, #94a3b8 1px, transparent 1px); background-size: 40px 40px;"></div>

    <div class="min-h-screen flex items-center justify-center p-4">
        <div class="error-card bg-white/80 dark:bg-white/[0.04] border border-slate-200 dark:border-white/[0.06] rounded-2xl shadow-2xl dark:shadow-red-900/10 max-w-lg w-full p-8 sm:p-12 text-center">

            <!-- Shield Icon -->
            <div class="shield-icon mx-auto mb-6 w-20 h-20 rounded-2xl bg-gradient-to-br from-red-500/20 to-orange-500/20 dark:from-red-500/10 dark:to-orange-500/10 border border-red-500/20 flex items-center justify-center">
                <svg class="w-10 h-10 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m0-10.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 15.75h.008v.008H12v-.008z" />
                </svg>
            </div>

            <!-- Error Code -->
            <h1 class="error-code text-7xl sm:text-8xl font-extrabold mb-2 tracking-tight">403</h1>

            <!-- Decorative Line -->
            <div class="line-decoration h-px w-32 mx-auto my-5 rounded-full"></div>

            <!-- Title -->
            <h2 class="text-xl sm:text-2xl font-bold text-slate-800 dark:text-white mb-3">
                {{ __('Access Denied') }}
            </h2>

            <!-- Message -->
            <p class="text-sm sm:text-base text-slate-500 dark:text-slate-400 leading-relaxed mb-8">
                {{ $exception->getMessage() ? __($exception->getMessage()) : __('You do not have the required permissions to access this page. Please contact your system administrator if you believe this is an error.') }}
            </p>

            <!-- Actions -->
            <div class="flex flex-col sm:flex-row items-center justify-center gap-3">
                <a href="{{ url()->previous() }}"
                   class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-semibold rounded-xl border border-slate-200 dark:border-white/10 text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-white/5 transition-colors">
                    <svg class="w-4 h-4 {{ app()->getLocale() == 'ar' ? 'rotate-180' : '' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                    </svg>
                    {{ __('Back') }}
                </a>

                @auth('admin')
                <a href="{{ route('admin.dashboard') }}"
                   class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-semibold rounded-xl bg-gradient-to-r from-amber-500 to-orange-500 text-white shadow-lg shadow-amber-500/25 hover:shadow-amber-500/40 hover:from-amber-600 hover:to-orange-600 transition-all">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
                    </svg>
                    {{ __('Dashboard') }}
                </a>
                @endauth
            </div>

            <!-- Footer info -->
            <div class="mt-8 pt-6 border-t border-slate-100 dark:border-white/5">
                <div class="flex items-center justify-center gap-2 text-xs text-slate-400 dark:text-slate-500">
                    <div class="w-5 h-5 rounded-lg bg-gradient-to-tr from-amber-600 to-orange-400 p-[1px]">
                        <div class="w-full h-full bg-white dark:bg-[#121419] rounded-lg flex items-center justify-center">
                            <img src="{{ asset('logo.svg') }}" alt="Logo" class="w-3 h-3">
                        </div>
                    </div>
                    {{ __('Bakery Accounting System') }}
                </div>
            </div>

        </div>
    </div>
</body>
</html>
