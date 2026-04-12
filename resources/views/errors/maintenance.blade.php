<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('Website Under Maintenance') }} - {{ config('app.name', 'Bakery') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Noto+Kufi+Arabic:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            font-family: 'Plus Jakarta Sans', 'Noto Kufi Arabic', sans-serif;
        }
        .bg-mesh {
            background-color: #0f172a;
            background-image: 
                radial-gradient(at 0% 0%, rgba(234, 179, 8, 0.15) 0, transparent 50%), 
                radial-gradient(at 100% 0%, rgba(239, 68, 68, 0.1) 0, transparent 50%);
        }
        .glass {
            background: rgba(30, 41, 59, 0.7);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.05);
        }
    </style>
</head>
<body class="bg-mesh text-slate-200 min-h-screen flex items-center justify-center p-6 overflow-hidden">
    <div class="absolute inset-0 overflow-hidden pointer-events-none">
        <div class="absolute top-[-10%] left-[-10%] w-[40%] h-[40%] bg-amber-500/10 rounded-full blur-[120px]"></div>
        <div class="absolute bottom-[-10%] right-[-10%] w-[40%] h-[40%] bg-red-500/10 rounded-full blur-[120px]"></div>
    </div>

    <div class="max-w-xl w-full relative z-10">
        <div class="glass p-12 rounded-[2.5rem] shadow-2xl text-center">
            {{-- Icon/Illustration --}}
            <div class="relative w-24 h-24 mx-auto mb-10">
                <div class="absolute inset-0 bg-amber-500/20 rounded-3xl rotate-12 animate-pulse"></div>
                <div class="absolute inset-0 bg-amber-500/30 rounded-3xl -rotate-6 transition-transform hover:rotate-0 duration-500"></div>
                <div class="relative w-full h-full bg-amber-500 rounded-3xl flex items-center justify-center text-white shadow-xl shadow-amber-500/20">
                    <svg class="w-10 h-10" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 4a2 2 0 114 0v1a1 1 0 001 1h3a1 1 0 011 1v3a1 1 0 01-1 1h-1a2 2 0 100 4h1a1 1 0 011 1v3a1 1 0 01-1 1h-3a1 1 0 01-1-1v-1a2 2 0 11-4 0v1a1 1 0 01-1 1H7a1 1 0 01-1-1v-3a1 1 0 00-1-1H4a2 2 0 110-4h1a1 1 0 001-1V7a1 1 0 011-1h3a1 1 0 001-1V4z" />
                    </svg>
                </div>
            </div>

            <h1 class="text-4xl md:text-5xl font-extrabold text-white mb-6 tracking-tight">
                {{ __('Website Under Maintenance') }}
            </h1>
            
            <p class="text-lg text-slate-400 leading-relaxed mb-10">
                {{ __('We are currently performing scheduled maintenance to improve our services. Please check back shortly.') }}
            </p>

            <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                <div class="flex items-center gap-2 px-4 py-2 rounded-full bg-white/5 border border-white/10 text-xs font-bold text-amber-500 uppercase tracking-widest">
                    <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                    {{ __('Status: Maintenance Mode') }}
                </div>
            </div>

        </div>
        
        <p class="text-center mt-8 text-slate-600 text-xs font-medium uppercase tracking-[0.2em]">
            &copy; {{ date('Y') }} {{ config('app.name', 'Bakery Management System') }}
        </p>
    </div>
</body>
</html>
