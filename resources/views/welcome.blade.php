<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth dark" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ \App\Models\Setting::get('bakery_name', config('app.name', 'Bakery')) }} - {{ __('Modern Elegance') }}</title>

        <!-- Fonts & Icons -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@300;400;500;600;700&family=Playfair+Display:wght@500;600;700&display=swap" rel="stylesheet">

        <!-- Use App Vite Assets -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            :root {
                --ink: #0b0704;
                --ink-soft: #140d07;
                --cream: #f8f2e8;
                --sand: #f1e4d3;
                --caramel: #d28a45;
                --toffee: #f2b36f;
                --cocoa: #3b2716;
                --rose: #f2c3a0;
            }

            body {
                font-family: 'Manrope', sans-serif;
                background:
                    radial-gradient(1200px 900px at 70% 20%, rgba(210, 138, 69, 0.2), transparent 60%),
                    radial-gradient(1000px 700px at 20% 80%, rgba(242, 179, 111, 0.2), transparent 65%),
                    linear-gradient(180deg, #0b0704 0%, #110b06 40%, #060402 100%);
                color: var(--cream);
            }

            h1, h2, h3, h4 {
                font-family: 'Playfair Display', serif;
                letter-spacing: -0.02em;
            }
            
            /* Custom glowing text effect */
            .text-glow {
                text-shadow: 0 0 45px rgba(242, 179, 111, 0.45);
            }
            
            /* Enhanced glass styling for the landing page */
            .landing-glass {
                background: rgba(16, 12, 8, 0.6);
                backdrop-filter: blur(18px);
                -webkit-backdrop-filter: blur(18px);
                border: 1px solid rgba(242, 179, 111, 0.18);
                box-shadow: 0 20px 50px rgba(6, 4, 2, 0.45);
            }

            .content-overlay {
                position: relative;
                z-index: 10;
            }


            /* Premium hero 3D scene override */
            .hero-ambience {
                background:
                    radial-gradient(1200px 900px at 75% 40%, rgba(242, 179, 111, 0.26), transparent 65%),
                    radial-gradient(1000px 700px at 85% 80%, rgba(242, 195, 160, 0.22), transparent 70%),
                    linear-gradient(110deg, rgba(11, 7, 4, 1) 0%, rgba(11, 7, 4, 0.68) 48%, rgba(11, 7, 4, 0.2) 100%);
            }

            .hero-grain {
                background-image:
                    radial-gradient(rgba(255, 255, 255, 0.12) 1px, transparent 1px),
                    radial-gradient(rgba(255, 255, 255, 0.06) 1px, transparent 1px);
                background-size: 120px 120px, 70px 70px;
                background-position: 0 0, 25px 35px;
                opacity: 0.16;
                mix-blend-mode: screen;
            }

            .hero-fade {
                background: linear-gradient(90deg, rgba(11, 7, 4, 0.98) 0%, rgba(11, 7, 4, 0.9) 38%, rgba(11, 7, 4, 0.56) 58%, rgba(11, 7, 4, 0) 100%);
            }

            /* RTL Overrides for Lighting */
            [dir="rtl"] .hero-ambience {
                background:
                    radial-gradient(1200px 900px at 25% 40%, rgba(242, 179, 111, 0.26), transparent 65%),
                    radial-gradient(1000px 700px at 15% 80%, rgba(242, 195, 160, 0.22), transparent 70%),
                    linear-gradient(250deg, rgba(11, 7, 4, 1) 0%, rgba(11, 7, 4, 0.68) 48%, rgba(11, 7, 4, 0.2) 100%);
            }

            [dir="rtl"] .hero-fade {
                background: linear-gradient(270deg, rgba(11, 7, 4, 0.98) 0%, rgba(11, 7, 4, 0.9) 38%, rgba(11, 7, 4, 0.56) 58%, rgba(11, 7, 4, 0) 100%);
            }

            .hero-reveal {
                animation: hero-rise 0.9s cubic-bezier(0.22, 1, 0.36, 1) both;
                animation-delay: var(--delay, 0s);
            }

            @keyframes hero-rise {
                from {
                    opacity: 0;
                    transform: translateY(28px);
                }
                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }

            .hero-scene {
                position: absolute;
                inset: 0;
                margin: 0 auto;
                max-width: 1440px;
                display: flex;
                align-items: center;
                justify-content: flex-end;
                padding-inline-end: clamp(1rem, 7vw, 6rem);
                pointer-events: none;
                z-index: 0;
            }

            .hero-sculpture {
                --tilt-x: -8;
                --tilt-y: 8;
                position: relative;
                top: 50%;
                transform: translateY(-50%) rotateX(calc(var(--tilt-y) * 1deg)) rotateY(calc(var(--tilt-x) * 1deg));
                width: min(680px, 96vw);
                aspect-ratio: 1 / 1;
                transform-style: preserve-3d;
                perspective: 1400px;
                will-change: transform;
                transition: transform 350ms cubic-bezier(0.2, 0.8, 0.2, 1);
            }

            @media (max-width: 1024px) {
                .hero-sculpture {
                    transform: translateY(-50%) rotateX(calc(var(--tilt-y) * 1deg)) rotateY(calc(var(--tilt-x) * 1deg)) scale(0.65);
                    opacity: 0.35;
                }
                .hero-scene {
                    justify-content: center;
                    padding-inline-end: 0;
                }
            }

            @media (max-width: 640px) {
                .hero-sculpture {
                    transform: translateY(-50%) rotateX(calc(var(--tilt-y) * 1deg)) rotateY(calc(var(--tilt-x) * 1deg)) scale(0.45);
                    opacity: 0.25;
                }
            }

            .hero-sculpture::before {
                content: "";
                position: absolute;
                inset: 16%;
                border-radius: 50%;
                background: radial-gradient(circle at 50% 55%, rgba(242, 179, 111, 0.26), rgba(11, 7, 4, 0) 72%);
                filter: blur(6px);
                transform: translateZ(-20px);
            }

            .hero-aura {
                position: absolute;
                inset: 0;
                border-radius: 50%;
                background: radial-gradient(circle at 50% 48%, rgba(242, 179, 111, 0.45), rgba(11, 7, 4, 0) 68%);
                filter: blur(1px);
                opacity: 0.95;
                animation: aura-pulse 7s ease-in-out infinite;
            }

            @keyframes aura-pulse {
                0%, 100% {
                    transform: scale(0.98) translateZ(0);
                    opacity: 0.72;
                }
                50% {
                    transform: scale(1.05) translateZ(24px);
                    opacity: 1;
                }
            }

            .hero-beam {
                position: absolute;
                width: 42%;
                height: 74%;
                left: 50%;
                top: 50%;
                transform: translate(-50%, -50%) translateZ(70px);
                border-radius: 999px;
                background: linear-gradient(180deg, rgba(255, 255, 255, 0.28), rgba(255, 255, 255, 0.04) 44%, rgba(255, 255, 255, 0));
                filter: blur(0.8px);
                opacity: 0.72;
                animation: beam-breathe 8s ease-in-out infinite;
            }

            @keyframes beam-breathe {
                0%, 100% {
                    opacity: 0.5;
                    transform: translate(-50%, -50%) translateZ(64px) scaleY(0.95);
                }
                50% {
                    opacity: 0.86;
                    transform: translate(-50%, -50%) translateZ(82px) scaleY(1.05);
                }
            }

            .hero-halo {
                position: absolute;
                left: 50%;
                top: 50%;
                border-radius: 50%;
                transform-style: preserve-3d;
            }

            .hero-halo--outer {
                width: 72%;
                height: 72%;
                border: 1px solid rgba(242, 195, 160, 0.36);
                box-shadow: 0 0 44px rgba(242, 179, 111, 0.28), inset 0 0 26px rgba(242, 179, 111, 0.16);
                transform: translate(-50%, -50%) rotateX(70deg) translateZ(36px);
                animation: halo-turn 18s linear infinite;
            }

            .hero-halo--inner {
                width: 50%;
                height: 50%;
                border: 1px solid rgba(248, 242, 232, 0.45);
                box-shadow: inset 0 0 22px rgba(248, 242, 232, 0.24);
                transform: translate(-50%, -50%) rotateX(72deg) translateZ(48px);
                animation: halo-turn-reverse 12s linear infinite;
            }

            @keyframes halo-turn {
                from {
                    transform: translate(-50%, -50%) rotateX(70deg) rotateZ(0deg) translateZ(36px);
                }
                to {
                    transform: translate(-50%, -50%) rotateX(70deg) rotateZ(360deg) translateZ(36px);
                }
            }

            @keyframes halo-turn-reverse {
                from {
                    transform: translate(-50%, -50%) rotateX(72deg) rotateZ(360deg) translateZ(48px);
                }
                to {
                    transform: translate(-50%, -50%) rotateX(72deg) rotateZ(0deg) translateZ(48px);
                }
            }

            .hero-orbit {
                position: absolute;
                inset: 12%;
                transform-style: preserve-3d;
                animation: hero-orbit-turn 20s linear infinite;
            }

            @keyframes hero-orbit-turn {
                from {
                    transform: rotateY(0deg) rotateX(12deg);
                }
                to {
                    transform: rotateY(360deg) rotateX(12deg);
                }
            }

            .hero-flour {
                position: absolute;
                inset: 0;
            }

            .hero-flour span {
                position: absolute;
                left: var(--x);
                top: var(--y);
                width: var(--size);
                height: var(--size);
                border-radius: 50%;
                background: radial-gradient(circle, rgba(255, 255, 255, 0.95), rgba(255, 255, 255, 0));
                opacity: 0.68;
                transform: translate(-50%, -50%);
                animation: flour-drift 6s ease-in-out infinite;
                animation-delay: var(--delay);
            }

            @keyframes flour-drift {
                0%, 100% {
                    opacity: 0.3;
                    transform: translate(-50%, -50%) scale(0.7);
                }
                50% {
                    opacity: 0.9;
                    transform: translate(-50%, -50%) translateY(-12px) scale(1.2);
                }
            }

            /* Bread-focused hero centerpiece */
            .hero-orbit--bread {
                inset: 8%;
                animation-duration: 24s;
            }

            .hero-garnish {
                --angle: 0deg;
                --radius: 250px;
                --lift: 0px;
                position: absolute;
                top: 50%;
                left: 50%;
                transform: translate(-50%, -50%) rotateY(var(--angle)) translateZ(var(--radius)) rotateY(calc(var(--angle) * -1)) translateY(var(--lift));
                filter: drop-shadow(0 14px 24px rgba(6, 4, 2, 0.45));
                animation: garnish-bob 7s ease-in-out infinite;
                animation-delay: var(--delay, 0s);
            }

            .hero-garnish--grain {
                width: 18px;
                height: 92px;
                border-radius: 999px;
                background: linear-gradient(180deg, #f7d49d, #d08b43 50%, #8f531f);
                border: 1px solid rgba(255, 255, 255, 0.26);
            }

            .hero-garnish--seed {
                width: 54px;
                height: 34px;
                border-radius: 58% 42% 56% 44%;
                background: linear-gradient(145deg, #f6c887, #b46934);
                border: 1px solid rgba(255, 255, 255, 0.2);
            }

            .hero-garnish--1 { --angle: 34deg; --radius: 246px; --lift: -8px; --delay: 0.3s; }
            .hero-garnish--2 { --angle: 132deg; --radius: 232px; --lift: 10px; --delay: 1.4s; }
            .hero-garnish--3 { --angle: 222deg; --radius: 256px; --lift: -6px; --delay: 0.8s; }
            .hero-garnish--4 { --angle: 314deg; --radius: 242px; --lift: 7px; --delay: 1.8s; }

            @keyframes garnish-bob {
                0%, 100% {
                    transform: translate(-50%, -50%) rotateY(var(--angle)) translateZ(var(--radius)) rotateY(calc(var(--angle) * -1)) translateY(var(--lift));
                }
                50% {
                    transform: translate(-50%, -50%) rotateY(var(--angle)) translateZ(calc(var(--radius) + 12px)) rotateY(calc(var(--angle) * -1)) translateY(calc(var(--lift) - 12px));
                }
            }

            .hero-bread-stage {
                position: absolute;
                left: 50%;
                top: 54%;
                width: 62%;
                height: 64%;
                transform: translate(-50%, -50%) translateZ(110px);
                transform-style: preserve-3d;
            }

            .hero-bread-stage::before {
                content: "";
                position: absolute;
                left: 50%;
                bottom: 2%;
                width: 88%;
                height: 18%;
                transform: translateX(-50%) translateZ(-8px);
                border-radius: 999px;
                background: radial-gradient(ellipse at center, rgba(0, 0, 0, 0.42), rgba(0, 0, 0, 0));
                filter: blur(2px);
                opacity: 0.72;
            }

            .hero-rolling-pin {
                position: absolute;
                left: 50%;
                bottom: 20%;
                width: 86%;
                height: 12%;
                transform: translateX(-50%) rotateZ(-8deg) translateZ(10px);
                border-radius: 999px;
                background: linear-gradient(180deg, #d99a62, #a76333);
                border: 1px solid rgba(255, 255, 255, 0.2);
                box-shadow: 0 12px 24px rgba(6, 4, 2, 0.36), inset 0 6px 10px rgba(255, 255, 255, 0.16), inset 0 -8px 10px rgba(84, 45, 21, 0.35);
                opacity: 0.82;
            }

            .hero-rolling-pin::before,
            .hero-rolling-pin::after {
                content: "";
                position: absolute;
                top: 26%;
                width: 12%;
                height: 48%;
                border-radius: 999px;
                background: linear-gradient(180deg, #f0cf9f, #a76a39);
                border: 1px solid rgba(255, 255, 255, 0.24);
            }

            .hero-rolling-pin::before {
                left: -8%;
            }

            .hero-rolling-pin::after {
                right: -8%;
            }

            .loaf-stack {
                position: absolute;
                left: 50%;
                bottom: 18%;
                width: 78%;
                height: 62%;
                transform: translateX(-50%) translateZ(44px);
                transform-style: preserve-3d;
                animation: loaf-stack-rise 8s ease-in-out infinite;
            }

            @keyframes loaf-stack-rise {
                0%, 100% {
                    transform: translateX(-50%) translateZ(44px) translateY(0);
                }
                50% {
                    transform: translateX(-50%) translateZ(62px) translateY(-8px);
                }
            }

            .loaf {
                position: absolute;
                left: 50%;
                transform: translateX(-50%);
                border-radius: 56% 44% 48% 52% / 62% 58% 42% 38%;
                border: 1px solid rgba(255, 255, 255, 0.24);
                box-shadow: 0 14px 28px rgba(6, 4, 2, 0.42), inset 0 8px 14px rgba(255, 255, 255, 0.2), inset 0 -12px 16px rgba(81, 41, 17, 0.35);
                overflow: hidden;
            }

            .loaf::before {
                content: "";
                position: absolute;
                left: 18%;
                right: 18%;
                top: 30%;
                height: 10%;
                border-radius: 999px;
                background: rgba(255, 242, 224, 0.55);
                box-shadow: 0 18px 0 rgba(255, 242, 224, 0.35);
                transform: rotate(-8deg);
                opacity: 0.75;
            }

            .loaf::after {
                content: "";
                position: absolute;
                inset: 10% 12%;
                border-radius: inherit;
                border: 1px solid rgba(255, 255, 255, 0.16);
                opacity: 0.36;
            }

            .loaf--base {
                width: 86%;
                height: 42%;
                bottom: 0;
                background: linear-gradient(165deg, #f1b97b 0%, #c0743a 58%, #8d481f 100%);
            }

            .loaf--middle {
                width: 68%;
                height: 34%;
                bottom: 30%;
                background: linear-gradient(165deg, #f4c78f 0%, #cc8148 58%, #9e582a 100%);
            }

            .loaf--top {
                width: 52%;
                height: 28%;
                bottom: 55%;
                background: linear-gradient(165deg, #f8d9ac 0%, #d28c57 58%, #af6735 100%);
            }

            .hero-bread-board {
                position: absolute;
                left: 50%;
                bottom: 6%;
                width: 88%;
                height: 16%;
                transform: translateX(-50%) translateZ(0);
                border-radius: 999px;
                background: linear-gradient(180deg, #deb484, #9d6032);
                border: 1px solid rgba(255, 255, 255, 0.24);
                box-shadow: 0 22px 38px rgba(6, 4, 2, 0.55), inset 0 8px 12px rgba(255, 255, 255, 0.2), inset 0 -10px 16px rgba(84, 46, 22, 0.34);
            }

            .hero-bread-board::after {
                content: "";
                position: absolute;
                inset: 18% 12%;
                border-radius: 999px;
                border: 1px solid rgba(255, 255, 255, 0.18);
                opacity: 0.5;
            }

            .aroma {
                position: absolute;
                width: 78px;
                height: 150px;
                border-radius: 50%;
                border: 2px solid rgba(255, 242, 224, 0.36);
                border-color: rgba(255, 242, 224, 0.5) transparent transparent transparent;
                filter: blur(0.25px);
                animation: aroma-rise 6s ease-in-out infinite;
                --rot: 0deg;
            }

            .aroma--1 {
                left: 34%;
                bottom: 54%;
                --rot: -10deg;
                animation-delay: 0.1s;
            }

            .aroma--2 {
                left: 48%;
                bottom: 58%;
                --rot: 5deg;
                animation-delay: 1.1s;
            }

            .aroma--3 {
                left: 62%;
                bottom: 54%;
                --rot: -3deg;
                animation-delay: 2s;
            }

            @keyframes aroma-rise {
                0% {
                    opacity: 0;
                    transform: rotate(var(--rot)) translateY(18px) scale(0.92);
                }
                40% {
                    opacity: 0.7;
                }
                100% {
                    opacity: 0;
                    transform: rotate(var(--rot)) translateY(-34px) scale(1.08);
                }
            }

            @media (max-width: 1024px) {
                .hero-scene {
                    justify-content: center;
                    padding-right: 0;
                }

                .hero-sculpture {
                    width: min(540px, 112vw);
                    opacity: 0.78;
                }

                .hero-bread-stage {
                    width: 70%;
                    height: 66%;
                }
            }

            @media (max-width: 640px) {
                .hero-sculpture {
                    width: min(430px, 134vw);
                    opacity: 0.62;
                }

                .hero-bread-stage {
                    width: 76%;
                    height: 68%;
                }

                .hero-garnish {
                    opacity: 0.75;
                }
            }

            /* Floating animation for images */
            @keyframes float {
                0% { transform: translateY(0px); }
                50% { transform: translateY(-15px); }
                100% { transform: translateY(0px); }
            }
            .animate-float {
                animation: float 8s ease-in-out infinite;
            }

            @media (prefers-reduced-motion: reduce) {
                .hero-reveal,
                .animate-float,
                .hero-aura,
                .hero-beam,
                .hero-halo,
                .hero-garnish,
                .hero-bread-stage,
                .loaf-stack,
                .aroma,
                .hero-orbit,
                .hero-flour span {
                    animation: none !important;
                }
            }
        </style>
    </head>
    <body class="antialiased selection:bg-amber-500 selection:text-white overflow-x-hidden">

        <!-- Navigation -->
        <nav class="fixed w-full z-50 transition-all duration-300 glass-navbar py-4">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
                <div class="flex justify-between items-center">
                    <div class="flex items-center gap-2 cursor-pointer">
                        <div class="w-10 h-10 rounded-full bg-gradient-to-br from-amber-400 to-amber-600 flex items-center justify-center text-white font-bold text-xl shadow-lg shadow-amber-500/30">
                            {{ mb_substr(\App\Models\Setting::get('bakery_name', config('app.name', 'Bakery')), 0, 1) }}
                        </div>
                        <span class="text-2xl font-bold text-white tracking-tight">{{ \App\Models\Setting::get('bakery_name', config('app.name', 'Bakery')) }}</span>
                    </div>
                    
                    <div class="hidden md:flex items-center gap-8">
                        <a href="#about" class="text-slate-300 hover:text-amber-400 transition-colors font-medium">{{ __('About Us') }}</a>
                        <a href="#offer" class="text-slate-300 hover:text-amber-400 transition-colors font-medium">{{ __('What We Offer') }}</a>
                        <a href="#contact" class="text-slate-300 hover:text-amber-400 transition-colors font-medium">{{ __('Contact Us') }}</a>
                        
                        <!-- Language Switcher -->
                        <a href="{{ route('admin.setLang', app()->getLocale() == 'en' ? 'ar' : 'en') }}" class="group flex items-center gap-2 px-3 py-1.5 rounded-full bg-white/5 hover:bg-white/10 border border-white/10 transition-all duration-300">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4 text-amber-400 group-hover:rotate-12 transition-transform"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>
                            <span class="text-xs font-bold uppercase tracking-widest text-slate-300 group-hover:text-white transition-colors">
                                {{ app()->getLocale() == 'en' ? 'AR' : 'EN' }}
                            </span>
                        </a>

                        @auth('admin')
                            <a href="{{ url('/admin/dashboard') }}" class="px-5 py-2 rounded-full bg-amber-500 hover:bg-amber-600 text-white font-medium transition duration-300 shadow-lg shadow-amber-500/20">{{ __('Dashboard') }}</a>
                        @else
                            <a href="{{ route('admin.login.page') }}" class="px-5 py-2 rounded-full bg-white/10 hover:bg-white/20 border border-white/10 text-white font-medium transition duration-300 backdrop-blur-md">{{ __('Log in') }}</a>
                        @endauth
                    </div>

                    <!-- Mobile Menu Button -->
                    <button id="mobile-menu-toggle" class="md:hidden p-2 text-slate-300 focus:outline-none" aria-label="Toggle Menu">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path class="menu-open" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7" />
                            <path class="menu-close hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Mobile Menu Overlay -->
            <div id="mobile-menu" class="fixed inset-0 z-50 bg-[#0b0704]/95 backdrop-blur-2xl flex flex-col items-center justify-center gap-8 transition-all duration-500 opacity-0 pointer-events-none translate-y-[-10%]">
                <a href="#about" class="mobile-nav-link text-3xl font-bold text-white tracking-widest hover:text-amber-500 transition-colors">{{ __('About Us') }}</a>
                <a href="#offer" class="mobile-nav-link text-3xl font-bold text-white tracking-widest hover:text-amber-500 transition-colors">{{ __('What We Offer') }}</a>
                <a href="#contact" class="mobile-nav-link text-3xl font-bold text-white tracking-widest hover:text-amber-500 transition-colors">{{ __('Contact Us') }}</a>
                
                <div class="h-px w-24 bg-white/10 my-4"></div>

                <a href="{{ route('admin.setLang', app()->getLocale() == 'en' ? 'ar' : 'en') }}" class="flex items-center gap-3 px-6 py-3 rounded-full bg-white/5 border border-white/10 text-white font-bold uppercase tracking-widest">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-5 h-5 text-amber-400"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>
                    {{ app()->getLocale() == 'en' ? 'Arabic' : 'English' }}
                </a>

                @auth('admin')
                    <a href="{{ url('/admin/dashboard') }}" class="px-8 py-4 rounded-full bg-amber-500 text-white font-bold tracking-widest shadow-xl">{{ __('Dashboard') }}</a>
                @else
                    <a href="{{ route('admin.login.page') }}" class="px-8 py-4 rounded-full bg-white/10 text-white font-bold tracking-widest border border-white/20">{{ __('Log in') }}</a>
                @endauth
            </div>
        </nav>

        <!-- Hero Section -->
        <section class="relative min-h-screen flex items-center pt-20 overflow-hidden" data-hero-3d>
            <!-- Premium Bakery-Themed 3D Hero -->
            <div class="absolute inset-0 hero-ambience"></div>
            <div class="absolute inset-0 hero-grain"></div>
            <div class="hero-scene" aria-hidden="true">
                <div class="hero-sculpture" data-hero-sculpture>
                    <div class="hero-aura"></div>
                    <div class="hero-beam"></div>
                    <div class="hero-halo hero-halo--outer"></div>
                    <div class="hero-halo hero-halo--inner"></div>
                    <div class="hero-orbit hero-orbit--bread">
                        <span class="hero-garnish hero-garnish--grain hero-garnish--1"></span>
                        <span class="hero-garnish hero-garnish--seed hero-garnish--2"></span>
                        <span class="hero-garnish hero-garnish--grain hero-garnish--3"></span>
                        <span class="hero-garnish hero-garnish--seed hero-garnish--4"></span>
                    </div>
                    <div class="hero-bread-stage">
                        <div class="hero-rolling-pin"></div>
                        <div class="loaf-stack">
                            <div class="loaf loaf--top"></div>
                            <div class="loaf loaf--middle"></div>
                            <div class="loaf loaf--base"></div>
                        </div>
                        <div class="hero-bread-board"></div>
                        <div class="aroma aroma--1"></div>
                        <div class="aroma aroma--2"></div>
                        <div class="aroma aroma--3"></div>
                    </div>
                    <div class="hero-flour">
                        <span style="--x: 18%; --y: 22%; --size: 10px; --delay: 0.3s;"></span>
                        <span style="--x: 36%; --y: 14%; --size: 8px; --delay: 1.1s;"></span>
                        <span style="--x: 72%; --y: 22%; --size: 12px; --delay: 0.8s;"></span>
                        <span style="--x: 84%; --y: 40%; --size: 9px; --delay: 1.8s;"></span>
                        <span style="--x: 24%; --y: 70%; --size: 10px; --delay: 1.4s;"></span>
                        <span style="--x: 68%; --y: 78%; --size: 8px; --delay: 2.1s;"></span>
                    </div>
                </div>
            </div>
            
            <!-- Left Gradient Fade to make text readable over 3D -->
            <div class="absolute inset-0 hero-fade z-0 pointer-events-none"></div>

            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full content-overlay">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
                    <div class="flex flex-col justify-center max-w-xl">
                        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-amber-500/10 border border-amber-500/20 text-amber-300 text-sm font-medium mb-6 w-fit hero-reveal" style="--delay: 0.1s;">
                            <span class="w-2 h-2 rounded-full bg-amber-400 animate-pulse"></span>
                            {{ __('Small Batch, Big Flavor') }}
                        </div>
                        <h1 class="text-4xl sm:text-5xl lg:text-7xl font-extrabold text-white leading-tight mb-6 tracking-tight text-glow hero-reveal" style="--delay: 0.2s;">
                            {{ __('Artisan') }} <span class="text-transparent bg-clip-text bg-gradient-to-r from-amber-300 to-amber-500">{{ __('Baking,') }}</span><br>{{ __('Modern Patisserie.') }}
                        </h1>
                        <p class="text-base sm:text-lg text-slate-300 mb-8 leading-relaxed font-light hero-reveal" style="--delay: 0.3s;">
                            {{ __('Slow-fermented breads, delicate pastries, and celebration cakes finished with a designer\'s touch. Every bite balances warmth, craft, and a clean modern feel.') }}
                        </p>
                        <div class="flex flex-wrap gap-4 hero-reveal" style="--delay: 0.4s;">
                            <a href="#offer" class="px-8 py-4 rounded-full bg-amber-500 hover:bg-amber-600 text-white font-semibold transition-all duration-300 shadow-[0_0_20px_rgba(245,158,11,0.3)] hover:shadow-[0_0_30px_rgba(245,158,11,0.5)] hover:-translate-y-1 block text-center">
                                {{ __('Explore Menu') }}
                            </a>
                            <a href="#about" class="px-8 py-4 rounded-full bg-white/5 hover:bg-white/10 text-white border border-white/10 font-semibold transition-all duration-300 hover:-translate-y-1 block text-center backdrop-blur-md">
                                {{ __('Discover Our Story') }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Scroll Indicator -->
            <div class="absolute bottom-10 left-1/2 -translate-x-1/2 animate-bounce flex flex-col items-center content-overlay opacity-50 hidden md:flex">
                <span class="text-xs uppercase tracking-widest text-slate-400 mb-2">{{ __('Scroll') }}</span>
                <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path></svg>
            </div>
        </section>

        <!-- About Us Section -->
        <section id="about" class="py-16 md:py-24 relative z-10 bg-[#0b0704]">
            <div class="absolute top-0 inset-inline-end-0 w-96 h-96 bg-amber-500/10 rounded-full blur-[120px] pointer-events-none"></div>
            
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
                <div class="flex flex-col lg:flex-row items-center gap-16">
                    <div class="lg:w-1/2 relative">
                        <div class="absolute inset-0 bg-gradient-to-tr from-amber-500/20 to-transparent rounded-3xl transform rotate-3 scale-105 opacity-50"></div>
                        <img src="https://images.unsplash.com/photo-1509440159596-0249088772ff?ixlib=rb-4.0.3&auto=format&fit=crop&w=1000&q=80" alt="Beautiful fresh bread" class="rounded-3xl shadow-2xl relative z-10 w-full h-[500px] object-cover animate-float border border-white/10">
                        
                        <!-- Floating Badge -->
                        <div class="absolute bottom-4 lg:bottom-8 inset-inline-end-0 lg:inset-inline-end-[-2rem] z-20 landing-glass p-4 lg:p-6 rounded-2xl shadow-xl flex items-center gap-4">
                            <div class="text-amber-500">
                                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </div>
                            <div>
                                <h4 class="text-white font-bold text-xl">{{ __('Since 1999') }}</h4>
                                <p class="text-slate-400 text-sm">{{ __('Crafting excellence') }}</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="lg:w-1/2">
                        <h2 class="text-amber-500 font-semibold tracking-wider uppercase text-sm mb-3 relative inline-block">
                            {{ __('About Us') }}
                            <span class="absolute -bottom-2 {{ app()->getLocale() == 'ar' ? 'right-0' : 'left-0' }} w-1/2 h-0.5 bg-amber-500"></span>
                        </h2>
                        <h3 class="text-4xl lg:text-5xl font-bold text-white mb-6 mt-4">{{ __('Where Tradition Meets True Innovation') }}</h3>
                        <p class="text-slate-400 mb-6 text-lg leading-relaxed font-light">
                            {{ __('We don\'t just bake; we create edible works of art. Rooted in traditional techniques handed down through generations, our master bakers infuse modern flavors and breathtaking designs into everything we make.') }}
                        </p>
                        <p class="text-slate-400 mb-8 text-lg leading-relaxed font-light">
                            {{ __('From the crackle of hand-shaped artisan loaves to the delicate crumb of our signature pastries, we guarantee an unparalleled culinary experience that tantalizes your taste buds and delights your eyes.') }}
                        </p>
                        
                        <div class="grid grid-cols-2 gap-6">
                            <div class="border-s-2 border-amber-500 ps-4">
                                <h4 class="text-2xl font-bold text-white">100%</h4>
                                <p class="text-slate-400">{{ __('Organic Ingredients') }}</p>
                            </div>
                            <div class="border-s-2 border-amber-500 ps-4">
                                <h4 class="text-2xl font-bold text-white">25+</h4>
                                <p class="text-slate-400">{{ __('Master Bakers') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- What We Offer Section -->
        <section id="offer" class="py-16 md:py-24 relative z-10 overflow-hidden">
            <!-- Background Elements -->
            <div class="absolute inset-0 bg-[#120c08]/70"></div>
            <div class="absolute inset-inline-start-[-12rem] bottom-0 w-96 h-96 bg-amber-500/10 rounded-full blur-[120px] pointer-events-none"></div>

            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
                <div class="text-center max-w-3xl mx-auto mb-16">
                    <h2 class="text-amber-500 font-semibold tracking-wider uppercase text-sm mb-3">{{ __('What We Offer') }}</h2>
                    <h3 class="text-4xl lg:text-5xl font-bold text-white mb-6">{{ __('Our Masterpieces') }}</h3>
                    <p class="text-slate-300 text-lg font-light leading-relaxed">{{ __('Handcrafted daily using only the finest, carefully sourced ingredients.') }}</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    <!-- Card 1 -->
                    <div class="glass-card group rounded-2xl overflow-hidden hover:-translate-y-2 transition-all duration-300">
                        <div class="h-64 overflow-hidden relative">
                            <div class="absolute inset-0 bg-black/20 z-10 group-hover:bg-transparent transition-all duration-300"></div>
                            <img src="https://images.unsplash.com/photo-1586444248902-2f64eddc13df?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" alt="Artisan Bread" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                        </div>
                        <div class="p-8">
                            <h4 class="text-2xl font-bold text-white mb-3 group-hover:text-amber-400 transition-colors lowercase first-letter:uppercase">{{ __('Artisan Bread') }}</h4>
                            <p class="text-slate-400 mb-6 line-clamp-3">{{ __('Naturally leavened sourdough and rustic loaves, baked on stone hearths for a perfect crust and airy crumb.') }}</p>
                            <a href="#" class="text-amber-400 font-medium inline-flex items-center hover:text-amber-300 transition-colors">
                                {{ __('Discover More') }} <svg class="w-4 h-4 ms-1 group-hover:translate-x-1 rtl:group-hover:-translate-x-1 rtl:rotate-180 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                            </a>
                        </div>
                    </div>

                    <!-- Card 2 -->
                    <div class="glass-card group rounded-2xl overflow-hidden hover:-translate-y-2 transition-all duration-300">
                        <div class="h-64 overflow-hidden relative">
                            <div class="absolute inset-0 bg-black/20 z-10 group-hover:bg-transparent transition-all duration-300"></div>
                            <img src="https://images.unsplash.com/photo-1588195538326-c5b1e9f80a1b?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" alt="Signature Cakes" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                        </div>
                        <div class="p-8">
                            <h4 class="text-2xl font-bold text-white mb-3 group-hover:text-amber-400 transition-colors lowercase first-letter:uppercase">{{ __('Signature Cakes') }}</h4>
                            <p class="text-slate-400 mb-6 line-clamp-3">{{ __('Elegant, custom-designed cakes featuring breathtaking modern aesthetics and luxurious, mouth-watering flavors.') }}</p>
                            <a href="#" class="text-amber-400 font-medium inline-flex items-center hover:text-amber-300 transition-colors">
                                {{ __('Discover More') }} <svg class="w-4 h-4 ms-1 group-hover:translate-x-1 rtl:group-hover:-translate-x-1 rtl:rotate-180 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                            </a>
                        </div>
                    </div>

                    <!-- Card 3 -->
                    <div class="glass-card group rounded-2xl overflow-hidden hover:-translate-y-2 transition-all duration-300">
                        <div class="h-64 overflow-hidden relative">
                            <div class="absolute inset-0 bg-black/20 z-10 group-hover:bg-transparent transition-all duration-300"></div>
                            <img src="https://images.unsplash.com/photo-1603532648955-039310d9ed75?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" alt="French Pastries" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                        </div>
                        <div class="p-8">
                            <h4 class="text-2xl font-bold text-white mb-3 group-hover:text-amber-400 transition-colors lowercase first-letter:uppercase">{{ __('French Pastries') }}</h4>
                            <p class="text-slate-400 mb-6 line-clamp-3">{{ __('Flaky, buttery croissants, delicate macarons, and rich tartes crafted with authentic European techniques.') }}</p>
                            <a href="#" class="text-amber-400 font-medium inline-flex items-center hover:text-amber-300 transition-colors">
                                {{ __('Discover More') }} <svg class="w-4 h-4 ms-1 group-hover:translate-x-1 rtl:group-hover:-translate-x-1 rtl:rotate-180 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Contact Us Section -->
        <section id="contact" class="py-16 md:py-24 relative bg-[#0c0805] border-t border-white/5">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-16">
                    <div>
                        <h2 class="text-amber-500 font-semibold tracking-wider uppercase text-sm mb-3">{{ __('Contact Us') }}</h2>
                        <h3 class="text-4xl lg:text-5xl font-bold text-white mb-8">{{ __('Let\'s Bring Your Vision to Life.') }}</h3>
                        <p class="text-slate-400 mb-10 text-lg leading-relaxed">
                            {{ __('Whether you need a custom cake for a monumental event or just want to reserve your favorite morning pastry, our team is here for you. We\'d love to hear from you.') }}
                        </p>
                        
                        <div class="space-y-8">
                            <div class="flex items-start gap-4">
                                <div class="w-12 h-12 rounded-full bg-slate-800/80 border border-slate-700 flex items-center justify-center text-amber-500 flex-shrink-0 shadow-lg">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                </div>
                                <div>
                                    <h4 class="text-lg font-bold text-white mb-1">{{ __('Our Location') }}</h4>
                                    <p class="text-slate-400 font-light">{{ \App\Models\Setting::get('bakery_address', __('123 Baker Street, Artisan District, NY 10001')) }}</p>
                                </div>
                            </div>
                            
                            <div class="flex items-start gap-4">
                                <div class="w-12 h-12 rounded-full bg-slate-800/80 border border-slate-700 flex items-center justify-center text-amber-500 flex-shrink-0 shadow-lg">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                                </div>
                                <div>
                                    <h4 class="text-lg font-bold text-white mb-1">{{ __('Email Us') }}</h4>
                                    <p class="text-slate-400 font-light">{{ \App\Models\Setting::get('bakery_email', __('contact@bakery.com')) }}</p>
                                </div>
                            </div>
                            
                            <div class="flex items-start gap-4">
                                <div class="w-12 h-12 rounded-full bg-slate-800/80 border border-slate-700 flex items-center justify-center text-amber-500 flex-shrink-0 shadow-lg">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                                </div>
                                <div>
                                    <h4 class="text-lg font-bold text-white mb-1">{{ __('Call Us') }}</h4>
                                    <p class="text-slate-400 font-light">{{ \App\Models\Setting::get('bakery_phone', __('+1 (555) 123-4567')) }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Decorative Element / Subtle Form -->
                    <div class="relative">
                        <div class="absolute inset-0 bg-gradient-to-tr from-amber-500/10 to-transparent rounded-3xl transform -rotate-1 opacity-50 z-0"></div>
                        <div class="landing-glass rounded-3xl p-8 relative z-10 h-full flex flex-col justify-center">
                            <h4 class="text-2xl font-bold text-white mb-6">{{ __('Send a Message') }}</h4>
                            <form id="publicContactForm" class="space-y-4">
                                <div id="formSuccess" class="p-3 bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 rounded-xl text-sm" style="display: none;"></div>
                                <div id="formError" class="p-3 bg-red-500/10 border border-red-500/20 text-red-400 rounded-xl text-sm" style="display: none;"></div>
                                
                                <div>
                                    <input type="text" id="contactName" required placeholder="{{ __('Your Name') }}" class="w-full glass-input px-4 py-3 rounded-xl bg-slate-900/50 border border-slate-700 text-white placeholder-slate-500 focus:outline-none focus:border-amber-500 transition-colors">
                                </div>
                                <div>
                                    <input type="email" id="contactEmail" required placeholder="{{ __('Your Email') }}" class="w-full glass-input px-4 py-3 rounded-xl bg-slate-900/50 border border-slate-700 text-white placeholder-slate-500 focus:outline-none focus:border-amber-500 transition-colors">
                                </div>
                                <div>
                                    <textarea id="contactMessage" required placeholder="{{ __('How can we help you?') }}" rows="4" class="w-full glass-input px-4 py-3 rounded-xl bg-slate-900/50 border border-slate-700 text-white placeholder-slate-500 focus:outline-none focus:border-amber-500 transition-colors resize-none"></textarea>
                                </div>
                                <button type="submit" id="contactSubmit" class="w-full py-4 rounded-xl bg-amber-500 hover:bg-amber-600 disabled:opacity-50 disabled:cursor-not-allowed text-white font-semibold transition-all duration-300 shadow-[0_0_15px_rgba(245,158,11,0.2)] hover:shadow-[0_0_25px_rgba(245,158,11,0.4)] flex justify-center items-center">
                                    <span id="submitText">{{ __('Send Message') }}</span>
                                    <svg id="submitSpinner" class="animate-spin h-5 w-5 text-white" style="display: none;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                </button>
                            </form>

                            <script>
                                document.addEventListener('DOMContentLoaded', function() {
                                    const form = document.getElementById('publicContactForm');
                                    form.addEventListener('submit', function(e) {
                                        e.preventDefault();
                                        
                                        const name = document.getElementById('contactName').value;
                                        const email = document.getElementById('contactEmail').value;
                                        const message = document.getElementById('contactMessage').value;
                                        
                                        const btn = document.getElementById('contactSubmit');
                                        const submitText = document.getElementById('submitText');
                                        const spinner = document.getElementById('submitSpinner');
                                        const successDiv = document.getElementById('formSuccess');
                                        const errorDiv = document.getElementById('formError');
                                        
                                        btn.disabled = true;
                                        submitText.style.display = 'none';
                                        spinner.style.display = 'block';
                                        successDiv.style.display = 'none';
                                        errorDiv.style.display = 'none';
                                        
                                        fetch('{{ route('contact.store') }}', {
                                            method: 'POST',
                                            headers: {
                                                'Content-Type': 'application/json',
                                                'Accept': 'application/json',
                                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]') ? document.querySelector('meta[name="csrf-token"]').getAttribute('content') : ''
                                            },
                                            body: JSON.stringify({ name, email, message })
                                        })
                                        .then(response => response.json())
                                        .then(data => {
                                            btn.disabled = false;
                                            spinner.style.display = 'none';
                                            submitText.style.display = 'block';
                                            
                                            if(data.status === 'success') {
                                                successDiv.textContent = data.message;
                                                successDiv.style.display = 'block';
                                                form.reset();
                                                setTimeout(() => successDiv.style.display = 'none', 5000);
                                            } else {
                                                errorDiv.textContent = data.message || 'Error occurred';
                                                errorDiv.style.display = 'block';
                                            }
                                        })
                                        .catch(error => {
                                            btn.disabled = false;
                                            spinner.style.display = 'none';
                                            submitText.style.display = 'block';
                                            errorDiv.textContent = 'A network error occurred. Please try again.';
                                            errorDiv.style.display = 'block';
                                        });
                                    });
                                });
                            </script>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Minimal Footer -->
        <footer class="bg-[#0a0704] py-8 border-t border-white/5">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col md:flex-row justify-between items-center gap-4 relative z-10">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-full bg-gradient-to-br from-amber-400 to-amber-600 flex items-center justify-center text-white font-bold text-sm shadow-lg shadow-amber-500/30">
                        {{ mb_substr(\App\Models\Setting::get('bakery_name', config('app.name', 'Bakery')), 0, 1) }}
                    </div>
                    <span class="text-lg font-bold text-white tracking-tight">{{ \App\Models\Setting::get('bakery_name', config('app.name', 'Bakery')) }}</span>
                </div>
                
                <p class="text-slate-500 text-sm">
                    &copy; {{ date('Y') }} {{ \App\Models\Setting::get('bakery_name', config('app.name', 'Bakery')) }}. {{ __('All rights reserved.') }} | {{ __('Modern Elegance') }}
                </p>
                
                <div class="flex gap-4 text-slate-500">
                    <a href="#" class="hover:text-amber-500 transition-colors"><svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path fill-rule="evenodd" d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z" clip-rule="evenodd" /></svg></a>
                    <a href="#" class="hover:text-amber-500 transition-colors"><svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path fill-rule="evenodd" d="M12.315 2c2.43 0 2.784.013 3.808.06 1.064.049 1.791.218 2.427.465a4.902 4.902 0 011.772 1.153 4.902 4.902 0 011.153 1.772c.247.636.416 1.363.465 2.427.048 1.067.06 1.407.06 4.123v.08c0 2.643-.012 2.987-.06 4.043-.049 1.064-.218 1.791-.465 2.427a4.902 4.902 0 01-1.153 1.772 4.902 4.902 0 01-1.772 1.153c-.636.247-1.363.416-2.427.465-1.067.048-1.407.06-4.123.06h-.08c-2.643 0-2.987-.012-4.043-.06-1.064-.049-1.791-.218-2.427-.465a4.902 4.902 0 01-1.772-1.153 4.902 4.902 0 01-1.153-1.772c-.247-.636-.416-1.363-.465-2.427-.047-1.024-.06-1.379-.06-3.808v-.63c0-2.43.013-2.784.06-3.808.049-1.064.218-1.791.465-2.427a4.902 4.902 0 011.153-1.772A4.902 4.902 0 015.45 2.525c.636-.247 1.363-.416 2.427-.465C8.901 2.013 9.256 2 11.685 2h.63zm-.081 1.802h-.468c-2.456 0-2.784.011-3.807.058-.975.045-1.504.207-1.857.344-.467.182-.8.398-1.15.748-.35.35-.566.683-.748 1.15-.137.353-.3.882-.344 1.857-.047 1.023-.058 1.351-.058 3.807v.468c0 2.456.011 2.784.058 3.807.045.975.207 1.504.344 1.857.182.466.399.8.748 1.15.35.35.683.566 1.15.748.353.137.882.3 1.857.344 1.054.048 1.37.058 4.041.058h.08c2.597 0 2.917-.01 3.96-.058.976-.045 1.505-.207 1.858-.344.466-.182.8-.398 1.15-.748.35-.35.566-.683.748-1.15.137-.353.3-.882.344-1.857.048-1.055.058-1.37.058-4.041v-.08c0-2.597-.01-2.917-.058-3.96-.045-.976-.207-1.505-.344-1.858a3.097 3.097 0 00-.748-1.15 3.098 3.098 0 00-1.15-.748c-.353-.137-.882-.3-1.857-.344-1.023-.047-1.351-.058-3.807-.058zM12 6.865a5.135 5.135 0 110 10.27 5.135 5.135 0 010-10.27zm0 1.802a3.333 3.333 0 100 6.666 3.333 3.333 0 000-6.666zm5.338-3.205a1.2 1.2 0 110 2.4 1.2 1.2 0 010-2.4z" clip-rule="evenodd" /></svg></a>
                </div>
            </div>
        </footer>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // --- Mobile Navigation Logic ---
                const menuToggle = document.getElementById('mobile-menu-toggle');
                const mobileMenu = document.getElementById('mobile-menu');
                const menuOpenPath = document.querySelector('.menu-open');
                const menuClosePath = document.querySelector('.menu-close');
                const mobileLinks = document.querySelectorAll('.mobile-nav-link');
                let isMenuOpen = false;

                function toggleMenu() {
                    isMenuOpen = !isMenuOpen;
                    if (isMenuOpen) {
                        mobileMenu.classList.remove('opacity-0', 'pointer-events-none', 'translate-y-[-10%]');
                        mobileMenu.classList.add('opacity-100', 'pointer-events-auto', 'translate-y-0');
                        menuOpenPath.classList.add('hidden');
                        menuClosePath.classList.remove('hidden');
                        document.body.style.overflow = 'hidden'; // Prevent background scroll
                    } else {
                        mobileMenu.classList.remove('opacity-100', 'pointer-events-auto', 'translate-y-0');
                        mobileMenu.classList.add('opacity-0', 'pointer-events-none', 'translate-y-[-10%]');
                        menuOpenPath.classList.remove('hidden');
                        menuClosePath.classList.add('hidden');
                        document.body.style.overflow = '';
                    }
                }

                if (menuToggle && mobileMenu) {
                    menuToggle.addEventListener('click', toggleMenu);
                    // Close menu when clicking links
                    mobileLinks.forEach(link => {
                        link.addEventListener('click', toggleMenu);
                    });
                }

                // --- 3D Hero Interaction Logic ---
                var hero = document.querySelector('[data-hero-3d]');
                var sculpture = document.querySelector('[data-hero-sculpture]');

                if (hero && sculpture) {
                    var currentTiltX = -8;
                    var currentTiltY = 8;
                    var targetTiltX = currentTiltX;
                    var targetTiltY = currentTiltY;
                    var rafId = null;

                    function tick() {
                        currentTiltX += (targetTiltX - currentTiltX) * 0.12;
                        currentTiltY += (targetTiltY - currentTiltY) * 0.12;

                        sculpture.style.setProperty('--tilt-x', currentTiltX.toFixed(2));
                        sculpture.style.setProperty('--tilt-y', currentTiltY.toFixed(2));

                        if (Math.abs(targetTiltX - currentTiltX) > 0.02 || Math.abs(targetTiltY - currentTiltY) > 0.02) {
                            rafId = window.requestAnimationFrame(tick);
                        } else {
                            rafId = null;
                        }
                    }

                    function scheduleTick() {
                        if (rafId === null) {
                            rafId = window.requestAnimationFrame(tick);
                        }
                    }

                    function handlePointerMove(event) {
                        // Disable heavy 3D calculations on small screens
                        if (window.innerWidth < 1024) return;
                        
                        var rect = hero.getBoundingClientRect();
                        var normalizedX = ((event.clientX - rect.left) / rect.width - 0.5) * 2;
                        var normalizedY = ((event.clientY - rect.top) / rect.height - 0.5) * 2;

                        targetTiltX = normalizedX * 12;
                        targetTiltY = normalizedY * -10 + 2;
                        scheduleTick();
                    }

                    function handlePointerLeave() {
                        targetTiltX = -8;
                        targetTiltY = 8;
                        scheduleTick();
                    }

                    hero.addEventListener('pointermove', handlePointerMove, { passive: true });
                    hero.addEventListener('pointerleave', handlePointerLeave);
                }
            });
        </script>

    </body>
</html>
