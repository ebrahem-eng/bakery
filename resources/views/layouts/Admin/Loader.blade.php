<div id="global-loader" class="fixed inset-0 z-[100] bg-white dark:bg-[#0f1115] flex items-center justify-center transition-opacity duration-500">
    <div class="flex flex-col items-center">
        <!-- Glowing Background Orb -->
        <div class="absolute w-64 h-64 bg-amber-500/10 rounded-full blur-[80px] animate-pulse pointer-events-none"></div>

        <!-- Spinning Logo/Brain Elements -->
        <div class="relative w-24 h-24 mb-6">
            <!-- Outer spinning ring -->
            <div class="absolute inset-0 rounded-full border-t-2 border-r-2 border-amber-500 opacity-70 animate-spin" style="animation-duration: 1.5s;"></div>
            <!-- Inner spinning ring (reverse) -->
            <div class="absolute inset-2 rounded-full border-b-2 border-l-2 border-orange-400 opacity-60 animate-spin" style="animation-duration: 2s; animation-direction: reverse;"></div>
            
            <!-- Central Arc -->
            <div class="absolute inset-0 flex items-center justify-center">
                <div class="w-8 h-8 rounded-full bg-gradient-to-tr from-amber-500 to-orange-500 shadow-[0_0_20px_rgba(245,158,11,0.5)] animate-pulse"></div>
            </div>

            <!-- Orbiting Nodes -->
            <div class="absolute inset-0 animate-spin" style="animation-duration: 3s;">
                <div class="absolute top-0 left-1/2 -ml-1 -mt-1 w-2 h-2 rounded-full bg-amber-300 shadow-[0_0_5px_#fcd34d]"></div>
            </div>
            <div class="absolute inset-0 animate-spin" style="animation-duration: 4s; animation-direction: reverse;">
                <div class="absolute bottom-0 right-1/2 -mr-1 -mb-1 w-2 h-2 rounded-full bg-orange-500 shadow-[0_0_5px_#f97316]"></div>
            </div>
        </div>

        <!-- Text -->
        <h2 class="text-lg font-extrabold tracking-widest text-transparent bg-clip-text bg-gradient-to-r from-amber-400 to-orange-500 animate-pulse">
            {{ strtoupper(\App\Models\Setting::get('bakery_name', __('Bakery Portal'))) }}
        </h2>
        <p class="text-xs text-slate-500 mt-2 tracking-widest uppercase">{{ __('Initializing Environment...') }}</p>
    </div>
</div>

<script>
    window.addEventListener('load', function() {
        const loader = document.getElementById('global-loader');
        setTimeout(() => {
            loader.style.opacity = '0';
            setTimeout(() => {
                loader.style.display = 'none';
            }, 500);
        }, 300);
    });
</script>
