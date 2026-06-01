<x-filament-widgets::widget>
    <x-filament::section>
        <div class="space-y-6">
            <div class="flex items-center justify-between">
                <h3 class="text-sm font-black uppercase tracking-[2px] text-slate-400">Research Lifecycle Protocol</h3>
                <span class="px-3 py-1 bg-primary-500 text-white text-[10px] font-black uppercase tracking-widest rounded-lg shadow-lg shadow-primary-500/10">
                    Current Phase: {{ strtoupper(str_replace('_', ' ', $record->status)) }}
                </span>
            </div>

            <div class="relative pt-8 pb-4">
                {{-- Progress Bar Background --}}
                <div class="absolute top-1/2 left-0 w-full h-0.5 bg-slate-100 dark:bg-white/5 -translate-y-1/2 rounded-full"></div>
                
                {{-- Active Progress --}}
                <div 
                    class="absolute top-1/2 left-0 h-0.5 bg-primary-500 -translate-y-1/2 rounded-full transition-all duration-1000 ease-out"
                    style="width: {{ $this->getStatusProgress() }}%"
                ></div>

                {{-- Status Nodes --}}
                <div class="relative flex justify-between items-center z-10">
                    @php $statuses = $this->getStatuses(); @endphp
                    @foreach($statuses as $statusKey => $data)
                        @php 
                            $isActive = $record->status === $statusKey;
                            $isCompleted = array_search($record->status, array_keys($statuses)) > array_search($statusKey, array_keys($statuses));
                        @endphp
                        <div class="flex flex-col items-center group">
                            <div @class([
                                'w-10 h-10 rounded-xl flex items-center justify-center transition-all duration-500 border-2',
                                'bg-primary-500 border-primary-500 text-white shadow-xl shadow-primary-500/20' => $isActive,
                                'bg-white dark:bg-navy-900 border-primary-500 text-primary-500' => $isCompleted,
                                'bg-white dark:bg-navy-950 border-slate-100 dark:border-white/5 text-slate-300' => !$isActive && !$isCompleted,
                            ])>
                                <x-filament::icon
                                    icon="{{ $data['icon'] }}"
                                    class="w-5 h-5"
                                />
                            </div>
                            
                            <div class="absolute mt-14 opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none">
                                <div class="bg-navy-950 text-white text-[10px] font-black uppercase tracking-widest px-3 py-1.5 rounded-lg shadow-2xl whitespace-nowrap">
                                    {{ $data['label'] }}
                                </div>
                            </div>

                            @if($isActive)
                                <div class="mt-4 text-center">
                                    <span class="text-[9px] font-black uppercase tracking-widest text-primary-500 animate-pulse">In Progress</span>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
            
            <div class="pt-4 border-t border-slate-50 dark:border-white/5">
                <p class="text-[11px] text-slate-500 font-medium italic">
                    * The Research Lifecycle represents the standard scientific vetting process from initial draft to global repository archival.
                </p>
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
