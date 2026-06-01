<div class="sticky top-24 space-y-8">
    <div class="bg-white border border-slate-200 rounded-[2.5rem] p-8 space-y-6 shadow-soft">
        <h3 class="text-xs font-black uppercase tracking-widest text-navy-400 border-b border-slate-200 pb-4">Alur Onboarding</h3>
        <div class="space-y-4">
            @foreach([
                '1' => 'Registrasi Sistem',
                '2' => 'Penautan ORCID (Opsional)',
                '3' => 'Kirim Meta Manuskrip',
                '4' => 'Unggah File & Verifikasi'
            ] as $step => $label)
            <div class="flex items-center gap-4">
                <span class="w-8 h-8 rounded-xl bg-navy-50 text-navy-900 flex items-center justify-center text-[10px] font-black">{{ $step }}</span>
                <span class="text-xs font-bold text-navy-900">{{ $label }}</span>
            </div>
            @endforeach
        </div>
        <div class="pt-4">
            <a href="{{ route('filament.console.auth.register') }}" class="w-full flex items-center justify-center gap-3 bg-navy-900 text-white py-4 rounded-2xl text-[10px] font-black uppercase tracking-widest hover:bg-primary-500 transition shadow-lg shadow-navy-100">
                Mulai Pengiriman
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
            </a>
        </div>
    </div>

    <div class="px-6 text-center">
        <p class="text-[10px] text-slate-400 font-medium leading-relaxed">Proses peninjauan mitra bestari anonim ganda kami biasanya memakan waktu 4-6 minggu untuk protokol keputusan pertama.</p>
    </div>
</div>
