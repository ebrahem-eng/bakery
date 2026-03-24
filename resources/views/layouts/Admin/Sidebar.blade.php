        <!-- Mobile sidebar backdrop -->
        <div x-show="sidebarOpen" x-transition.opacity class="fixed inset-0 z-20 bg-black/50 lg:hidden" @click="sidebarOpen = false" style="display: none;"></div>

        <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'" class="fixed inset-y-0 {{ app()->getLocale() == 'ar' ? 'right-0' : 'left-0' }} z-30 w-64 glass-sidebar transition-transform duration-300 ease-in-out lg:translate-x-0 lg:static lg:inset-0 h-full flex flex-col pt-6 pb-4">
            
            <!-- Logo area -->
            <div class="flex items-center justify-center px-6 mb-8 logo-container">
                <div>
                    <h2 class="text-xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-[#eab308] to-[#f59e0b]">{{ __('Bakery') }}</h2>
                    <p class="text-[10px] uppercase tracking-widest text-[#fbbf24] opacity-80">{{ __('Admin Portal') }}</p>
                </div>
            </div>

            <!-- Navigation Links -->
            <nav class="flex-1 px-4 space-y-2 overflow-y-auto custom-scrollbar">
                <!-- Dashboard -->
                <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'sidebar-item-active text-[#fde047]' : 'text-slate-400 hover:text-white hover:bg-white/5' }} flex items-center px-4 py-3 text-sm font-medium rounded-xl group transition-colors">
                    <svg class="w-5 h-5 {{ app()->getLocale() == 'ar' ? 'ml-3' : 'mr-3' }} {{ request()->routeIs('admin.dashboard') ? 'text-[#f59e0b]' : 'text-slate-500 group-hover:text-[#fde047] transition-colors' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                    </svg>
                    {{ __('Dashboard') }}
                </a>

                <!-- Work Days -->
                <a href="#" class="text-slate-400 hover:text-white hover:bg-white/5 flex items-center px-4 py-3 text-sm font-medium rounded-xl group transition-colors">
                    <svg class="w-5 h-5 {{ app()->getLocale() == 'ar' ? 'ml-3' : 'mr-3' }} text-slate-500 group-hover:text-[#fde047] transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    {{ __('Work Days') }}
                </a>

                <!-- Supplies & Suppliers -->
                <a href="#" class="text-slate-400 hover:text-white hover:bg-white/5 flex items-center px-4 py-3 text-sm font-medium rounded-xl group transition-colors">
                    <svg class="w-5 h-5 {{ app()->getLocale() == 'ar' ? 'ml-3' : 'mr-3' }} text-slate-500 group-hover:text-[#fde047] transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    {{ __('Purchases & Suppliers') }}
                </a>

                <!-- Workers -->
                <a href="#" class="text-slate-400 hover:text-white hover:bg-white/5 flex items-center px-4 py-3 text-sm font-medium rounded-xl group transition-colors">
                    <svg class="w-5 h-5 {{ app()->getLocale() == 'ar' ? 'ml-3' : 'mr-3' }} text-slate-500 group-hover:text-[#fde047] transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    {{ __('Workers & Wages') }}
                </a>

                <!-- Distributors -->
                <a href="#" class="text-slate-400 hover:text-white hover:bg-white/5 flex items-center px-4 py-3 text-sm font-medium rounded-xl group transition-colors">
                    <svg class="w-5 h-5 {{ app()->getLocale() == 'ar' ? 'ml-3' : 'mr-3' }} text-slate-500 group-hover:text-[#fde047] transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    {{ __('Distributors & Sales') }}
                </a>

                <!-- Expenses -->
                <a href="#" class="text-slate-400 hover:text-white hover:bg-white/5 flex items-center px-4 py-3 text-sm font-medium rounded-xl group transition-colors">
                    <svg class="w-5 h-5 {{ app()->getLocale() == 'ar' ? 'ml-3' : 'mr-3' }} text-slate-500 group-hover:text-[#fde047] transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    {{ __('Expenses & Drawings') }}
                </a>

                <div class="border-t border-white/5 my-2"></div>

                <!-- Admin Accounts -->
                <a href="#" class="text-slate-400 hover:text-white hover:bg-white/5 flex items-center px-4 py-3 text-sm font-medium rounded-xl group transition-colors">
                    <svg class="w-5 h-5 {{ app()->getLocale() == 'ar' ? 'ml-3' : 'mr-3' }} text-slate-500 group-hover:text-[#fde047] transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                    {{ __('Admin Accounts') }}
                </a>

                <!-- Settings -->
                <a href="#" class="text-slate-400 hover:text-white hover:bg-white/5 flex items-center px-4 py-3 text-sm font-medium rounded-xl group transition-colors">
                    <svg class="w-5 h-5 {{ app()->getLocale() == 'ar' ? 'ml-3' : 'mr-3' }} text-slate-500 group-hover:text-[#fde047] transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    {{ __('Settings') }}
                </a>
            </nav>

            <div class="px-4 mt-auto">
                <div class="p-3 glass-card rounded-xl flex items-center justify-between border border-white/5">
                    <div class="flex items-center">
                        <div class="relative w-8 h-8 rounded-full bg-gradient-to-tr from-[#f59e0b] to-[#fbbf24] flex items-center justify-center text-[#451a03] font-bold text-sm overflow-hidden border border-[#f59e0b]/30">
                            {{ substr(auth()->guard('admin')->user()->name ?? 'A', 0, 1) }}
                        </div>
                        <div class="{{ app()->getLocale() == 'ar' ? 'mr-3' : 'ml-3' }}">
                            <p class="text-xs font-semibold text-white truncate max-w-[120px]" title="{{ auth()->guard('admin')->user()->name ?? 'Admin' }}">{{ auth()->guard('admin')->user()->name ?? 'Admin' }}</p>
                            <p class="text-[10px] text-slate-400 truncate max-w-[120px]">{{ __('System Manager') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </aside>
