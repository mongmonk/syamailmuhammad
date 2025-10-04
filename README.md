# Aplikasi Web Buku Syamail Muhammadiyah

Aplikasi web Buku Syamail Muhammadiyah adalah platform digital yang menghadirkan Kitab Syamail An-Nabiy karya Imam At-Tirmidzi yang menghimpun 56 bab yang menggambarkan pribadi dan fisik Rasulullah SAW secara terperinci. Aplikasi ini dikembangkan menggunakan Laravel 12 dan PostgreSQL dengan fokus pada keamanan data sensitif pengguna dan performa optimal.

## Fitur Utama

- **Manajemen Konten**: 56 bab Syamail dengan hadits-hadits terkait
- **Teks Multibahasa**: Hadits dalam bahasa Arab, terjemahan, dan tafsir
- **Audio Streaming**: Fitur audio pembacaan hadits dengan lazy loading untuk performa optimal
- **Pencarian Konten**: Sistem pencarian komprehensif pada teks Arab, terjemahan, dan tafsir
- **Bookmark & Catatan**: Fitur bookmark dan catatan pribadi untuk pengguna yang terautentikasi
- **Keamanan Data**: Enkripsi data sensitif pengguna (email, nomor telepon)
- **Performa Optimal**: Strategi caching dengan Redis untuk pengalaman pengguna yang cepat

## Teknologi yang Digunakan

- **Backend**: Laravel 12
- **Database**: PostgreSQL
- **Frontend**: Blade, Tailwind CSS, JavaScript/Alpine.js
- **Enkripsi**: Laravel Encryption dan Hashing
- **Caching**: Redis/Database Cache
- **Audio**: HTML5 Audio API dengan lazy loading

## Struktur Database

Aplikasi ini menggunakan 7 tabel utama:

1. **users**: Data pengguna dengan enkripsi data sensitif
2. **chapters**: Data 56 bab Syamail
3. **hadiths**: Data hadits dalam setiap bab
4. **audio_files**: Data file audio untuk setiap hadits
5. **bookmarks**: Data bookmark hadits oleh pengguna
6. **user_notes**: Catatan pribadi pengguna pada hadits
7. **search_history**: Riwayat pencarian pengguna

### Diagram ERD

Lihat dokumentasi lengkap ERD di [`docs/erd-syamail.md`](docs/erd-syamail.md) dan visualisasi PlantUML di [`docs/erd-syamail-plantuml.txt`](docs/erd-syamail-plantuml.txt).

## Arsitektur Sistem

Aplikasi ini mengikuti arsitektur berlapis dengan pemisahan tanggung jawab yang jelas:

- **Client Layer**: Web Browser / Mobile Browser
- **Presentation Layer**: Laravel Routes, Controllers, Blade Views, JavaScript/Alpine.js, Tailwind CSS
- **Application Layer**: Application Services, Logging Service, Encryption Service, Search Service, Cache Service, Audio Streaming Service
- **Domain Layer**: Laravel Models, Repositories, Domain Entities
- **Infrastructure Layer**: PostgreSQL, Redis Cache, File Storage, Queue System

Lihat dokumentasi lengkap arsitektur di [`docs/diagram-arsitektur-syamail.md`](docs/diagram-arsitektur-syamail.md).

## Instalasi dan Konfigurasi

### Persyaratan Sistem

- PHP 8.2+
- PostgreSQL
- Redis (opsional, untuk caching)
- Node.js (untuk manajemen asset frontend)
- Composer

### Langkah-langkah Instalasi

1. **Clone repository**
   ```bash
   git clone https://github.com/username/syamail.git
   cd syamail
   ```

2. **Install dependencies**
   ```bash
   composer install
   npm install
   npm run build
   ```

3. **Konfigurasi environment**
   ```bash
   cp .env.example .env
   ```
   
   Edit file `.env` dan sesuaikan konfigurasi database:
   ```env
   DB_CONNECTION=pgsql
   DB_HOST=127.0.0.1
   DB_PORT=5432
   DB_DATABASE=syamail
   DB_USERNAME=postgres
   DB_PASSWORD=password_anda
   ```

4. **Generate application key**
   ```bash
   php artisan key:generate
   ```

5. **Jalankan migrasi database**
   ```bash
   php artisan migrate
   ```

6. **Seed data awal**
   ```bash
   php artisan db:seed
   ```

7. **Jalankan aplikasi**
   ```bash
   php artisan serve
   ```

## Dokumentasi

Dokumentasi lengkap proyek tersedia di folder `docs/`:

- [`rencana-pengembangan-syamail.md`](docs/rencana-pengembangan-syamail.md): Rencana pengembangan aplikasi secara keseluruhan
- [`diagram-arsitektur-syamail.md`](docs/diagram-arsitektur-syamail.md): Diagram arsitektur sistem
- [`erd-syamail.md`](docs/erd-syamail.md): Dokumentasi Entity Relationship Diagram
- [`erd-syamail-plantuml.txt`](docs/erd-syamail-plantuml.txt): Representasi PlantUML untuk ERD
- [`prompt.md`](docs/prompt.md): Prompt pengembangan untuk setiap fitur

## Fitur-fitur Detail

### Manajemen Konten

- Navigasi 56 bab Syamail
- Detail setiap bab dengan daftar hadits
- Tampilan hadits lengkap dengan teks Arab, terjemahan, dan tafsir
- Navigasi antar hadits dalam bab yang sama

### Sistem Audio

- Streaming audio dengan range support
- Lazy loading untuk performa optimal
- Kontrol audio yang responsif

### Pencarian

- Pencarian full-text pada teks Arab, terjemahan, dan tafsir
- Pencarian lanjutan dengan filter bab dan sumber narrasi
- Riwayat pencarian untuk pengguna yang terautentikasi
- Pencarian populer

### Keamanan

- Enkripsi data sensitif pengguna (email, nomor telepon)
- Autentikasi dan autorisasi
- Rate limiting untuk mencegah abuse
- Security headers

### Performa

- Caching dengan Redis
- Optimasi query database
- Indeks database untuk pencarian cepat
- Lazy loading untuk file audio

## Kontribusi

Jika Anda ingin berkontribusi pada pengembangan aplikasi ini, silakan ikuti langkah-langkah berikut:

1. Fork repository
2. Buat branch fitur baru (`git checkout -b fitur/baru`)
3. Commit perubahan Anda (`git commit -am 'Tambah fitur baru'`)
4. Push ke branch (`git push origin fitur/baru`)
5. Buat Pull Request

## Lisensi

Proyek ini dilisensikan di bawah lisensi MIT. Lihat file [LICENSE](LICENSE) untuk informasi lebih lanjut.

## Dukungan

Jika Anda menemukan masalah atau memiliki pertanyaan, silakan buat issue di [GitHub Issues](https://github.com/username/syamail/issues).

## Penghargaan

Terima kasih kepada semua kontributor dan pihak yang telah mendukung pengembangan aplikasi ini.
