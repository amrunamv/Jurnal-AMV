# AMV Open Science (Jurnal-AMV)

**AMV Open Science (Jurnal-AMV)** adalah sistem manajemen dan penerbitan jurnal ilmiah (Journal Management System) berbasis *open-source* yang dibangun menggunakan framework modern [Laravel](https://laravel.com/). 

Proyek ini dibuat dengan tujuan untuk menyediakan alternatif sistem jurnal yang lebih modern, ringan, responsif, dan mudah dikembangkan dibandingkan platform yang sudah ada seperti Open Journal Systems (OJS).

## 🚀 Visi & Misi

Kami ingin menjadikan proyek ini sebagai standar baru untuk platform publikasi ilmiah di Indonesia dan global. Fokus utama kami adalah UI/UX yang ramah pengguna (baik bagi *Author*, *Reviewer*, maupun *Editor*), serta kemudahan kustomisasi bagi para *Developer*.

## ✨ Rencana Fitur
- **User Management**: Peran khusus untuk Admin, Journal Manager, Editor, Reviewer, dan Author.
- **Workflow Publikasi**: Sistem *submission* (pengiriman naskah), *peer-review*, penyuntingan (*copyediting*), hingga publikasi.
- **Modern UI/UX**: Tampilan responsif dan intuitif dengan dukungan Tailwind CSS.
- **OAI-PMH Support**: Integrasi untuk indeksasi jurnal internasional (seperti DOAJ, Google Scholar, dll).
- **Export & Import**: Kemudahan ekspor data artikel dan meta-data.

## 🛠️ Stack Teknologi
- **Backend:** [Laravel](https://laravel.com/) (PHP)
- **Frontend:** [Tailwind CSS](https://tailwindcss.com/), [Vite](https://vitejs.dev/)
- **Database:** MySQL / PostgreSQL / SQLite

## 💻 Panduan Instalasi untuk Developer

Jika Anda ingin mencoba atau berkontribusi, ikuti langkah-langkah instalasi berikut:

1. **Clone repositori ini:**
   ```bash
   git clone https://github.com/amrunamv/Jurnal-AMV.git
   cd "Jurnal-AMV"
   ```

2. **Install dependensi PHP (Composer):**
   ```bash
   composer install
   ```

3. **Install dependensi Node.js (NPM):**
   ```bash
   npm install
   ```

4. **Konfigurasi Environment:**
   Salin file `.env.example` menjadi `.env` lalu sesuaikan konfigurasi database Anda.
   ```bash
   cp .env.example .env
   ```

5. **Generate Application Key:**
   ```bash
   php artisan key:generate
   ```

6. **Migrasi Database:**
   ```bash
   php artisan migrate
   ```

7. **Jalankan *Development Server*:**
   Jalankan server PHP dan *build tool* frontend:
   ```bash
   php artisan serve
   npm run dev
   ```
   Aplikasi dapat diakses melalui `http://localhost:8000`.

## 🤝 Mari Berkontribusi!

Proyek ini sangat membutuhkan bantuan dan ide dari **Anda**! Kami mengundang seluruh *developer*, *UI/UX designer*, penulis, dan akademisi untuk ikut serta mengembangkan sistem jurnal ini agar sekelas OJS namun dengan teknologi kekinian.

Tidak peduli apakah Anda pemula atau profesional, setiap kontribusi sekecil apa pun (perbaikan *bug*, penambahan fitur, peningkatan dokumentasi, atau desain) akan sangat dihargai.

Silakan baca panduan lengkap cara berkontribusi di file [CONTRIBUTING.md](CONTRIBUTING.md).

## 🐛 Pelaporan Bug & Request Fitur
Menemukan *bug* atau punya ide fitur yang keren? Jangan ragu untuk membuat [Issue baru](https://github.com/amrunamv/Jurnal-AMV/issues) di repositori ini.

## 📄 Lisensi

AMV Open Science (Jurnal-AMV) adalah perangkat lunak *open-source* yang dilisensikan di bawah [MIT License](LICENSE).
