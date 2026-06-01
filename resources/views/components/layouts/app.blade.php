<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? \App\Models\Setting::get('site_name', config('app.name', 'AMV Open Science')) }}</title>
    <meta name="description" content="{{ $description ?? \App\Models\Setting::get('site_description', __('home.hero.description')) }}">
    
    <!-- Favicon -->
    @if($siteFavicon = \App\Models\Setting::get('site_favicon'))
        <link rel="icon" type="image/x-icon" href="{{ \Illuminate\Support\Facades\Storage::url($siteFavicon) }}">
    @endif
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css2?family=Inter:wght@400;500;600;700;800&family=Roboto+Mono:wght@400;500&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles

    {!! $head ?? '' !!}

    <style>
        [x-cloak] { display: none !important; }
    </style>
</head>
<body 
    class="font-sans antialiased bg-white text-navy-900 min-h-screen flex flex-col selection:bg-primary-100 selection:text-primary-700 transition-colors duration-500" 
    x-data="{ 
        darkMode: localStorage.getItem('darkMode') === 'true', 
        mobileMenu: false,
        toggleTheme() {
            this.darkMode = !this.darkMode;
            localStorage.setItem('darkMode', this.darkMode);
        }
    }" 
    :class="{ 'dark': darkMode }"
>
    <!-- Premium Header -->
    <header class="fixed top-0 inset-x-0 z-50 transition-all duration-300" x-data="{ scrolled: false }" :class="scrolled ? 'glass-effect shadow-soft py-2' : 'bg-transparent py-4'" @scroll.window="scrolled = (window.pageYOffset > 20)">
        <nav class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <a href="{{ route('home') }}" class="flex items-center gap-3 group" wire:navigate>
                    @if($siteLogo = \App\Models\Setting::get('site_logo'))
                        <img src="{{ \Illuminate\Support\Facades\Storage::url($siteLogo) }}" alt="{{ \App\Models\Setting::get('site_name', config('app.name')) }}" class="h-10 w-auto rounded-lg object-contain">
                    @else
                        <div class="w-10 h-10 bg-navy-950 rounded-xl flex items-center justify-center shadow-lg group-hover:bg-primary-500 transition-colors duration-500">
                            <span class="text-white font-bold text-lg">{{ substr(\App\Models\Setting::get('site_name', config('app.name')), 0, 1) }}</span>
                        </div>
                    @endif
                    <div class="flex flex-col">
                        <span class="font-bold text-lg text-navy-900 tracking-tight leading-none">{{ \App\Models\Setting::get('site_name', config('app.name', 'AMV Open Science')) }}</span>
                        <span class="text-[10px] text-slate-400 font-bold uppercase tracking-widest mt-1">{{ __('home.nav.academic_platform') }}</span>
                    </div>
                </a>

                <!-- Language Switcher -->
                <div class="relative ml-4" x-data="{ open: false }">
                    <button @click="open = !open" class="flex items-center text-gray-700 hover:text-indigo-600 focus:outline-none transition-colors duration-200">
                        <span class="mr-1 text-sm font-medium uppercase">{{ App::getLocale() }}</span>
                        <svg class="h-4 w-4 fill-current" viewBox="0 0 20 20">
                            <path d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" />
                        </svg>
                    </button>
                    <div x-show="open" @click.away="open = false" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="transform opacity-0 scale-95" x-transition:enter-end="transform opacity-100 scale-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="transform opacity-100 scale-100" x-transition:leave-end="transform opacity-0 scale-95" class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50 ring-1 ring-black ring-opacity-5">
                        <a href="{{ route('lang.switch', 'id') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 {{ App::getLocale() == 'id' ? 'bg-indigo-50 font-bold' : '' }}">Bahasa Indonesia</a>
                        <a href="{{ route('lang.switch', 'en') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 {{ App::getLocale() == 'en' ? 'bg-indigo-50 font-bold' : '' }}">English</a>
                    </div>
                </div>

                <!-- Desktop Navigation -->
                <div class="hidden md:flex items-center gap-1">
                    @foreach([
                        ['label' => 'home.nav.home', 'route' => 'home'],
                        ['label' => 'home.nav.archives', 'route' => 'archives.index'],
                        ['label' => 'home.nav.announcements', 'route' => 'announcements.index'],
                        ['label' => 'home.nav.authors', 'route' => 'authors.index'],
                        ['label' => 'home.nav.journals', 'route' => 'journals.index'],
                    ] as $nav)
                    <a href="{{ route($nav['route']) }}" wire:navigate class="px-4 py-2 text-xs font-bold text-navy-600 uppercase tracking-wider hover:text-primary-600 hover:bg-primary-50 rounded-xl transition-all">
                        {{ __($nav['label']) }}
                    </a>
                    @endforeach
                </div>

                <!-- Actions -->
                <div class="flex items-center gap-3">
                    <!-- Theme Toggle -->
                    <button @click="toggleTheme()" class="p-2 text-navy-400 hover:text-primary-500 transition-colors duration-300">
                        <svg x-show="!darkMode" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg>
                        <svg x-show="darkMode" x-cloak class="w-5 h-5 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 18v1m9-9h1M3 12h1m16.121-7.121l.707.707M4.879 16.879l.707.707M16.121 16.121l.707.707M4.879 4.879l.707.707M12 8a4 4 0 100 8 4 4 0 000-8z"/></svg>
                    </button>

                    <!-- Language -->
                    <div class="hidden sm:flex items-center gap-1 p-1 bg-slate-100 rounded-xl border border-slate-200">
                        @foreach(['id', 'en'] as $lang)
                        <a href="{{ route('lang.switch', $lang) }}" class="px-2 py-1 text-[10px] font-black rounded-lg transition {{ app()->getLocale() === $lang ? 'bg-white text-navy-900 shadow-sm' : 'text-slate-400 hover:text-navy-600' }}">
                            {{ strtoupper($lang) }}
                        </a>
                        @endforeach
                    </div>

                    @auth
                        <a href="{{ route('filament.console.pages.dashboard') }}" class="hidden md:flex px-6 py-2.5 bg-navy-900 text-white text-xs font-bold rounded-xl hover:bg-navy-800 transition shadow-lg shadow-navy-100">
                            {{ __('home.nav.dashboard') }}
                        </a>
                    @else
                        <a href="{{ route('filament.console.auth.login') }}" class="hidden md:flex px-6 py-2.5 bg-navy-900 text-white text-xs font-bold rounded-xl hover:bg-navy-800 transition shadow-lg shadow-navy-100">
                            {{ __('home.nav.login') }}
                        </a>
                    @endauth

                    <!-- Mobile Toggle -->
                    <button @click="mobileMenu = !mobileMenu" class="md:hidden p-2 text-navy-900">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"/></svg>
                    </button>
                </div>
            </div>
        </nav>

        <!-- Mobile Menu Container -->
        <div 
            x-show="mobileMenu" 
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 -translate-y-4"
            x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 -translate-y-4"
            class="md:hidden glass-effect border-t border-slate-100 dark:border-white/10"
            x-cloak
        >
            <div class="px-4 pt-4 pb-8 space-y-2">
                @foreach([
                    ['label' => 'home.nav.home', 'route' => 'home'],
                    ['label' => 'home.nav.archives', 'route' => 'archives.index'],
                    ['label' => 'home.nav.announcements', 'route' => 'announcements.index'],
                    ['label' => 'home.nav.authors', 'route' => 'authors.index'],
                    ['label' => 'home.nav.journals', 'route' => 'journals.index'],
                ] as $nav)
                <a href="{{ route($nav['route']) }}" wire:navigate class="block px-4 py-3 text-sm font-bold text-navy-600 dark:text-navy-300 uppercase tracking-wider hover:bg-primary-50 dark:hover:bg-primary-900/20 rounded-xl transition-all">
                    {{ __($nav['label']) }}
                </a>
                @endforeach
                
                <div class="pt-4 border-t border-slate-100 dark:border-white/10 flex flex-col gap-3">
                    @auth
                        <a href="{{ route('filament.console.pages.dashboard') }}" class="flex justify-center px-6 py-3 bg-navy-900 dark:bg-primary-600 text-white text-xs font-bold rounded-xl shadow-lg">
                            {{ __('home.nav.dashboard') }}
                        </a>
                    @else
                        <a href="{{ route('filament.console.auth.login') }}" class="flex justify-center px-6 py-3 bg-navy-900 dark:bg-primary-600 text-white text-xs font-bold rounded-xl shadow-lg">
                            {{ __('home.nav.login') }}
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="flex-grow pt-24">
        {{ $slot }}
    </main>

    <!-- Professional Footer -->
    <footer class="bg-navy-950 text-white py-24">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-16">
                <div class="md:col-span-2">
                    <div class="flex items-center gap-3 mb-8">
                        @if($siteLogo = \App\Models\Setting::get('site_logo'))
                             <img src="{{ \Illuminate\Support\Facades\Storage::url($siteLogo) }}" alt="{{ \App\Models\Setting::get('site_name', config('app.name')) }}" class="h-12 w-auto bg-white rounded-lg object-contain p-1">
                        @else
                            <div class="w-12 h-12 bg-white rounded-2xl flex items-center justify-center">
                                <span class="text-navy-950 font-black text-xl">{{ substr(\App\Models\Setting::get('site_name', config('app.name')), 0, 1) }}</span>
                            </div>
                        @endif
                        <span class="font-extrabold text-2xl tracking-tighter">{{ \App\Models\Setting::get('site_name', config('app.name', 'AMV Open Science')) }}</span>
                    </div>
                    <p class="text-navy-300 text-sm leading-relaxed max-w-sm">
                        {{ \App\Models\Setting::get('footer_about', __('home.footer.desc')) }}
                    </p>
                </div>

                <div>
                    <h4 class="text-xs font-black uppercase tracking-[3px] text-primary-500 mb-8">{{ __('home.footer.navigation') }}</h4>
                    <ul class="space-y-4">
                        <li><a href="{{ route('articles.index') }}" wire:navigate class="text-sm text-navy-400 hover:text-white transition">{{ __('home.footer.articles') }}</a></li>
                        <li><a href="{{ route('journals.index') }}" wire:navigate class="text-sm text-navy-400 hover:text-white transition">{{ __('home.footer.journals') }}</a></li>
                        <li><a href="{{ route('about') }}" wire:navigate class="text-sm text-navy-400 hover:text-white transition">{{ __('home.footer.about') }}</a></li>
                        <li><a href="#" class="text-sm text-navy-400 hover:text-white transition">{{ __('home.footer.ethical_standards') }}</a></li>
                    </ul>
                </div>

                <div>
                    <h4 class="text-xs font-black uppercase tracking-[3px] text-primary-500 mb-8">{{ __('home.footer.author_lab') }}</h4>
                    <ul class="space-y-4">
                        <li><a href="{{ route('submission.guidelines') }}" wire:navigate class="text-sm text-navy-400 hover:text-white transition">{{ __('home.footer.guidelines') }}</a></li>
                        <li><a href="#" class="text-sm text-navy-400 hover:text-white transition">{{ __('home.footer.review_process') }}</a></li>
                        <li><a href="#" class="text-sm text-navy-400 hover:text-white transition">{{ __('home.footer.doi_services') }}</a></li>
                        <li><a href="#" class="text-sm text-navy-400 hover:text-white transition">{{ __('home.footer.ai_integrity') }}</a></li>
                        <li><a href="{{ url('/oai') }}" class="text-sm text-navy-400 hover:text-white transition">OAI-PMH</a></li>
                    </ul>
                </div>
            </div>

            <div class="mt-24 pt-8 border-t border-navy-900 flex flex-col md:flex-row justify-between items-center gap-6">
                <p class="text-xs font-bold text-navy-500 uppercase tracking-widest">
                    @if($copyright = \App\Models\Setting::get('footer_copyright'))
                        {{ $copyright }}
                    @else
                        &copy; {{ date('Y') }} {{ \App\Models\Setting::get('site_name', config('app.name', 'AMV Open Science')) }}. {{ __('home.footer.rights') }}
                    @endif
                </p>
                <div class="flex gap-8">
                    <a href="#" class="text-xs font-bold text-navy-500 hover:text-white transition uppercase tracking-widest">{{ __('home.footer.privacy') }}</a>
                    <a href="#" class="text-xs font-bold text-navy-500 hover:text-white transition uppercase tracking-widest">{{ __('home.footer.terms') }}</a>
                    <a href="{{ route('sitemap') }}" class="text-xs font-bold text-navy-500 hover:text-white transition uppercase tracking-widest">{{ __('home.footer.sitemap') }}</a>
                </div>
            </div>
        </div>
    </footer>

    <x-toast />
    @stack('scripts')
    @livewireScripts
</body>
</html>
