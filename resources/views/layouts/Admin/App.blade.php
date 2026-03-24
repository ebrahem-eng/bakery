<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}" x-data="{ isDark: localStorage.getItem('theme') !== 'light' }" :class="{ 'dark': isDark }" x-init="$watch('isDark', val => localStorage.setItem('theme', val ? 'dark' : 'light'))">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('Bakery Accounting System') }}</title>
    @include('layouts.Admin.Links')
</head>
<body class="antialiased font-sans text-slate-300" x-data="{ sidebarOpen: false, profileOpen: false, notificationsOpen: false }">

    <!-- Global Loader -->
    @include('layouts.Admin.Loader')

    <!-- Decorative exact background match from logo -->
    <div class="fixed inset-0 z-[-2] bg-gradient-to-br from-[#0f172a] to-[#0d1b2e]"></div>
    
    <!-- Animated background orbs matching neural nodes -->
    <div class="bg-orb-1 fixed z-[-1] pointer-events-none"></div>
    <div class="bg-orb-2 fixed z-[-1] pointer-events-none"></div>

    <!-- Subtle grid overlay -->
    <div class="fixed inset-0 z-[-1] opacity-[0.02] pointer-events-none" style="background-image: linear-gradient(#94a3b8 1px, transparent 1px), linear-gradient(90deg, #94a3b8 1px, transparent 1px); background-size: 40px 40px;"></div>

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
