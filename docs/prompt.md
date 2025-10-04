# Prompt untuk Pengembangan Aplikasi Buku Syamail Muhammadiyah

## Prompt 1: Persiapan Lingkungan Pengembangan
**Mode: code**

Buatkan persiapan lingkungan pengembangan untuk aplikasi Buku Syamail Muhammadiyah dengan Laravel 12 dan PostgreSQL. Lakukan langkah-langkah berikut:

1. Konfigurasi file .env untuk koneksi PostgreSQL dengan database `syamail` dan username `postgres`
2. Install package yang diperlukan untuk enkripsi data dan caching
3. Buat migrasi database untuk semua tabel yang telah didesain (users, chapters, hadiths, audio_files, bookmarks, user_notes, search_history)
4. Buat model-model yang diperlukan dengan relasi yang sesuai
5. Buat seeder untuk data chapters dan hadiths (minimal 3 bab dan 5 hadits sebagai contoh)

## Prompt 2: Implementasi Sistem Enkripsi Data Pengguna
**Mode: code**

Implementasikan sistem enkripsi data sensitif pengguna (email dan nomor telepon) dengan langkah-langkah berikut:

1. Buat UserEncryptionService untuk enkripsi dan dekripsi data
2. Buat custom EncryptedString cast untuk otomatisasi enkripsi/dekripsi
3. Update model User untuk menggunakan custom cast
4. Update registrasi dan profil user untuk mendukung enkripsi data
5. Buat test untuk memastikan enkripsi dan dekripsi berfungsi dengan benar

## Prompt 3: Implementasi Fitur Manajemen Konten (56 Bab Syamail)
**Mode: code**

Implementasikan fitur manajemen konten untuk 56 bab Syamail dengan langkah-langkah berikut:

1. Buat ChapterController dengan metode index dan show
2. Buat HadithController dengan metode show
3. Buat view untuk menampilkan daftar bab (chapters/index.blade.php)
4. Buat view untuk menampilkan detail bab dan daftar hadits (chapters/show.blade.php)
5. Buat view untuk menampilkan detail hadits (hadiths/show.blade.php) dengan navigasi antar hadits
6. Buat seeder lengkap untuk 56 bab Syamail dan hadits-hadits terkait

## Prompt 4: Implementasi Fitur Teks (Hadits Arab, Terjemahan, Tafsir)
**Mode: code**

Implementasikan fitur teks untuk menampilkan hadits dalam bahasa Arab, terjemahan, dan tafsir dengan langkah-langkah berikut:

1. Update view hadits/show.blade.php untuk menampilkan teks Arab dengan formatting yang benar (rtl)
2. Implementasikan styling khusus untuk teks Arab menggunakan font Arab
3. Buat komponen Blade untuk menampilkan hadits dengan struktur yang konsisten
4. Tambahkan fitur untuk mengubah ukuran font teks Arab dan terjemahan
5. Implementasikan navigasi antar hadits (sebelumnya/berikutnya) yang tetap dalam bab yang sama

## Prompt 5: Implementasi Fitur Audio dengan Lazy Loading
**Mode: code**

Implementasikan fitur audio dengan lazy loading untuk performa optimal dengan langkah-langkah berikut:

1. Buat AudioStreamingService untuk streaming audio file dengan range support
2. Buat AudioController dengan metode stream dan getAudioUrl
3. Buat JavaScript audio player dengan lazy loading (resources/js/audio-player.js)
4. Implementasikan komponen audio player di view hadits/show.blade.php
5. Buat test untuk memastikan streaming audio berfungsi dengan benar
6. Upload beberapa contoh audio file dan hubungkan dengan hadits yang sesuai

## Prompt 6: Implementasi Strategi Caching untuk Performa Optimal
**Mode: code**

Implementasikan strategi caching untuk performa optimal dengan langkah-langkah berikut:

1. Konfigurasi Redis untuk caching di file .env
2. Buat CacheService dengan metode untuk caching chapters, hadiths, dan search results
3. Implementasikan caching di ChapterController dan HadithController
4. Buat middleware CacheHeaders untuk mengatur cache headers di browser
5. Implementasikan cache untuk fitur pencarian
6. Buat test untuk memastikan caching berfungsi dengan benar

## Prompt 7: Desain dan Implementasi Antarmuka Pengguna (UI/UX)
**Mode: code**

Desain dan implementasikan antarmuka pengguna (UI/UX) yang responsif dan menarik dengan langkah-langkah berikut:

1. Buat layout utama (layouts/app.blade.php) dengan header dan footer
2. Implementasikan navigasi yang responsif untuk desktop dan mobile
3. Buat halaman beranda (welcome.blade.php) yang menampilkan informasi aplikasi
4. Implementasikan desain untuk halaman daftar bab, detail bab, dan detail hadits
5. Tambahkan animasi dan transisi untuk meningkatkan pengalaman pengguna
6. Pastikan aplikasi terlihat baik di berbagai ukuran layar (mobile-first approach)

