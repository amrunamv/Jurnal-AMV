<x-layouts.app title="Announcements">
    <div class="bg-blue-dim dark:bg-slate-900 py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h1 class="text-3xl font-bold text-slate-900 dark:text-white mb-4">Announcements</h1>
                <p class="text-lg text-slate-600 dark:text-slate-400">Latest news, updates, and calls for papers.</p>
            </div>

            <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                @foreach($announcements as $announcement)
                    <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-lg overflow-hidden hover:shadow-xl transition-shadow duration-300">
                        <div class="p-6">
                            <div class="flex items-center justify-between mb-4">
                                <span class="px-3 py-1 text-xs font-semibold rounded-full 
                                    @if($announcement->type === 'call_for_papers') bg-green-accent/20 text-green-700 dark:text-green-400
                                    @elseif($announcement->type === 'policy') bg-orange-100 text-orange-700 dark:text-orange-400
                                    @else bg-blue-100 text-blue-700 dark:text-blue-400 @endif">
                                    {{ str_replace('_', ' ', ucfirst($announcement->type)) }}
                                </span>
                                <span class="text-xs text-slate-500">{{ $announcement->published_at->format('M d, Y') }}</span>
                            </div>
                            <h2 class="text-xl font-bold text-slate-900 dark:text-white mb-3 line-clamp-2">
                                <a href="{{ route('announcements.show', $announcement->slug) }}" class="hover:text-primary transition-colors">
                                    {{ $announcement->title }}
                                </a>
                            </h2>
                            <div class="text-slate-600 dark:text-slate-400 text-sm line-clamp-3 mb-4">
                                {!! Str::limit(strip_tags($announcement->content), 150) !!}
                            </div>
                            <a href="{{ route('announcements.show', $announcement->slug) }}" class="text-primary font-medium hover:text-orange-700 text-sm flex items-center gap-1">
                                Read More
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-8">
                {{ $announcements->links() }}
            </div>
        </div>
    </div>
</x-layouts.app>
