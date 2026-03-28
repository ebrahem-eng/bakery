        <!-- Mobile sidebar backdrop -->
        <div x-show="sidebarOpen" x-transition.opacity class="fixed inset-0 z-20 bg-black/50 lg:hidden" @click="sidebarOpen = false" style="display: none;"></div>

        <aside :class="sidebarOpen ? 'translate-x-0' : '{{ app()->getLocale() == 'ar' ? 'translate-x-full' : '-translate-x-full' }}'" class="fixed inset-y-0 {{ app()->getLocale() == 'ar' ? 'right-0 border-l' : 'left-0 border-r' }} z-40 w-64 glass-sidebar border-slate-200 dark:border-white/5 shadow-2xl lg:shadow-none transition-transform duration-300 ease-in-out lg:translate-x-0 lg:static lg:inset-0 h-full flex flex-col pt-6 pb-4">
            
            <!-- Logo area -->
            <div class="flex items-center justify-center px-6 mb-8 logo-container gap-3">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-amber-600 to-orange-400 p-[1px] shadow-[0_0_15px_rgba(245,158,11,0.3)]">
                    <div class="w-full h-full bg-[#121419] rounded-xl flex items-center justify-center">
                        <img src="{{ asset('logo.svg') }}" alt="Bakery Logo" class="w-7 h-7">
                    </div>
                </div>
                <div>
                    <h2 class="text-xl font-bold text-slate-900 dark:text-transparent dark:bg-clip-text dark:bg-gradient-to-r dark:from-amber-400 dark:to-orange-500">{{ __('Bakery') }}</h2>
                    <p class="text-[10px] uppercase tracking-widest text-amber-600 dark:text-amber-500 opacity-80">{{ __('System Portal') }}</p>
                </div>
            </div>

            <!-- Navigation Links -->
            <nav class="flex-1 px-4 space-y-2 overflow-y-auto custom-scrollbar">
                <!-- Dashboard -->
                @can('view dashboard')
                <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'sidebar-item-active text-amber-600 dark:text-[#fde047]' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-200/50 dark:hover:bg-white/5' }} flex items-center px-4 py-3 text-sm font-medium rounded-xl group transition-colors">
                    <svg class="w-5 h-5 {{ app()->getLocale() == 'ar' ? 'ml-3' : 'mr-3' }} {{ request()->routeIs('admin.dashboard') ? 'text-amber-500' : 'text-slate-400 dark:text-slate-500 group-hover:text-amber-500 dark:group-hover:text-[#fde047] transition-colors' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                    </svg>
                    {{ __('Dashboard') }}
                </a>
                @endcan

                <!-- Accounts -->
                @can('view accounts')
                <a href="{{ route('admin.accounts.index') }}" class="{{ request()->routeIs('admin.accounts.*') ? 'sidebar-item-active text-amber-600 dark:text-[#fde047]' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-200/50 dark:hover:bg-white/5' }} flex items-center px-4 py-3 text-sm font-medium rounded-xl group transition-colors">
                    <svg class="w-5 h-5 {{ app()->getLocale() == 'ar' ? 'ml-3' : 'mr-3' }} {{ request()->routeIs('admin.accounts.*') ? 'text-amber-500' : 'text-slate-400 dark:text-slate-500 group-hover:text-amber-500 dark:group-hover:text-[#fde047]' }} transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                    </svg>
                    {{ __('Accounts') }}
                </a>
                @endcan

                <!-- Work Days -->
                @can('view work days')
                <a href="{{ route('admin.work_days.index') }}" class="{{ request()->routeIs('admin.work_days.*') ? 'sidebar-item-active text-amber-600 dark:text-[#fde047]' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-200/50 dark:hover:bg-white/5' }} flex items-center px-4 py-3 text-sm font-medium rounded-xl group transition-colors">
                    <svg class="w-5 h-5 {{ app()->getLocale() == 'ar' ? 'ml-3' : 'mr-3' }} {{ request()->routeIs('admin.work_days.*') ? 'text-amber-500' : 'text-slate-400 dark:text-slate-500 group-hover:text-amber-500 dark:group-hover:text-[#fde047]' }} transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    {{ __('Work Days') }}
                </a>
                @endcan

                <!-- Supplies & Suppliers -->
                @if(auth('admin')->user() && (auth('admin')->user()->can('view supplies') || auth('admin')->user()->can('view suppliers')))
                <div x-data="{ open: {{ request()->routeIs('admin.suppliers.*') || request()->routeIs('admin.supplies.*') ? 'true' : 'false' }} }">
                    <button @click="open = !open" class="{{ request()->routeIs('admin.suppliers.*') || request()->routeIs('admin.supplies.*') ? 'sidebar-item-active text-amber-500' : 'text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-200/50 dark:hover:bg-white/5' }} w-full flex justify-between items-center px-4 py-3 text-sm font-medium rounded-xl transition-colors">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 {{ app()->getLocale() == 'ar' ? 'ml-3' : 'mr-3' }} {{ request()->routeIs('admin.suppliers.*') || request()->routeIs('admin.supplies.*') ? 'text-amber-400' : 'text-slate-500' }} transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                            {{ __('Purchases & Suppliers') }}
                        </div>
                        <svg :class="{'rotate-180': open}" class="w-4 h-4 transition-transform text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div x-show="open" class="my-1 space-y-1">
                        @can('view supplies')
                        <a href="{{ route('admin.supplies.index') }}" class="{{ request()->routeIs('admin.supplies.*') ? 'text-slate-900 dark:text-white font-bold' : 'text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white' }} block {{ app()->getLocale() == 'ar' ? 'pr-12' : 'pl-12' }} py-2 text-xs transition-colors">{{ __('Supply Records') }}</a>
                        @endcan
                        @can('view suppliers')
                        <a href="{{ route('admin.suppliers.index') }}" class="{{ request()->routeIs('admin.suppliers.*') ? 'text-slate-900 dark:text-white font-bold' : 'text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white' }} block {{ app()->getLocale() == 'ar' ? 'pr-12' : 'pl-12' }} py-2 text-xs transition-colors">{{ __('Manage Vendors') }}</a>
                        @endcan
                    </div>
                </div>
                @endif

                <!-- Warehouse -->
                @can('view warehouse')
                <a href="{{ route('admin.warehouse.index') }}" class="{{ request()->routeIs('admin.warehouse.*') ? 'sidebar-item-active text-amber-600 dark:text-[#fde047]' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-200/50 dark:hover:bg-white/5' }} flex items-center px-4 py-3 text-sm font-medium rounded-xl group transition-colors">
                    <svg class="w-5 h-5 {{ app()->getLocale() == 'ar' ? 'ml-3' : 'mr-3' }} {{ request()->routeIs('admin.warehouse.*') ? 'text-amber-500' : 'text-slate-400 dark:text-slate-500 group-hover:text-amber-500 dark:group-hover:text-[#fde047]' }} transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                    </svg>
                    {{ __('Warehouse') }}
                </a>
                @endcan

                <!-- Expenses -->
                @can('view expenses')
                <a href="{{ route('admin.expenses.index') }}" class="{{ request()->routeIs('admin.expenses.*') ? 'sidebar-item-active text-amber-600 dark:text-[#fde047]' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-200/50 dark:hover:bg-white/5' }} flex items-center px-4 py-3 text-sm font-medium rounded-xl group transition-colors">
                    <svg class="w-5 h-5 {{ app()->getLocale() == 'ar' ? 'ml-3' : 'mr-3' }} {{ request()->routeIs('admin.expenses.*') ? 'text-amber-500' : 'text-slate-400 dark:text-slate-500 group-hover:text-amber-500 dark:group-hover:text-[#fde047]' }} transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    {{ __('Expenses & Drawings') }}
                </a>
                @endcan

                <!-- Distributors -->
                @if(auth('admin')->user() && (auth('admin')->user()->can('view distributions') || auth('admin')->user()->can('view distributors')))
                <div x-data="{ open: {{ request()->routeIs('admin.distributors.*') || request()->routeIs('admin.distributions.*') ? 'true' : 'false' }} }">
                    <button @click="open = !open" class="{{ request()->routeIs('admin.distributors.*') || request()->routeIs('admin.distributions.*') ? 'sidebar-item-active text-amber-500' : 'text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-200/50 dark:hover:bg-white/5' }} w-full flex justify-between items-center px-4 py-3 text-sm font-medium rounded-xl transition-colors">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 {{ app()->getLocale() == 'ar' ? 'ml-3' : 'mr-3' }} {{ request()->routeIs('admin.distributors.*') || request()->routeIs('admin.distributions.*') ? 'text-amber-400' : 'text-slate-500' }} transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            {{ __('Distributors & Sales') }}
                        </div>
                        <svg :class="{'rotate-180': open}" class="w-4 h-4 transition-transform text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div x-show="open" class="my-1 space-y-1">
                        @can('view distributions')
                        <a href="{{ route('admin.distributions.index') }}" class="{{ request()->routeIs('admin.distributions.*') ? 'text-slate-900 dark:text-white font-bold' : 'text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white' }} block {{ app()->getLocale() == 'ar' ? 'pr-12' : 'pl-12' }} py-2 text-xs transition-colors">{{ __('Active Distributions') }}</a>
                        @endcan
                        @can('view distributors')
                        <a href="{{ route('admin.distributors.index') }}" class="{{ request()->routeIs('admin.distributors.*') ? 'text-slate-900 dark:text-white font-bold' : 'text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white' }} block {{ app()->getLocale() == 'ar' ? 'pr-12' : 'pl-12' }} py-2 text-xs transition-colors">{{ __('Manage Distributors') }}</a>
                        @endcan
                    </div>
                </div>
                @endif

                <!-- Workers (HR) -->
                @if(auth('admin')->user() && (auth('admin')->user()->can('view workers') || auth('admin')->user()->can('view attendance')))
                <div x-data="{ open: {{ request()->routeIs('admin.workers.*') || request()->routeIs('admin.attendance.*') ? 'true' : 'false' }} }">
                    <button @click="open = !open" class="{{ request()->routeIs('admin.workers.*') || request()->routeIs('admin.attendance.*') ? 'sidebar-item-active text-amber-500' : 'text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-200/50 dark:hover:bg-white/5' }} w-full flex justify-between items-center px-4 py-3 text-sm font-medium rounded-xl transition-colors">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 {{ app()->getLocale() == 'ar' ? 'ml-3' : 'mr-3' }} {{ request()->routeIs('admin.workers.*') || request()->routeIs('admin.attendance.*') ? 'text-amber-400' : 'text-slate-500' }} transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                            {{ __('Workers HR') }}
                        </div>
                        <svg :class="{'rotate-180': open}" class="w-4 h-4 transition-transform text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div x-show="open" class="my-1 space-y-1">
                        @can('view workers')
                        <a href="{{ route('admin.workers.index') }}" class="{{ request()->routeIs('admin.workers.*') ? 'text-slate-900 dark:text-white font-bold' : 'text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white' }} block {{ app()->getLocale() == 'ar' ? 'pr-12' : 'pl-12' }} py-2 text-xs transition-colors">{{ __('Personnel Roster') }}</a>
                        @endcan
                        @can('view attendance')
                        <a href="{{ route('admin.attendance.presence') }}" class="{{ request()->routeIs('admin.attendance.presence') ? 'text-slate-900 dark:text-white font-bold' : 'text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white' }} block {{ app()->getLocale() == 'ar' ? 'pr-12' : 'pl-12' }} py-2 text-xs transition-colors">{{ __('Daily Presence') }}</a>
                        <a href="{{ route('admin.attendance.index') }}" class="{{ request()->routeIs('admin.attendance.index') ? 'text-slate-900 dark:text-white font-bold' : 'text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white' }} block {{ app()->getLocale() == 'ar' ? 'pr-12' : 'pl-12' }} py-2 text-xs transition-colors">{{ __('Attendance (Shifts)') }}</a>
                        @endcan
                    </div>
                </div>
                @endif

                <div class="border-t border-white/5 my-2"></div>

                <!-- Admin Accounts -->
                @can('view admins')
                <a href="{{ route('admin.manage_admins.index') }}" class="{{ request()->routeIs('admin.manage_admins.*') ? 'sidebar-item-active text-amber-600 dark:text-[#fde047]' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-200/50 dark:hover:bg-white/5' }} flex items-center px-4 py-3 text-sm font-medium rounded-xl group transition-colors">
                    <svg class="w-5 h-5 {{ app()->getLocale() == 'ar' ? 'ml-3' : 'mr-3' }} {{ request()->routeIs('admin.manage_admins.*') ? 'text-amber-500' : 'text-slate-400 dark:text-slate-500 group-hover:text-amber-500 dark:group-hover:text-[#fde047]' }} transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                    {{ __('Admin Accounts') }}
                </a>
                @endcan

                <!-- Activity Log -->
                @if(auth('admin')->user() && auth('admin')->user()->can('view activity log'))
                <a href="{{ route('admin.activity-log.index') }}" class="{{ request()->routeIs('admin.activity-log.*') ? 'sidebar-item-active text-amber-600 dark:text-[#fde047]' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-200/50 dark:hover:bg-white/5' }} flex items-center px-4 py-3 text-sm font-medium rounded-xl group transition-colors">
                    <svg class="w-5 h-5 {{ app()->getLocale() == 'ar' ? 'ml-3' : 'mr-3' }} {{ request()->routeIs('admin.activity-log.*') ? 'text-amber-500' : 'text-slate-400 dark:text-slate-500 group-hover:text-amber-500 dark:group-hover:text-[#fde047]' }} transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                    {{ __('Activity Log') }}
                </a>
                @endif

                <!-- Roles & Permissions -->
                @can('view roles')
                <a href="{{ route('admin.roles.index') }}" class="{{ request()->routeIs('admin.roles.*') ? 'sidebar-item-active text-amber-600 dark:text-[#fde047]' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-200/50 dark:hover:bg-white/5' }} flex items-center px-4 py-3 text-sm font-medium rounded-xl group transition-colors">
                    <svg class="w-5 h-5 {{ app()->getLocale() == 'ar' ? 'ml-3' : 'mr-3' }} {{ request()->routeIs('admin.roles.*') ? 'text-amber-500' : 'text-slate-400 dark:text-slate-500 group-hover:text-amber-500 dark:group-hover:text-[#fde047]' }} transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                    {{ __('Roles & Permissions') }}
                </a>
                @endcan

                <!-- Settings -->
                @can('manage settings')
                <a href="{{ route('admin.settings.index') }}" class="{{ request()->routeIs('admin.settings.*') ? 'sidebar-item-active text-amber-600 dark:text-[#fde047]' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-200/50 dark:hover:bg-white/5' }} flex items-center px-4 py-3 text-sm font-medium rounded-xl group transition-colors">
                    <svg class="w-5 h-5 {{ app()->getLocale() == 'ar' ? 'ml-3' : 'mr-3' }} {{ request()->routeIs('admin.settings.*') ? 'text-amber-500' : 'text-slate-400 dark:text-slate-500 group-hover:text-amber-500 dark:group-hover:text-[#fde047]' }} transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    {{ __('Settings') }}
                </a>
                @endcan
            </nav>

            <div class="px-4 mt-auto">
                <div class="p-3 glass-card rounded-xl flex items-center justify-between border border-slate-200 dark:border-white/5">
                    <div class="flex items-center">
                        <div class="relative w-8 h-8 rounded-full bg-gradient-to-tr from-[#f59e0b] to-[#fbbf24] flex items-center justify-center text-[#451a03] font-bold text-sm overflow-hidden border border-[#f59e0b]/30">
                            {{ substr(auth()->guard('admin')->user()->name ?? 'A', 0, 1) }}
                        </div>
                        <div class="{{ app()->getLocale() == 'ar' ? 'mr-3' : 'ml-3' }}">
                            <p class="text-xs font-semibold text-slate-900 dark:text-white truncate max-w-[120px]" title="{{ auth()->guard('admin')->user()->name ?? 'Admin' }}">{{ auth()->guard('admin')->user()->name ?? 'Admin' }}</p>
                            <p class="text-[10px] text-slate-500 dark:text-slate-400 truncate max-w-[120px]">{{ __('System Manager') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </aside>
