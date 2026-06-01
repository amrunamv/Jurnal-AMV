<x-layouts.app title="{{ __('home.nav.archives') }}">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20">
        <!-- Header -->
        <div class="mb-20">
            <h1 class="text-5xl font-black text-navy-900 tracking-tighter mb-4 text-center md:text-left">{{ __('home.nav.archives') }}</h1>
            <p class="text-slate-500 font-medium text-lg text-center md:text-left">{{ __('common.peer_reviewed_manuscripts') }}</p>
        </div>

        <div class="space-y-20">
            @foreach($volumes as $volume)
                <section class="space-y-8">
                    <!-- Volume Header -->
                    <div class="flex items-center gap-6 border-b border-slate-100 pb-6">
                        <div class="h-14 w-14 bg-navy-900 rounded-2xl flex items-center justify-center text-white text-lg font-black shadow-lg shadow-navy-200">
                            {{ $volume->number }}
                        </div>
                        <div>
                             <h2 class="text-2xl font-black text-navy-900 tracking-tight">{{ __('common.volume') }} {{ $volume->number }}</h2>
                             <p class="text-slate-400 font-bold text-sm">{{ $volume->year }}</p>
                        </div>
                    </div>

                    <!-- Issues Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                        @foreach($volume->issues as $issue)
                            <a href="{{ route('archives.show', $issue->id) }}" class="group relative bg-white border border-slate-200 rounded-3xl p-6 hover:border-primary-400 hover:shadow-xl hover:shadow-primary-100 transition-all duration-300 flex flex-col h-full">
                                <div class="flex justify-between items-start mb-4">
                                     <span class="px-3 py-1 bg-slate-50 text-navy-600 rounded-lg text-[10px] font-black uppercase tracking-widest border border-slate-100 group-hover:bg-primary-50 group-hover:text-primary-600 transition">
                                        {{ __('common.issue') }} {{ $issue->number }}
                                     </span>
                                     <span class="text-[10px] font-bold text-slate-400">{{ $issue->published_at ? $issue->published_at->format('M Y') : __('common.pre_print') }}</span>
                                </div>
                                
                                <h3 class="text-lg font-bold text-navy-900 mb-2 group-hover:text-primary-600 transition line-clamp-2">
                                    {{ $issue->title ?? 'Standard Issue Edition' }}
                                </h3>
                                
                                @if($issue->journal)
                                <p class="text-xs text-slate-500 font-medium mb-6 line-clamp-1 border-t border-slate-50 pt-3 mt-auto">
                                    {{ $issue->journal->name }}
                                </p>
                                @endif

                                <div class="mt-auto flex items-center justify-between text-primary-600 text-xs font-black uppercase tracking-wider">
                                    <span>{{ __('common.browse_issue') }}</span>
                                    <svg class="w-4 h-4 transform group-hover:translate-x-1 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </section>
            @endforeach
        </div>
    </div>
</x-layouts.app>
