<x-layouts.app title="Public Journals">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20">
        <!-- Header -->
        <div class="mb-20">
            <h1 class="text-5xl font-black text-navy-900 dark:text-white tracking-tighter mb-4">{{ __('home.hubs.title') }}</h1>
            <p class="text-slate-500 font-medium text-lg">{{ __('common.peer_reviewed_manuscripts') }}</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @foreach($journals as $journal)
                <a href="{{ route('journals.show', $journal->slug) }}" class="group relative bg-white dark:bg-navy-900 border border-slate-200 dark:border-white/5 rounded-[2.5rem] p-10 hover:border-primary-400 hover:shadow-soft transition-all duration-500 overflow-hidden">
                    @if($journal->cover_image)
                    <div class="absolute inset-0 opacity-10 group-hover:opacity-20 transition-opacity duration-700">
                        <img src="{{ asset('storage/' . $journal->cover_image) }}" alt="{{ $journal->name }}" class="w-full h-full object-cover">
                    </div>
                    @else
                    <div class="absolute top-0 right-0 p-8 opacity-5 group-hover:opacity-10 transition-opacity">
                        <svg class="w-32 h-32 text-navy-950 dark:text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2L1 21h22L12 2zm0 3.45l8.15 14.1H3.85L12 5.45z"/></svg>
                    </div>
                    @endif

                    <div class="relative z-10 space-y-6">
                        <div class="flex items-center gap-3">
                            <span class="w-3 h-3 rounded-full {{ $journal->is_active ? 'bg-emerald-500' : 'bg-slate-300' }}"></span>
                            <span class="text-[10px] font-black uppercase tracking-widest text-slate-400">{{ __('common.journal') }} {{ $journal->is_active ? 'Aktif' : 'Nonaktif' }}</span>
                        </div>

                        <h2 class="text-3xl font-black text-navy-900 dark:text-white group-hover:text-primary-600 transition leading-tight">
                            {{ $journal->name }}
                        </h2>

                        @if($journal->issn)
                        <div class="flex items-center gap-2">
                            <span class="text-[10px] font-bold text-navy-400 dark:text-navy-300 bg-navy-50 dark:bg-white/5 px-3 py-1 rounded-lg">ISSN: {{ $journal->issn }}</span>
                        </div>
                        @endif

                        <div class="pt-8 flex items-center justify-between border-t border-slate-100 dark:border-white/5">
                            <div>
                                <span class="block text-[10px] font-black uppercase tracking-widest text-slate-400">{{ __('common.manuscripts') }}</span>
                                <span class="text-xl font-bold text-navy-900 dark:text-primary-400 tabular-nums">{{ $journal->manuscripts_count ?? 0 }}</span>
                            </div>
                            <div class="w-12 h-12 bg-navy-50 dark:bg-white/5 rounded-2xl flex items-center justify-center text-navy-900 dark:text-white group-hover:bg-primary-500 group-hover:text-white transition-all duration-500">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                            </div>
                        </div>
                    </div>
                </a>
            @endforeach

            <!-- Submission CTA Card -->
            <div class="relative bg-navy-950 rounded-[2.5rem] p-10 text-white flex flex-col justify-center overflow-hidden">
                <div class="absolute inset-0 bg-gradient-to-br from-primary-600/20 to-transparent"></div>
                <div class="relative z-10 space-y-6">
                    <h2 class="text-3xl font-black tracking-tighter leading-tight">{{ __('home.cta.advance') }}</h2>
                    <p class="text-navy-300 font-medium">{{ __('home.cta.advance_text') }}</p>
                    <a href="{{ route('submission.guidelines') }}" class="inline-flex items-center gap-3 bg-white text-navy-900 px-8 py-4 rounded-2xl font-black uppercase tracking-widest text-xs hover:bg-primary-500 hover:text-white transition-all duration-300">
                        {{ __('home.cta.submit_manuscript') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
