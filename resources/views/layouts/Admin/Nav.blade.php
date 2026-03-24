            <!-- Navbar -->
            <header class="glass-navbar border-b border-white/5 z-10 w-full">
                <div class="flex items-center justify-between px-6 py-4">
                    <!-- Hamburger Toggle -->
                    <button @click="sidebarOpen = true" class="text-slate-400 hover:text-white lg:hidden">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>

                    <!-- Search Bar -->
                    <div class="hidden sm:flex flex-1 max-w-md {{ app()->getLocale() == 'ar' ? 'mr-4' : 'ml-4' }} text-sm font-medium">
                        <div class="relative w-full">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-4 w-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </div>
                            <input type="text" class="glass-input block w-full pl-9 pr-3 py-2 border-transparent rounded-xl leading-5 bg-black/30 text-slate-300 placeholder-slate-500 focus:outline-none focus:bg-black/50 focus:border-[#38bdf8] focus:ring-1 focus:ring-[#38bdf8] transition-all" placeholder="Search patterns, nodes, logs...">
                        </div>
                    </div>

                    <div class="flex items-center {{ app()->getLocale() == 'ar' ? 'mr-auto' : 'ml-auto' }} gap-3 sm:gap-5">
                        <!-- Theme Toggle -->
                        <button @click="isDark = !isDark" class="text-slate-400 hover:text-white p-1.5 rounded-full hover:bg-white/5 transition-colors focus:outline-none">
                            <svg x-show="!isDark" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                            </svg>
                            <svg x-show="isDark" style="display: none;" class="w-5 h-5 text-yellow-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                        </button>

                        <!-- Lang Toggle -->
                        <a href="{{ route('admin.setLang', app()->getLocale() == 'ar' ? 'en' : 'ar') }}" class="text-slate-400 hover:text-white font-bold px-2 py-1 rounded-full hover:bg-white/5 transition-colors text-xs border border-white/5">
                            {{ app()->getLocale() == 'ar' ? 'English' : 'العربية' }}
                        </a>

                        <!-- Notifications -->
                        <div class="relative" x-data="{ notificationsOpen: false }">
                            <button @click="notificationsOpen = !notificationsOpen" @click.away="notificationsOpen = false" class="text-slate-400 hover:text-white relative p-1.5 rounded-full hover:bg-white/5 transition-colors outline-none">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                </svg>
                                @if(auth()->guard('admin')->user()->unreadNotifications->count() > 0)
                                    <!-- Notification dot -->
                                    <span class="absolute top-1.5 right-1.5 w-2 h-2 rounded-full bg-[#f43f5e] shadow-[0_0_8px_#f43f5e]" id="admin-notif-dot"></span>
                                @endif
                            </button>
                            <!-- Notification Dropdown -->
                            <div x-show="notificationsOpen" x-transition class="absolute right-0 mt-2 w-80 glass-dropdown rounded-2xl py-2 z-50 origin-top-right shadow-[0_10px_40px_rgba(0,0,0,0.5)] border border-white/10" style="display: none;">
                                <div class="px-4 py-2 border-b border-white/5 flex justify-between items-center">
                                    <h3 class="text-sm font-semibold text-white">System Alerts</h3>
                                    @if(auth()->guard('admin')->user()->unreadNotifications->count() > 0)
                                        <span class="text-[10px] font-bold text-[#f43f5e] bg-[#f43f5e]/10 px-2 py-0.5 rounded-full" id="admin-notif-badge">{{ auth()->guard('admin')->user()->unreadNotifications->count() }} New</span>
                                    @endif
                                </div>
                                <div class="max-h-64 overflow-y-auto" id="admin-notif-list">
                                    @forelse(auth()->guard('admin')->user()->unreadNotifications as $notification)
                                        <a href="{{ $notification->data['url'] ?? '#' }}" class="block px-4 py-3 hover:bg-white/5 transition-colors group border-b border-white/5 last:border-0 notif-item" data-id="{{ $notification->id }}" onclick="markNotificationReadAdmin('{{ $notification->id }}', event, '{{ $notification->data['url'] ?? '#' }}')">
                                            <p class="text-sm text-slate-300 group-hover:text-white font-medium">{{ $notification->data['title'] ?? 'Alert' }}</p>
                                            <p class="text-xs text-slate-400 mt-1">{{ collect($notification->data)->get('message', 'System Notification') }}</p>
                                            <p class="text-[10px] text-slate-500 mt-1 uppercase tracking-wider">{{ $notification->created_at->diffForHumans() }}</p>
                                        </a>
                                    @empty
                                        <div class="px-4 py-6 text-center text-slate-500 text-sm">
                                            No new system alerts.
                                        </div>
                                    @endforelse
                                </div>
                                @if(auth()->guard('admin')->user()->unreadNotifications->count() > 0)
                                <div class="px-4 py-2 border-t border-white/5 text-center">
                                    <button type="button" onclick="markAllNotificationsReadAdmin()" class="text-xs font-medium text-[#38bdf8] hover:text-white transition-colors">Clear All Unread</button>
                                </div>
                                @endif
                            </div>
                        </div>

                        <script>
                            function markNotificationReadAdmin(id, event, url) {
                                event.preventDefault();
                                fetch(`/admin/notifications/${id}/mark-as-read`, {
                                    method: 'POST',
                                    headers: {
                                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                        'Content-Type': 'application/json',
                                        'Accept': 'application/json'
                                    }
                                }).then(response => {
                                    if(url !== '#') window.location.href = url;
                                    else {
                                        const el = event.currentTarget;
                                        el.style.opacity = '0.5';
                                        setTimeout(() => el.remove(), 300);
                                    }
                                });
                            }

                            function markAllNotificationsReadAdmin() {
                                fetch(`/admin/notifications/mark-all-as-read`, {
                                    method: 'POST',
                                    headers: {
                                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                        'Content-Type': 'application/json',
                                        'Accept': 'application/json'
                                    }
                                }).then(response => {
                                    document.getElementById('admin-notif-list').innerHTML = '<div class="px-4 py-6 text-center text-slate-500 text-sm">No new system alerts.</div>';
                                    const dot = document.getElementById('admin-notif-dot');
                                    if(dot) dot.remove();
                                    const badge = document.getElementById('admin-notif-badge');
                                    if(badge) badge.remove();
                                });
                            }
                        </script>

                        <!-- Profile Dropdown -->
                        <div class="relative">
                            <button @click="profileOpen = !profileOpen" @click.away="profileOpen = false" class="flex items-center text-sm rounded-full focus:outline-none focus:ring-2 focus:ring-[#38bdf8] border border-white/10 hover:border-white/30 transition-colors">
                                @if(auth()->guard('admin')->user()->img)
                                    <img class="h-8 w-8 rounded-full object-cover" src="{{ asset('storage/' . auth()->guard('admin')->user()->img) }}" alt="{{ auth()->guard('admin')->user()->name }}">
                                @else
                                    <img class="h-8 w-8 rounded-full" src="https://ui-avatars.com/api/?name={{ urlencode(auth()->guard('admin')->user()->name) }}&background=0f172a&color=38bdf8" alt="Admin">
                                @endif
                            </button>
                            <div x-show="profileOpen" x-transition class="absolute right-0 mt-2 w-48 glass-dropdown rounded-xl py-1 z-50 origin-top-right" style="display: none;">
                                <a href="{{ route('admin.manage_admins.show', auth()->guard('admin')->id()) }}" class="block px-4 py-2 text-sm text-slate-300 hover:bg-white/10 hover:text-white transition-colors">System Preferences</a>
                                <a href="#" class="block px-4 py-2 text-sm text-slate-300 hover:bg-white/10 hover:text-white transition-colors">Access Tokens</a>
                                <form method="POST" action="{{ route('admin.logout') }}">
                                    @csrf
                                    <button type="submit" class="w-full text-left block px-4 py-2 text-sm text-red-400 hover:bg-white/10 hover:text-red-300 transition-colors border-t border-white/5 mt-1 pt-2">Terminate Session</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </header>
