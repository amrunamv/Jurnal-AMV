<div class="min-h-screen bg-slate-50 dark:bg-navy-950 flex flex-col font-sans transition-colors duration-500">
    <!-- Analysis Protocol Header -->
    <header class="bg-navy-950 text-white border-b border-white/5 sticky top-0 z-[60] shadow-2xl">
        <div class="max-w-full mx-auto px-6 py-4 flex items-center justify-between">
            <div class="flex items-center gap-6">
                <div class="h-10 w-10 bg-primary-500 rounded-xl flex items-center justify-center shadow-lg shadow-primary-500/20">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/></svg>
                </div>
                <div>
                    <h1 class="text-sm font-black uppercase tracking-[3px] text-white">The Review Lab</h1>
                    <p class="text-[10px] font-bold text-navy-400 mt-0.5">{{ $review->manuscript->journal->name }} • Protocol v{{ $review->round }}.0</p>
                </div>
            </div>

            <div class="flex items-center gap-8">
                <div class="hidden md:flex flex-col items-end">
                    <span class="text-[10px] font-black uppercase tracking-widest text-navy-500">Analysis Deadline</span>
                    <span class="text-xs font-bold {{ now()->diffInDays($review->due_date, false) < 3 ? 'text-red-400' : 'text-primary-400' }}">
                        {{ $review->due_date?->format('F d, Y') }} ({{ now()->diffInDays($review->due_date, false) }} Days Remaining)
                    </span>
                </div>
                <div class="h-8 w-px bg-white/10"></div>
                @if($review->status === 'in_progress')
                    <span class="px-4 py-1.5 bg-primary-500 text-white text-[10px] font-black uppercase tracking-widest rounded-lg shadow-lg shadow-primary-500/10">Active Evaluation</span>
                @endif
            </div>
        </div>
    </header>

    @if($review->status === 'pending')
        <!-- Invitation Protocol -->
        <main class="flex-grow flex items-center justify-center p-6">
            <div class="max-w-2xl w-full bg-white dark:bg-navy-900 rounded-[3rem] shadow-2xl overflow-hidden border border-slate-100 dark:border-white/5">
                <div class="bg-navy-950 p-12 text-center relative">
                    <div class="absolute inset-0 bg-gradient-to-br from-primary-600/20 to-transparent"></div>
                    <div class="relative z-10">
                        <div class="w-24 h-24 bg-white/10 backdrop-blur-md rounded-[2rem] flex items-center justify-center mx-auto mb-8 border border-white/10">
                            <svg class="h-10 w-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/></svg>
                        </div>
                        <h2 class="text-3xl font-black text-white tracking-tighter mb-4">Scientific Evaluation Request</h2>
                        <p class="text-navy-300 font-medium">Invitation to participate in the peer-review cycle.</p>
                    </div>
                </div>

                <div class="p-12 space-y-10">
                    <div class="space-y-4">
                        <span class="text-[10px] font-black uppercase tracking-widest text-slate-400">Target Manuscript</span>
                        <h3 class="text-2xl font-black text-navy-900 dark:text-white leading-tight">{{ $review->manuscript->title }}</h3>
                    </div>

                    <div class="grid grid-cols-2 gap-8 py-8 border-y border-slate-50">
                        <div>
                            <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Journal Origin</span>
                            <span class="text-sm font-black text-navy-900">{{ $review->manuscript->journal->name }}</span>
                        </div>
                        <div>
                            <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Response Required</span>
                            <span class="text-sm font-black text-navy-900">{{ $review->due_date?->format('M d, Y') }}</span>
                        </div>
                    </div>

                    <div class="flex gap-4">
                        <button wire:click="acceptInvitation" class="flex-grow bg-navy-950 text-white rounded-2xl py-5 font-black uppercase tracking-widest text-xs hover:bg-primary-600 transition shadow-xl shadow-navy-100">Accept Protocol</button>
                        <button wire:click="$set('showDeclineModal', true)" class="px-10 bg-slate-50 text-navy-400 rounded-2xl font-black uppercase tracking-widest text-xs border border-slate-100 hover:bg-red-50 hover:text-red-500 hover:border-red-100 transition">Decline</button>
                    </div>
                </div>
            </div>
        </main>
    @else
        <!-- Lab Environment (Split View) -->
        <main class="flex-grow flex h-[calc(100vh-80px)] overflow-hidden">
            <!-- Left Panel: The Manuscript Repository -->
            <div class="w-1/2 bg-slate-200 border-r border-slate-300 relative flex flex-col">
                <div class="bg-white/50 backdrop-blur-sm px-6 py-2 border-b border-slate-300 flex items-center justify-between">
                    <span class="text-[10px] font-black uppercase tracking-widest text-slate-500">Primary Manuscript.pdf</span>
                    <div class="flex gap-2">
                        <button class="p-2 text-slate-400 hover:bg-white rounded-lg transition"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg></button>
                        <button class="p-2 text-slate-400 hover:bg-white rounded-lg transition"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/></svg></button>
                    </div>
                </div>
                <div class="flex-grow bg-slate-400 relative overflow-hidden shadow-inner">
                    <iframe 
                        src="{{ route('reviews.pdf', $review) }}" 
                        class="w-full h-full"
                        title="Analysis Subject"
                    ></iframe>
                </div>
            </div>

            <!-- Right Panel: Evaluation Interface -->
            <div class="w-1/2 flex flex-col bg-white dark:bg-navy-900 overflow-y-auto">
                <div class="p-12 max-w-2xl mx-auto w-full space-y-16">
                    <!-- Progress Step -->
                    <div class="space-y-12">
                        <div class="flex items-center gap-4">
                            <h2 class="text-xs font-black uppercase tracking-[4px] text-primary-500 m-0">Evaluation Rubric</h2>
                            <div class="h-px bg-slate-100 flex-grow mt-1"></div>
                        </div>

                        <div class="space-y-8">
                            @foreach([
                                'originality' => ['Novelty & Innovation', 'Assess the uniqueness of the scientific findings.'],
                                'methodology' => ['Technical Integrity', 'Evaluate the experimental design and statistical rigor.'],
                                'clarity' => ['Communicative Precision', 'Rate the organization and linguistic clarity.'],
                                'references' => ['Scholarly Context', 'Quality and relevance of the cited literature.'],
                                'contribution' => ['Scientific Impact', 'Potential impact on the current state of the art.']
                            ] as $key => $data)
                                <div class="space-y-4">
                                    <div class="flex justify-between items-end">
                                        <div>
                                            <label class="block text-sm font-black text-navy-900 dark:text-white tracking-tight">{{ $data[0] }}</label>
                                            <p class="text-[10px] text-slate-400 font-medium">{{ $data[1] }}</p>
                                        </div>
                                        <span class="text-lg font-black text-primary-500 tabular-nums">{{ $rubricScores[$key] ?? 0 }}/5</span>
                                    </div>
                                    <div class="grid grid-cols-5 gap-3">
                                        @for($i = 1; $i <= 5; $i++)
                                            <button 
                                                wire:click="setRubricScore('{{ $key }}', {{ $i }})"
                                                class="h-12 rounded-xl border-2 transition-all duration-300 {{ ($rubricScores[$key] ?? 0) >= $i ? 'bg-primary-500 border-primary-500 shadow-lg shadow-primary-500/20 text-white' : 'bg-white border-slate-100 text-slate-300 hover:border-primary-200 hover:text-primary-400' }}"
                                            >
                                                <span class="text-xs font-black">{{ $i }}</span>
                                            </button>
                                        @endfor
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Global Recommendation -->
                    <div class="space-y-8">
                        <div class="flex items-center gap-4">
                            <h2 class="text-xs font-black uppercase tracking-[4px] text-primary-500 m-0">Final Determination</h2>
                            <div class="h-px bg-slate-100 flex-grow mt-1"></div>
                        </div>

                        <div class="space-y-8">
                            <div class="bg-navy-50 rounded-[2rem] p-8 border border-navy-100">
                                <label class="block text-[10px] font-black uppercase tracking-widest text-navy-400 mb-6">Overall Technical Score (1-10)</label>
                                <input type="range" wire:model.live="overallScore" min="1" max="10" class="w-full h-1.5 bg-navy-200 rounded-full appearance-none cursor-pointer accent-primary-500">
                                <div class="flex justify-between mt-4">
                                    <span class="text-[8px] font-black text-navy-300 uppercase">Insufficient</span>
                                    <span class="text-4xl font-black text-navy-900 dark:text-white tabular-nums">{{ $overallScore ?: '-' }}</span>
                                    <span class="text-[8px] font-black text-navy-300 uppercase">State of Art</span>
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                @foreach([
                                    'accept' => ['Direct Adoption', 'emerald'],
                                    'minor_revision' => ['Minor Optimization', 'blue'],
                                    'major_revision' => ['Major Reconstruction', 'amber'],
                                    'reject' => ['Rejection Protocol', 'red']
                                ] as $value => $meta)
                                    <label class="relative cursor-pointer group">
                                        <input type="radio" wire:model="recommendation" value="{{ $value }}" class="sr-only peer">
                                        <div class="p-6 rounded-2xl border-2 border-slate-50 bg-slate-50 peer-checked:border-{{ $meta[1] }}-500 peer-checked:bg-{{ $meta[1] }}-50 transition-all duration-300 group-hover:bg-slate-100">
                                            <span class="block text-[10px] font-black uppercase tracking-widest {{ $recommendation === $value ? 'text-' . $meta[1] . '-600' : 'text-slate-400' }}">{{ $meta[0] }}</span>
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <!-- Narrative Analysis -->
                    <div class="space-y-12">
                        <div class="flex items-center gap-4">
                            <h2 class="text-xs font-black uppercase tracking-[4px] text-primary-500 m-0">Narration & Critique</h2>
                            <div class="h-px bg-slate-100 flex-grow mt-1"></div>
                        </div>

                        <div class="space-y-10">
                            <div class="space-y-4">
                                <label class="block text-xs font-black text-navy-900 dark:text-white uppercase tracking-widest">Confidential Editor Notes</label>
                                <textarea wire:model="commentsToEditor" rows="4" class="w-full bg-slate-50 dark:bg-navy-950 border border-slate-100 dark:border-white/5 rounded-2xl p-6 text-sm font-medium focus:ring-2 focus:ring-primary-500 transition-all dark:text-white" placeholder="Enter findings restricted for editorial staff..."></textarea>
                            </div>
                            <div class="space-y-4">
                                <label class="block text-xs font-black text-navy-900 uppercase tracking-widest">Formal Author Critique</label>
                                <textarea wire:model="commentsToAuthor" rows="8" class="w-full bg-slate-50 border border-slate-100 rounded-2xl p-6 text-sm font-medium focus:ring-2 focus:ring-primary-500 transition-all" placeholder="Document constructive analysis for the researchers..."></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Submission Loop -->
                    <div class="flex gap-4 pt-12">
                        <button wire:click="saveDraft" class="flex-grow bg-slate-100 text-navy-900 rounded-2xl py-5 font-black uppercase tracking-widest text-xs hover:bg-slate-200 transition border border-slate-200">Save Analytical Draft</button>
                        <button 
                            wire:click="submitReview" 
                            wire:confirm="Initiate final transmission of evaluation? This cannot be reversed."
                            class="flex-grow bg-navy-950 text-white rounded-2xl py-5 font-black uppercase tracking-widest text-xs hover:bg-primary-600 transition shadow-xl shadow-navy-100"
                        >
                            Finalize Protocol
                        </button>
                    </div>
                </div>
            </div>
        </main>
    @endif

    <!-- Decline Invitation Modal -->
    @if($showDeclineModal)
        <div class="fixed inset-0 z-[100] flex items-center justify-center p-4">
            <div class="fixed inset-0 bg-navy-950/80 backdrop-blur-md" wire:click="$set('showDeclineModal', false)"></div>
            <div class="relative bg-white rounded-[3rem] shadow-2xl max-w-md w-full p-12 overflow-hidden border border-white/10">
                <h3 class="text-2xl font-black text-navy-900 tracking-tighter mb-4">Decline Invitation</h3>
                <p class="text-slate-500 text-sm font-medium mb-8">Please provide your analytical reasoning for declining this peer-review request.</p>
                
                <textarea wire:model="declineReason" rows="4" class="w-full bg-slate-50 border border-slate-100 rounded-2xl p-6 text-sm font-medium mb-8 focus:ring-2 focus:ring-red-500 transition-all" placeholder="Reasoning..."></textarea>
                
                <div class="flex flex-col gap-3">
                    <button wire:click="declineInvitation" class="w-full bg-red-600 text-white py-4 rounded-xl font-black uppercase tracking-widest text-[10px] hover:bg-red-700 transition shadow-lg shadow-red-100">Confirm Dejection</button>
                    <button wire:click="$set('showDeclineModal', false)" class="w-full py-4 text-slate-400 font-bold text-[10px] uppercase tracking-widest hover:text-navy-900 transition">Cancel</button>
                </div>
            </div>
        </div>
    @endif
</div>
