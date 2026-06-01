<?php

namespace Database\Seeders;

use App\Models\Announcement;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class AnnouncementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::role('super_admin')->first();

        $announcements = [
            [
                'title' => 'Pembukaan Submission untuk Volume 1 Issue 1',
                'slug' => Str::slug('Pembukaan Submission untuk Volume 1 Issue 1'),
                'content' => '<p>Kami dengan bangga mengumumkan bahwa <strong>AMV Open Science Journal</strong> kini resmi membuka submission untuk Volume 1, Issue 1!</p>

<p>Kami mengundang para peneliti, akademisi, dan praktisi dari berbagai bidang ilmu untuk mengirimkan naskah penelitian mereka. Jurnal kami berkomitmen untuk menyediakan platform publikasi yang transparan, berkualitas tinggi, dan mudah diakses.</p>

<h3>Topik yang Diterima:</h3>
<ul>
    <li>Ilmu Komputer dan Teknologi Informasi</li>
    <li>Teknik dan Rekayasa</li>
    <li>Sains dan Matematika</li>
    <li>Ilmu Sosial dan Humaniora</li>
    <li>Kesehatan dan Kedokteran</li>
</ul>

<h3>Informasi Penting:</h3>
<ul>
    <li><strong>Deadline Submission:</strong> 30 April 2026</li>
    <li><strong>Proses Review:</strong> Double-blind peer review</li>
    <li><strong>Waktu Review:</strong> 4-6 minggu</li>
    <li><strong>Biaya Publikasi:</strong> Gratis untuk 50 submission pertama</li>
</ul>

<p>Untuk informasi lebih lanjut dan panduan penulisan, silakan kunjungi halaman <a href="/submission-guidelines">Submission Guidelines</a>.</p>',
                'type' => 'news',
                'published_at' => now()->subDays(5),
                'expires_at' => now()->addMonths(3),
                'is_active' => true,
                'user_id' => $admin?->id,
            ],
            [
                'title' => 'Webinar: Best Practices in Academic Writing',
                'slug' => Str::slug('Webinar Best Practices in Academic Writing'),
                'content' => '<p>Bergabunglah dengan kami dalam webinar gratis tentang <strong>"Best Practices in Academic Writing"</strong>!</p>

<h3>Detail Acara:</h3>
<ul>
    <li><strong>Tanggal:</strong> 25 Februari 2026</li>
    <li><strong>Waktu:</strong> 14:00 - 16:00 WIB</li>
    <li><strong>Platform:</strong> Zoom Meeting</li>
    <li><strong>Pembicara:</strong> Prof. Dr. Ahmad Wijaya, M.Sc. (Editor-in-Chief)</li>
</ul>

<h3>Topik yang Akan Dibahas:</h3>
<ol>
    <li>Struktur penulisan artikel ilmiah yang efektif</li>
    <li>Tips menulis abstract yang menarik</li>
    <li>Cara menghindari plagiarisme</li>
    <li>Strategi merespons reviewer comments</li>
    <li>Q&A Session</li>
</ol>

<p><strong>Pendaftaran:</strong> Gratis dan terbuka untuk umum. Link pendaftaran akan dibagikan melalui email newsletter kami.</p>

<p>Jangan lewatkan kesempatan ini untuk meningkatkan kualitas penulisan akademik Anda!</p>',
                'type' => 'event',
                'published_at' => now()->subDays(10),
                'expires_at' => now()->addDays(9),
                'is_active' => true,
                'user_id' => $admin?->id,
            ],
            [
                'title' => 'Pembaruan Template Manuscript',
                'slug' => Str::slug('Pembaruan Template Manuscript'),
                'content' => '<p>Kami telah memperbarui template manuscript untuk meningkatkan konsistensi dan kualitas publikasi.</p>

<h3>Perubahan Utama:</h3>
<ul>
    <li>✅ Format heading yang lebih jelas dan terstruktur</li>
    <li>✅ Penambahan section "Data Availability Statement"</li>
    <li>✅ Update format sitasi sesuai APA 7th Edition</li>
    <li>✅ Perbaikan template untuk tabel dan gambar</li>
    <li>✅ Penambahan panduan untuk supplementary materials</li>
</ul>

<p><strong>Template baru dapat diunduh di halaman <a href="/submission-guidelines">Submission Guidelines</a>.</strong></p>

<p>Untuk submission yang sudah dalam proses review, Anda tidak perlu mengupdate format. Template baru hanya berlaku untuk submission baru mulai 1 Maret 2026.</p>

<p>Jika ada pertanyaan, silakan hubungi kami di <a href="mailto:editorial@amvopenscience.id">editorial@amvopenscience.id</a></p>',
                'type' => 'announcement',
                'published_at' => now()->subDays(3),
                'expires_at' => null,
                'is_active' => true,
                'user_id' => $admin?->id,
            ],
            [
                'title' => 'Call for Reviewers: Bergabung dengan Tim Editorial',
                'slug' => Str::slug('Call for Reviewers Bergabung dengan Tim Editorial'),
                'content' => '<p>AMV Open Science Journal sedang mencari <strong>reviewer ahli</strong> untuk bergabung dengan tim editorial kami!</p>

<h3>Kualifikasi:</h3>
<ul>
    <li>Minimal gelar S2/Master di bidang terkait</li>
    <li>Memiliki publikasi di jurnal internasional bereputasi</li>
    <li>Pengalaman dalam peer review (diutamakan)</li>
    <li>Komitmen untuk melakukan review dalam waktu 3-4 minggu</li>
    <li>Menguasai bahasa Inggris dengan baik</li>
</ul>

<h3>Bidang yang Dibutuhkan:</h3>
<ul>
    <li>Artificial Intelligence & Machine Learning</li>
    <li>Data Science & Big Data</li>
    <li>Cybersecurity</li>
    <li>Software Engineering</li>
    <li>Renewable Energy</li>
    <li>Biomedical Engineering</li>
</ul>

<h3>Benefit:</h3>
<ul>
    <li>✅ Sertifikat reviewer dari jurnal</li>
    <li>✅ Akses gratis ke semua artikel</li>
    <li>✅ Networking dengan peneliti internasional</li>
    <li>✅ Diskon publikasi untuk artikel Anda</li>
</ul>

<p><strong>Cara Mendaftar:</strong> Kirimkan CV dan motivation letter ke <a href="mailto:reviewer@amvopenscience.id">reviewer@amvopenscience.id</a></p>',
                'type' => 'announcement',
                'published_at' => now()->subDays(7),
                'expires_at' => now()->addMonths(1),
                'is_active' => true,
                'user_id' => $admin?->id,
            ],
            [
                'title' => 'Maintenance Sistem: 20 Februari 2026',
                'slug' => Str::slug('Maintenance Sistem 20 Februari 2026'),
                'content' => '<p>Kami akan melakukan <strong>maintenance sistem</strong> untuk meningkatkan performa dan keamanan platform.</p>

<h3>Jadwal Maintenance:</h3>
<ul>
    <li><strong>Tanggal:</strong> 20 Februari 2026</li>
    <li><strong>Waktu:</strong> 01:00 - 05:00 WIB</li>
    <li><strong>Durasi:</strong> Maksimal 4 jam</li>
</ul>

<h3>Dampak:</h3>
<ul>
    <li>❌ Website tidak dapat diakses selama maintenance</li>
    <li>❌ Submission dan review sementara tidak tersedia</li>
    <li>❌ Email notification akan tertunda</li>
</ul>

<h3>Yang Akan Diperbaiki:</h3>
<ul>
    <li>✅ Update sistem keamanan</li>
    <li>✅ Optimasi database</li>
    <li>✅ Perbaikan bug pada submission form</li>
    <li>✅ Peningkatan kecepatan loading</li>
</ul>

<p>Mohon maaf atas ketidaknyamanan ini. Kami akan mengirimkan notifikasi email ketika sistem sudah kembali normal.</p>

<p>Terima kasih atas pengertian Anda!</p>',
                'type' => 'announcement',
                'published_at' => now()->subDays(2),
                'expires_at' => now()->addDays(4),
                'is_active' => true,
                'user_id' => $admin?->id,
            ],
        ];

        foreach ($announcements as $announcement) {
            Announcement::create($announcement);
        }

        $this->command->info('✅ 5 sample announcements created successfully!');
    }
}
