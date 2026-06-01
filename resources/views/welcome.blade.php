<x-layouts.app>
    <x-slot name="title">{{ __('home.hero.badge') }} - {{ \App\Models\Setting::get('site_name', config('app.name')) }}</x-slot>

    <!-- Hero Section: AI Search Focused -->
    <section class="relative pt-20 md:pt-32 pb-16 md:pb-24 overflow-hidden border-b border-slate-50">
        <!-- Animated Background Elements -->
        <div class="absolute inset-0 z-0">
            <div class="absolute top-0 right-0 w-1/2 h-1/2 bg-gradient-to-br from-primary-500/10 to-transparent rounded-full blur-[120px] -mr-40 -mt-20 animate-pulse"></div>
            <div class="absolute bottom-0 left-0 w-1/3 h-1/3 bg-gradient-to-tr from-indigo-500/10 to-transparent rounded-full blur-[100px] -ml-20 -mb-20"></div>
            <div class="absolute inset-0 bg-[url('https://grainy-gradients.vercel.app/noise.svg')] opacity-[0.03] brightness-0"></div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="text-center max-w-5xl mx-auto mb-16">
                <div class="inline-flex items-center gap-3 px-5 py-2 rounded-2xl bg-white shadow-soft-xl border border-slate-100 text-navy-600 text-[10px] font-black tracking-[2px] uppercase mb-10 animate-fade-in-up">
                    <span class="relative flex h-2 w-2">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-primary-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-primary-500"></span>
                    </span>
                    {{ __('home.hero.badge') }}
                </div>
                
                <h1 class="text-4xl md:text-6xl lg:text-8xl font-black text-navy-900 mb-10 tracking-tight leading-[0.95] animate-fade-in">
                    {{ __('home.hero.title') }}
                </h1>
                
                <p class="text-base md:text-xl text-slate-500 font-medium max-w-2xl mx-auto mb-12 leading-relaxed opacity-80 decoration-primary-500/30 underline-offset-8">
                    {{ __('home.hero.description') }}
                </p>

                <!-- Premium Search Bar -->
                <div 
                    x-data="{ search: '', focused: false }"
                    class="relative max-w-3xl mx-auto transition-all duration-700"
                    :class="focused ? 'scale-105' : 'scale-100'"
                >
                    <div class="absolute -inset-2 bg-gradient-to-r from-primary-600 via-indigo-600 to-primary-600 rounded-[2.5rem] blur opacity-10 group-hover:opacity-20 transition duration-1000 animate-gradient-x"></div>
                    <form 
                        @submit.prevent="window.location.href = '{{ route('articles.index') }}?search=' + encodeURIComponent(search)"
                        class="relative bg-white dark:bg-navy-900 rounded-[2rem] border border-slate-200 dark:border-white/10 shadow-2xl p-3 flex items-center gap-2 group"
                        @focusin="focused = true"
                        @focusout="focused = false"
                    >
                        <div class="pl-3 md:pl-6 text-slate-400 group-hover:text-primary-500 transition-colors">
                            <svg class="w-5 h-5 md:w-6 md:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        </div>
                        <input 
                            type="text" 
                            x-model="search"
                            placeholder="{{ __('home.hero.search_placeholder') }}" 
                            class="flex-grow py-3 md:py-5 bg-transparent border-none focus:ring-0 text-sm md:text-lg text-navy-800 dark:text-white placeholder-slate-400 font-bold"
                        >
                        <button type="submit" class="bg-navy-950 dark:bg-primary-600 text-white px-4 py-3 md:px-10 md:py-5 rounded-xl md:rounded-2xl font-black uppercase tracking-widest text-[10px] md:text-xs hover:bg-primary-600 transition shadow-xl shadow-navy-900/10 hover:shadow-primary-500/20 active:scale-95 group-hover:translate-x-0.5">
                            {{ __('home.hero.search_button') }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <!-- Latest Research: Integrated Grid -->
    <section class="py-32 bg-white dark:bg-navy-950 px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto">
            <div class="flex flex-col md:flex-row md:items-end justify-between mb-20 gap-8">
                <div class="max-w-2xl">
                    <h2 class="text-xs font-black uppercase tracking-[6px] text-primary-500 mb-6">{{ __('home.latest.title') }}</h2>
                    <p class="text-4xl md:text-5xl font-black text-navy-900 leading-tight tracking-tight">
                        {{ __('home.frontier') }} <span class="text-transparent bg-clip-text bg-gradient-to-r from-primary-600 to-indigo-600">{{ __('home.open_science') }}</span>
                    </p>
                </div>
                <a href="{{ route('articles.index') }}" wire:navigate class="group flex items-center gap-4 text-sm font-bold text-navy-900 border-b-2 border-primary-500 pb-1 hover:border-navy-900 transition-all">
                    {{ __('home.latest.archives') }}
                    <svg class="w-5 h-5 group-hover:translate-x-1 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                </a>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12">
                <!-- Featured Side (8 cols) -->
                @php $featured = \App\Models\Manuscript::published()->with(['journal', 'contributors'])->latest('published_at')->first(); @endphp
                @if($featured)
                <div class="lg:col-span-8 space-y-12">
                    <div class="group relative bg-slate-50 dark:bg-navy-900 rounded-[3rem] p-12 border border-slate-100 dark:border-white/5 overflow-hidden transition-all duration-700 hover:shadow-2xl">
                        <div class="absolute -top-24 -right-24 w-64 h-64 bg-primary-500/5 rounded-full blur-3xl group-hover:scale-150 transition duration-1000"></div>
                        
                        <div class="relative z-10 flex flex-col h-full justify-between">
                            <div class="space-y-8">
                                <div class="flex items-center gap-4">
                                    <span class="px-4 py-1.5 bg-navy-900 text-white text-[10px] font-black uppercase tracking-widest rounded-xl">
                                        {{ $featured->journal->name }}
                                    </span>
                                    <div class="h-px bg-slate-200 flex-grow"></div>
                                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">{{ $featured->published_at->format('M d, Y') }}</span>
                                </div>
                                
                                <h3 class="text-4xl font-black text-navy-900 leading-[1.1] hover:text-primary-600 transition">
                                    <a href="{{ route('articles.show', $featured->slug) }}" wire:navigate>{{ $featured->title }}</a>
                                </h3>
                                
                                <p class="text-slate-500 text-lg leading-relaxed font-medium line-clamp-3">
                                    {{ $featured->abstract }}
                                </p>
                            </div>

                            <div class="mt-12 flex items-center justify-between border-t border-slate-200/60 pt-10">
                                <div class="flex items-center gap-4">
                                    <div class="flex -space-x-3">
                                        @foreach($featured->contributors->take(3) as $c)
                                        <div class="w-10 h-10 rounded-full border-2 border-white bg-white shadow-soft flex items-center justify-center text-[10px] font-black group-hover:translate-x-1 transition duration-500">
                                            {{ substr($c->given_name, 0, 1) }}{{ substr($c->family_name, 0, 1) }}
                                        </div>
                                        @endforeach
                                    </div>
                                    <span class="text-xs font-bold text-navy-900">{{ $featured->contributors->first()?->full_name }} et al.</span>
                                </div>
                                <a href="{{ route('articles.show', $featured->slug) }}" wire:navigate class="w-14 h-14 bg-navy-950 text-white rounded-2xl flex items-center justify-center hover:bg-primary-600 transition shadow-xl active:scale-90">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Stats & More (4 cols) -->
                <div class="lg:col-span-4 space-y-12">
                    <div class="bg-navy-950 rounded-[3rem] p-10 text-white relative overflow-hidden group">
                        <div class="absolute inset-0 bg-gradient-to-br from-primary-600/20 to-transparent"></div>
                        <h4 class="text-[10px] font-black uppercase tracking-[3px] text-navy-400 mb-10 relative z-10">{{ __('home.stats.access') }}</h4>
                        <div class="grid grid-cols-2 gap-8 relative z-10">
                            <div>
                                <span class="block text-3xl font-black mb-1 tabular-nums">{{ number_format(\App\Models\Manuscript::published()->count()) }}</span>
                                <span class="text-[10px] font-bold text-navy-500 uppercase tracking-widest">{{ __('home.stats.articles') }}</span>
                            </div>
                            <div>
                                @php
                                    $totalViews = \App\Models\Manuscript::sum('view_count');
                                    $formattedViews = $totalViews >= 1000 ? number_format($totalViews / 1000, 1) . 'k' : number_format($totalViews);
                                @endphp
                                <span class="block text-3xl font-black mb-1 tabular-nums">{{ $formattedViews }}</span>
                                <span class="text-[10px] font-bold text-navy-500 uppercase tracking-widest">{{ __('home.common.reads') ?? 'Reads' }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="bg-primary-500 rounded-[3rem] p-10 text-white group hover:bg-primary-600 transition duration-500">
                        <h4 class="text-2xl font-black leading-tight mb-4 tracking-tighter">{{ __('home.cta.title') }}</h4>
                        <p class="text-primary-100 text-xs font-bold leading-relaxed mb-8 opacity-80">{{ __('home.cta.text') }}</p>
                        <a href="{{ route('submission.guidelines') }}" class="inline-flex items-center gap-3 px-8 py-4 bg-white text-navy-900 rounded-2xl text-[10px] font-black uppercase tracking-widest shadow-xl hover:-translate-y-1 transition duration-300">
                            {{ __('home.cta.button') }}
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Global Collaboration Section -->
    <section class="py-40 bg-slate-50 overflow-hidden relative">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-24 items-center">
                <div class="space-y-10">
                    <h2 class="text-xs font-black uppercase tracking-[6px] text-primary-500">{{ __('home.global.title') }}</h2>
                    <h3 class="text-5xl font-black text-navy-900 tracking-tight leading-[1.1]">
                        {{ __('home.global.subtitle') }}
                    </h3>
                    <p class="text-lg text-slate-500 font-medium leading-relaxed">
                        {{ __('home.global.desc') }}
                    </p>
                    <div class="flex gap-12 pt-6">
                        <div class="text-center">
                            <span class="block text-4xl font-black text-navy-900 mb-2">{{ \App\Models\User::whereNotNull('country_code')->distinct('country_code')->count() }}+</span>
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">{{ __('home.global.stats.countries') }}</span>
                        </div>
                        <div class="text-center">
                            <span class="block text-4xl font-black text-navy-900 mb-2">{{ \App\Models\Affiliation::count() }}</span>
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">{{ __('home.global.stats.institutions') }}</span>
                        </div>
                        <div class="text-center">
                            <span class="block text-4xl font-black text-navy-900 mb-2">{{ \App\Models\User::role('reviewer')->count() }}</span>
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">{{ __('home.global.stats.reviewers') }}</span>
                        </div>
                    </div>
                </div>
                <!-- Visual Element -->
                <div class="relative">
                    <div class="absolute inset-0 bg-primary-500/10 rounded-full blur-[100px] animate-pulse"></div>
                    <div class="relative bg-white p-12 rounded-[4rem] shadow-2xl border border-slate-100 transform rotate-3 hover:rotate-0 transition duration-700">
                        <div class="space-y-8">
                            @foreach(\App\Models\Journal::where('is_active', true)->take(2)->get() as $journal)
                            <div class="flex items-center gap-6 p-6 rounded-[2rem] hover:bg-slate-50 transition border border-transparent hover:border-slate-100">
                                <div class="w-16 h-16 bg-navy-900 rounded-2xl flex items-center justify-center text-primary-500 flex-shrink-0">
                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l.691.34a2 2 0 01-1.783 0l-.691-.34a6 6 0 00-3.86-.517l-2.387.477a2 2 0 00-1.022.547l-1.162 1.626a2 2 0 00.339 2.586l.149.149a3 3 0 004.243 0l.303-.303a1 1 0 011.414 0l.303.303a3 3 0 004.243 0l.303-.303a1 1 0 011.414 0l.303.303a3 3 0 004.243 0l.149-.149a2 2 0 00.339-2.586l-1.162-1.626z"/></svg>
                                </div>
                                <div>
                                    <h4 class="font-black text-navy-900">{{ $journal->name }}</h4>
                                    <span class="text-[10px] font-bold text-primary-600 uppercase tracking-widest">{{ __('home.global.active_collaboration') }}</span>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Journals Grid: Professional Hubs -->
    <section class="bg-navy-950 py-48 text-center text-white relative">
        <div class="absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-white/20 to-transparent"></div>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="max-w-3xl mx-auto mb-32">
                <h2 class="text-xs font-black uppercase tracking-[8px] text-primary-500 mb-8">{{ __('home.hubs.title') }}</h2>
                <p class="text-4xl font-black tracking-tight leading-tight">{{ __('home.hubs.subtitle') }}</p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-12">
                @foreach(\App\Models\Journal::where('is_active', true)->get() as $journal)
                <div class="group relative bg-white/5 backdrop-blur-3xl border border-white/5 p-12 rounded-[4rem] hover:bg-white/10 hover:border-primary-500/50 transition-all duration-700">
                    <div class="w-24 h-24 bg-navy-900 rounded-[2.5rem] mx-auto mb-10 flex items-center justify-center text-primary-500 group-hover:scale-110 group-hover:bg-primary-500 group-hover:text-white transition-all duration-700 shadow-2xl overflow-hidden">
                        @if($journal->cover_image)
                        <img src="{{ asset('storage/' . $journal->cover_image) }}" alt="{{ $journal->name }}" class="w-full h-full object-cover">
                        @else
                        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 2L1 21h22L12 2zm0 3.45l8.15 14.1H3.85L12 5.45z"/></svg>
                        @endif
                    </div>
                    <h3 class="text-2xl font-black mb-6 tracking-tight">{{ $journal->name }}</h3>
                    <p class="text-navy-300 text-sm font-medium leading-relaxed mb-12 h-20 overflow-hidden opacity-60">{{ Str::limit($journal->description, 100) }}</p>
                    <a href="{{ route('journals.show', $journal->slug) }}" class="inline-flex items-center gap-4 px-10 py-4 rounded-2xl bg-white text-navy-950 text-[10px] font-black uppercase tracking-[4px] hover:bg-primary-500 hover:text-white transition-all duration-500 shadow-xl">
                        {{ __('home.hubs.enter') }}
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                    </a>
                </div>
                @endforeach
            </div>
        </div>
    </section>

</x-layouts.app>
