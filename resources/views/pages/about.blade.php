<x-layouts.app title="About the Journal">
    <div class="bg-blue-dim dark:bg-slate-900 py-12">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-slate-800 rounded-3xl shadow-xl overflow-hidden">
                <div class="p-8 md:p-12">
                    <h1 class="text-3xl md:text-4xl font-bold text-slate-900 dark:text-white mb-8 border-b border-slate-200 dark:border-slate-700 pb-4">
                        Tentang Jurnal
                    </h1>
                    
                    <div class="prose dark:prose-invert max-w-none text-slate-700 dark:text-slate-300">
                        @php
                            $aboutContent = App\Models\Setting::get('about_content');
                        @endphp

                        @if($aboutContent)
                            {!! $aboutContent !!}
                        @else
                            <section class="mb-8">
                                <h3 class="text-2xl font-bold text-primary mb-4">Fokus & Ruang Lingkup</h3>
                                <p>AMV Open Science adalah jurnal akses terbuka yang ditinjau oleh mitra bestari dan didedikasikan untuk kemajuan sains dan teknologi. Kami menerbitkan artikel riset asli, makalah tinjauan, dan studi kasus dalam berbagai bidang, termasuk:</p>
                                <ul class="list-disc pl-5 space-y-2 mt-4">
                                    <li>Ilmu Komputer dan Teknologi Informasi</li>
                                    <li>Teknik dan Ilmu Terapan</li>
                                    <li>Matematika dan Statistik</li>
                                    <li>Ilmu Fisika dan Ilmu Hayati</li>
                                </ul>
                            </section>

                            <section class="mb-8">
                                <h3 class="text-2xl font-bold text-primary mb-4">Frekuensi Publikasi</h3>
                                <p>Jurnal ini diterbitkan dua kali setahun pada bulan <strong>Juni</strong> dan <strong>Desember</strong>. Artikel diterbitkan secara daring segera setelah diterima dan diproduksi akhir.</p>
                            </section>

                            <section class="mb-8">
                                <h3 class="text-2xl font-bold text-primary mb-4">Kebijakan Akses Terbuka</h3>
                                <p>AMV Open Science menyediakan akses terbuka langsung ke kontennya berdasarkan prinsip bahwa membuat riset tersedia secara bebas bagi publik mendukung pertukaran pengetahuan global yang lebih besar.</p>
                            </section>

                            <section>
                                <h3 class="text-2xl font-bold text-primary mb-4">Sejarah Jurnal</h3>
                                <p>Didirikan pada tahun 2024, AMV Open Science bertujuan untuk menjembatani kesenjangan antara riset akademik dan aplikasi praktis, mendorong inovasi dan kolaborasi lintas disiplin.</p>
                            </section>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