## Prompt 8: Implementasi Sistem Autentikasi dan Autorisasi
**Mode: code**

Implementasikan sistem autentikasi dan autorisasi dengan langkah-langkah berikut:

1. Custom implementasi Laravel Breeze atau Jetstream untuk autentikasi
2. Tambahkan validasi kustom untuk registrasi
3. Buat ProfileController untuk manajemen profil pengguna
4. Implementasikan middleware untuk proteksi route
5. Buat halaman login, register, dan profil yang sesuai dengan desain aplikasi
6. Implementasikan fitur lupa password dan verifikasi email

## Prompt 9: Implementasi Fitur Pencarian Konten
**Mode: code**

Implementasikan fitur pencarian konten yang komprehensif dengan langkah-langkah berikut:

1. Buat SearchService dengan metode untuk pencarian dasar dan lanjutan
2. Buat SearchController dengan metode form, search, dan advanced search
3. Buat view untuk form pencarian (search/form.blade.php)
4. Buat view untuk hasil pencarian (search/results.blade.php)
5. Implementasikan pencarian full-text pada kolom arabic_text, translation, dan interpretation
6. Tambahkan fitur pencarian berdasarkan bab dan sumber narrasi
7. Implementasikan riwayat pencarian untuk pengguna yang sudah login
8. Tampilkan pencarian populer di halaman pencarian

## Prompt 10: Implementasi Fitur Bookmark dan Catatan Pribadi
**Mode: code**

Implementasikan fitur bookmark dan catatan pribadi dengan langkah-langkah berikut:

1. Buat BookmarkController dan UserNoteController
2. Implementasikan fungsi untuk menambah, mengedit, dan menghapus bookmark
3. Implementasikan fungsi untuk menambah, mengedit, dan menghapus catatan pribadi
4. Buat view untuk daftar bookmark (bookmarks/index.blade.php)
5. Buat view untuk daftar catatan pribadi (notes/index.blade.php)
6. Tambahkan tombol bookmark dan catatan di halaman detail hadits
7. Implementasikan JavaScript untuk menambah bookmark dan catatan tanpa reload halaman
8. Buat test untuk memastikan fitur bookmark dan catatan berfungsi dengan benar

## Prompt 11: Optimasi Performa dan Keamanan Aplikasi
**Mode: debug**

Lakukan optimasi performa dan keamanan aplikasi dengan langkah-langkah berikut:

1. Identifikasi bottleneck performa dengan menggunakan debugging tools
2. Optimasi query database dengan eager loading dan indexing
3. Implementasikan security headers untuk melindungi aplikasi
4. Tambahkan rate limiting untuk mencegah abuse
5. Implementasikan HTTPS dan force HTTPS di production
6. Validasi dan sanitasi input pengguna
7. Lakukan security audit untuk mencari vulnerability
8. Implementasikan logging untuk monitoring keamanan

## Prompt 12: Pengujian Aplikasi dan Debugging
**Mode: debug**

Lakukan pengujian aplikasi dan debugging yang komprehensif dengan langkah-langkah berikut:

1. Buat feature tests untuk semua fitur utama aplikasi
2. Buat unit tests untuk services dan models
3. Buat browser tests dengan Laravel Dusk untuk testing UI
4. Implementasikan testing untuk enkripsi data, caching, dan streaming audio
5. Gunakan debugging tools untuk mengidentifikasi dan memperbaiki bug
6. Lakukan load testing untuk memastikan aplikasi dapat menangani traffic
7. Perbaiki bug yang ditemukan selama testing
8. Pastikan semua tests passing sebelum deployment

## Prompt 13: Dokumentasi Aplikasi dan Deployment
**Mode: code**

Buat dokumentasi aplikasi dan siapkan deployment dengan langkah-langkah berikut:

1. Buat dokumentasi API untuk aplikasi
2. Buat dokumentasi untuk fitur-fitur aplikasi
3. Tulis panduan instalasi dan konfigurasi
4. Siapkan skrip deployment untuk production
5. Konfigurasi environment untuk production
6. Implementasikan backup strategy untuk database dan file
7. Setup monitoring dan logging untuk production
8. Buat panduan maintenance aplikasi

## Prompt 14: Finalisasi dan Review Aplikasi
**Mode: debug**

Lakukan finalisasi dan review aplikasi secara keseluruhan dengan langkah-langkah berikut:

1. Review semua fitur yang telah diimplementasikan
2. Pastikan semua requirements dari rencana pengembangan telah terpenuhi
3. Lakukan user acceptance testing (UAT)
4. Perbaiki bug dan issue yang ditemukan
5. Optimasi performa aplikasi secara keseluruhan
6. Pastikan keamanan aplikasi sudah optimal
7. Siapkan aplikasi untuk production
8. Buat laporan final implementasi aplikasi