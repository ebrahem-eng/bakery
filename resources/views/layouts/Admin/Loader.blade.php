<!-- Global Loader Component -->
<div id="neural-global-loader" class="fixed inset-0 z-[9999] bg-[#0f172a] flex flex-col items-center justify-center transition-opacity duration-500 opacity-0 pointer-events-none">
    
    <!-- Background Elements Match Theme -->
    <div class="absolute inset-0 z-0 bg-gradient-to-br from-[#0f172a] to-[#0d1b2e]"></div>
    <div class="fixed inset-0 z-0 opacity-[0.02]" style="background-image: linear-gradient(#94a3b8 1px, transparent 1px), linear-gradient(90deg, #94a3b8 1px, transparent 1px); background-size: 40px 40px;"></div>

    <div class="relative z-10 flex flex-col items-center">
        <!-- Spinning Logo/Brain Elements -->
        <div class="relative w-24 h-24 mb-6">
            <!-- Outer spinning ring -->
            <div class="absolute inset-0 rounded-full border-t-2 border-r-2 border-[#38bdf8] opacity-70 animate-spin" style="animation-duration: 1.5s;"></div>
            <!-- Inner spinning ring (reverse) -->
            <div class="absolute inset-2 rounded-full border-b-2 border-l-2 border-[#6366f1] opacity-60 animate-spin" style="animation-duration: 2s; animation-direction: reverse;"></div>
            
            <!-- Central Neural Node -->
            <div class="absolute inset-0 flex items-center justify-center">
                <div class="w-8 h-8 rounded-full bg-gradient-to-tr from-[#0ea5e9] to-[#818cf8] shadow-[0_0_20px_#0ea5e9] animate-pulse"></div>
            </div>

            <!-- Orbiting Nodes -->
            <div class="absolute inset-0 animate-spin" style="animation-duration: 3s;">
                <div class="absolute top-0 left-1/2 -ml-1 -mt-1 w-2 h-2 rounded-full bg-[#38bdf8] shadow-[0_0_5px_#38bdf8]"></div>
            </div>
            <div class="absolute inset-0 animate-spin" style="animation-duration: 4s; animation-direction: reverse;">
                <div class="absolute bottom-0 right-1/2 -mr-1 -mb-1 w-2 h-2 rounded-full bg-[#f472b6] shadow-[0_0_5px_#f472b6]"></div>
            </div>
        </div>

        <!-- Text -->
        <h2 class="text-lg font-bold tracking-widest text-transparent bg-clip-text bg-gradient-to-r from-[#0ea5e9] to-[#818cf8] animate-pulse">
            INITIALIZING
        </h2>
        <p class="text-xs text-slate-500 mt-2 tracking-widest uppercase">Establishing Neural Links...</p>
    </div>
</div>

<script>
    // Show the loader logic
    function showGlobalLoader() {
        const loader = document.getElementById('neural-global-loader');
        if (loader) {
            loader.classList.remove('opacity-0', 'pointer-events-none');
            loader.classList.add('opacity-100', 'pointer-events-auto');
        }
    }

    // Hide the loader logic
    function hideGlobalLoader() {
        const loader = document.getElementById('neural-global-loader');
        if (loader) {
            loader.classList.remove('opacity-100', 'pointer-events-auto');
            loader.classList.add('opacity-0', 'pointer-events-none');
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        // Hide loader when the page finishes loading initially
        // Using a tiny timeout to ensure it feels smooth if the page loads instantly
        setTimeout(hideGlobalLoader, 300);

        // Attach to all forms to show loader on submit
        const forms = document.querySelectorAll('form');
        forms.forEach(form => {
            form.addEventListener('submit', (e) => {
                // If the form has native validation and it fails, don't show loader
                if (!form.checkValidity()) {
                    return;
                }
                showGlobalLoader();
            });
        });

        // Attach to anchor links to show loader navigating between pages
        const links = document.querySelectorAll('a[href]:not([href^="#"]):not([href^="javascript:"]):not([target="_blank"])');
        links.forEach(link => {
            link.addEventListener('click', (e) => {
                // Optional: Don't show for ctrl+click or meta+click (open in new tab)
                if (e.ctrlKey || e.metaKey || e.shiftKey || e.altKey) return;
                
                showGlobalLoader();
            });
        });
    });

    // Ensure loader hides if user uses browser back button (pageshow event with persisted cache)
    window.addEventListener('pageshow', (event) => {
        if (event.persisted) {
            hideGlobalLoader();
        }
    });

    // Initially show the loader as soon as the component is parsed in the DOM
    document.getElementById('neural-global-loader').classList.remove('opacity-0', 'pointer-events-none');
    document.getElementById('neural-global-loader').classList.add('opacity-100', 'pointer-events-auto');
</script>
