<x-layouts.app title="Protokol Pengiriman Ilmiah">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-20">
        <!-- Guidelines Header -->
        <div class="mb-16 border-b border-slate-100 pb-12">
            <h1 class="text-4xl md:text-5xl font-black text-navy-900 tracking-tighter mb-4">Panduan Pengiriman</h1>
            <p class="text-slate-500 font-medium text-lg">Protokol dan standar terperinci untuk mendiseminasikan riset ilmiah Anda melalui jaringan peninjauan sejawat kami.</p>
        </div>

        <div class="space-y-16">
            @php
                $guidelinesContent = App\Models\Setting::get('submission_guidelines_content');
            @endphp

            @if($guidelinesContent)
                <div class="grid grid-cols-1 md:grid-cols-12 gap-16">
                    <div class="md:col-span-8">
                        <div class="prose max-w-none text-navy-900 dark:text-slate-300">
                            {!! $guidelinesContent !!}
                        </div>
                    </div>
                    <div class="md:col-span-4 space-y-8">
                        @include('pages.partials.submission-sidebar')
                    </div>
                </div>
            @else
                <!-- Original content -->
                <div class="bg-navy-950 rounded-[2.5rem] p-10 text-white shadow-2xl shadow-navy-900/40 relative overflow-hidden group">
                    <div class="absolute top-0 right-0 p-8 opacity-10">
                        <svg class="w-24 h-24" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2L1 21h22L12 2zm0 3.45l8.15 14.1H3.85L12 5.45z"/></svg>
                    </div>
                    <div class="relative z-10 flex items-start gap-6">
                        <div class="w-12 h-12 bg-primary-500 rounded-2xl flex-shrink-0 flex items-center justify-center">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <div>
                            <h3 class="text-xl font-black tracking-tight mb-2 text-white">Protokol Pengiriman Digital</h3>
                            <p class="text-navy-300 font-medium leading-relaxed">Manuskrip harus dikirimkan secara eksklusif melalui antarmuka laboratorium daring kami yang terenkripsi. Pengiriman fisik atau lampiran email tidak diterima untuk menjaga integritas data dan standar peninjauan buta (blinded review).</p>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-12 gap-16">
                    <div class="md:col-span-8 space-y-12">
                        <section class="space-y-6">
                            <h2 class="text-xs font-black uppercase tracking-[4px] text-primary-500">1. Kepatuhan Penulis</h2>
                            <div class="space-y-4">
                                @foreach([
                                    'Manuskrip mewakili riset asli yang belum pernah didiseminasikan sebelumnya atau sedang dalam proses peninjauan di tempat lain.',
                                    'Berkas pengiriman harus disediakan dalam format OpenOffice, Microsoft Word (.docx), atau RTF.',
                                    'Tautan DOI dinamis harus disediakan untuk semua referensi jika tersedia.',
                                    'Teks harus diketik dengan spasi tunggal, menggunakan tipografi profesional berukuran 12 poin (serif lebih disukai untuk teks utama).'
                                ] as $check)
                                <div class="flex gap-4 items-start group">
                                    <span class="w-6 h-6 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center text-xs font-bold shrink-0 mt-1">✓</span>
                                    <p class="text-navy-900 font-medium leading-relaxed group-hover:text-primary-600 transition">{{ $check }}</p>
                                </div>
                                @endforeach
                            </div>
                        </section>

                        <section class="space-y-6 pt-12 border-t border-slate-50">
                            <h2 class="text-xs font-black uppercase tracking-[4px] text-primary-500">2. Dasar Riset</h2>
                            <div class="bg-slate-50 rounded-[2rem] p-8 space-y-6 border border-slate-100">
                                <h3 class="text-lg font-black text-navy-900 tracking-tight">Infrastruktur Manuskrip (Templat)</h3>
                                <p class="text-slate-500 text-sm font-medium">Untuk menjaga standarisasi global, penulis harus menggunakan templat ilmiah terverifikasi kami untuk semua pengiriman.</p>
                                <a href="{{ route('template.default.download') }}" class="inline-flex items-center gap-4 bg-white border border-slate-200 px-8 py-4 rounded-2xl text-xs font-black uppercase tracking-widest text-navy-900 hover:border-primary-400 hover:shadow-soft transition-all duration-300">
                                    <svg class="w-5 h-5 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                    Unduh Templat Riset (.DOCX)
                                </a>
                            </div>
                        </section>
                    </div>

                    <div class="md:col-span-4 space-y-8">
                        @include('pages.partials.submission-sidebar')
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-layouts.app>
