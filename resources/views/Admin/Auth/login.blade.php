<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login | Neural Admin Portal</title>
    <!-- Tailwind CSS for rapid modern styling -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset('build/assets/admin_page/style.css') }}">
</head>
<body class="min-h-screen flex items-center justify-center relative">

    <!-- Global Loader -->
    @include('layouts.Admin.Loader')

    <!-- Decorative exact background match from logo -->
    <div class="fixed inset-0 z-0 bg-gradient-to-br from-[#0f172a] to-[#0d1b2e]"></div>
    
    <!-- Animated background orbs matching neural nodes -->
    <div class="bg-orb-1"></div>
    <div class="bg-orb-2"></div>

    <!-- Subtle grid overlay (like the one inside the logo circle) -->
    <div class="fixed inset-0 z-0 opacity-[0.02]" style="background-image: linear-gradient(#94a3b8 1px, transparent 1px), linear-gradient(90deg, #94a3b8 1px, transparent 1px); background-size: 40px 40px;"></div>

    <!-- Main Login Container -->
    <div class="relative z-10 w-full max-w-md px-6 py-12">
        <div class="glass-panel rounded-3xl p-8 sm:p-10 relative overflow-hidden">
            
            <!-- Top shimmer line -->
            <div class="absolute top-0 left-0 right-0 h-[2px] bg-gradient-to-r from-transparent via-[#38bdf8] to-transparent opacity-60"></div>

            <!-- Logo -->
            <div class="flex justify-center mb-8 logo-container">
                <img src="{{ asset('build/assets/admin_page/logo.svg') }}" alt="Neural Admin Logo" class="w-[110px] h-[110px] drop-shadow-2xl">
            </div>

            <!-- Title Section -->
            <div class="text-center mb-8">
                <h1 class="text-2xl font-bold tracking-wide text-transparent bg-clip-text bg-gradient-to-r from-[#0ea5e9] to-[#818cf8]">
                    System Access
                </h1>
                <p class="text-sm font-light text-slate-400 mt-2">Neural Admin Authentication</p>
            </div>

            <!-- Alerts -->
            @if(session('error_message'))
                <div class="mb-6 p-4 rounded-xl bg-red-500/10 border border-red-500/20 text-red-400 text-sm flex items-center shadow-[0_0_15px_rgba(239,68,68,0.1)]">
                    <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    {{ session('error_message') }}
                </div>
            @endif

            @if(session('success_message'))
                <div class="mb-6 p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-sm flex items-center shadow-[0_0_15px_rgba(16,185,129,0.1)]">
                    <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    {{ session('success_message') }}
                </div>
            @endif

            @if($errors->any())
                <div class="mb-6 p-4 rounded-xl bg-red-500/10 border border-red-500/20 text-red-400 text-sm shadow-[0_0_15px_rgba(239,68,68,0.1)]">
                    <div class="flex items-center mb-2 font-medium">
                        <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        Authentication Failed
                    </div>
                    <ul class="list-disc pl-11 space-y-1 text-xs">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Form -->
            <form action="{{ route('admin.login') }}" method="POST" class="space-y-6">
                @csrf
                
                <!-- Email Field -->
                <div>
                    <label for="email" class="block text-xs font-medium text-slate-400 uppercase tracking-wider mb-2">Email Address</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-[#3b82f6] opacity-70" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207" />
                            </svg>
                        </div>
                        <input type="email" id="email" name="email" value="{{ old('email') }}" required 
                            class="glass-input block w-full pl-10 pr-3 py-3 rounded-xl text-sm placeholder-slate-500 text-white focus:outline-none focus:ring-1 focus:ring-[#38bdf8]" 
                            placeholder="admin@neural.network">
                    </div>
                </div>

                <!-- Password Field -->
                <div>
                    <label for="password" class="block text-xs font-medium text-slate-400 uppercase tracking-wider mb-2">Security Key</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-[#6366f1] opacity-70" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                        </div>
                        <input type="password" id="password" name="password" required 
                            class="glass-input block w-full pl-10 pr-3 py-3 rounded-xl text-sm placeholder-slate-500 text-white" 
                            placeholder="••••••••">
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex items-center justify-between text-sm">
                    <label class="flex items-center text-slate-300 cursor-pointer group">
                        <input type="checkbox" name="remember" class="w-4 h-4 rounded border-slate-600 bg-slate-900/50 text-[#0ea5e9] focus:ring-[#0ea5e9] focus:ring-offset-slate-900 border-opacity-30">
                        <span class="ml-2 group-hover:text-white transition-colors">Remember sequence</span>
                    </label>
                    
                    <a href="#" class="text-[#38bdf8] hover:text-white transition-colors duration-300">Recover access</a>
                </div>

                <!-- Submit Button -->
                <button type="submit" 
                    class="w-full py-3.5 px-4 bg-gradient-to-r from-[#0ea5e9] via-[#3b82f6] to-[#6366f1] hover:from-[#38bdf8] hover:via-[#60a5fa] hover:to-[#818cf8] text-white font-medium rounded-xl transition-all duration-300 shadow-[0_0_20px_rgba(59,130,246,0.3)] hover:shadow-[0_0_30px_rgba(59,130,246,0.6)] hover:scale-[1.01] active:scale-[0.99] flex justify-center items-center group relative overflow-hidden">
                    
                    <!-- Button inner glow element -->
                    <div class="absolute inset-0 w-full h-full bg-gradient-to-r from-transparent via-white/20 to-transparent translate-x-[-100%] group-hover:translate-x-[100%] transition-transform duration-700"></div>

                    <span class="relative z-10 flex items-center">
                        Initialize Connection
                        <svg class="ml-2 w-4 h-4 group-hover:translate-x-1 transition-transform" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                        </svg>
                    </span>
                </button>
            </form>
            
            <!-- Bottom styling elements -->
            <div class="mt-8 flex justify-center gap-2 pb-2">
                <span class="w-2 h-2 rounded-full bg-[#0ea5e9] opacity-40"></span>
                <span class="w-2 h-2 rounded-full bg-[#3b82f6] opacity-60"></span>
                <span class="w-2 h-2 rounded-full bg-[#6366f1] opacity-40"></span>
            </div>
            
        </div>
    </div>
</body>
</html>
