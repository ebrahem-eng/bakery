<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth dark">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ config('app.name', 'Bakery') }} - Modern Elegance</title>

        <!-- Fonts & Icons -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
        
        <!-- Spline 3D Viewer -->
        <script type="module" src="https://unpkg.com/@splinetool/viewer@1.0.51/build/spline-viewer.js"></script>

        <!-- Use App Vite Assets -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            body {
                font-family: 'Outfit', sans-serif;
                background-color: #000000;
                color: #f8fafc;
            }
            
            /* Custom glowing text effect */
            .text-glow {
                text-shadow: 0 0 40px rgba(245, 158, 11, 0.4);
            }
            
            /* Enhanced glass styling for the landing page */
            .landing-glass {
                background: rgba(15, 23, 42, 0.4);
                backdrop-filter: blur(16px);
                -webkit-backdrop-filter: blur(16px);
                border: 1px solid rgba(255, 255, 255, 0.05);
            }
            
            .spline-container {
                position: absolute;
                top: 0;
                right: 0;
                width: 100%;
                height: 100%;
                z-index: 0;
                pointer-events: none; /* Let clicks pass through to text where overlapped */
            }

            .content-overlay {
                position: relative;
                z-index: 10;
            }
            
            /* Floating animation for images */
            @keyframes float {
                0% { transform: translateY(0px); }
                50% { transform: translateY(-15px); }
                100% { transform: translateY(0px); }
            }
            .animate-float {
                animation: float 6s ease-in-out infinite;
            }
        </style>
    </head>
    <body class="antialiased font-sans selection:bg-amber-500 selection:text-white overflow-x-hidden">

        <!-- Navigation -->
        <nav class="fixed w-full z-50 transition-all duration-300 glass-navbar py-4">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center">
                    <div class="flex items-center gap-2 cursor-pointer">
                        <div class="w-10 h-10 rounded-full bg-gradient-to-br from-amber-400 to-amber-600 flex items-center justify-center text-white font-bold text-xl shadow-lg shadow-amber-500/30">
                            B
                        </div>
                        <span class="text-2xl font-bold text-white tracking-tight">{{ config('app.name', 'Bakery') }}</span>
                    </div>
                    
                    <div class="hidden md:flex items-center space-x-8">
                        <a href="#about" class="text-slate-300 hover:text-amber-400 transition-colors font-medium">About Us</a>
                        <a href="#offer" class="text-slate-300 hover:text-amber-400 transition-colors font-medium">What We Offer</a>
                        <a href="#contact" class="text-slate-300 hover:text-amber-400 transition-colors font-medium">Contact Us</a>
                        @auth('admin')
                            <a href="{{ url('/admin/dashboard') }}" class="px-5 py-2 rounded-full bg-amber-500 hover:bg-amber-600 text-white font-medium transition duration-300 shadow-lg shadow-amber-500/20">Dashboard</a>
                        @else
                            <a href="{{ route('admin.login.page') }}" class="px-5 py-2 rounded-full bg-white/10 hover:bg-white/20 border border-white/10 text-white font-medium transition duration-300 backdrop-blur-md">Log in</a>
                        @endauth
                    </div>
                </div>
            </div>
        </nav>

        <!-- Hero Section -->
        <section class="relative min-h-screen flex items-center pt-20 overflow-hidden">
            <!-- 3D Spline Background/Foreground -->
            <!-- Using a gorgeous glass template spline -->
            <div class="absolute inset-0 w-full h-full z-0 pointer-events-auto">
                <!-- Using a suitable abstract background Spline shape -->
                <spline-viewer url="https://prod.spline.design/6Wq1Q7YGyM-iab9i/scene.splinecode" style="width: 100%; height: 100%;"></spline-viewer>
            </div>
            
            <!-- Left Gradient Fade to make text readable over 3D -->
            <div class="absolute inset-0 bg-gradient-to-r from-black via-black/70 to-transparent z-0 pointer-events-none"></div>

            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full content-overlay">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
                    <div class="flex flex-col justify-center max-w-xl">
                        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-amber-500/10 border border-amber-500/20 text-amber-400 text-sm font-medium mb-6 w-fit">
                            <span class="w-2 h-2 rounded-full bg-amber-400 animate-pulse"></span>
                            Freshly Baked Daily
                        </div>
                        <h1 class="text-5xl lg:text-7xl font-extrabold text-white leading-tight mb-6 tracking-tight text-glow">
                            Artisan <span class="text-transparent bg-clip-text bg-gradient-to-r from-amber-400 to-amber-600">Baking,</span><br>Modern Elegance.
                        </h1>
                        <p class="text-lg text-slate-400 mb-8 leading-relaxed font-light">
                            Discover the perfect fusion of traditional, handcrafted recipes and a contemporary aesthetic. Every bite is an experience, designed to captivate your senses.
                        </p>
                        <div class="flex flex-wrap gap-4">
                            <a href="#offer" class="px-8 py-4 rounded-full bg-amber-500 hover:bg-amber-600 text-white font-semibold transition-all duration-300 shadow-[0_0_20px_rgba(245,158,11,0.3)] hover:shadow-[0_0_30px_rgba(245,158,11,0.5)] hover:-translate-y-1 block text-center">
                                Explore Menu
                            </a>
                            <a href="#about" class="px-8 py-4 rounded-full bg-white/5 hover:bg-white/10 text-white border border-white/10 font-semibold transition-all duration-300 hover:-translate-y-1 block text-center backdrop-blur-md">
                                Discover Our Story
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Scroll Indicator -->
            <div class="absolute bottom-10 left-1/2 -translate-x-1/2 animate-bounce flex flex-col items-center content-overlay opacity-50 block md:block hidden">
                <span class="text-xs uppercase tracking-widest text-slate-400 mb-2">Scroll</span>
                <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path></svg>
            </div>
        </section>

        <!-- About Us Section -->
        <section id="about" class="py-24 relative z-10 bg-black">
            <div class="absolute top-0 right-0 w-96 h-96 bg-amber-500/10 rounded-full blur-[120px] pointer-events-none"></div>
            
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex flex-col lg:flex-row items-center gap-16">
                    <div class="lg:w-1/2 relative">
                        <div class="absolute inset-0 bg-gradient-to-tr from-amber-500/20 to-transparent rounded-3xl transform rotate-3 scale-105 opacity-50"></div>
                        <img src="https://images.unsplash.com/photo-1509440159596-0249088772ff?ixlib=rb-4.0.3&auto=format&fit=crop&w=1000&q=80" alt="Beautiful fresh bread" class="rounded-3xl shadow-2xl relative z-10 w-full h-[500px] object-cover animate-float border border-white/10">
                        
                        <!-- Floating Badge -->
                        <div class="absolute bottom-8 -right-8 z-20 landing-glass p-6 rounded-2xl shadow-xl flex items-center gap-4">
                            <div class="text-amber-500">
                                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </div>
                            <div>
                                <h4 class="text-white font-bold text-xl">Since 1999</h4>
                                <p class="text-slate-400 text-sm">Crafting excellence</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="lg:w-1/2">
                        <h2 class="text-amber-500 font-semibold tracking-wider uppercase text-sm mb-3 relative inline-block">
                            About Us
                            <span class="absolute -bottom-2 left-0 w-1/2 h-0.5 bg-amber-500"></span>
                        </h2>
                        <h3 class="text-4xl lg:text-5xl font-bold text-white mb-6 mt-4">Where Tradition Meets True Innovation</h3>
                        <p class="text-slate-400 mb-6 text-lg leading-relaxed font-light">
                            We don't just bake; we create edible works of art. Rooted in traditional techniques handed down through generations, our master bakers infuse modern flavors and breathtaking designs into everything we make.
                        </p>
                        <p class="text-slate-400 mb-8 text-lg leading-relaxed font-light">
                            From the crackle of hand-shaped artisan loaves to the delicate crumb of our signature pastries, we guarantee an unparalleled culinary experience that tantalizes your taste buds and delights your eyes.
                        </p>
                        
                        <div class="grid grid-cols-2 gap-6">
                            <div class="border-l-2 border-amber-500 pl-4">
                                <h4 class="text-2xl font-bold text-white">100%</h4>
                                <p class="text-slate-400">Organic Ingredients</p>
                            </div>
                            <div class="border-l-2 border-amber-500 pl-4">
                                <h4 class="text-2xl font-bold text-white">25+</h4>
                                <p class="text-slate-400">Master Bakers</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- What We Offer Section -->
        <section id="offer" class="py-24 relative z-10 overflow-hidden">
            <!-- Background Elements -->
            <div class="absolute inset-0 bg-slate-900/50"></div>
            <div class="absolute -left-48 bottom-0 w-96 h-96 bg-amber-500/10 rounded-full blur-[120px] pointer-events-none"></div>

            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
                <div class="text-center max-w-3xl mx-auto mb-16">
                    <h2 class="text-amber-500 font-semibold tracking-wider uppercase text-sm mb-3">What We Offer</h2>
                    <h3 class="text-4xl lg:text-5xl font-bold text-white mb-6">Our Masterpieces</h3>
                    <p class="text-slate-400 text-lg">Handcrafted daily using only the finest, carefully sourced ingredients.</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    <!-- Card 1 -->
                    <div class="glass-card group rounded-2xl overflow-hidden hover:-translate-y-2 transition-all duration-300">
                        <div class="h-64 overflow-hidden relative">
                            <div class="absolute inset-0 bg-black/20 z-10 group-hover:bg-transparent transition-all duration-300"></div>
                            <img src="https://images.unsplash.com/photo-1586444248902-2f64eddc13df?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" alt="Artisan Bread" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                        </div>
                        <div class="p-8">
                            <h4 class="text-2xl font-bold text-white mb-3 group-hover:text-amber-400 transition-colors">Artisan Bread</h4>
                            <p class="text-slate-400 mb-6 line-clamp-3">Naturally leavened sourdough and rustic loaves, baked on stone hearths for a perfect crust and airy crumb.</p>
                            <a href="#" class="text-amber-400 font-medium inline-flex items-center hover:text-amber-300 transition-colors">
                                Discover More <svg class="w-4 h-4 ml-1 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
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
                            <h4 class="text-2xl font-bold text-white mb-3 group-hover:text-amber-400 transition-colors">Signature Cakes</h4>
                            <p class="text-slate-400 mb-6 line-clamp-3">Elegant, custom-designed cakes featuring breathtaking modern aesthetics and luxurious, mouth-watering flavors.</p>
                            <a href="#" class="text-amber-400 font-medium inline-flex items-center hover:text-amber-300 transition-colors">
                                Discover More <svg class="w-4 h-4 ml-1 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
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
                            <h4 class="text-2xl font-bold text-white mb-3 group-hover:text-amber-400 transition-colors">French Pastries</h4>
                            <p class="text-slate-400 mb-6 line-clamp-3">Flaky, buttery croissants, delicate macarons, and rich tartes crafted with authentic European techniques.</p>
                            <a href="#" class="text-amber-400 font-medium inline-flex items-center hover:text-amber-300 transition-colors">
                                Discover More <svg class="w-4 h-4 ml-1 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Contact Us Section -->
        <section id="contact" class="py-24 relative bg-[#0a0a0a] border-t border-white/5">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-16">
                    <div>
                        <h2 class="text-amber-500 font-semibold tracking-wider uppercase text-sm mb-3">Contact Us</h2>
                        <h3 class="text-4xl lg:text-5xl font-bold text-white mb-8">Let's Bring Your Vision to Life.</h3>
                        <p class="text-slate-400 mb-10 text-lg leading-relaxed">
                            Whether you need a custom cake for a monumental event or just want to reserve your favorite morning pastry, our team is here for you. We'd love to hear from you.
                        </p>
                        
                        <div class="space-y-8">
                            <div class="flex items-start gap-4">
                                <div class="w-12 h-12 rounded-full bg-slate-800/80 border border-slate-700 flex items-center justify-center text-amber-500 flex-shrink-0 shadow-lg">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                </div>
                                <div>
                                    <h4 class="text-lg font-bold text-white mb-1">Our Location</h4>
                                    <p class="text-slate-400 font-light">{{ \App\Models\Setting::get('address', '123 Baker Street, Artisan District, NY 10001') }}</p>
                                </div>
                            </div>
                            
                            <div class="flex items-start gap-4">
                                <div class="w-12 h-12 rounded-full bg-slate-800/80 border border-slate-700 flex items-center justify-center text-amber-500 flex-shrink-0 shadow-lg">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                                </div>
                                <div>
                                    <h4 class="text-lg font-bold text-white mb-1">Email Us</h4>
                                    <p class="text-slate-400 font-light">{{ \App\Models\Setting::get('email', 'contact@bakery.com') }}</p>
                                </div>
                            </div>
                            
                            <div class="flex items-start gap-4">
                                <div class="w-12 h-12 rounded-full bg-slate-800/80 border border-slate-700 flex items-center justify-center text-amber-500 flex-shrink-0 shadow-lg">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                                </div>
                                <div>
                                    <h4 class="text-lg font-bold text-white mb-1">Call Us</h4>
                                    <p class="text-slate-400 font-light">{{ \App\Models\Setting::get('phone', '+1 (555) 123-4567') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Decorative Element / Subtle Form -->
                    <div class="relative">
                        <div class="absolute inset-0 bg-gradient-to-tr from-amber-500/10 to-transparent rounded-3xl transform -rotate-1 opacity-50 z-0"></div>
                        <div class="landing-glass rounded-3xl p-8 relative z-10 h-full flex flex-col justify-center">
                            <h4 class="text-2xl font-bold text-white mb-6">Send a Message</h4>
                            <form class="space-y-4">
                                <div>
                                    <input type="text" placeholder="Your Name" class="w-full glass-input px-4 py-3 rounded-xl bg-slate-900/50 border border-slate-700 text-white placeholder-slate-500 focus:outline-none focus:border-amber-500 transition-colors">
                                </div>
                                <div>
                                    <input type="email" placeholder="Your Email" class="w-full glass-input px-4 py-3 rounded-xl bg-slate-900/50 border border-slate-700 text-white placeholder-slate-500 focus:outline-none focus:border-amber-500 transition-colors">
                                </div>
                                <div>
                                    <textarea placeholder="How can we help you?" rows="4" class="w-full glass-input px-4 py-3 rounded-xl bg-slate-900/50 border border-slate-700 text-white placeholder-slate-500 focus:outline-none focus:border-amber-500 transition-colors resize-none"></textarea>
                                </div>
                                <button type="button" class="w-full py-4 rounded-xl bg-amber-500 hover:bg-amber-600 text-white font-semibold transition-all duration-300 shadow-[0_0_15px_rgba(245,158,11,0.2)] hover:shadow-[0_0_25px_rgba(245,158,11,0.4)]">
                                    Send Message
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Minimal Footer -->
        <footer class="bg-black py-8 border-t border-white/5">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col md:flex-row justify-between items-center gap-4">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-full bg-gradient-to-br from-amber-400 to-amber-600 flex items-center justify-center text-white font-bold text-sm shadow-lg shadow-amber-500/30">
                        B
                    </div>
                    <span class="text-lg font-bold text-white tracking-tight">{{ config('app.name', 'Bakery') }}</span>
                </div>
                
                <p class="text-slate-500 text-sm">
                    &copy; {{ date('Y') }} {{ config('app.name', 'Bakery') }}. All rights reserved.
                </p>
                
                <div class="flex gap-4 text-slate-500">
                    <a href="#" class="hover:text-amber-500 transition-colors"><svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path fill-rule="evenodd" d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z" clip-rule="evenodd" /></svg></a>
                    <a href="#" class="hover:text-amber-500 transition-colors"><svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path fill-rule="evenodd" d="M12.315 2c2.43 0 2.784.013 3.808.06 1.064.049 1.791.218 2.427.465a4.902 4.902 0 011.772 1.153 4.902 4.902 0 011.153 1.772c.247.636.416 1.363.465 2.427.048 1.067.06 1.407.06 4.123v.08c0 2.643-.012 2.987-.06 4.043-.049 1.064-.218 1.791-.465 2.427a4.902 4.902 0 01-1.153 1.772 4.902 4.902 0 01-1.772 1.153c-.636.247-1.363.416-2.427.465-1.067.048-1.407.06-4.123.06h-.08c-2.643 0-2.987-.012-4.043-.06-1.064-.049-1.791-.218-2.427-.465a4.902 4.902 0 01-1.772-1.153 4.902 4.902 0 01-1.153-1.772c-.247-.636-.416-1.363-.465-2.427-.047-1.024-.06-1.379-.06-3.808v-.63c0-2.43.013-2.784.06-3.808.049-1.064.218-1.791.465-2.427a4.902 4.902 0 011.153-1.772A4.902 4.902 0 015.45 2.525c.636-.247 1.363-.416 2.427-.465C8.901 2.013 9.256 2 11.685 2h.63zm-.081 1.802h-.468c-2.456 0-2.784.011-3.807.058-.975.045-1.504.207-1.857.344-.467.182-.8.398-1.15.748-.35.35-.566.683-.748 1.15-.137.353-.3.882-.344 1.857-.047 1.023-.058 1.351-.058 3.807v.468c0 2.456.011 2.784.058 3.807.045.975.207 1.504.344 1.857.182.466.399.8.748 1.15.35.35.683.566 1.15.748.353.137.882.3 1.857.344 1.054.048 1.37.058 4.041.058h.08c2.597 0 2.917-.01 3.96-.058.976-.045 1.505-.207 1.858-.344.466-.182.8-.398 1.15-.748.35-.35.566-.683.748-1.15.137-.353.3-.882.344-1.857.048-1.055.058-1.37.058-4.041v-.08c0-2.597-.01-2.917-.058-3.96-.045-.976-.207-1.505-.344-1.858a3.097 3.097 0 00-.748-1.15 3.098 3.098 0 00-1.15-.748c-.353-.137-.882-.3-1.857-.344-1.023-.047-1.351-.058-3.807-.058zM12 6.865a5.135 5.135 0 110 10.27 5.135 5.135 0 010-10.27zm0 1.802a3.333 3.333 0 100 6.666 3.333 3.333 0 000-6.666zm5.338-3.205a1.2 1.2 0 110 2.4 1.2 1.2 0 010-2.4z" clip-rule="evenodd" /></svg></a>
                </div>
            </div>
        </footer>

    </body>
</html>
