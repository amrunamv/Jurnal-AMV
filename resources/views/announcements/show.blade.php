<x-layouts.app :title="$announcement->title">
    <div class="bg-blue-dim dark:bg-slate-900 py-12">
        <article class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-slate-800 rounded-3xl shadow-xl overflow-hidden">
                <div class="p-8 md:p-12">
                    @php
                        $badgeClasses = match($announcement->type) {
                            'call_for_papers' => 'bg-green-accent/20 text-green-700 dark:text-green-400',
                            'policy' => 'bg-orange-100 text-orange-700 dark:text-orange-400',
                            default => 'bg-blue-100 text-blue-700 dark:text-blue-400',
                        };
                    @endphp
                    <div class="flex items-center gap-4 mb-6">
                        <span class="px-3 py-1 text-sm font-semibold rounded-full {{ $badgeClasses }}">
                            {{ str_replace('_', ' ', ucfirst($announcement->type)) }}
                        </span>
                        <span class="text-slate-500 text-sm">{{ $announcement->published_at->format('F d, Y') }}</span>
                    </div>

                    <h1 class="text-3xl md:text-4xl font-bold text-slate-900 dark:text-white mb-8">{{ $announcement->title }}</h1>

                    <div class="prose dark:prose-invert max-w-none text-slate-700 dark:text-slate-300 text-justify">
                        {!! $announcement->content !!}
                    </div>

                    <div class="mt-12 pt-8 border-t border-slate-200 dark:border-slate-700">
                        <a href="{{ route('announcements.index') }}" class="inline-flex items-center text-slate-600 dark:text-slate-400 hover:text-primary transition-colors">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                            Back to Announcements
                        </a>
                    </div>
                </div>
            </div>
        </article>
    </div>
</x-layouts.app>
