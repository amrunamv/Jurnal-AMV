<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <!-- Header -->
    <div class="mb-12">
        <h1 class="text-4xl font-extrabold text-navy-900 dark:text-white tracking-tight mb-3">{{ __('common.research_exploration') }}</h1>
        <p class="text-slate-500 font-medium">{{ __('common.accessing') }} <span class="text-navy-900 dark:text-primary-400 font-black">{{ $articles->total() }}</span> {{ __('common.peer_reviewed_manuscripts') }}</p>
    </div>

    <div class="flex flex-col lg:flex-row gap-12">
        <!-- Sidebar Filters -->
        <aside class="lg:w-80 space-y-10">
            <!-- Search -->
            <div class="space-y-4">
                <h3 class="text-[10px] font-black uppercase tracking-[2px] text-navy-400">{{ __('common.search_query') }}</h3>
                <div class="relative group">
                    <input 
                        type="text" 
                        wire:model.live.debounce.300ms="search"
                        placeholder="{{ __('home.hero.search_placeholder') }}"
                        class="w-full pl-10 pr-4 py-3 bg-slate-50 border-slate-200 border rounded-2xl text-sm font-medium focus:ring-0 focus:border-primary-500 transition-all"
                    >
                    <svg class="absolute left-3 top-3 w-4 h-4 text-slate-400 group-hover:text-primary-500 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
            </div>

            <!-- Journals -->
            <div class="space-y-4">
                <h3 class="text-[10px] font-black uppercase tracking-[2px] text-navy-400">{{ __('common.filter_by_journal') }}</h3>
                <div class="space-y-2">
                    <button wire:click="$set('journalId', '')" class="w-full text-left px-4 py-2 text-xs font-bold rounded-xl transition {{ !$journalId ? 'bg-navy-900 text-white shadow-lg shadow-navy-200' : 'text-slate-500 hover:bg-slate-50' }}">
                        {{ __('common.all_publications') }}
                    </button>
                    @foreach($journals as $journal)
                    <button wire:click="$set('journalId', '{{ $journal->id }}')" class="w-full text-left px-4 py-2 text-xs font-bold rounded-xl transition {{ $journalId == $journal->id ? 'bg-navy-900 text-white shadow-lg shadow-navy-200' : 'text-slate-500 hover:bg-slate-50' }}">
                        {{ $journal->name }}
                    </button>
                    @endforeach
                </div>
            </div>

            <!-- Publication Year -->
            <div class="space-y-4">
                <h3 class="text-[10px] font-black uppercase tracking-[2px] text-navy-400">{{ __('common.publication_year') }}</h3>
                <select wire:model.live="year" class="w-full py-3 px-4 bg-slate-50 border-slate-200 border rounded-2xl text-xs font-bold text-navy-800 focus:ring-0 focus:border-primary-500">
                    <option value="">{{ __('common.historical_data') }}</option>
                    @foreach($years as $y)
                        <option value="{{ $y }}">{{ $y }}</option>
                    @endforeach
                </select>
            </div>

            @if($search || $journalId || $year)
            <button wire:click="clearFilters" class="w-full py-3 bg-red-50 text-red-600 text-[10px] font-black uppercase tracking-widest rounded-2xl hover:bg-red-100 transition">
                {{ __('common.reset_parameters') }}
            </button>
            @endif
        </aside>

        <!-- Results Management -->
        <div class="flex-grow">
            <!-- Toolbar -->
            <div class="flex items-center justify-between mb-8 pb-4 border-b border-slate-100">
                <div class="flex items-center gap-4">
                    <span class="text-[10px] font-black uppercase tracking-widest text-slate-400">{{ __('common.order_by') }}</span>
                    <div class="flex bg-slate-100 p-1 rounded-xl">
                        @foreach(['latest' => __('common.recency'), 'most_viewed' => __('common.impact_sort'), 'most_downloaded' => __('common.popularity')] as $key => $label)
                        <button wire:click="$set('sortBy', '{{ $key }}')" class="px-4 py-1.5 rounded-lg text-[10px] font-extrabold uppercase tracking-tight transition {{ $sortBy === $key ? 'bg-white text-navy-900 shadow-sm' : 'text-slate-400 hover:text-navy-600' }}">
                            {{ $label }}
                        </button>
                        @endforeach
                    </div>
                </div>
                
                <div wire:loading class="flex items-center gap-2 text-primary-500">
                    <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"/></svg>
                    <span class="text-[10px] font-black uppercase tracking-widest">{{ __('common.processing') }}</span>
                </div>
            </div>

            <!-- Article Feed -->
            <div class="relative min-h-[600px]">
                <!-- Loading Skeleton -->
                <div wire:loading.flex class="absolute inset-0 z-10 bg-slate-50/50 backdrop-blur-[2px] flex-col gap-8 transition-all duration-300">
                    @foreach(range(1, 3) as $i)
                    <div class="bg-white dark:bg-navy-900 p-8 rounded-[2.5rem] border border-slate-100 dark:border-white/5 shadow-sm space-y-6 animate-pulse w-full">
                        <div class="flex gap-3">
                            <div class="h-6 w-24 bg-slate-100 rounded-lg"></div>
                            <div class="h-6 w-32 bg-slate-50 rounded-lg"></div>
                        </div>
                        <div class="space-y-3">
                            <div class="h-8 w-3/4 bg-slate-100 rounded-xl"></div>
                            <div class="h-4 w-1/2 bg-slate-50 rounded-lg"></div>
                        </div>
                        <div class="space-y-2">
                            <div class="h-4 w-full bg-slate-50 rounded-lg"></div>
                            <div class="h-4 w-5/6 bg-slate-50 rounded-lg"></div>
                        </div>
                    </div>
                    @endforeach
                </div>

                <!-- Main Content -->
                <div class="space-y-8" wire:loading.remove>
                    @forelse($articles as $article)
                    <article class="relative bg-white dark:bg-navy-900/50 p-8 rounded-[2.5rem] border border-slate-200 dark:border-white/5 hover:border-primary-400 group transition-all duration-500 hover:shadow-2xl hover:shadow-primary-500/5">
                        <div class="flex flex-col md:flex-row gap-10">
                            <div class="flex-grow space-y-6">
                                <div class="flex flex-wrap items-center gap-4">
                                    <span class="px-3 py-1 bg-navy-50 text-navy-900 text-[10px] font-black uppercase tracking-[2px] rounded-xl border border-navy-100">
                                        {{ $article->journal->name }}
                                    </span>
                                    @if($article->doi)
                                    <span class="bg-primary-50 text-primary-600 px-3 py-1 rounded-xl text-[10px] font-mono font-black tracking-tighter border border-primary-100/50">
                                        DOI: {{ $article->doi }}
                                    </span>
                                    @endif
                                    <span class="text-[10px] text-slate-400 font-black uppercase tracking-widest">{{ optional($article->published_at)->format('M d, Y') ?? 'N/A' }}</span>
                                </div>

                                <h2 class="text-3xl font-black text-navy-900 dark:text-white group-hover:text-primary-600 transition-colors duration-300 leading-tight tracking-tight">
                                    <a href="{{ route('articles.show', $article->slug) }}" wire:navigate>
                                        {{ $article->title }}
                                    </a>
                                </h2>

                                <div class="flex items-center gap-3">
                                    <div class="flex -space-x-2">
                                        @foreach($article->contributors->take(3) as $c)
                                            <div class="w-8 h-8 rounded-full border-2 border-white bg-slate-100 flex items-center justify-center text-[8px] font-black text-navy-400 uppercase">
                                                {{ substr($c->given_name, 0, 1) }}{{ substr($c->family_name, 0, 1) }}
                                            </div>
                                        @endforeach
                                    </div>
                                    <p class="text-[10px] font-black text-navy-400 uppercase tracking-widest truncate">
                                        @foreach($article->contributors->take(3) as $contributor)
                                            <span class="{{ $contributor->is_corresponding ? 'text-primary-600 underline decoration-primary-200 underline-offset-4' : '' }}">
                                                {{ $contributor->family_name }}
                                            </span>@if(!$loop->last), @endif
                                        @endforeach
                                        @if($article->contributors->count() > 3) <span class="text-slate-300 italic lowercase font-medium">et al.</span> @endif
                                    </p>
                                </div>

                                <p class="text-slate-500 text-sm leading-relaxed line-clamp-2 font-medium opacity-80 italic">
                                    "{{ Str::limit($article->abstract, 250) }}"
                                </p>
                            </div>

                            <!-- Side Impact Metrics -->
                            <div class="flex-shrink-0 md:w-32 flex md:flex-col justify-between md:items-end gap-6 border-l border-slate-100 pl-10 ml-auto">
                                <div class="text-right">
                                    <span class="block text-[10px] font-black uppercase tracking-[2px] text-slate-400 mb-1">{{ __('common.impact') }}</span>
                                    <span class="text-2xl font-black text-navy-900 tabular-nums">{{ number_format($article->view_count) }}</span>
                                </div>
                                <div class="text-right">
                                    <span class="block text-[10px] font-black uppercase tracking-[2px] text-slate-400 mb-1">{{ __('common.downloads') }}</span>
                                    <span class="text-2xl font-black text-navy-900 tabular-nums">{{ number_format($article->download_count) }}</span>
                                </div>
                                <a href="{{ route('articles.show', $article->slug) }}" wire:navigate class="mt-auto w-12 h-12 bg-navy-50 text-navy-950 rounded-2xl flex items-center justify-center group-hover:bg-primary-600 group-hover:text-white transition-all duration-500 shadow-xl group-hover:shadow-primary-500/20">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                </a>
                            </div>
                        </div>
                    </article>
                    @empty
                    <div class="text-center py-40 bg-white rounded-[3rem] border border-slate-100 shadow-soft">
                        <div class="w-20 h-20 bg-slate-50 rounded-[2rem] flex items-center justify-center mx-auto mb-8 border border-slate-100">
                            <svg class="h-10 w-10 text-slate-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        </div>
                        <h3 class="text-2xl font-black text-navy-900 tracking-tighter mb-2">{{ __('common.no_research_found') }}</h3>
                        <p class="text-slate-400 font-bold text-sm uppercase tracking-widest">{{ __('common.adjust_parameters') }}</p>
                    </div>
                    @endforelse
                </div>
            </div>

            <!-- Pagination -->
            <div class="mt-12">
                {{ $articles->links() }}
            </div>
        </div>
    </div>
</div>
