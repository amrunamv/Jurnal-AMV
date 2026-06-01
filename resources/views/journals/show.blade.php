<x-layouts.app title="{{ $journal->name }}">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20">
        <!-- Journal Hero -->
        <div class="flex flex-col lg:flex-row gap-16 items-start mb-24 pb-16 border-b border-slate-100">
            <div class="lg:w-2/3 space-y-8">
                <div class="flex items-center gap-4">
                    <span class="px-3 py-1 bg-primary-500 text-white text-[10px] font-black uppercase tracking-[2px] rounded-lg">Publication Hub</span>
                    @if($journal->issn)
                    <span class="text-[10px] font-bold text-slate-400 bg-slate-50 px-3 py-1 rounded-lg tracking-widest border border-slate-100">ISSN: {{ $journal->issn }}</span>
                    @endif
                </div>
                
                <h1 class="text-5xl md:text-6xl font-black text-navy-900 tracking-tighter leading-none">
                    {{ $journal->name }}
                </h1>
                
                <div class="flex flex-col md:flex-row gap-12 items-start">
                    @if($journal->cover_image)
                    <div class="w-full md:w-64 flex-shrink-0">
                        <img src="{{ asset('storage/' . $journal->cover_image) }}" alt="{{ $journal->name }}" class="w-full h-auto rounded-3xl shadow-2xl border border-slate-100">
                    </div>
                    @endif
                    <div class="flex-1 space-y-8">
                        <div class="prose prose-slate prose-lg max-w-3xl text-slate-500 font-medium leading-relaxed">
                            {{ $journal->description ?? __('common.no_research_found') }}
                        </div>

                        <div class="flex flex-wrap gap-4 pt-4">
                            <a href="{{ route('submission.guidelines') }}" class="bg-navy-950 text-white px-8 py-5 rounded-2xl font-black uppercase tracking-widest text-xs hover:bg-primary-600 transition shadow-xl shadow-navy-100">
                                {{ __('home.cta.submit_manuscript') }}
                            </a>
                            <a href="#archives" class="bg-slate-50 text-navy-900 px-8 py-5 rounded-2xl font-black uppercase tracking-widest text-xs border border-slate-100 hover:bg-slate-100 transition">
                                {{ __('home.latest.archives') }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <aside class="lg:w-1/3 w-full bg-slate-50 border border-slate-100 rounded-[2.5rem] p-10 space-y-8">
                <h3 class="text-xs font-black uppercase tracking-widest text-navy-400 border-b border-slate-200 pb-4">{{ __('home.footer.editorial_board') }}</h3>
                <div class="grid grid-cols-2 gap-8">
                    <div>
                        <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">{{ __('common.articles') }}</span>
                        <span class="text-3xl font-black text-navy-900">{{ $journal->manuscripts_count ?? 0 }}</span>
                    </div>
                    <div>
                        <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">{{ __('common.volume') }}</span>
                        <span class="text-3xl font-black text-navy-900">{{ $journal->volumes->count() }}</span>
                    </div>
                </div>
                <div class="pt-4 space-y-4">
                    <a href="{{ route('journals.template', $journal->slug) }}" class="w-full flex items-center justify-center gap-3 bg-white border border-slate-200 py-4 rounded-xl text-[10px] font-black uppercase tracking-widest text-navy-900 hover:border-primary-400 hover:shadow-soft transition-all duration-300">
                        <svg class="w-5 h-5 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                        Templat Riset (.DOCX)
                    </a>
                    <p class="text-[10px] text-slate-400 leading-tight font-medium italic">{{ __('home.footer.desc') }}</p>
                </div>
            </aside>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-16">
            <!-- Latest Research Feed -->
            <div class="lg:col-span-8 space-y-12">
                <div class="flex items-center justify-between mb-8">
                    <h2 class="text-2xl font-black text-navy-900 tracking-tight">{{ __('home.latest.title') }}</h2>
                    <a href="{{ route('articles.index', ['journal' => $journal->id]) }}" class="text-xs font-black uppercase tracking-widest text-primary-600 hover:underline">{{ __('home.latest.view_all') }}</a>
                </div>

                <div class="space-y-6">
                    @forelse ($articles as $article)
                    <article class="group bg-white p-8 rounded-[2rem] border border-slate-200 hover:border-primary-300 hover:shadow-soft transition-all duration-500">
                        <div class="flex flex-col gap-6">
                            <div class="flex flex-wrap items-center gap-3">
                                <span class="text-[10px] text-slate-400 font-bold uppercase tracking-widest">{{ optional($article->published_at)->format('M d, Y') ?? 'N/A' }}</span>
                                @if($article->doi)
                                <span class="bg-navy-50 text-navy-400 px-3 py-1 rounded-lg text-[10px] font-mono tracking-tighter">DOI:{{ $article->doi }}</span>
                                @endif
                            </div>

                            <h3 class="text-xl font-extrabold text-navy-900 group-hover:text-primary-600 transition leading-tight">
                                <a href="{{ route('articles.show', $article->slug) }}" wire:navigate>
                                    {{ $article->title }}
                                </a>
                            </h3>

                            <p class="text-[10px] font-bold text-navy-400 uppercase tracking-widest leading-loose">
                                @foreach($article->contributors->take(3) as $contributor)
                                    <span>{{ $contributor->full_name }}</span>@if(!$loop->last), @endif
                                @endforeach
                                @if($article->contributors->count() > 3) <span class="text-slate-300">et al.</span> @endif
                            </p>
                        </div>
                    </article>
                    @empty
                    <div class="py-20 text-center bg-slate-50 border-2 border-dashed border-slate-200 rounded-[2rem]">
                        <p class="text-slate-400 font-bold uppercase text-xs tracking-widest">{{ __('common.no_research_found') }}</p>
                    </div>
                    @endforelse
                </div>
                
                <div class="pt-8">
                    {{ $articles->links() }}
                </div>
            </div>

            <!-- Archive Navigation -->
            <aside id="archives" class="lg:col-span-4 space-y-12">
                <h2 class="text-2xl font-black text-navy-900 tracking-tight">{{ __('home.latest.archives') }}</h2>
                
                <div class="space-y-8">
                    @foreach($journal->volumes as $volume)
                    <div class="bg-white border-l-4 border-navy-950 pl-8 space-y-4">
                        <h4 class="text-xs font-black uppercase tracking-[3px] text-navy-400">{{ __('common.volume') }} {{ $volume->number }} ({{ $volume->year }})</h4>
                        <div class="flex flex-wrap gap-4">
                            @foreach($volume->issues as $issue)
                            <a href="{{ route('archives.show', $issue->id) }}" class="px-4 py-3 bg-slate-50 border border-slate-100 rounded-xl text-xs font-black text-navy-900 hover:bg-navy-950 hover:text-white hover:shadow-xl transition-all duration-300">
                                {{ __('common.issue') }} {{ $issue->number }}
                            </a>
                            @endforeach
                        </div>
                    </div>
                    @endforeach
                </div>
            </aside>
        </div>
    </div>
</x-layouts.app>
