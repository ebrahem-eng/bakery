        <!-- Mobile sidebar backdrop -->
        <div x-show="sidebarOpen" x-transition.opacity class="fixed inset-0 z-20 bg-black/50 lg:hidden" @click="sidebarOpen = false" style="display: none;"></div>

        <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'" class="fixed inset-y-0 left-0 z-30 w-64 glass-sidebar transition-transform duration-300 ease-in-out lg:translate-x-0 lg:static lg:inset-0 h-full flex flex-col pt-6 pb-4">
            
            <!-- Logo area -->
            <div class="flex items-center justify-center px-6 mb-8 logo-container">
                <!-- SVG logo scaled down -->
                <img src="{{ asset('build/assets/admin_page/logo.svg') }}" alt="Neural Admin Logo" class="w-[50px] h-[50px] drop-shadow-lg mr-3">
                <div>
                    <h2 class="text-xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-[#0ea5e9] to-[#818cf8]">Neural</h2>
                    <p class="text-[10px] uppercase tracking-widest text-[#38bdf8] opacity-80">Admin Portal</p>
                </div>
            </div>

            <!-- Navigation Links -->
            <nav class="flex-1 px-4 space-y-2 overflow-y-auto">
                <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'sidebar-item-active' : 'text-slate-400 hover:text-white hover:bg-white/5' }} flex items-center px-4 py-3 text-sm font-medium rounded-xl group transition-colors">
                    <svg class="w-5 h-5 mr-3 {{ request()->routeIs('admin.dashboard') ? 'text-[#38bdf8]' : 'text-slate-500 group-hover:text-[#818cf8] transition-colors' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                    </svg>
                    Dashboard Board
                </a>

                <!-- Analysis Dropdown using Alpine -->
                <!-- <div x-data="{ open: false }" class="space-y-1">
                    <button @click="open = !open" class="w-full flex items-center justify-between px-4 py-3 text-sm font-medium rounded-xl text-slate-400 hover:text-white hover:bg-white/5 transition-colors">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-3 text-slate-500 group-hover:text-white transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                            Analysis & Data
                        </div>
                        <svg :class="{'rotate-180': open}" class="w-4 h-4 transition-transform text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button> -->
                    <!-- Submenu -->
                    <!-- <div x-show="open" x-collapse x-transition class="pl-12 pr-4 space-y-1 pb-2">
                        <a href="#" class="block py-2 text-sm text-[#38bdf8] hover:text-[#0ea5e9] transition-colors relative before:absolute before:left-[-16px] before:top-[14px] before:w-[6px] before:h-[6px] before:rounded-full before:bg-[#38bdf8] before:shadow-[0_0_8px_#38bdf8]">Network Stats</a>
                        <a href="#" class="block py-2 text-sm text-slate-400 hover:text-[#38bdf8] transition-colors relative before:absolute before:left-[-16px] before:top-[14px] before:w-[6px] before:h-[6px] before:rounded-full before:bg-slate-600 hover:before:bg-[#38bdf8]">Node Activity</a>
                        <a href="#" class="block py-2 text-sm text-slate-400 hover:text-[#38bdf8] transition-colors relative before:absolute before:left-[-16px] before:top-[14px] before:w-[6px] before:h-[6px] before:rounded-full before:bg-slate-600 hover:before:bg-[#38bdf8]">System Reports</a>
                    </div> -->
                <!-- </div> -->

                <!-- Roles & Permissions -->
                <a href="{{ route('admin.roles.index') }}" class="{{ request()->routeIs('admin.roles.*') ? 'sidebar-item-active' : 'text-slate-400 hover:text-white hover:bg-white/5' }} flex items-center px-4 py-3 text-sm font-medium rounded-xl group transition-colors">
                    <svg class="w-5 h-5 mr-3 {{ request()->routeIs('admin.roles.*') ? 'text-[#38bdf8]' : 'text-slate-500 group-hover:text-[#818cf8] transition-colors' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                    Manage Roles
                </a>

                <!-- Admin Management -->
                <a href="{{ route('admin.manage_admins.index') }}" class="{{ request()->routeIs('admin.manage_admins.*') ? 'sidebar-item-active' : 'text-slate-400 hover:text-white hover:bg-white/5' }} flex items-center px-4 py-3 text-sm font-medium rounded-xl group transition-colors">
                    <svg class="w-5 h-5 mr-3 {{ request()->routeIs('admin.manage_admins.*') ? 'text-[#38bdf8]' : 'text-slate-500 group-hover:text-[#818cf8] transition-colors' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                    Admin Accounts
                </a>

                <!-- Medical Centers -->
                <a href="{{ route('admin.medical-centers.index') }}" class="flex items-center px-4 py-3 rounded-xl transition-all duration-300 {{ request()->routeIs('admin.medical-centers.*') ? 'bg-[#0ea5e9]/10 text-white border-l-4 border-[#0ea5e9]' : 'text-slate-400 hover:bg-white/5 hover:text-white' }} group shrink-0">
                    <svg class="w-5 h-5 mr-3 {{ request()->routeIs('admin.medical-centers.*') ? 'text-[#0ea5e9]' : 'text-slate-500 group-hover:text-slate-300' }} transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                    Medical Centers
                </a>
                
                <a href="{{ route('admin.plans.index') }}" class="flex items-center px-4 py-3 rounded-xl transition-all duration-300 {{ request()->routeIs('admin.plans.*') ? 'bg-[#0ea5e9]/10 text-white border-l-4 border-[#0ea5e9]' : 'text-slate-400 hover:bg-white/5 hover:text-white' }} group shrink-0">
                    <svg class="w-5 h-5 mr-3 {{ request()->routeIs('admin.plans.*') ? 'text-[#0ea5e9]' : 'text-slate-500 group-hover:text-slate-300' }} transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                    </svg>
                    Subscription Plans
                </a>

                <!-- Global Contact Form Data -->
                <a href="{{ route('admin.contact-messages.index') }}" class="flex items-center px-4 py-3 rounded-xl transition-all duration-300 {{ request()->routeIs('admin.contact-messages.*') ? 'bg-purple-500/10 text-white border-l-4 border-purple-500' : 'text-slate-400 hover:bg-white/5 hover:text-white' }} group shrink-0">
                    <svg class="w-5 h-5 mr-3 {{ request()->routeIs('admin.contact-messages.*') ? 'text-purple-400' : 'text-slate-500 group-hover:text-slate-300' }} transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                    </svg>
                    Uplink Messages
                    @php
                        $unreadCount = \App\Models\ContactMessage::where('is_read', false)->count();
                    @endphp
                    @if($unreadCount > 0)
                        <span class="ml-auto bg-purple-500 text-white text-[10px] font-bold px-2 py-0.5 rounded-full animate-pulse group-hover:animate-none group-hover:bg-purple-400 transition-colors">
                            {{ $unreadCount }}
                        </span>
                    @endif
                </a>
                
                <!-- Settings -->
                <a href="{{ route('admin.settings.index') }}" class="flex items-center px-4 py-3 rounded-xl transition-all duration-300 {{ request()->routeIs('admin.settings.*') ? 'bg-[#0ea5e9]/10 text-white border-l-4 border-[#0ea5e9]' : 'text-slate-400 hover:bg-white/5 hover:text-white' }} group shrink-0">
                    <svg class="w-5 h-5 mr-3 {{ request()->routeIs('admin.settings.*') ? 'text-[#0ea5e9]' : 'text-slate-500 group-hover:text-slate-300' }} transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    System Settings
                </a>
            </nav>

            <div class="px-4 mt-auto">
                <div class="p-3 glass-card rounded-xl flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="relative w-8 h-8 rounded-full bg-gradient-to-tr from-[#0ea5e9] to-[#6366f1] flex items-center justify-center text-white font-bold text-sm shadow-[0_0_10px_rgba(56,189,248,0.4)] overflow-hidden">
                            @if(auth()->guard('admin')->user()->img)
                                <img src="{{ asset('storage/' . auth()->guard('admin')->user()->img) }}" alt="{{ auth()->guard('admin')->user()->name }}" class="w-full h-full object-cover">
                            @else
                                {{ substr(auth()->guard('admin')->user()->name, 0, 1) }}
                            @endif
                        </div>
                        <div class="ml-3">
                            <p class="text-xs font-semibold text-white truncate max-w-[120px]" title="{{ auth()->guard('admin')->user()->name }}">{{ auth()->guard('admin')->user()->name }}</p>
                            <p class="text-[10px] text-slate-400 truncate max-w-[120px]" title="{{ auth()->guard('admin')->user()->role ? auth()->guard('admin')->user()->role->name : 'Basic Access' }}">{{ auth()->guard('admin')->user()->role ? auth()->guard('admin')->user()->role->name : 'Basic Access' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </aside>
