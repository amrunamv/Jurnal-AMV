<x-layouts.app>
    <x-slot name="title">{{ __('Authors') }} - {{ config('app.name') }}</x-slot>

    <div class="bg-slate-50 min-h-screen py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h1 class="text-4xl font-extrabold text-navy-900 tracking-tight mb-4">
                    {{ __('Our Authors') }}
                </h1>
                <p class="text-lg text-slate-500 max-w-2xl mx-auto">
                    {{ __('Discover the researchers contributing to our scientific community.') }}
                </p>
            </div>

            <!-- Search -->
            <div class="max-w-xl mx-auto mb-16">
                <form action="{{ route('authors.index') }}" method="GET" class="relative">
                    <input 
                        type="text" 
                        name="search" 
                        value="{{ request('search') }}"
                        placeholder="{{ __('Search authors by name or research interests...') }}" 
                        class="w-full pl-12 pr-4 py-4 bg-white border border-slate-200 rounded-2xl shadow-sm focus:ring-2 focus:ring-primary-500 focus:border-transparent transition"
                    >
                    <svg class="w-6 h-6 text-slate-400 absolute left-4 top-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </form>
            </div>

            <!-- Authors Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @forelse($authors as $author)
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100 hover:shadow-lg transition duration-300 group">
                    <div class="flex items-center gap-4 mb-6">
                        @if($author->avatar)
                            <img src="{{ \Illuminate\Support\Facades\Storage::url($author->avatar) }}" alt="{{ $author->name }}" class="w-16 h-16 rounded-full object-cover border-2 border-white shadow-md">
                        @else
                            <div class="w-16 h-16 rounded-full bg-navy-900 text-white flex items-center justify-center text-xl font-bold shadow-md">
                                {{ substr($author->first_name ?? 'A', 0, 1) }}{{ substr($author->last_name ?? 'U', 0, 1) }}
                            </div>
                        @endif
                        <div>
                            <h3 class="text-lg font-bold text-navy-900 group-hover:text-primary-600 transition">
                                {{ $author->full_name }}
                            </h3>
                            <p class="text-xs font-medium text-slate-400 uppercase tracking-widest mb-1">
                                {{ $author->affiliation->name ?? __('Unknown Affiliation') }}
                            </p>
                            @if($author->country_code)
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-slate-100 text-slate-800">
                                    {{ $author->country_code }}
                                </span>
                            @endif
                        </div>
                    </div>

                    <!-- Metrics -->
                    <div class="grid grid-cols-2 gap-4 mb-6 pt-6 border-t border-slate-50">
                        <div class="text-center">
                            <span class="block text-2xl font-black text-navy-900">{{ $author->h_index_scopus ?? '-' }}</span>
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">H-Index Scopus</span>
                        </div>
                        <div class="text-center border-l border-slate-50">
                            <span class="block text-2xl font-black text-navy-900">{{ $author->h_index_google_scholar ?? '-' }}</span>
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">H-Index Scholar</span>
                        </div>
                    </div>

                    <!-- Interests -->
                    @if(is_array($author->research_interests) && count($author->research_interests) > 0)
                    <div class="flex flex-wrap gap-2">
                        @foreach(array_slice($author->research_interests, 0, 3) as $interest)
                            <span class="px-2 py-1 bg-primary-50 text-primary-700 text-[10px] font-bold uppercase tracking-wider rounded-lg">
                                {{ $interest }}
                            </span>
                        @endforeach
                        @if(count($author->research_interests) > 3)
                            <span class="px-2 py-1 bg-slate-50 text-slate-500 text-[10px] font-bold rounded-lg">
                                +{{ count($author->research_interests) - 3 }}
                            </span>
                        @endif
                    </div>
                    @endif
                </div>
                @empty
                <div class="col-span-full text-center py-20">
                    <p class="text-slate-400 font-medium">{{ __('No authors found matching your criteria.') }}</p>
                </div>
                @endforelse
            </div>

            <!-- Pagination -->
            <div class="mt-16">
                {{ $authors->withQueryString()->links() }}
            </div>
        </div>
    </div>
</x-layouts.app>
