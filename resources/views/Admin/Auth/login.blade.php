<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('Admin Login') }} | Bakery Secure</title>
    <!-- Tailwind CSS -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet" />
    <style>
        body { font-family: 'Outfit', sans-serif; background-color: #0f1115; }
        .glass-login {
            background: rgba(18, 20, 25, 0.7);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(245, 158, 11, 0.15);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.7);
        }
        .bg-grid-pattern {
            background-image: linear-gradient(to right, rgba(245, 158, 11, 0.05) 1px, transparent 1px),
                              linear-gradient(to bottom, rgba(245, 158, 11, 0.05) 1px, transparent 1px);
            background-size: 40px 40px;
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center relative overflow-hidden text-slate-200">

    <!-- Global Background & Glow -->
    <div class="fixed inset-0 z-0 bg-[#0f1115]">
        <div class="absolute inset-0 bg-grid-pattern"></div>
        <div class="absolute top-[-10%] left-[-10%] w-[40%] h-[40%] rounded-full bg-amber-500/10 blur-[120px] pointer-events-none"></div>
        <div class="absolute bottom-[-10%] right-[-10%] w-[40%] h-[40%] rounded-full bg-orange-600/10 blur-[120px] pointer-events-none"></div>
    </div>

    <!-- Login Container -->
    <div class="relative z-10 w-full max-w-md px-6 py-12">
        <div class="glass-login rounded-3xl p-8 relative overflow-hidden">
            
            <!-- Top shimmer line -->
            <div class="absolute top-0 left-0 right-0 h-[2px] bg-gradient-to-r from-transparent via-amber-500 to-transparent opacity-80"></div>

            <!-- Logo -->
            <div class="flex justify-center mb-6">
                <div class="w-20 h-20 rounded-2xl bg-gradient-to-tr from-amber-600 to-orange-400 p-[2px] shadow-[0_0_20px_rgba(245,158,11,0.4)]">
                    <div class="w-full h-full bg-[#121419] rounded-2xl flex items-center justify-center">
                        <img src="{{ asset('logo.svg') }}" alt="Bakery Logo" class="w-10 h-10">
                    </div>
                </div>
            </div>

            <!-- Title -->
            <div class="text-center mb-8">
                <h1 class="text-2xl font-extrabold tracking-wide text-transparent bg-clip-text bg-gradient-to-r from-amber-400 to-orange-500">
                    {{ __('Bakery Portal') }}
                </h1>
                <p class="text-xs font-light tracking-widest text-slate-400 mt-2 uppercase">{{ __('Secure Admin Access') }}</p>
            </div>

            <!-- Alerts -->
            @if(session('error_message'))
                <div class="mb-6 p-4 rounded-xl bg-red-500/10 border border-red-500/20 text-red-400 text-sm flex items-center">
                    <svg class="w-5 h-5 {{ app()->getLocale() == 'ar' ? 'ml-3' : 'mr-3' }} flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    {{ session('error_message') }}
                </div>
            @endif

            <!-- Form -->
            <form action="{{ route('admin.login') }}" method="POST" class="space-y-6">
                @csrf
                
                <!-- Email Field -->
                <div>
                    <label for="email" class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">{{ __('Email Address') }}</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 {{ app()->getLocale() == 'ar' ? 'right-0 pr-3' : 'left-0 pl-3' }} flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-amber-500/70" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207" />
                            </svg>
                        </div>
                        <input type="email" id="email" name="email" value="{{ old('email') }}" required 
                            class="block w-full {{ app()->getLocale() == 'ar' ? 'pr-10 pl-3' : 'pl-10 pr-3' }} py-3.5 bg-[#0f1115] border border-white/5 rounded-xl text-sm placeholder-slate-600 text-white focus:outline-none focus:border-amber-500/50 focus:ring-1 focus:ring-amber-500/50 transition-all font-medium" 
                            placeholder="admin@bakery.com">
                    </div>
                </div>

                <!-- Password Field -->
                <div>
                    <label for="password" class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">{{ __('Security Key') }}</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 {{ app()->getLocale() == 'ar' ? 'right-0 pr-3' : 'left-0 pl-3' }} flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-amber-500/70" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                        </div>
                        <input type="password" id="password" name="password" required 
                            class="block w-full {{ app()->getLocale() == 'ar' ? 'pr-10 pl-3' : 'pl-10 pr-3' }} py-3.5 bg-[#0f1115] border border-white/5 rounded-xl text-sm placeholder-slate-600 text-white focus:outline-none focus:border-amber-500/50 focus:ring-1 focus:ring-amber-500/50 transition-all font-medium" 
                            placeholder="••••••••">
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex items-center justify-between text-sm mt-4">
                    <label class="flex items-center text-slate-400 cursor-pointer group hover:text-white transition-colors">
                        <input type="checkbox" name="remember" class="w-4 h-4 rounded border-slate-700 bg-[#0f1115] text-amber-500 focus:ring-amber-500/50">
                        <span class="{{ app()->getLocale() == 'ar' ? 'mr-2' : 'ml-2' }}">{{ __('Remember me') }}</span>
                    </label>
                </div>

                <!-- Submit Button -->
                <button type="submit" 
                    class="w-full mt-2 py-3.5 px-4 bg-gradient-to-r from-amber-500 to-orange-500 hover:from-amber-400 hover:to-orange-400 text-[#0f1115] font-bold rounded-xl transition-all duration-300 shadow-[0_0_20px_rgba(245,158,11,0.3)] hover:shadow-[0_0_30px_rgba(245,158,11,0.5)] active:scale-95 flex justify-center items-center group relative overflow-hidden">
                    <span class="relative z-10 flex items-center uppercase tracking-wider text-sm">
                        {{ __('Authorize') }}
                        <svg class="{{ app()->getLocale() == 'ar' ? 'mr-2 rotate-180 group-hover:-translate-x-1' : 'ml-2 group-hover:translate-x-1' }} w-4 h-4 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                        </svg>
                    </span>
                </button>
            </form>
        </div>
        
        <div class="mt-8 flex flex-col items-center gap-6">
            <a href="{{ url('/') }}" class="group relative px-6 py-2.5 rounded-full overflow-hidden transition-all duration-500">
                <!-- Animated background glow -->
                <div class="absolute inset-0 bg-white/5 group-hover:bg-amber-500/10 border border-white/5 group-hover:border-amber-500/20 transition-all duration-500 rounded-full blur-[1px]"></div>
                
                <div class="relative flex items-center gap-3 text-slate-400 group-hover:text-amber-400 transition-colors duration-500">
                    <svg class="w-4 h-4 transition-transform duration-500 group-hover:-translate-x-1 rtl:group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    <span class="text-[10px] font-extrabold uppercase tracking-[0.25em] pt-0.5">{{ __('Back to Website') }}</span>
                </div>
            </a>

            <!-- Refined Language Switcher -->
            <a href="{{ route('admin.setLang', app()->getLocale() == 'en' ? 'ar' : 'en') }}" class="text-[9px] font-bold text-slate-600 hover:text-amber-500/70 transition-all uppercase tracking-[0.4em] flex items-center justify-center gap-2 grayscale hover:grayscale-0 opacity-50 hover:opacity-100">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129" /></svg>
                {{ app()->getLocale() == 'en' ? 'Arabic' : 'English' }}
            </a>
        </div>
    </div>
</body>
</html>
