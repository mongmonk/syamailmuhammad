# ERD (Entity Relationship Diagram) - Aplikasi Web Buku Syamail Muhammadiyah

## Gambaran Umum
ERD ini menggambarkan struktur database untuk aplikasi web Buku Syamail Muhammadiyah yang dikembangkan dengan Laravel 12 dan PostgreSQL. Database ini dirancang untuk mengelola konten 56 bab Syamail, hadits, audio, bookmark, catatan pengguna, dan sistem pencarian.

## Entitas (Tabel) Database

### 1. Tabel `users`
**Deskripsi:** Menyimpan data pengguna aplikasi dengan enkripsi data sensitif
```sql
- id (primary key, auto-increment)
- name (string, required)
- email (string, encrypted, required, unique)
- phone (string, encrypted, nullable, unique)
- password (string, hashed, required)
- email_verified_at (timestamp, nullable)
- remember_token (string, nullable)
- created_at (timestamp)
- updated_at (timestamp)
```

### 2. Tabel `chapters`
**Deskripsi:** Menyimpan data 56 bab Syamail
```sql
- id (primary key, auto-increment)
- title (string, required)
- description (text, nullable)
- chapter_number (integer, required)
- created_at (timestamp)
- updated_at (timestamp)
```

### 3. Tabel `hadiths`
**Deskripsi:** Menyimpan data hadits dalam setiap bab
```sql
- id (primary key, auto-increment)
- chapter_id (foreign key, references chapters.id)
- arabic_text (text, required)
- translation (text, required)
- interpretation (text, nullable)
- narration_source (string, required)
- hadith_number (integer, required)
- created_at (timestamp)
- updated_at (timestamp)
```

### 4. Tabel `audio_files`
**Deskripsi:** Menyimpan data file audio untuk setiap hadits
```sql
- id (primary key, auto-increment)
- hadith_id (foreign key, references hadiths.id, unique)
- file_path (string, required)
- duration (integer, nullable, in seconds)
- file_size (integer, nullable, in bytes)
- created_at (timestamp)
- updated_at (timestamp)
```

### 5. Tabel `bookmarks`
**Deskripsi:** Menyimpan data bookmark hadits oleh pengguna
```sql
- id (primary key, auto-increment)
- user_id (foreign key, references users.id)
- hadith_id (foreign key, references hadiths.id)
- notes (text, nullable)
- created_at (timestamp)
- updated_at (timestamp)
```

### 6. Tabel `user_notes`
**Deskripsi:** Menyimpan catatan pribadi pengguna pada hadits
```sql
- id (primary key, auto-increment)
- user_id (foreign key, references users.id)
- hadith_id (foreign key, references hadiths.id)
- note_content (text, required)
- created_at (timestamp)
- updated_at (timestamp)
```

### 7. Tabel `search_history`
**Deskripsi:** Menyimpan riwayat pencarian pengguna
```sql
- id (primary key, auto-increment)
- user_id (foreign key, references users.id, nullable)
- query (string, required)
- results_count (integer, default: 0)
- created_at (timestamp)
```

## Relasi Antar Tabel

### Relasi One-to-Many
1. **users → bookmarks**
   - Satu user dapat memiliki banyak bookmark
   - Satu bookmark hanya dimiliki oleh satu user
   - Foreign key: `bookmarks.user_id` → `users.id`

2. **users → user_notes**
   - Satu user dapat memiliki banyak catatan
   - Satu catatan hanya dimiliki oleh satu user
   - Foreign key: `user_notes.user_id` → `users.id`

3. **users → search_history**
   - Satu user dapat memiliki banyak riwayat pencarian
   - Satu riwayat pencarian hanya dimiliki oleh satu user (bisa juga anonymous)
   - Foreign key: `search_history.user_id` → `users.id` (nullable)

4. **chapters → hadiths**
   - Satu bab dapat memiliki banyak hadits
   - Satu hadits hanya termasuk dalam satu bab
   - Foreign key: `hadiths.chapter_id` → `chapters.id`

### Relasi One-to-One
1. **hadiths → audio_files**
   - Satu hadits memiliki satu file audio
   - Satu file audio hanya dimiliki oleh satu hadits
   - Foreign key: `audio_files.hadith_id` → `hadiths.id` (unique)

### Relasi Many-to-Many
1. **hadiths ↔ users (melalui bookmarks)**
   - Satu hadits dapat di-bookmark oleh banyak user
   - Satu user dapat meng-bookmark banyak hadits
   - Tabel penghubung: `bookmarks`

2. **hadiths ↔ users (melalui user_notes)**
   - Satu hadits dapat memiliki catatan dari banyak user
   - Satu user dapat membuat catatan untuk banyak hadits
   - Tabel penghubung: `user_notes`

## Indeks Database

### Indeks untuk Performa Pencarian
1. **Tabel `users`**
   ```sql
   CREATE INDEX idx_users_email ON users(email);
   CREATE INDEX idx_users_phone ON users(phone);
   ```

2. **Tabel `chapters`**
   ```sql
   CREATE INDEX idx_chapters_number ON chapters(chapter_number);
   ```

3. **Tabel `hadiths`**
   ```sql
   CREATE INDEX idx_hadiths_number ON hadiths(hadith_number);
   CREATE INDEX idx_hadiths_chapter ON hadiths(chapter_id);
   ```

