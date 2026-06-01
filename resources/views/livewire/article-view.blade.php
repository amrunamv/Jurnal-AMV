<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
    <!-- Research Header -->
    <div class="mb-16 border-b border-slate-100 pb-12">
        <div class="flex flex-wrap items-center gap-4 mb-8">
            <span class="px-3 py-1 bg-navy-950 text-white text-[10px] font-black uppercase tracking-[2px] rounded-lg">
                {{ $article->journal->name }}
            </span>
            @if($article->doi)
            <span class="text-[10px] font-bold text-primary-600 bg-primary-50 px-3 py-1 rounded-lg font-mono tracking-tighter">
                DOI: {{ $article->doi }}
            </span>
            @endif
            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest pl-4 border-l border-slate-200">
                Published {{ $article->published_at?->format('F d, Y') }}
            </span>
        </div>

        <h1 class="text-4xl md:text-5xl font-extrabold text-navy-900 dark:text-white leading-[1.1] mb-10 tracking-tight max-w-5xl">
            {{ $article->title }}
        </h1>

        <div class="flex flex-wrap gap-6 items-center">
            @foreach($article->contributors as $contributor)
                <div class="flex items-center gap-3 bg-slate-50 dark:bg-navy-900 border border-slate-100 dark:border-white/5 pr-6 pl-1.5 py-1.5 rounded-2xl group transition hover:border-primary-200">
                    <div class="w-10 h-10 rounded-xl bg-white border border-slate-200 flex items-center justify-center text-xs font-black text-navy-900 shadow-sm group-hover:bg-primary-500 group-hover:text-white transition">
                        {{ substr($contributor->given_name, 0, 1) }}{{ substr($contributor->family_name, 0, 1) }}
                    </div>
                    <div>
                        <span class="block text-sm font-bold text-navy-900 leading-tight">
                            {{ $contributor->full_name }}
                            @if($contributor->orcid)
                            <a href="https://orcid.org/{{ $contributor->orcid }}" target="_blank" class="inline-block ml-1 align-middle opacity-50 hover:opacity-100 transition">
                                <svg class="w-3.5 h-3.5 fill-[#A6CE39]" viewBox="0 0 24 24"><path d="M12 0C5.372 0 0 5.372 0 12s5.372 12 12 12 12-5.372 12-12S18.628 0 12 0zM7.369 4.378c.541 0 .942.428.942.946s-.401.946-.942.946-.942-.428-.942-.946.401-.946.942-.946zm.745 3.327h1.252v10.364H8.114V7.705zm14.492-4.14a4.8 4.8 0 01-1.42 1.423c-.454.26-.94.41-1.45.45-.512.04-1.037.062-1.57.062H9.366v10.364h1.252v-2.32H12.3c.72 0 1.344-.14 1.868-.42.524-.28.932-.676 1.224-1.188.29-.512.438-1.12.438-1.82 0-1.04-.31-1.896-.924-2.57-.61-.676-1.524-1.1-2.73-1.272.356-.1.68-.24.972-.42.292-.18.536-.4.732-.66.196-.26.348-.55.454-.87.108-.32.164-.67.164-1.05 0-1-.312-1.826-.938-2.478-.626-.65-1.554-1-2.784-1.042h-1.636V4.49zm-8.23 6.942h3.18c.63 0 1.12.16 1.474.48.35.32.53.76.53 1.32 0 .56-.18 1-.53 1.32-.354.32-.844.48-1.474.48h-3.18v-3.6zM12.3 4.49c.81 0 1.436.21 1.88.63.44.42.66.97.66 1.65 0 .68-.22 1.23-.66 1.65-.444.42-1.07.63-1.88.63h-2.93V4.49z"/></svg>
                            </a>
                            @endif
                        </span>
                        @if($contributor->affiliation)
                        <span class="block text-[10px] text-slate-400 font-medium tracking-tight whitespace-nowrap overflow-hidden text-ellipsis max-w-[120px]">
                            {{ $contributor->affiliation->name }}
                        </span>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-16 relative">
        <!-- Main Body: Reading Experience -->
        <div class="lg:col-span-8 space-y-16">
            <section class="prose prose-slate dark:prose-invert prose-lg max-w-none">
                <div class="flex items-center gap-4 mb-8">
                    <h2 class="text-xs font-black uppercase tracking-[4px] text-primary-500 m-0">Abstract</h2>
                    <div class="h-px bg-slate-100 flex-grow mt-1"></div>
                </div>
                <div class="text-navy-800 leading-[1.8] font-medium text-lg border-l-4 border-navy-950 pl-8 ml-2">
                    {{ $article->abstract }}
                </div>
            </section>

            @if($article->body)
            <section class="prose prose-slate prose-lg max-w-none">
                <div class="flex items-center gap-4 mb-8">
                    <h2 class="text-xs font-black uppercase tracking-[4px] text-primary-500 m-0">Full Manuscript</h2>
                    <div class="h-px bg-slate-100 flex-grow mt-1"></div>
                </div>
                <div class="text-navy-900 leading-relaxed font-normal">
                    {!! $article->body !!}
                </div>
            </section>
            @endif

            <!-- References Section -->
            @if($article->citations && $article->citations->count() > 0)
            <section class="pt-16 border-t border-slate-100">
                <div class="flex items-center gap-4 mb-12">
                    <h2 class="text-xs font-black uppercase tracking-[4px] text-primary-500 m-0">Scientific References</h2>
                    <div class="h-px bg-slate-100 flex-grow mt-1"></div>
                </div>
                <div class="space-y-6">
                    @foreach($article->citations as $citation)
                    <div class="flex gap-6 group">
                        <span class="text-xs font-black text-slate-300 group-hover:text-primary-500 transition">[{{ $loop->iteration }}]</span>
                        <p class="text-xs text-slate-500 leading-relaxed group-hover:text-navy-900 transition font-medium">
                            {{ $citation->raw_text }}
                            @if($citation->doi)
                                <a href="https://doi.org/{{ $citation->doi }}" target="_blank" class="ml-2 font-mono font-bold text-primary-600 hover:underline tracking-tighter">DOI:{{ $citation->doi }}</a>
                            @endif
                        </p>
                    </div>
                    @endforeach
                </div>
            </section>
            @endif
        </div>

        <!-- Sidebar: Laboratory & Metrics -->
        <div class="lg:col-span-4 lg:sticky lg:top-24 h-fit space-y-8">
            <!-- Impact Radar -->
            <div class="bg-navy-950 rounded-[2.5rem] p-10 text-white shadow-2xl shadow-navy-900/40 relative overflow-hidden group">
                <div class="absolute top-0 right-0 p-6 opacity-10 group-hover:rotate-12 transition duration-700">
                    <svg class="w-24 h-24" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2L1 21h22L12 2zm0 3.45l8.15 14.1H3.85L12 5.45z"/></svg>
                </div>
                <h3 class="text-xs font-black uppercase tracking-[3px] text-navy-400 mb-8">Scientific Impact</h3>
                <div class="grid grid-cols-2 gap-8 relative z-10">
                    <div>
                        <span class="block text-[10px] font-bold text-navy-500 uppercase tracking-widest mb-1">Total Reads</span>
                        <span class="text-3xl font-black tabular-nums">{{ number_format($article->view_count) }}</span>
                    </div>
                    <div>
                        <span class="block text-[10px] font-bold text-navy-500 uppercase tracking-widest mb-1">Indexations</span>
                        <span class="text-3xl font-black tabular-nums">{{ number_format($article->download_count) }}</span>
                    </div>
                </div>
            </div>

            <!-- Laboratory Actions -->
            <div class="bg-white dark:bg-navy-900 p-8 border border-slate-200 dark:border-white/5 shadow-soft space-y-4">
                <button wire:click="downloadPdf" class="w-full group bg-navy-900 text-white rounded-2xl py-5 px-6 font-bold text-sm tracking-tight flex items-center justify-between hover:bg-navy-800 transition shadow-lg shadow-navy-100">
                    <span>Retrieve Full PDF</span>
                    <svg class="w-5 h-5 group-hover:translate-y-0.5 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                </button>
                <button wire:click="toggleCitationModal" class="w-full bg-slate-50 text-navy-900 rounded-2xl py-5 px-6 font-bold text-sm tracking-tight border border-slate-100 flex items-center justify-between hover:bg-slate-100 transition">
                    <span>Generate Citation</span>
                    <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                </button>
            </div>

            <!-- Metadata Box -->
            <div class="px-8 space-y-6">
                <div class="space-y-2">
                    <h4 class="text-[10px] font-black uppercase tracking-widest text-slate-400">Publication Context</h4>
                    @if($article->issue)
                    <p class="text-xs font-bold text-navy-900">
                        Volume {{ $article->issue->volume?->number }}, Issue {{ $article->issue->number }}
                    </p>
                    @endif
                    @if($article->page_range)
                    <p class="text-xs font-bold text-navy-900 leading-tight">Pages {{ $article->page_range }}</p>
                    @endif
                </div>

                <div class="space-y-4">
                    <h4 class="text-[10px] font-black uppercase tracking-widest text-slate-400">Collaborate & Share</h4>
                    <div class="flex gap-4">
                        <button class="w-10 h-10 bg-slate-50 rounded-xl flex items-center justify-center text-slate-400 hover:bg-primary-50 hover:text-primary-600 transition border border-slate-100">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/></svg>
                        </button>
                        <button class="w-10 h-10 bg-slate-50 rounded-xl flex items-center justify-center text-slate-400 hover:bg-primary-50 hover:text-primary-600 transition border border-slate-100">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Enhanced Citation Modal -->
    @if($showCitationModal)
    <div class="fixed inset-0 z-[100] overflow-y-auto" x-data x-on:keydown.escape.window="$wire.toggleCitationModal()">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-navy-950/80 backdrop-blur-md" wire:click="toggleCitationModal"></div>
            <div class="relative bg-white dark:bg-navy-900 rounded-[3rem] shadow-2xl max-w-2xl w-full p-12 z-10 border border-slate-100 dark:border-white/10">
                <div class="flex items-center justify-between mb-12">
                    <h3 class="text-3xl font-black text-navy-900 tracking-tighter">Academic Citation</h3>
                    <button wire:click="toggleCitationModal" class="w-12 h-12 flex items-center justify-center bg-slate-50 text-slate-400 rounded-2xl hover:bg-red-50 hover:text-red-500 transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <div class="flex flex-wrap gap-2 mb-10">
                    @foreach(['apa' => 'APA 7th', 'mla' => 'MLA 9th', 'harvard' => 'Harvard', 'bibtex' => 'BibTeX'] as $format => $label)
                        <button wire:click="$set('citationFormat', '{{ $format }}')" class="px-6 py-3 text-xs font-black uppercase tracking-widest rounded-xl transition {{ $citationFormat === $format ? 'bg-primary-500 text-white shadow-lg shadow-primary-200' : 'bg-slate-50 text-slate-500 hover:bg-slate-100 hover:text-navy-900' }}">
                            {{ $label }}
                        </button>
                    @endforeach
                </div>

                <div class="bg-navy-50 rounded-3xl p-8 mb-10 border border-navy-100 relative group">
                    <pre class="text-sm font-bold text-navy-900 whitespace-pre-wrap leading-relaxed">{{ $citation }}</pre>
                    <div class="absolute inset-0 bg-primary-500/0 group-hover:bg-primary-500/5 transition pointer-events-none rounded-3xl"></div>
                </div>

                <button 
                    x-data="{ copied: false }"
                    x-on:click="navigator.clipboard.writeText(`{{ $citation }}`); copied = true; setTimeout(() => copied = false, 2000)"
                    class="w-full flex items-center justify-center gap-4 py-5 bg-navy-900 text-white font-black uppercase tracking-widest text-xs rounded-2xl shadow-xl hover:bg-navy-800 transition transform active:scale-95"
                >
                    <span x-show="!copied">Copy Protocol</span>
                    <span x-show="copied" x-cloak class="flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        Copied to Clipboard
                    </span>
                </button>
            </div>
        </div>
    </div>
    @endif

    @push('scripts')
    <script>
        document.addEventListener('livewire:init', () => {
            Livewire.on('download', (event) => {
                window.open(event.url, '_blank');
            });
        });
    </script>
    @endpush
</div>
