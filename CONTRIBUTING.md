# Panduan Berkontribusi (Contributing Guide)

Terima kasih atas ketertarikan Anda untuk berkontribusi pada proyek **AMV Open Science (Jurnal-AMV)**! 🎉

Proyek ini dibangun secara *open-source* dengan visi menjadi alternatif modern untuk sistem manajemen jurnal (seperti OJS). Kami sangat menghargai kontribusi dari komunitas, baik itu berupa kode, desain, pelaporan *bug*, maupun penyempurnaan dokumentasi.

Berikut adalah panduan langkah demi langkah untuk mulai berkontribusi.

## 1. Menemukan Sesuatu untuk Dikerjakan
- Cek bagian [Issues](https://github.com/amrunamv/Jurnal-AMV/issues) di GitHub kami.
- Cari *issue* dengan label `good first issue` atau `help wanted` jika Anda baru bergabung.
- Jika Anda memiliki ide baru atau menemukan *bug*, silakan buat *issue* baru sebelum mulai menulis kode agar kita bisa mendiskusikannya terlebih dahulu.

## 2. Cara Mengirimkan Perubahan Kode (Pull Request)

1. **Fork Repositori Ini**
   Klik tombol `Fork` di pojok kanan atas repositori ini untuk membuat salinan proyek ke akun GitHub Anda.

2. **Clone Repositori Fork Anda**
   ```bash
   git clone https://github.com/USERNAME-ANDA/Jurnal-AMV.git
   cd Jurnal-AMV
   ```

3. **Buat Branch Baru**
   Selalu buat *branch* baru dari `main` untuk setiap fitur atau perbaikan *bug*.
   ```bash
   git checkout -b fitur-baru-anda
   ```
   Gunakan nama *branch* yang deskriptif, misal: `fitur/tambah-login`, `bugfix/error-upload-file`, atau `docs/update-readme`.

4. **Tulis Kode Anda**
   Lakukan perubahan, tambahkan fitur, atau perbaiki *bug*. Pastikan Anda mematuhi standar *coding* (PSR-12 untuk PHP/Laravel) dan memastikan kode berjalan dengan baik di lokal.

5. **Commit Perubahan Anda**
   Tulis pesan *commit* yang jelas dan mendeskripsikan apa yang Anda ubah.
   ```bash
   git add .
   git commit -m "feat: Menambahkan fitur role management untuk editor"
   ```

6. **Push ke GitHub**
   ```bash
   git push origin fitur-baru-anda
   ```

7. **Buat Pull Request (PR)**
   Buka halaman repositori fork Anda di GitHub, Anda akan melihat tombol hijau **"Compare & pull request"**. Klik tombol tersebut.
   Berikan judul dan deskripsi yang jelas tentang apa yang dilakukan oleh PR tersebut.

## 3. Standar *Coding* & Konvensi
- **PHP / Laravel**: Kami mengikuti standar [PSR-12](https://www.php-fig.org/psr/psr-12/). Pastikan kode Anda rapi.
- **Frontend**: Jika menambahkan *styling*, gunakan *utility classes* dari Tailwind CSS sebisa mungkin.
- **Bahasa**: Anda dapat menggunakan Bahasa Indonesia atau Bahasa Inggris dalam penulisan komentar/dokumentasi, namun diutamakan Bahasa Inggris untuk penamaan variabel/fungsi/kelas (contoh: `JournalController` bukan `PengontrolJurnal`).

## 4. Komunikasi
Jika Anda memiliki pertanyaan lebih lanjut, jangan ragu untuk berdiskusi di bagian [Issues](https://github.com/amrunamv/Jurnal-AMV/issues) GitHub.

Sekali lagi, terima kasih telah meluangkan waktu untuk membantu proyek ini tumbuh!