4. **Tabel `bookmarks`**
   ```sql
   CREATE INDEX idx_bookmarks_user ON bookmarks(user_id);
   CREATE INDEX idx_bookmarks_hadith ON bookmarks(hadith_id);
   ```

5. **Tabel `user_notes`**
   ```sql
   CREATE INDEX idx_user_notes_user ON user_notes(user_id);
   CREATE INDEX idx_user_notes_hadith ON user_notes(hadith_id);
   ```

6. **Tabel `search_history`**
   ```sql
   CREATE INDEX idx_search_history_user ON search_history(user_id);
   CREATE INDEX idx_search_history_query ON search_history(query);
   ```

### Indeks Full-Text untuk Pencarian Konten
```sql
-- Untuk PostgreSQL
CREATE INDEX idx_hadiths_arabic_text ON hadiths USING gin(to_tsvector('arabic', arabic_text));
CREATE INDEX idx_hadiths_translation ON hadiths USING gin(to_tsvector('english', translation));
CREATE INDEX idx_hadiths_interpretation ON hadiths USING gin(to_tsvector('english', interpretation));

-- Atau menggunakan ILIKE untuk pencarian sederhana
CREATE INDEX idx_hadiths_arabic_like ON hadiths(arabic_text varchar_pattern_ops);
CREATE INDEX idx_hadiths_translation_like ON hadiths(translation varchar_pattern_ops);
CREATE INDEX idx_hadiths_interpretation_like ON hadiths(interpretation varchar_pattern_ops);
```

## Diagram ERD (Representasi Visual)

```
┌─────────────┐       ┌─────────────┐       ┌─────────────┐
│    users    │       │  chapters   │       │   hadiths   │
├─────────────┤       ├─────────────┤       ├─────────────┤
│ id (PK)     │──┐    │ id (PK)     │──┐    │ id (PK)     │
│ name        │  │    │ title       │  │    │ chapter_id  │◀─┘
│ email       │  │    │ description │  │    │ arabic_text │
│ phone       │  │    │ chapter_no  │  │    │ translation │
│ password    │  │    │ created_at  │  │    │ interpretation│
│ email_v...  │  │    │ updated_at  │  │    │ narration...│
│ remember... │  │    └─────────────┘  │    │ hadith_no   │
│ created_at  │  │                     │    │ created_at  │
│ updated_at  │  │                     │    │ updated_at  │
└─────────────┘  │                     └─────┴─────────────┘
        │         │                           │
        │         │                           │
        │         │                           │
        │    ┌─────────────┐                 │
        │    │search_history│                 │
        │    ├─────────────┤                 │
        │    │ id (PK)     │                 │
        └───▶│ user_id     │                 │
             │ query       │                 │
             │ results...  │                 │
             │ created_at  │                 │
             └─────────────┘                 │
                                          │
        ┌─────────────┐                    │
        │  bookmarks  │                    │
        ├─────────────┤                    │
        │ id (PK)     │                    │
        │ user_id     │◀───┐               │
        │ hadith_id   │────┼───┐           │
        │ notes       │    │   │           │
        │ created_at  │    │   │           │
        │ updated_at  │    │   │           │
        └─────────────┘    │   │           │
                           │   │           │
        ┌─────────────┐    │   │           │
        │ user_notes  │    │   │           │
        ├─────────────┤    │   │           │
        │ id (PK)     │    │   │           │
        │ user_id     │◀───┘   │           │
        │ hadith_id   │────────┘           │
        │ note_content│                    │
        │ created_at  │                    │
        │ updated_at  │                    │
        └─────────────┘                    │
                                          │
        ┌─────────────┐                    │
        │ audio_files │                    │
        ├─────────────┤                    │
        │ id (PK)     │                    │
        │ hadith_id   │◀───────────────────┘
        │ file_path   │
        │ duration    │
        │ file_size   │
        │ created_at  │
        │ updated_at  │
        └─────────────┘
```

## Keterangan Tambahan

### Enkripsi Data
- Kolom `email` dan `phone` pada tabel `users` akan dienkripsi menggunakan Laravel Encryption
- Implementasi enkripsi menggunakan Custom Casts `EncryptedString`

### Constraints
- **Unique Constraints:**
  - `users.email`
  - `users.phone`
  - `audio_files.hadith_id`

- **Foreign Key Constraints:**
  - `hadiths.chapter_id` → `chapters.id` (ON DELETE CASCADE)
  - `audio_files.hadith_id` → `hadiths.id` (ON DELETE CASCADE)
  - `bookmarks.user_id` → `users.id` (ON DELETE CASCADE)
  - `bookmarks.hadith_id` → `hadiths.id` (ON DELETE CASCADE)
  - `user_notes.user_id` → `users.id` (ON DELETE CASCADE)
  - `user_notes.hadith_id` → `hadiths.id` (ON DELETE CASCADE)
  - `search_history.user_id` → `users.id` (ON DELETE SET NULL)

### Optimasi Performa
- Penggunaan indeks untuk kolom yang sering digunakan dalam pencarian dan join
- Implementasi caching untuk data yang sering diakses (daftar bab, hadits per bab)
- Strategi lazy loading untuk file audio
- Full-text search untuk pencarian konten hadits

### Skalabilitas
- Desain database mendukung penambahan fitur di masa depan
- Struktur yang modular memungkinkan ekstensi tanpa mengubah schema secara drastis
- Penggunaan tipe data yang tepat untuk optimasi storage dan performa