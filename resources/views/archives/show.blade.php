<x-layouts.app title="Vol {{ $issue->volume->number }} No {{ $issue->number }}">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20">
        <!-- Archive Hero Header -->
        <div class="relative bg-navy-950 rounded-[3rem] p-12 md:p-20 overflow-hidden mb-20 shadow-2xl shadow-navy-900/40">
            <div class="absolute top-0 right-0 p-12 opacity-10">
                <svg class="w-64 h-64 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2L1 21h22L12 2zm0 3.45l8.15 14.1H3.85L12 5.45z"/></svg>
            </div>
            
            <div class="relative z-10 space-y-8">
                <nav class="flex items-center gap-4 text-[10px] font-black uppercase tracking-[3px] text-navy-400">
                    <a href="{{ route('archives.index') }}" class="hover:text-white transition">Archives</a>
                    <span class="text-navy-700">/</span>
                    <span class="text-white">Volume {{ $issue->volume->number }}</span>
                </nav>

                <h1 class="text-4xl md:text-6xl font-black text-white tracking-tighter leading-tight max-w-4xl">
                    Issue {{ $issue->number }}: {{ $issue->title ?? 'Regular Periodical Edition' }}
                </h1>

                <div class="flex flex-wrap gap-8 pt-8 border-t border-navy-900">
                    <div>
                        <span class="block text-[10px] font-bold text-navy-500 uppercase tracking-widest mb-2">Released Protocol</span>
                        <span class="text-xl font-black text-white">{{ $issue->published_at ? $issue->published_at->format('F d, Y') : 'In Production' }}</span>
                    </div>
                    <div>
                        <span class="block text-[10px] font-bold text-navy-500 uppercase tracking-widest mb-2">Curated Research</span>
                        <span class="text-xl font-black text-white">{{ $issue->manuscripts->count() }} Scholarly Works</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Manuscripts Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-16">
            <div class="lg:col-span-8 space-y-8">
                <h2 class="text-xs font-black uppercase tracking-[4px] text-primary-500 mb-12">Scientific Index</h2>
                
                @foreach($issue->manuscripts as $article)
                <article class="group relative bg-white border border-slate-200 rounded-[2.5rem] p-10 hover:border-primary-400 hover:shadow-soft transition-all duration-500">
                    <div class="flex flex-col gap-6">
                        <div class="flex flex-wrap items-center gap-4">
                            @if($article->doi)
                            <span class="bg-navy-50 text-navy-400 px-3 py-1 rounded-lg text-[10px] font-mono tracking-tighter">DOI:{{ $article->doi }}</span>
                            @endif
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Pages {{ $article->page_range ?? 'N/A' }}</span>
                        </div>

                        <h3 class="text-2xl font-black text-navy-900 group-hover:text-primary-600 transition leading-tight">
                            <a href="{{ route('articles.show', $article->slug) }}" wire:navigate>
                                {{ $article->title }}
                            </a>
                        </h3>

                        <p class="text-[10px] font-bold text-navy-400 uppercase tracking-widest leading-loose">
                            @foreach($article->contributors->take(5) as $contributor)
                                <span class="{{ $contributor->is_corresponding ? 'text-primary-600 underline decoration-primary-200 underline-offset-4' : '' }}">
                                    {{ $contributor->full_name }}
                                </span>@if(!$loop->last), @endif
                            @endforeach
                            @if($article->contributors->count() > 5) <span class="text-slate-300">et al.</span> @endif
                        </p>

                        <div class="pt-8 border-t border-slate-50 flex items-center justify-between">
                            <div class="flex gap-4">
                                <a href="{{ route('articles.show', $article->slug) }}" wire:navigate class="text-[10px] font-black uppercase tracking-widest text-primary-600 hover:text-navy-900 transition">Abstract Interface</a>
                                <a href="{{ route('articles.download', $article->slug) }}" class="text-[10px] font-black uppercase tracking-widest text-slate-400 hover:text-navy-900 transition">Retrieve PDF</a>
                            </div>
                            <div class="w-10 h-10 bg-slate-50 rounded-xl flex items-center justify-center text-slate-400 group-hover:bg-primary-500 group-hover:text-white transition-all duration-500">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                            </div>
                        </div>
                    </div>
                </article>
                @endforeach
            </div>

            <!-- Sidebar Context -->
            <aside class="lg:col-span-4 space-y-12">
                <div class="bg-slate-50 border border-slate-100 rounded-[2.5rem] p-10 space-y-6">
                    <h3 class="text-[10px] font-black uppercase tracking-widest text-navy-400 border-b border-slate-200 pb-4">Editorial Context</h3>
                    <div class="prose prose-sm text-slate-500 font-medium">
                        Explore the research themes present in this issue. Each article has undergone a rigorous double-blind peer review protocol to ensure scientific integrity.
                    </div>
                </div>

                <div class="px-8 flex flex-col items-center text-center space-y-4">
                    <h4 class="text-[10px] font-black uppercase tracking-widest text-slate-400">Knowledge Network</h4>
                    <p class="text-xs font-bold text-navy-900">Are you researching in this field?</p>
                    <a href="{{ route('submission.guidelines') }}" class="w-full py-4 bg-navy-900 text-white rounded-2xl text-[10px] font-black uppercase tracking-widest hover:bg-primary-500 transition shadow-xl shadow-navy-100">
                        Submit Your Work
                    </a>
                </div>
            </aside>
        </div>
    </div>
</x-layouts.app>
