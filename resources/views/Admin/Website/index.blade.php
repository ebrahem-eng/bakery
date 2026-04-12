@extends('layouts.Admin.App')

@section('content')
<div class="space-y-6 pb-20" x-data="websiteManager()">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold text-slate-900 dark:text-white">{{ __('Website Control') }}</h1>
            <p class="text-slate-500 dark:text-slate-400 mt-1">{{ __('Manage your landing page content, visibility, and multimedia.') }}</p>
        </div>
        <div class="flex items-center gap-3">
            <button @click="saveAll()" :disabled="loading"
                class="bg-amber-500 hover:bg-amber-600 text-white px-6 py-2.5 rounded-xl font-bold shadow-lg shadow-amber-500/20 transition-all flex items-center gap-2">
                <template x-if="!loading">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/></svg>
                </template>
                <template x-if="loading">
                    <svg class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                </template>
                <span x-text="loading ? '{{ __('Saving...') }}' : '{{ __('Save Changes') }}'"></span>
            </button>
        </div>
    </div>

    <!-- Navigation Tabs -->
    <div class="flex items-center gap-2 p-1 bg-slate-200/50 dark:bg-white/5 rounded-2xl w-fit border border-slate-200 dark:border-white/10 overflow-x-auto max-w-full no-scrollbar">
        <button @click="activeTab = 'hero'" :class="activeTab === 'hero' ? 'bg-white dark:bg-amber-500 text-amber-600 dark:text-white shadow-md' : 'text-slate-500 hover:bg-white/50 dark:hover:bg-white/10'" class="px-6 py-2 rounded-xl text-sm font-bold transition-all whitespace-nowrap">
            {{ __('Hero Section') }}
        </button>
        <button @click="activeTab = 'about'" :class="activeTab === 'about' ? 'bg-white dark:bg-amber-500 text-amber-600 dark:text-white shadow-md' : 'text-slate-500 hover:bg-white/50 dark:hover:bg-white/10'" class="px-6 py-2 rounded-xl text-sm font-bold transition-all whitespace-nowrap">
            {{ __('About Us') }}
        </button>
        <button @click="activeTab = 'offer'" :class="activeTab === 'offer' ? 'bg-white dark:bg-amber-500 text-amber-600 dark:text-white shadow-md' : 'text-slate-500 hover:bg-white/50 dark:hover:bg-white/10'" class="px-6 py-2 rounded-xl text-sm font-bold transition-all whitespace-nowrap">
            {{ __('What We Offer') }}
        </button>
        <button @click="activeTab = 'contact'" :class="activeTab === 'contact' ? 'bg-white dark:bg-amber-500 text-amber-600 dark:text-white shadow-md' : 'text-slate-500 hover:bg-white/50 dark:hover:bg-white/10'" class="px-6 py-2 rounded-xl text-sm font-bold transition-all whitespace-nowrap">
            {{ __('Contact Section') }}
        </button>
    </div>

    <!-- Tab Content -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Main Content Area -->
        <div class="lg:col-span-2 space-y-8">
            
            <!-- Hero Tab -->
            <div x-show="activeTab === 'hero'" x-transition:enter="transition ease-out duration-300 transform" x-transition:enter-start="opacity-0 translate-y-4" class="space-y-6">
                <div class="glass-card p-6 rounded-3xl border border-slate-200 dark:border-white/10">
                    <div class="flex items-center justify-between mb-8">
                        <h3 class="text-xl font-bold flex items-center gap-2">
                            <svg class="w-6 h-6 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                            {{ __('Hero Text Content') }}
                        </h3>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" x-model="form.hero_visible" class="sr-only peer">
                            <div class="w-12 h-6.5 bg-slate-200 dark:bg-white/10 peer-focus:outline-none rounded-full peer dark:peer-focus:ring-amber-800 peer-checked:bg-amber-500 transition-all duration-300"></div>
                            <div class="absolute top-[3px] ltr:left-[3px] rtl:right-[3px] w-5 h-5 bg-white rounded-full transition-all duration-300 transform peer-checked:ltr:translate-x-5.5 peer-checked:rtl:-translate-x-5.5 shadow-sm"></div>
                            <span class="ms-3 text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">{{ __('Visible') }}</span>
                        </label>
                    </div>
                    
                    <div class="grid grid-cols-1 gap-6">
                        <div>
                            <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">{{ __('Announcement Badge') }}</label>
                            <input type="text" x-model="form.hero_badge" class="w-full bg-slate-100 dark:bg-white/5 border-0 rounded-xl px-4 py-3 focus:ring-2 focus:ring-amber-500 transition-all" placeholder="{{ __('Small Batch, Big Flavor') }}">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">{{ __('Main Heading') }}</label>
                            <input type="text" x-model="form.hero_title" class="w-full bg-slate-100 dark:bg-white/5 border-0 rounded-xl px-4 py-3 focus:ring-2 focus:ring-amber-500 transition-all font-serif text-lg">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">{{ __('Hero Description') }}</label>
                            <textarea x-model="form.hero_description" rows="3" class="w-full bg-slate-100 dark:bg-white/5 border-0 rounded-xl px-4 py-3 focus:ring-2 focus:ring-amber-500 transition-all" placeholder="{{ __('Describe your bakery...') }}"></textarea>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">{{ __('Primary CTA Button') }}</label>
                                <input type="text" x-model="form.hero_cta_primary" class="w-full bg-slate-100 dark:bg-white/5 border-0 rounded-xl px-4 py-3 focus:ring-2 focus:ring-amber-500 transition-all">
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">{{ __('Secondary CTA Button') }}</label>
                                <input type="text" x-model="form.hero_cta_secondary" class="w-full bg-slate-100 dark:bg-white/5 border-0 rounded-xl px-4 py-3 focus:ring-2 focus:ring-amber-500 transition-all">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- About Tab -->
            <div x-show="activeTab === 'about'" x-transition:enter="transition ease-out duration-300 transform" x-transition:enter-start="opacity-0 translate-y-4" class="space-y-6">
                <div class="glass-card p-6 rounded-3xl border border-slate-200 dark:border-white/10">
                    <div class="flex items-center justify-between mb-8">
                        <h3 class="text-xl font-bold flex items-center gap-2">
                            <svg class="w-6 h-6 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                            {{ __('About Section Content') }}
                        </h3>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" x-model="form.about_visible" class="sr-only peer">
                            <div class="w-12 h-6.5 bg-slate-200 dark:bg-white/10 peer-focus:outline-none rounded-full peer dark:peer-focus:ring-amber-800 peer-checked:bg-amber-500 transition-all duration-300"></div>
                            <div class="absolute top-[3px] ltr:left-[3px] rtl:right-[3px] w-5 h-5 bg-white rounded-full transition-all duration-300 transform peer-checked:ltr:translate-x-5.5 peer-checked:rtl:-translate-x-5.5 shadow-sm"></div>
                            <span class="ms-3 text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">{{ __('Visible') }}</span>
                        </label>
                    </div>
                    
                    <div class="grid grid-cols-1 gap-6">
                        <div>
                            <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">{{ __('Section Small Heading') }}</label>
                            <input type="text" x-model="form.about_heading" class="w-full bg-slate-100 dark:bg-white/5 border-0 rounded-xl px-4 py-3 focus:ring-2 focus:ring-amber-500 transition-all" placeholder="{{ __('Since 1999') }}">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">{{ __('Main Title') }}</label>
                            <input type="text" x-model="form.about_title" class="w-full bg-slate-100 dark:bg-white/5 border-0 rounded-xl px-4 py-3 focus:ring-2 focus:ring-amber-500 transition-all">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">{{ __('Descriptive Paragraph 1') }}</label>
                            <textarea x-model="form.about_desc_1" rows="4" class="w-full bg-slate-100 dark:bg-white/5 border-0 rounded-xl px-4 py-3 focus:ring-2 focus:ring-amber-500 transition-all"></textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">{{ __('Descriptive Paragraph 2') }}</label>
                            <textarea x-model="form.about_desc_2" rows="4" class="w-full bg-slate-100 dark:bg-white/5 border-0 rounded-xl px-4 py-3 focus:ring-2 focus:ring-amber-500 transition-all"></textarea>
                        </div>
                    </div>
                </div>

                <div class="glass-card p-6 rounded-3xl border border-slate-200 dark:border-white/10">
                    <h3 class="text-xl font-bold mb-6 flex items-center gap-2">
                        <svg class="w-6 h-6 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        {{ __('Featured Image') }}
                    </h3>
                    <div class="flex flex-col items-center">
                        <div class="relative group w-full max-w-md h-64 rounded-2xl overflow-hidden shadow-xl border-4 border-white/5">
                            <img :src="previews.about_image || 'https://images.unsplash.com/photo-1509440159596-0249088772ff?ixlib=rb-4.0.3&auto=format&fit=crop&w=1000&q=80'" class="w-full h-full object-cover">
                            <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center backdrop-blur-sm">
                                <label class="cursor-pointer bg-white text-slate-900 px-6 py-2 rounded-full font-bold shadow-lg transform translate-y-4 group-hover:translate-y-0 transition-transform">
                                    {{ __('Upload New') }}
                                    <input type="file" @change="uploadImage($event, 'about_image')" class="hidden" accept="image/*">
                                </label>
                            </div>
                        </div>
                        <p class="text-xs text-slate-500 mt-4">{{ __('Recommended: 1000x800px or larger. Professional food photography works best.') }}</p>
                    </div>
                </div>
            </div>

            <!-- Offer Tab -->
            <div x-show="activeTab === 'offer'" x-transition:enter="transition ease-out duration-300 transform" x-transition:enter-start="opacity-0 translate-y-4" class="space-y-6">
                <div class="glass-card p-6 rounded-3xl border border-slate-200 dark:border-white/10">
                    <div class="flex items-center justify-between mb-8">
                        <h3 class="text-xl font-bold flex items-center gap-2">
                            <svg class="w-6 h-6 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                            {{ __('Offerings Section') }}
                        </h3>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" x-model="form.offer_visible" class="sr-only peer">
                            <div class="w-12 h-6.5 bg-slate-200 dark:bg-white/10 peer-focus:outline-none rounded-full peer dark:peer-focus:ring-amber-800 peer-checked:bg-amber-500 transition-all duration-300"></div>
                            <div class="absolute top-[3px] ltr:left-[3px] rtl:right-[3px] w-5 h-5 bg-white rounded-full transition-all duration-300 transform peer-checked:ltr:translate-x-5.5 peer-checked:rtl:-translate-x-5.5 shadow-sm"></div>
                            <span class="ms-3 text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">{{ __('Visible') }}</span>
                        </label>
                    </div>
                    
                    <div class="grid grid-cols-1 gap-6">
                        <div>
                            <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">{{ __('Main Title') }}</label>
                            <input type="text" x-model="form.offer_title" class="w-full bg-slate-100 dark:bg-white/5 border-0 rounded-xl px-4 py-3 focus:ring-2 focus:ring-amber-500 transition-all">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">{{ __('Subtitle') }}</label>
                            <input type="text" x-model="form.offer_subtitle" class="w-full bg-slate-100 dark:bg-white/5 border-0 rounded-xl px-4 py-3 focus:ring-2 focus:ring-amber-500 transition-all">
                        </div>
                    </div>
                </div>

                <!-- Cards Management -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <template x-for="i in [1, 2, 3]">
                        <div class="glass-card p-4 rounded-2xl border border-slate-200 dark:border-white/10 space-y-4">
                            <div class="flex items-center justify-between">
                                <h4 class="font-bold text-amber-500" x-text="'{{ __('Card') }} ' + i"></h4>
                            </div>
                            
                            <div class="relative group h-32 rounded-xl overflow-hidden bg-slate-200 dark:bg-white/5">
                                <img :src="previews['offer_card'+i+'_image'] || 'https://images.unsplash.com/photo-1509440159596-0249088772ff?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=60'" class="w-full h-full object-cover">
                                <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center backdrop-blur-sm">
                                    <label class="cursor-pointer bg-white text-slate-900 px-3 py-1 rounded-full text-xs font-bold shadow-lg">
                                        {{ __('Upload') }}
                                        <input type="file" @change="uploadImage($event, 'offer_card'+i+'_image')" class="hidden" accept="image/*">
                                    </label>
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-500 mb-1">{{ __('Card Title') }}</label>
                                <input type="text" x-model="form['offer_card'+i+'_title']" class="w-full text-sm bg-slate-100 dark:bg-white/5 border-0 rounded-lg px-3 py-2">
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-500 mb-1">{{ __('Card Description') }}</label>
                                <textarea x-model="form['offer_card'+i+'_desc']" rows="3" class="w-full text-sm bg-slate-100 dark:bg-white/5 border-0 rounded-lg px-3 py-2"></textarea>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            <!-- Contact Tab -->
            <div x-show="activeTab === 'contact'" x-transition:enter="transition ease-out duration-300 transform" x-transition:enter-start="opacity-0 translate-y-4" class="space-y-6">
                <div class="glass-card p-6 rounded-3xl border border-slate-200 dark:border-white/10">
                    <div class="flex items-center justify-between mb-8">
                        <h3 class="text-xl font-bold flex items-center gap-2">
                            <svg class="w-6 h-6 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                            {{ __('Contact Section') }}
                        </h3>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" x-model="form.contact_visible" class="sr-only peer">
                            <div class="w-12 h-6.5 bg-slate-200 dark:bg-white/10 peer-focus:outline-none rounded-full peer dark:peer-focus:ring-amber-800 peer-checked:bg-amber-500 transition-all duration-300"></div>
                            <div class="absolute top-[3px] ltr:left-[3px] rtl:right-[3px] w-5 h-5 bg-white rounded-full transition-all duration-300 transform peer-checked:ltr:translate-x-5.5 peer-checked:rtl:-translate-x-5.5 shadow-sm"></div>
                            <span class="ms-3 text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">{{ __('Visible') }}</span>
                        </label>
                    </div>
                    
                    <div class="grid grid-cols-1 gap-6">
                        <div>
                            <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">{{ __('Section Title') }}</label>
                            <input type="text" x-model="form.contact_title" class="w-full bg-slate-100 dark:bg-white/5 border-0 rounded-xl px-4 py-3 focus:ring-2 focus:ring-amber-500 transition-all">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">{{ __('Section Subtitle') }}</label>
                            <input type="text" x-model="form.contact_subtitle" class="w-full bg-slate-100 dark:bg-white/5 border-0 rounded-xl px-4 py-3 focus:ring-2 focus:ring-amber-500 transition-all">
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <!-- Sidebar / Statistics -->
        <div class="space-y-8">
            <div class="glass-card p-6 rounded-3xl border border-slate-200 dark:border-white/10 sticky top-6">
                <h4 class="font-bold mb-4 text-slate-800 dark:text-white">{{ __('Management Tips') }}</h4>
                <ul class="space-y-4">
                    <li class="flex gap-3 text-sm">
                        <div class="w-5 h-5 rounded-full bg-emerald-500/10 flex items-center justify-center shrink-0">
                            <svg class="w-3.5 h-3.5 text-emerald-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                        </div>
                        <span class="text-slate-500 dark:text-slate-400 font-light">{{ __('Use high-quality images with warm tones for the bakery feel.') }}</span>
                    </li>
                    <li class="flex gap-3 text-sm">
                        <div class="w-5 h-5 rounded-full bg-amber-500/10 flex items-center justify-center shrink-0">
                            <svg class="w-3.5 h-3.5 text-amber-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/></svg>
                        </div>
                        <span class="text-slate-500 dark:text-slate-400 font-light">{{ __('Short, punchy headlines are more readable on mobile.') }}</span>
                    </li>
                    <li class="flex gap-3 text-sm">
                        <div class="w-5 h-5 rounded-full bg-blue-500/10 flex items-center justify-center shrink-0">
                            <svg class="w-3.5 h-3.5 text-blue-500" fill="currentColor" viewBox="0 0 20 20"><path d="M10 12a2 2 0 100-4 2 2 0 000 4z"/><path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"/></svg>
                        </div>
                        <a href="{{ url('/') }}" target="_blank" class="text-amber-500 hover:underline font-bold">{{ __('Preview Website') }}</a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

