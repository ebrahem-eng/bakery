<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}" x-data="{ isDark: localStorage.getItem('theme') !== 'light' }" :class="{ 'dark': isDark }" x-init="$watch('isDark', val => localStorage.setItem('theme', val ? 'dark' : 'light'))">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('Bakery Accounting System') }}</title>
    
    <!-- Prevent Theme Flash -->
    <script>
        if (localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>

    @include('layouts.Admin.Links')
</head>
<body class="antialiased font-sans text-slate-900 dark:text-slate-300 transition-colors duration-500" x-data="{ sidebarOpen: false, profileOpen: false, notificationsOpen: false }">

    <!-- Global Loader -->
    @include('layouts.Admin.Loader')

    <!-- Global Background & Glow -->
    <div class="fixed inset-0 z-[-2] bg-slate-50 dark:bg-[#0f1115] transition-colors duration-300">
        <div class="hidden dark:block absolute top-[-10%] left-[-10%] w-[40%] h-[40%] rounded-full bg-amber-500/10 blur-[120px] pointer-events-none"></div>
        <div class="hidden dark:block absolute bottom-[-10%] right-[-10%] w-[40%] h-[40%] rounded-full bg-orange-600/10 blur-[120px] pointer-events-none"></div>
    </div>

    <!-- Subtle grid overlay -->
    <div class="fixed inset-0 z-[-1] opacity-[0.05] dark:opacity-[0.02] pointer-events-none" style="background-image: linear-gradient(#94a3b8 1px, transparent 1px), linear-gradient(90deg, #94a3b8 1px, transparent 1px); background-size: 40px 40px;"></div>

    <div class="flex h-screen overflow-hidden">
        
        @include('layouts.Admin.Sidebar')

        <!-- Main Workspace -->
        <main class="flex-1 flex flex-col h-screen overflow-hidden relative z-10">
            
            @include('layouts.Admin.Nav')

            <!-- Dashboard Content Scrollable Area -->
            <div class="flex-1 overflow-y-auto p-4 sm:p-6 lg:p-8">
                
                @yield('content')

            </div>
            
            @include('layouts.Admin.Footer')
        </main>
    </div>
</body>
</html>
