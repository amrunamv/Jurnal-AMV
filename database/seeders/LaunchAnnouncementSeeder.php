<?php

namespace Database\Seeders;

use App\Models\Announcement;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class LaunchAnnouncementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::role('super_admin')->first();

        Announcement::updateOrCreate(
            ['slug' => Str::slug('amv-studio-development-resmi-luncurkan-amv-open-science')],
            [
            'title' => 'AMV Studio Development Resmi Luncurkan "AMV Open Science", Wadah Publikasi Riset Teknologi Masa Depan',
            'slug' => Str::slug('amv-studio-development-resmi-luncurkan-amv-open-science'),
            'content' => '
                <p><strong>Jakarta, Indonesia</strong> – Dalam langkah signifikan untuk memajukan ekosistem riset dan inovasi digital, <strong>AMV Studio Development</strong> hari ini secara resmi mengumumkan peluncuran <strong>AMV Open Science</strong>, sebuah platform publikasi ilmiah revolusioner yang didedikasikan untuk teknologi masa depan dan sains terbuka.</p>

                <p>Platform ini dirancang untuk menjadi jembatan antara dunia akademis dan industri, menyediakan wadah bagi para peneliti, ilmuwan data, dan pengembang teknologi untuk mempublikasikan temuan mereka secara transparan, cepat, dan terakses secara global.</p>

                <h3>Visi Menuju Masa Depan Digital</h3>
                <p>"AMV Open Science bukan sekadar jurnal elektronik biasa," ujar CEO AMV Studio Development dalam sambutan peluncurannya. "Ini adalah manifestasi dari komitmen kami untuk mendemokratisasi akses terhadap pengetahuan. Kami percaya bahwa riset berkualitas tinggi tentang kecerdasan buatan, komputasi kuantum, dan bioteknologi tidak boleh terkunci di balik paywall yang mahal. Inovasi harus bebas mengalir."</p>

                <h3>Fitur Unggulan Platform</h3>
                <ul>
                    <li><strong>Akses Terbuka Penuh (Full Open Access):</strong> Seluruh artikel dapat diakses dan diunduh secara gratis oleh siapa saja di seluruh dunia.</li>
                    <li><strong>Peer-Review yang Ketat namun Efisien:</strong> Menggunakan sistem manajemen editorial berbasis AI untuk mempercepat proses peninjauan tanpa mengorbankan kualitas ilmiah.</li>
                    <li><strong>Integrasi Teknologi:</strong> Mendukung konten interaktif, dataset yang dapat diunduh, dan visualisasi data langsung di dalam artikel.</li>
                </ul>

                <h3>Jaminan Indeksasi & Visibilitas Global</h3>
                <p>AMV Open Science berkomitmen untuk memastikan setiap artikel yang diterbitkan mendapatkan pengakuan maksimal. Kami secara aktif bekerja sama dengan basis data pengindeksan terkemuka:</p>
                <ul>
                    <li><strong>Indeksasi Instan:</strong> Google Scholar, Microsoft Academic, dan Dimensions.</li>
                    <li><strong>Identifikasi Permanen:</strong> Setiap artikel mendapatkan Digital Object Identifier (DOI) melalui <strong>Crossref</strong>, menjamin sitasi yang akurat dan permanen.</li>
                    <li><strong>Akreditasi Jurnal:</strong> Menargetkan kepatuhan penuh terhadap standar <strong>DOAJ (Directory of Open Access Journals)</strong> dan akreditasi nasional <strong>SINTA (Science and Technology Index)</strong>.</li>
                    <li><strong>Target Reputasi Puncak:</strong> Mempersiapkan jurnal-jurnal unggulan kami untuk memenuhi kriteria ketat <strong>Scopus</strong> dan <strong>Web of Science (WoS)</strong> dalam 24 bulan pertama operasional.</li>
                </ul>

                <h3>Mengundang Para Inovator</h3>
                <p>AMV Open Science kini membuka panggilan untuk makalah (Call for Papers) perdana untuk edisi khusus bertajuk <em>"The Next Horizon of Artificial Intelligence"</em>. Para peneliti diundang untuk mengirimkan manuskrip mereka melalui sistem pengajuan daring yang telah terintegrasi.</p>

                <p>Untuk informasi lebih lanjut mengenai panduan penulisan dan proses submisi, silakan kunjungi halaman <a href="/submission-guidelines">Panduan Pengiriman</a>.</p>
                
                <p><em>Tentang AMV Studio Development:</em><br>
                AMV Studio Development adalah perusahaan teknologi yang berfokus pada pengembangan perangkat lunak, solusi enterprise, dan inovasi digital. Dengan peluncuran divisi Open Science, perusahaan memperluas portofolionya ke ranah publikasi ilmiah dan R&D.</p>
            ',
            'type' => 'news',
            'published_at' => now(),
            'is_active' => true,
            'user_id' => $admin ? $admin->id : 1,
        ]);

        $this->command->info('✅ Berita peluncuran AMV Open Science berhasil dibuat!');
    }
}