<script>
function websiteManager() {
    return {
        activeTab: 'hero',
        loading: false,
        form: {
            // Hero
            hero_visible: {{ \App\Models\Setting::get('hero_visible', '1') == '1' ? 'true' : 'false' }},
            hero_badge: '{{ \App\Models\Setting::get("hero_badge", __("Small Batch, Big Flavor")) }}',
            hero_title: '{{ addslashes(\App\Models\Setting::get("hero_title", __("Artisan") . " " . __("Baking,") . " " . __("Modern Patisserie."))) }}',
            hero_description: '{{ addslashes(\App\Models\Setting::get("hero_description", __("Slow-fermented breads, delicate pastries, and celebration cakes finished with a designer\'s touch. Every bite balances warmth, craft, and a clean modern feel."))) }}',
            hero_cta_primary: '{{ \App\Models\Setting::get("hero_cta_primary", __("Explore Menu")) }}',
            hero_cta_secondary: '{{ \App\Models\Setting::get("hero_cta_secondary", __("Discover Our Story")) }}',
            // About
            about_visible: {{ \App\Models\Setting::get('about_visible', '1') == '1' ? 'true' : 'false' }},
            about_heading: '{{ \App\Models\Setting::get("about_heading", __("Since 1999")) }}',
            about_title: '{{ \App\Models\Setting::get("about_title", __("Where Tradition Meets True Innovation")) }}',
            about_desc_1: '{{ addslashes(\App\Models\Setting::get("about_desc_1", __("We don\'t just bake; we create edible works of art. Rooted in traditional techniques handed down through generations, our master bakers infuse modern flavors and breathtaking designs into everything we make."))) }}',
            about_desc_2: '{{ addslashes(\App\Models\Setting::get("about_desc_2", __("From the crackle of hand-shaped artisan loaves to the delicate crumb of our signature pastries, we guarantee an unparalleled culinary experience that tantalizes your taste buds and delights your eyes."))) }}',
            // Offer
            offer_visible: {{ \App\Models\Setting::get('offer_visible', '1') == '1' ? 'true' : 'false' }},
            offer_title: '{{ \App\Models\Setting::get("offer_title", __("Our Masterpieces")) }}',
            offer_subtitle: '{{ \App\Models\Setting::get("offer_subtitle", __("Handcrafted daily using only the finest, carefully sourced ingredients.")) }}',
            
            offer_card1_title: '{{ \App\Models\Setting::get("offer_card1_title", __("Artisan Bread")) }}',
            offer_card1_desc: '{{ addslashes(\App\Models\Setting::get("offer_card1_desc", __("Naturally leavened sourdough and rustic loaves, baked on stone hearths for a perfect crust and airy crumb."))) }}',
            offer_card2_title: '{{ \App\Models\Setting::get("offer_card2_title", __("Signature Cakes")) }}',
            offer_card2_desc: '{{ addslashes(\App\Models\Setting::get("offer_card2_desc", __("Elegant, custom-designed cakes featuring breathtaking modern aesthetics and luxurious, mouth-watering flavors."))) }}',
            offer_card3_title: '{{ \App\Models\Setting::get("offer_card3_title", __("French Pastries")) }}',
            offer_card3_desc: '{{ addslashes(\App\Models\Setting::get("offer_card3_desc", __("Flaky, buttery croissants, delicate macarons, and rich tartes crafted with authentic European techniques."))) }}',

            // Contact
            contact_visible: {{ \App\Models\Setting::get('contact_visible', '1') == '1' ? 'true' : 'false' }},
            contact_title: '{{ \App\Models\Setting::get("contact_title", __("Let\'s Bring Your Vision to Life.")) }}',
            contact_subtitle: '{{ \App\Models\Setting::get("contact_subtitle", __("Whether you need a custom cake for a monumental event or just want to reserve your favorite morning pastry, our team is here for you. We\'d love to hear from you.")) }}',
        },
        previews: {
            about_image: '{{ \App\Models\Setting::get("about_image") ? asset("storage/" . \App\Models\Setting::get("about_image")) : "" }}',
            offer_card1_image: '{{ \App\Models\Setting::get("offer_card1_image") ? asset("storage/" . \App\Models\Setting::get("offer_card1_image")) : "" }}',
            offer_card2_image: '{{ \App\Models\Setting::get("offer_card2_image") ? asset("storage/" . \App\Models\Setting::get("offer_card2_image")) : "" }}',
            offer_card3_image: '{{ \App\Models\Setting::get("offer_card3_image") ? asset("storage/" . \App\Models\Setting::get("offer_card3_image")) : "" }}',
        },

        async saveAll() {
            this.loading = true;
            try {
                const data = {...this.form};
                Object.keys(data).forEach(key => {
                    if (typeof data[key] === 'boolean') data[key] = data[key] ? '1' : '0';
                });

                const response = await fetch("{{ route('admin.website.update') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify(data)
                });
                
                const result = await response.json();
                if (result.success) {
                    Toast.fire({
                        icon: 'success',
                        title: result.message
                    });
                }
            } catch (error) {
                console.error(error);
                alert('Error saving settings.');
            } finally {
                this.loading = false;
            }
        },

        async uploadImage(event, key) {
            const file = event.target.files[0];
            if (!file) return;

            const formData = new FormData();
            formData.append('image', file);
            formData.append('key', key);
            formData.append('_token', '{{ csrf_token() }}');

            try {
                const response = await fetch("{{ route('admin.website.image') }}", {
                    method: 'POST',
                    body: formData
                });
                
                const result = await response.json();
                if (result.success) {
                    this.previews[key] = result.path;
                    Toast.fire({
                        icon: 'success',
                        title: result.message
                    });
                }
            } catch (error) {
                console.error(error);
                alert('Error uploading image.');
            }
        }
    }
}
</script>

<style>
.no-scrollbar::-webkit-scrollbar { display: none; }
.no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
</style>
@endsection
