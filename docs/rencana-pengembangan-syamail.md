
# Rencana Pengembangan Aplikasi Web Buku Syamail Muhammadiyah

## Ringkasan Proyek
Aplikasi web Buku Syamail Muhammadiyah adalah platform digital yang menghadirkan Kitab Syamail An-Nabiy karya Imam At-Tirmidzi yang menghimpun 56 bab yang menggambarkan pribadi dan fisik Rasulullah SAW secara terperinci. Aplikasi ini akan dikembangkan menggunakan Laravel 12 dan PostgreSQL dengan fokus pada keamanan data sensitif pengguna dan performa optimal.

## 1. Analisis Kebutuhan Aplikasi dan Persiapan Lingkungan Pengembangan

### 1.1 Kebutuhan Fungsional
- Manajemen konten untuk 56 bab Syamail
- Penyajian hadits dalam bahasa Arab, terjemahan, dan tafsir
- Fitur audio pembacaan hadits dengan lazy loading
- Sistem pencarian konten
- Fitur bookmark dan catatan pribadi
- Sistem autentikasi dan profil pengguna

### 1.2 Kebutuhan Non-Fungsional
- Keamanan data sensitif pengguna (email, nomor telepon) dengan enkripsi
- Performa aplikasi yang optimal dengan strategi caching
- Responsivitas aplikasi di berbagai perangkat
- Skalabilitas untuk pengembangan fitur di masa depan

### 1.3 Lingkungan Pengembangan
- Laragon sebagai lingkungan pengembangan lokal
- PostgreSQL dengan nama database `syamail` dan username `postgres`
- PHP 8.2+ (sesuai dengan persyaratan Laravel 12)
- Node.js untuk manajemen asset frontend

### 1.4 Teknologi yang Digunakan
- Backend: Laravel 12
- Database: PostgreSQL
- Frontend: Blade, Tailwind CSS, JavaScript/Alpine.js
- Enkripsi: Laravel Encryption dan Hashing
- Caching: Redis/Database Cache
- Audio: HTML5 Audio API dengan lazy loading

## 2. Desain Arsitektur Database

### 2.1 Struktur Database Utama

#### Tabel `users`
```sql
- id (primary key)
- name
- email (encrypted)
- phone (encrypted, nullable)
- password (hashed)
- email_verified_at
- remember_token
- created_at
- updated_at
```

#### Tabel `chapters`
```sql
- id (primary key)
- title
- description
- chapter_number (integer)
- created_at
- updated_at
```

#### Tabel `hadiths`
```sql
- id (primary key)
- chapter_id (foreign key)
- arabic_text
- translation
- interpretation
- narration_source
- hadith_number
- created_at
- updated_at
```

#### Tabel `audio_files`
```sql
- id (primary key)
- hadith_id (foreign key)
- file_path
- duration
- file_size
- created_at
- updated_at
```

#### Tabel `bookmarks`
```sql
- id (primary key)
- user_id (foreign key)
- hadith_id (foreign key)
- notes (text, nullable)
- created_at
- updated_at
```

#### Tabel `user_notes`
```sql
- id (primary key)
- user_id (foreign key)
- hadith_id (foreign key)
- note_content
- created_at
- updated_at
```

#### Tabel `search_history`
```sql
- id (primary key)
- user_id (foreign key, nullable)
- query
- results_count
- created_at
```

### 2.2 Relasi Database
- `users` one-to-many `bookmarks`
- `users` one-to-many `user_notes`
- `users` one-to-many `search_history`
- `chapters` one-to-many `hadiths`
- `hadiths` one-to-one `audio_files`
- `hadiths` many-to-many `bookmarks`
- `hadiths` many-to-many `user_notes`

### 2.3 Indeks Database
- Indeks pada kolom `email` dan `phone` di tabel `users` untuk pencarian cepat
- Indeks pada kolom `chapter_number` di tabel `chapters`
- Indeks pada kolom `hadith_number` di tabel `hadiths`
- Indeks full-text pada kolom `arabic_text`, `translation`, dan `interpretation` untuk pencarian

## 3. Konfigurasi PostgreSQL dan Koneksi Database di Laravel

### 3.1 Konfigurasi Environment (.env)
```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=syamail
DB_USERNAME=postgres
DB_PASSWORD=password_anda
```

### 3.2 Konfigurasi Database Laravel
Memperbarui file `config/database.php` untuk optimasi koneksi PostgreSQL:
```php
'pgsql' => [
    'driver' => 'pgsql',
    'url' => env('DB_URL'),
    'host' => env('DB_HOST', '127.0.0.1'),
    'port' => env('DB_PORT', '5432'),
    'database' => env('DB_DATABASE', 'laravel'),
    'username' => env('DB_USERNAME', 'root'),
    'password' => env('DB_PASSWORD', ''),
    'charset' => 'utf8',
    'prefix' => '',
    'prefix_indexes' => true,
    'search_path' => 'public',
    'sslmode' => 'prefer',
    'options' => [
        PDO::ATTR_EMULATE_PREPARES => true,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ],
],
```

### 3.3 Migrasi Database
Membuat file migrasi untuk semua tabel yang telah didesain:
```bash
php artisan make:migration create_users_table
php artisan make:migration create_chapters_table
php artisan make:migration create_hadiths_table
php artisan make:migration create_audio_files_table
php artisan make:migration create_bookmarks_table
php artisan make:migration create_user_notes_table
php artisan make:migration create_search_history_table
```

## 4. Implementasi Sistem Enkripsi Data Sensitif Pengguna

### 4.1 Enkripsi Email dan Nomor Telepon
Membuat custom Encryption Service untuk data sensitif:
```php
<?php

namespace App\Services;

use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;

class UserEncryptionService
{
    /**
     * Encrypt sensitive user data
     */
    public function encrypt(string $value): string
    {
        return Crypt::encryptString($value);
    }

    /**
     * Decrypt sensitive user data
     */
    public function decrypt(string $value): ?string
    {
        try {
            return Crypt::decryptString($value);
        } catch (DecryptException $e) {
            return null;
        }
    }
}
```

### 4.2 Custom Casts untuk Model User
Membuat custom casts untuk otomatisasi enkripsi/dekripsi:
```php
<?php

namespace App\Models\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use App\Services\UserEncryptionService;

class EncryptedString implements CastsAttributes
{
    protected $encryptionService;

    public function __construct()
    {
        $this->encryptionService = new UserEncryptionService();
    }

    public function get($model, string $key, $value, array $attributes)
    {
        if (empty($value)) {
            return $value;
        }

        return $this->encryptionService->decrypt($value);
    }

    public function set($model, string $key, $value, array $attributes)
    {
        if (empty($value)) {
            return $value;
        }

        return $this->encryptionService->encrypt($value);
    }
}
```

### 4.3 Update Model User
Menggunakan custom casts di model User:
```php
<?php

namespace App\Models;

use App\Models\Casts\EncryptedString;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'email' => EncryptedString::class,
        'phone' => EncryptedString::class,
    ];
}
```

## 5. Desain Struktur Direktori Proyek yang Optimal

### 5.1 Struktur Direktori Aplikasi
```
app/
├── Console/
├── Exceptions/
├── Http/
│   ├── Controllers/
│   │   ├── Auth/
│   │   ├── ChapterController.php
│   │   ├── HadithController.php
│   │   ├── AudioController.php
│   │   ├── BookmarkController.php
│   │   ├── SearchController.php
│   │   ├── ProfileController.php
│   │   └── Controller.php
│   ├── Middleware/
│   └── Requests/
├── Models/
│   ├── User.php
│   ├── Chapter.php
│   ├── Hadith.php
│   ├── AudioFile.php
│   ├── Bookmark.php
│   ├── UserNote.php
│   └── SearchHistory.php
├── Providers/
├── Services/
│   ├── UserEncryptionService.php
│   ├── AudioStreamingService.php
│   └── SearchService.php
└── Rules/
```

### 5.2 Struktur Direktori Resources
```
resources/
├── css/
│   └── app.css
├── js/
│   ├── app.js
│   ├── audio-player.js
│   ├── search.js
│   └── bookmark.js
├── views/
│   ├── auth/
│   ├── chapters/
│   ├── hadiths/
│   ├── bookmarks/
│   ├── search/
│   ├── profile/
│   ├── layouts/
│   │   ├── app.blade.php
│   │   ├── guest.blade.php
│   │   └── partials/
│   │       ├── header.blade.php
│   │       ├── footer.blade.php
│   │       └── sidebar.blade.php
│   └── welcome.blade.php
└── lang/
    ├── id/
    └── en/
```

### 5.3 Struktur Direktori Database
```
database/
├── factories/
├── migrations/
├── seeders/
│   ├── DatabaseSeeder.php
│   ├── ChapterSeeder.php
│   └── HadithSeeder.php
└── sqlite/
```

### 5.4 Struktur Direktori Tests
```
tests/
├── Feature/
│   ├── Auth/
│   ├── ChapterTest.php
│   ├── HadithTest.php
│   ├── AudioTest.php
│   ├── BookmarkTest.php
│   └── SearchTest.php
├── Unit/
│   ├── Models/
│   └── Services/
└── TestCase.php
```

## 6. Implementasi Fitur Manajemen Konten (56 Bab Syamail)

### 6.1 Model Chapter
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Chapter extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'chapter_number',
    ];

    public function hadiths(): HasMany
    {
        return $this->hasMany(Hadith::class);
    }
}
```

### 6.2 Model Hadith
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Hadith extends Model
{
    use HasFactory;

    protected $fillable = [
        'chapter_id',
        'arabic_text',
        'translation',
        'interpretation',
        'narration_source',
        'hadith_number',
    ];

    public function chapter(): BelongsTo
    {
        return $this->belongsTo(Chapter::class);
    }

    public function audioFile(): HasOne
    {
        return $this->hasOne(AudioFile::class);
    }

    public function bookmarks(): MorphMany
    {
        return $this->morphMany(Bookmark::class, 'bookmarkable');
    }

    public function userNotes(): MorphMany
    {
        return $this->morphMany(UserNote::class, 'notable');
    }
}
```

### 6.3 ChapterController
```php
<?php

namespace App\Http\Controllers;

use App\Models\Chapter;
use Illuminate\Http\Request;

class ChapterController extends Controller
{
    public function index()
    {
        $chapters = Chapter::orderBy('chapter_number')->get();
        return view('chapters.index', compact('chapters'));
    }

    public function show(Chapter $chapter)
    {
        $hadiths = $chapter->hadiths()->orderBy('hadith_number')->get();
        return view('chapters.show', compact('chapter', 'hadiths'));
    }
}
```

### 6.4 HadithSeeder
Membuat seeder untuk data hadits berdasarkan 56 bab Syamail:
```php
<?php

namespace Database\Seeders;

use App\Models\Chapter;
use App\Models\Hadith;
use Illuminate\Database\Seeder;

class HadithSeeder extends Seeder
{
    public function run()
    {
        // Data 56 bab Syamail akan diisi di sini
        // Setiap bab akan memiliki hadits-hadits terkait
        
        $chapters = Chapter::all();
        
        foreach ($chapters as $chapter) {
            // Contoh data untuk bab 1
            if ($chapter->chapter_number === 1) {
                Hadith::create([
                    'chapter_id' => $chapter->id,
                    'arabic_text' => 'نص الحديث باللغة العربية هنا...',
                    'translation' => 'Terjemahan hadits dalam bahasa Indonesia...',
                    'interpretation' => 'Tafsir atau penjelasan hadits...',
                    'narration_source' => 'HR. Al-Bukhari',
                    'hadith_number' => 1,
                ]);
                
                // Tambahkan hadits lainnya untuk bab ini
            }
            
            // Lanjutkan untuk bab-bab lainnya
        }
    }
}
```

## 7. Implementasi Fitur Teks (Hadits Arab, Terjemahan, Tafsir)

### 7.1 HadithController
```php
<?php

namespace App\Http\Controllers;

use App\Models\Hadith;
use Illuminate\Http\Request;

class HadithController extends Controller
{
    public function show(Hadith $hadith)
    {
        // Load related data
        $hadith->load(['chapter', 'audioFile']);
        
        // Get user's bookmark and notes if authenticated
        $bookmark = null;
        $userNote = null;
        
        if (auth()->check()) {
            $bookmark = auth()->user()->bookmarks()
                ->where('hadith_id', $hadith->id)
                ->first();
                
            $userNote = auth()->user()->notes()
                ->where('hadith_id', $hadith->id)
                ->first();
        }
        
        return view('hadiths.show', compact('hadith', 'bookmark', 'userNote'));
    }
}
```

### 7.2 View untuk Menampilkan Hadits
```blade
{{-- resources/views/hadiths/show.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-800 mb-2">
                Bab {{ $hadith->chapter->chapter_number }}: {{ $hadith->chapter->title }}
            </h1>
            <p class="text-gray-600">{{ $hadith->chapter->description }}</p>
        </div>
        
        <div class="mb-6">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-semibold text-gray-800">
                    Hadits {{ $hadith->hadith_number }}
                </h2>
                <div class="flex space-x-2">
                    @auth
                    <button id="bookmark-btn" class="px-3 py-1 bg-gray-200 text-gray-700 rounded hover:bg-gray-300">
                        {{ $bookmark ? 'Hapus Bookmark' : 'Bookmark' }}
                    </button>
                    <button id="note-btn" class="px-3 py-1 bg-gray-200 text-gray-700 rounded hover:bg-gray-300">
                        {{ $userNote ? 'Edit Catatan' : 'Tambah Catatan' }}
                    </button>
                    @endauth
                </div>
            </div>
            
            <div class="border-t border-gray-200 pt-4">
                <!-- Teks Arab -->
                <div class="mb-6 text-right" dir="rtl">
                    <h3 class="text-lg font-medium text-gray-800 mb-2">نص الحديث</h3>
                    <p class="text-xl leading-loose text-gray-700">{{ $hadith->arabic_text }}</p>
                </div>
                
                <!-- Terjemahan -->
                <div class="mb-6">
                    <h3 class="text-lg font-medium text-gray-800 mb-2">Terjemahan</h3>
                    <p class="text-gray-700">{{ $hadith->translation }}</p>
                </div>
                
                <!-- Sumber Narrasi -->
                <div class="mb-6">
                    <h3 class="text-lg font-medium text-gray-800 mb-2">Sumber</h3>
                    <p class="text-gray-700">{{ $hadith->narration_source }}</p>
                </div>
                
                <!-- Tafsir -->
                <div class="mb-6">
                    <h3 class="text-lg font-medium text-gray-800 mb-2">Tafsir</h3>
                    <p class="text-gray-700">{{ $hadith->interpretation }}</p>
                </div>
                
                <!-- Audio Player -->
                @if($hadith->audioFile)
                <div class="mb-6">
                    <h3 class="text-lg font-medium text-gray-800 mb-2">Audio</h3>
                    <div class="audio-player" data-audio-id="{{ $hadith->audioFile->id }}">
                        <!-- Audio player akan diimplementasikan dengan JavaScript -->
                    </div>
                </div>
                @endif
            </div>
        </div>
        
        <!-- Catatan Pengguna -->
        @auth
        @if($userNote)
        <div class="mt-6 p-4 bg-yellow-50 rounded-lg">
            <h3 class="text-lg font-medium text-gray-800 mb-2">Catatan Saya</h3>
            <p class="text-gray-700">{{ $userNote->note_content }}</p>
        </div>
        @endif
        @endauth
    </div>
</div>
@endsection
```

### 7.3 Navigasi Antar Hadits
Menambahkan navigasi untuk hadits sebelumnya dan berikutnya:
```php
// Di HadithController
public function show(Hadith $hadith)
{
    $hadith->load(['chapter', 'audioFile']);
    
    // Get previous and next hadith
    $previousHadith = Hadith::where('chapter_id', $hadith->chapter_id)
        ->where('hadith_number', '<', $hadith->hadith_number)
        ->orderBy('hadith_number', 'desc')
        ->first();
        
    $nextHadith = Hadith::where('chapter_id', $hadith->chapter_id)
        ->where('hadith_number', '>', $hadith->hadith_number)
        ->orderBy('hadith_number', 'asc')
        ->first();
    
    // ... kode lainnya
    
    return view('hadiths.show', compact('hadith', 'previousHadith', 'nextHadith', 'bookmark', 'userNote'));
}
```

## 8. Implementasi Fitur Audio dengan Lazy Loading

### 8.1 Model AudioFile
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AudioFile extends Model
{
    use HasFactory;

    protected $fillable = [
        'hadith_id',
        'file_path',
        'duration',
        'file_size',
    ];

    public function hadith(): BelongsTo
    {
        return $this->belongsTo(Hadith::class);
    }
}
```

### 8.2 AudioStreamingService
```php
<?php

namespace App\Services;

use App\Models\AudioFile;
use Illuminate\Support\Facades\Storage;

class AudioStreamingService
{
    /**
     * Get audio file URL with streaming support
     */
    public function getAudioUrl(AudioFile $audioFile): string
    {
        return Storage::url($audioFile->file_path);
    }
    
    /**
     * Generate audio file with range support for streaming
     */
    public function streamAudio(AudioFile $audioFile)
    {
        $path = Storage::path($audioFile->file_path);
        $size = filesize($path);
        $time = date('r', filemtime($path));
        $fm = @fopen($path, 'rb');
        $begin = 0;
        $end = $size - 1;
        
        if (isset($_SERVER['HTTP_RANGE'])) {
            if (preg_match('/bytes=\h*(\d+)-(\d*)[\D.*]?/i', $_SERVER['HTTP_RANGE'], $matches)) {
                $begin = intval($matches[1]);
                if (!empty($matches[2])) {
                    $end = intval($matches[2]);
                }
            }
        }
        
        if ($begin > 0 || $end < $size - 1) {
            header('HTTP/1.0 206 Partial Content');
        } else {
            header('HTTP/1.0 200 OK');
        }
        
        header("Content-Type: audio/mpeg");
        header('Accept-Ranges: bytes');
        header("Content-Length: " . ($end - $begin + 1));
        header("Content-Disposition: inline;");
        header("Content-Range: bytes $begin-$end/$size");
        header("Content-Transfer-Encoding: binary");
        header("Last-Modified: $time");
        header('Connection: close');
        
        $cur = $begin;
        fseek($fm, $begin, 0);
        
        while (!feof($fm) && $cur <= $end && (connection_status() == 0)) {
            print fread($fm, min(1024 * 16, $end - $cur + 1));
            $cur += 1024 * 16;
            flush();
        }
        
        fclose($fm);
        exit;
    }
}
```

### 8.3 AudioController
```php
<?php

namespace App\Http\Controllers;

use App\Models\AudioFile;
use App\Services\AudioStreamingService;
use Illuminate\Http\Request;

class AudioController extends Controller
{
    protected $audioStreamingService;
    
    public function __construct(AudioStreamingService $audioStreamingService)
    {
        $this->audioStreamingService = $audioStreamingService;
    }
    
    public function stream(AudioFile $audioFile)
    {
        return $this->audioStreamingService->streamAudio($audioFile);
    }
    
    public function getAudioUrl(AudioFile $audioFile)
    {
        return response()->json([
            'url' => $this->audioStreamingService->getAudioUrl($audioFile),
            'duration' => $audioFile->duration,
            'file_size' => $audioFile->file_size,
        ]);
    }
}
```

### 8.4 JavaScript Audio Player dengan Lazy Loading
```javascript
// resources/js/audio-player.js
document.addEventListener('DOMContentLoaded', function() {
    const audioPlayers = document.querySelectorAll('.audio-player');
    
    audioPlayers.forEach(player => {
        const audioId = player.dataset.audioId;
        const playButton = document.createElement('button');
        playButton.textContent = 'Play Audio';
        playButton.className = 'px-4 py-2 bg-green-500 text-white rounded hover:bg-green-600';
        
        const audioElement = document.createElement('audio');
        audioElement.preload = 'none'; // Lazy loading
        
        // Fetch audio URL when play button is clicked
        playButton.addEventListener('click', async function() {
            if (!audioElement.src) {
                try {
                    const response = await fetch(`/api/audio/${audioId}/url`);
                    const data = await response.json();
                    
                    audioElement.src = data.url;
                    audioElement.preload = 'metadata';
                    
                    // Replace play button with audio controls
                    player.removeChild(playButton);
                    player.appendChild(audioElement);
                    
                    // Add custom controls if needed
                    audioElement.controls = true;
                    audioElement.play();
                } catch (error) {
                    console.error('Error loading audio:', error);
                    alert('Error loading audio file. Please try again.');
                }
            }
        });
        
        player.appendChild(playButton);
    });
});
```

### 8.5 Route untuk Audio
```php
// routes/web.php
Route::get('/audio/{audioFile}/stream', [AudioController::class, 'stream'])->name('audio.stream');
Route::get('/audio/{audioFile}/url', [AudioController::class, 'getAudioUrl'])->name('audio.url');

// routes/api.php
Route::get('/audio/{audioFile}/url', [AudioController::class, 'getAudioUrl']);
```

## 9. Implementasi Strategi Caching untuk Performa Optimal

### 9.1 Konfigurasi Cache
Menggunakan Redis untuk caching:
```env
CACHE_DRIVER=redis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379
```

### 9.2 Cache Service
```php
<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;

class CacheService
{
    /**
     * Cache chapters list
     */
    public function getChaptersList()
    {
        return Cache::remember('chapters.list', 86400, function () {
            return \App\Models\Chapter::orderBy('chapter_number')->get();
        });
    }
    
    /**
     * Cache hadiths by chapter
     */
    public function getHadithsByChapter($chapterId)
    {
        return Cache::remember("chapter.{$chapterId}.hadiths", 3600, function () use ($chapterId) {
            return \App\Models\Hadith::where('chapter_id', $chapterId)
                ->orderBy('hadith_number')
                ->get();
        });
    }
    
    /**
     * Cache hadith details
     */
    public function getHadithDetails($hadithId)
    {
        return Cache::remember("hadith.{$hadithId}.details", 3600, function () use ($hadithId) {
            return \App\Models\Hadith::with(['chapter', 'audioFile'])->find($hadithId);
        });
    }
    
    /**
     * Cache search results
     */
    public function getSearchResults($query, $page = 1)
    {
        $cacheKey = "search.{$query}.page.{$page}";
        
        return Cache::remember($cacheKey, 1800, function () use ($query, $page) {
            return \App\Models\Hadith::where(function ($q) use ($query) {
                    $q->where('arabic_text', 'ILIKE', "%{$query}%")
                      ->orWhere('translation', 'ILIKE', "%{$query}%")
                      ->orWhere('interpretation', 'ILIKE', "%{$query}%");
                })
                ->with('chapter')
                ->paginate(10, ['*'], 'page', $page);
        });
    }
    
    /**
     * Clear cache for specific hadith
     */
    public function clearHadithCache($hadithId)
    {
        $hadith = \App\Models\Hadith::find($hadithId);
        
        if ($hadith) {
            // Clear hadith details cache
            Cache::forget("hadith.{$hadithId}.details");
            
            // Clear chapter hadiths cache
            Cache::forget("chapter.{$hadith->chapter_id}.hadiths");
            
            // Clear any search results that might contain this hadith
            // This is more complex and might require a different approach
        }
    }
    
    /**
     * Clear all cache
     */
    public function clearAllCache()
    {
        Cache::flush();
    }
}
```

### 9.3 Implementasi Cache di Controller
```php
<?php

namespace App\Http\Controllers;

use App\Models\Chapter;
use App\Models\Hadith;
use App\Services\CacheService;
use Illuminate\Http\Request;

class ChapterController extends Controller
{
    protected $cacheService;
    
    public function __construct(CacheService $cacheService)
    {
        $this->cacheService = $cacheService;
    }
    
    public function index()
    {
        $chapters = $this->cacheService->getChaptersList();
        return view('chapters.index', compact('chapters'));
    }
    
    public function show(Chapter $chapter)
    {
        $hadiths = $this->cacheService->getHadithsByChapter($chapter->id);
        return view('chapters.show', compact('chapter', 'hadiths'));
    }
}
```

### 9.4 Cache untuk Pencarian
```php
<?php

namespace App\Http\Controllers;

use App\Services\CacheService;
use App\Services\SearchService;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    protected $cacheService;
    protected $searchService;
    
    public function __construct(CacheService $cacheService, SearchService $searchService)
    {
        $this->cacheService = $cacheService;
        $this->searchService = $searchService;
    }
    
    public function search(Request $request)
    {
        $query = $request->input('q');
        $page = $request->input('page', 1);
        
        if (empty($query)) {
            return redirect()->back();
        }
        
        // Save search history if user is authenticated
        if (auth()->check()) {
            auth()->user()->searchHistory()->create([
                'query' => $query,
                'results_count' => 0, // Will be updated after getting results
            ]);
        }
        
        $results = $this->cacheService->getSearchResults($query, $page);
        
        // Update search history with actual results count
        if (auth()->check()) {
            $searchHistory = auth()->user()->searchHistory()
                ->where('query', $query)
                ->latest()
                ->first();
                
            if ($searchHistory) {
                $searchHistory->results_count = $results->total();
                $searchHistory->save();
            }
        }
        
        return view('search.results', compact('query', 'results'));
    }
}
```

### 9.5 Middleware untuk Cache Headers
```php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CacheHeaders
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);
        
        if ($request->isMethod('GET')) {
            $response->header('Cache-Control', 'public, max-age=3600');
            $response->header('Expires', gmdate('D, d M Y H:i:s \G\M\T', time() + 3600));
        }
        
        return $response;
    }
}
```

## 10. Desain dan Implementasi Antarmuka Pengguna (UI/UX)

### 10.1 Layout Utama
```blade
{{-- resources/views/layouts/app.blade.php --}}
<!DOCTYPE html>
<html lang="id" dir="ltr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Buku Syamail Muhammadiyah - Ensiklopedia Hadits Ciri Fisik dan Kepribadian Rasulullah">
    <title>@yield('title', 'Buku Syamail Muhammadiyah')</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=family=Inter:wght@400;500;600;700&family=Amiri:ital,wght@0,400;0,700;1,400&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Custom styles -->
    <style>
        :root {
            --arabic-font: 'Amiri', serif;
            --latin-font: 'Inter', sans-serif;
        }
        
        .arabic-text {
            font-family: var(--arabic-font);
            line-height: 2;
            text-align: right;
            direction: rtl;
        }
        
        .latin-text {
            font-family: var(--latin-font);
        }
    </style>
</head>
<body class="bg-gray-50 text-gray-900 latin-text">
    @include('layouts.partials.header')
    
    <main class="min-h-screen">
        @yield('content')
    </main>
    
    @include('layouts.partials.footer')
    
    @stack('scripts')
</body>
</html>
```

### 10.2 Header Navigasi
```blade
{{-- resources/views/layouts/partials/header.blade.php --}}
<header class="bg-white shadow-sm sticky top-0 z-50">
    <div class="container mx-auto px-4">
        <div class="flex justify-between items-center h-16">
            <!-- Logo -->
            <div class="flex-shrink-0">
                <a href="{{ route('home') }}" class="flex items-center">
                    <span class="text-xl font-bold text-emerald-700">Syamail Muhammadiyah</span>
                </a>
            </div>
            
            <!-- Desktop Navigation -->
            <nav class="hidden md:flex space-x-8">
                <a href="{{ route('chapters.index') }}" class="text-gray-700 hover:text-emerald-600 px-3 py-2 rounded-md font-medium">
                    Daftar Bab
                </a>
                <a href="{{ route('search.form') }}" class="text-gray-700 hover:text-emerald-600 px-3 py-2 rounded-md font-medium">
                    Pencarian
                </a>
                @auth
                <a href="{{ route('bookmarks.index') }}" class="text-gray-700 hover:text-emerald-600 px-3 py-2 rounded-md font-medium">
                    Bookmark
                </a>
                <a href="{{ route('profile.show') }}" class="text-gray-700 hover:text-emerald-600 px-3 py-2 rounded-md font-medium">
                    Profil
                </a>
                @endauth
            </nav>
            
            <!-- Mobile menu button -->
            <div class="md:hidden">
                <button type="button" class="text-gray-700 hover:text-emerald-600 focus:outline-none" id="mobile-menu-button">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
            </div>
            
            <!-- Auth Links -->
            <div class="hidden md:flex items-center space-x-4">
                @guest
                <a href="{{ route('login') }}" class="text-gray-700 hover:text-emerald-600 px-3 py-2 rounded-md font-medium">
                    Masuk
                </a>
                <a href="{{ route('register') }}" class="bg-emerald-600 text-white px-4 py-2 rounded-md font-medium hover:bg-emerald-700">
                    Daftar
                </a>
                @else
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="text-gray-700 hover:text-emerald-600 px-3 py-2 rounded-md font-medium">
                        Keluar
                    </button>
                </form>
                @endguest
            </div>
        </div>
    </div>
    
    <!-- Mobile menu -->
    <div class="md:hidden hidden" id="mobile-menu">
        <div class="px-2 pt-2 pb-3 space-y-1 sm:px-3 bg-white border-t">
            <a href="{{ route('chapters.index') }}" class="text-gray-700 hover:text-emerald-600 block px-3 py-2 rounded-md font-medium">
                Daftar Bab
            </a>
            <a href="{{ route('search.form') }}" class="text-gray-700 hover:text-emerald-600 block px-3 py-2 rounded-md font-medium">
                Pencarian
            </a>
            @auth
            <a href="{{ route('bookmarks.index') }}" class="text-gray-700 hover:text-emerald-600 block px-3 py-2 rounded-md font-medium">
                Bookmark
            </a>
            <a href="{{ route('profile.show') }}" class="text-gray-700 hover:text-emerald-600 block px-3 py-2 rounded-md font-medium">
                Profil
            </a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="text-gray-700 hover:text-emerald-600 block w-full text-left px-3 py-2 rounded-md font-medium">
                    Keluar
                </button>
            </form>
            @else
            <a href="{{ route('login') }}" class="text-gray-700 hover:text-emerald-600 block px-3 py-2 rounded-md font-medium">
                Masuk
            </a>
            <a href="{{ route('register') }}" class="text-gray-700 hover:text-emerald-600 block px-3 py-2 rounded-md font-medium">
                Daftar
            </a>
            @endauth
        </div>
    </div>
</header>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const mobileMenuButton = document.getElementById('mobile-menu-button');
        const mobileMenu = document.getElementById('mobile-menu');
        
        mobileMenuButton.addEventListener('click', function() {
            mobileMenu.classList.toggle('hidden');
        });
    });
</script>
```

### 10.3 Halaman Daftar Bab
```blade
{{-- resources/views/chapters/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Daftar Bab - Buku Syamail Muhammadiyah')

@section('content')
<div class="bg-white py-12">
    <div class="container mx-auto px-4">
        <div class="max-w-4xl mx-auto">
            <div class="text-center mb-12">
                <h1 class="text-3xl font-bold text-gray-900 mb-4">Kitab Syamail An-Nabiy</h1>
                <p class="text-lg text-gray-600">
                    Karya Imam At-Tirmidzi yang menghimpun 56 bab yang menggambarkan pribadi dan fisik Rasulullah SAW secara terperinci
                </p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @foreach($chapters as $chapter)
                <div class="bg-white border border-gray-200 rounded-lg shadow-sm hover:shadow-md transition-shadow duration-300">
                    <a href="{{ route('chapters.show', $chapter->id) }}" class="block p-6">
                        <div class="flex items-start">
                            <div class="flex-shrink-0 h-12 w-12 rounded-full bg-emerald-100 flex items-center justify-center">
                                <span class="text-emerald-800 font-bold">{{ $chapter->chapter_number }}</span>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ $chapter->title }}</h3>
                                <p class="text-gray-600 text-sm">{{ $chapter->description }}</p>
                            </div>
                        </div>
                    </a>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection
```

## 11. Implementasi Sistem Autentikasi dan Autorisasi

### 11.1 Custom User Registration
```php
<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class RegisteredUserController extends Controller
{
    public function create()
    {
        return view('auth.register');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'phone' => ['nullable', 'string', 'max:20', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect(RouteServiceProvider::HOME);
    }
}
```

### 11.2 Custom Login
```php
<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    protected $redirectTo = RouteServiceProvider::HOME;

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function showLoginForm()
    {
        return view('auth.login');
    }

    protected function validateLogin(Request $request)
    {
        $request->validate([
            $this->username() => 'required|string',
            'password' => 'required|string',
        ]);
    }

    public function username()
    {
        return 'email';
    }

    protected function sendFailedLoginResponse(Request $request)
    {
        throw ValidationException::withMessages([
            $this->username() => [trans('auth.failed')],
        ]);
    }
}
```

### 11.3 Profile Controller
```php
<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function show()
    {
        return view('profile.show', ['user' => auth()->user()]);
    }

    public function edit()
    {
        return view('profile.edit', ['user' => auth()->user()]);
    }

    public function update(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'phone' => ['nullable', 'string', 'max:20', Rule::unique('users')->ignore($user->id)],
        ]);

        $user->update($validated);

        return redirect()->route('profile.show')
            ->with('status', 'profile-updated');
    }

    public function updatePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => ['required', 'string', 'current_password'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        auth()->user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()->route('profile.show')
            ->with('status', 'password-updated');
    }
}
```

### 11.4 Middleware untuk Autorisasi
```php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureUserIsVerified
{
    public function handle(Request $request, Closure $next)
    {
        if (! $request->user()->hasVerifiedEmail()) {
            return redirect()->route('verification.notice');
        }

        return $next($request);
    }
}
```

## 12. Implementasi Fitur Pencarian Konten

### 12.1 Search Service
```php
<?php

namespace App\Services;

use App\Models\Hadith;
use App\Models\SearchHistory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SearchService
{
    /**
     * Search hadiths by query
     */
    public function search($query, $page = 1, $perPage = 10)
    {
        return Hadith::where(function ($q) use ($query) {
                $q->where('arabic_text', 'ILIKE', "%{$query}%")
                  ->orWhere('translation', 'ILIKE', "%{$query}%")
                  ->orWhere('interpretation', 'ILIKE', "%{$query}%");
            })
            ->with('chapter')
            ->paginate($perPage, ['*'], 'page', $page);
    }
    
    /**
     * Advanced search with filters
     */
    public function advancedSearch($query, $filters = [], $page = 1, $perPage = 10)
    {
        $hadiths = Hadith::with('chapter');
        
        // Apply search query
        if (!empty($query)) {
            $hadiths->where(function ($q) use ($query) {
                $q->where('arabic_text', 'ILIKE', "%{$query}%")
                  ->orWhere('translation', 'ILIKE', "%{$query}%")
                  ->orWhere('interpretation', 'ILIKE', "%{$query}%");
            });
        }
        
        // Apply chapter filter
        if (!empty($filters['chapter_id'])) {
            $hadiths->where('chapter_id', $filters['chapter_id']);
        }
        
        // Apply narration source filter
        if (!empty($filters['narration_source'])) {
            $hadiths->where('narration_source', 'ILIKE', "%{$filters['narration_source']}%");
        }
        
        return $hadiths->paginate($perPage, ['*'], 'page', $page);
    }
    
    /**
     * Get popular searches
     */
    public function getPopularSearches($limit = 10)
    {
        return SearchHistory::select('query', DB::raw('count(*) as count'))
            ->groupBy('query')
            ->orderBy('count', 'desc')
            ->limit($limit)
            ->get();
    }
    
    /**
     * Get user search history
     */
    public function getUserSearchHistory($userId, $limit = 10)
    {
        return SearchHistory::where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }
    
    /**
     * Save search to history
     */
    public function saveSearchHistory($query, $userId = null, $resultsCount = 0)
    {
        if (empty($query)) {
            return;
        }
        
        $data = [
            'query' => $query,
            'results_count' => $resultsCount,
        ];
        
        if ($userId) {
            $data['user_id'] = $userId;
        }
        
        return SearchHistory::create($data);
    }
    
    /**
     * Clear search history
     */
    public function clearSearchHistory($userId = null)
    {
        if ($userId) {
            return SearchHistory::where('user_id', $userId)->delete();
        }
        
        return SearchHistory::whereNull('user_id')->delete();
    }
}
```

### 12.2 Search Controller
```php
<?php

namespace App\Http\Controllers;

use App\Services\CacheService;
use App\Services\SearchService;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    protected $searchService;
    protected $cacheService;
    
    public function __construct(SearchService $searchService, CacheService $cacheService)
    {
        $this->searchService = $searchService;
        $this->cacheService = $cacheService;
    }
    
    public function form()
    {
        $popularSearches = $this->searchService->getPopularSearches(5);
        
        $userSearchHistory = null;
        if (auth()->check()) {
            $userSearchHistory = $this->searchService->getUserSearchHistory(auth()->id(), 5);
        }
        
        return view('search.form', compact('popularSearches', 'userSearchHistory'));
    }
    
    public function search(Request $request)
    {
        $query = $request->input('q');
        $page = $request->input('page', 1);
        
        if (empty($query)) {
            return redirect()->route('search.form');
        }
        
        // Get search results with cache
        $results = $this->cacheService->getSearchResults($query, $page);
        
        // Save search history
        $userId = auth()->check() ? auth()->id() : null;
        $this->searchService->saveSearchHistory($query, $userId, $results->total());
        
        // Get popular searches and user history
        $popularSearches = $this->searchService->getPopularSearches(5);
        
        $userSearchHistory = null;
        if (auth()->check()) {
            $userSearchHistory = $this->searchService->getUserSearchHistory(auth()->id(), 5);
        }
        
        return view('search.results', compact('query', 'results', 'popularSearches', 'userSearchHistory'));
    }
    
    public function advanced(Request $request)
    {
        $query = $request->input('q');
        $filters = $request->only(['chapter_id', 'narration_source']);
        $page = $request->input('page', 1);
        
        $results = $this->searchService->advancedSearch($query, $filters, $page);
        
        // Save search history
        $userId = auth()->check() ? auth()->id() : null;
        $this->searchService->saveSearchHistory($query, $userId, $results->total());
        
        // Get chapters for filter dropdown
        $chapters = $this->cacheService->getChaptersList();
        
        return view('search.advanced-results', compact('query', 'filters', 'results', 'chapters'));
    }
}
```

### 12.3 Search Form View
```blade
{{-- resources/views/search/form.blade.php --}}
@extends('layouts.app')

@section('title', 'Pencarian - Buku Syamail Muhammadiyah')

@section('content')
<div class="bg-white py-12">
    <div class="container mx-auto px-4">
        <div class="max-w-3xl mx-auto">
            <div class="text-center mb-12">
                <h1 class="text-3xl font-bold text-gray-900 mb-4">Pencarian Hadits</h1>
                <p class="text-lg text-gray-600">
                    Cari hadits berdasarkan teks Arab, terjemahan, atau tafsir
                </p>
            </div>
            
            <div class="bg-white rounded-lg shadow-md p-6 mb-8">
                <form action="{{ route('search.results') }}" method="GET" class="space-y-4">
                    <div>
                        <label for="query" class="block text-sm font-medium text-gray-700 mb-1">
                            Kata Kunci Pencarian
                        </label>
                        <input type="text" 
                               id="query" 
                               name="q" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-emerald-500 focus:border-emerald-500" 
                               placeholder="Masukkan kata kunci pencarian..."
                               required>
                    </div>
                    
                    <div class="flex justify-between items-center">
                        <a href="{{ route('search.advanced') }}" class="text-emerald-600 hover:text-emerald-800 text-sm">
                            Pencarian Lanjutan
                        </a>
                        
                        <button type="submit" class="px-6 py-2 bg-emerald-600 text-white rounded-md hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500">
                            Cari
                        </button>
                    </div>
                </form>
            </div>
            
            <!-- Popular Searches -->
            @if($popularSearches->count() > 0)
            <div class="bg-white rounded-lg shadow-md p-6 mb-8">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Pencarian Populer</h2>
                <div class="flex flex-wrap gap-2">
                    @foreach($popularSearches as $search)
                    <a href="{{ route('search.results', ['q' => $search->query]) }}" 
                       class="px-3 py-1 bg-gray-100 text-gray-700 rounded-full text-sm hover:bg-gray-200">
                        {{ $search->query }}
                    </a>
                    @endforeach
                </div>
            </div>
            @endif
            
            <!-- User Search History -->
            @auth
            @if($userSearchHistory && $userSearchHistory->count() > 0)
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-lg font-semibold text-gray-900">Riwayat Pencarian</h2>
                    <form action="{{ route('search.history.clear') }}" method="POST" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-sm text-red-600 hover:text-red-800">
                            Hapus Riwayat
                        </button>
                    </form>
                </div>
                <ul class="space-y-2">
                    @foreach($userSearchHistory as $search)
                    <li>
                        <a href="{{ route('search.results', ['q' => $search->query]) }}" 
                           class="text-emerald-600 hover:text-emerald-800">
                            {{ $search->query }}
                        </a>
                        <span class="text-gray-500 text-sm ml-2">
                            {{ $search->created_at->diffForHumans() }}
                        </span>
                    </li>
                    @endforeach
                </ul>
            </div>
            @endif
            @endauth
        </div>
    </div>
</div>
@endsection
```

## 13. Implementasi Fitur Bookmark dan Catatan Pribadi

### 13.1 Bookmark Model
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Bookmark extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'hadith_id',
        'notes',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function hadith(): BelongsTo
    {
        return $this->belongsTo(Hadith::class);
    }
}
```

### 13.2 UserNote Model
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserNote extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'hadith_id',
        'note_content',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function hadith(): BelongsTo
    {
        return $this->belongsTo(Hadith::class);
    }
}
```

### 13.3 Bookmark Controller
```php
<?php

namespace App\Http\Controllers;

use App\Models\Bookmark;
use App\Models\Hadith;
use Illuminate\Http\Request;

class BookmarkController extends Controller
{
    public function index()
    {
        $bookmarks = auth()->user()->bookmarks()
            ->with('hadith.chapter')
            ->latest()
            ->paginate(10);
            
        return view('bookmarks.index', compact('bookmarks'));
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'hadith_id' => 'required|exists:hadiths,id',
            'notes' => 'nullable|string|max:1000',
        ]);
        
        // Check if bookmark already exists
        $existingBookmark = auth()->user()->bookmarks()
            ->where('hadith_id', $request->hadith_id)
            ->first();
            
        if ($existingBookmark) {
            return redirect()->back()
                ->with('error', 'Hadits ini sudah di-bookmark.');
        }
        
        $bookmark = auth()->user()->bookmarks()->create([
            'hadith_id' => $request->hadith_id,
            'notes' => $request->notes,
        ]);
        
        return redirect()->back()
            ->with('success', 'Hadits berhasil ditambahkan ke bookmark.');
    }
    
    public function update(Request $request, Bookmark $bookmark)
    {
        $this->authorize('update', $bookmark);
        
        $request->validate([
            'notes' => 'nullable|string|max:1000',
        ]);
        
        $bookmark->update([
            'notes' => $request->notes,
        ]);
        
        return redirect()->back()
            ->with('success', 'Catatan bookmark berhasil diperbarui.');
    }
    
    public function destroy(Bookmark $bookmark)
    {
        $this->authorize('delete', $bookmark);
        
        $bookmark->delete();
        
        return redirect()->back()
            ->with('success', 'Bookmark berhasil dihapus.');
    }
}
```

### 13.4 UserNote Controller
```php
<?php

namespace App\Http\Controllers;

use App\Models\Hadith;
use App\Models\UserNote;
use Illuminate\Http\Request;

class UserNoteController extends Controller
{
    public function index()
    {
        $notes = auth()->user()->notes()
            ->with('hadith.chapter')
            ->latest()
            ->paginate(10);
            
        return view('notes.index', compact('notes'));
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'hadith_id' => 'required|exists:hadiths,id',
            'note_content' => 'required|string|min:1|max:5000',
        ]);
        
        // Check if note already exists for this hadith
        $existingNote = auth()->user()->notes()
            ->where('hadith_id', $request->hadith_id)
            ->first();
            
        if ($existingNote) {
            return redirect()->back()
                ->with('error', 'Anda sudah memiliki catatan untuk hadits ini.');
        }
        
        $note = auth()->user()->notes()->create([
            'hadith_id' => $request->hadith_id,
            'note_content' => $request->note_content,
        ]);
        
        return redirect()->back()
            ->with('success', 'Catatan berhasil ditambahkan.');
    }
    
    public function update(Request $request, UserNote $note)
    {
        $this->authorize('update', $note);
        
        $request->validate([
            'note_content' => 'required|string|min:1|max:5000',
        ]);
        
        $note->update([
            'note_content' => $request->note_content,
        ]);
        
        return redirect()->back()
            ->with('success', 'Catatan berhasil diperbarui.');
    }
    
    public function destroy(UserNote $note)
    {
        $this->authorize('delete', $note);
        
        $note->delete();
        
        return redirect()->back()
            ->with('success', 'Catatan berhasil dihapus.');
    }
}
```

### 13.5 Bookmark View
```blade
{{-- resources/views/bookmarks/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Bookmark Saya - Buku Syamail Muhammadiyah')

@section('content')
<div class="bg-white py-12">
    <div class="container mx-auto px-4">
        <div class="max-w-4xl mx-auto">
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900 mb-2">Bookmark Saya</h1>
                <p class="text-gray-600">Daftar hadits yang Anda bookmark</p>
            </div>
            
            @if($bookmarks->count() > 0)
            <div class="space-y-6">
                @foreach($bookmarks as $bookmark)
                <div class="bg-white border border-gray-200 rounded-lg shadow-sm p-6">
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-1">
                                <a href="{{ route('hadiths.show', $bookmark->hadith->id) }}" 
                           class="text-emerald-600 hover:text-emerald-800">
                            Hadits {{ $bookmark->hadith->hadith_number }}
                        </a>
                            </h3>
                            <p class="text-gray-600">
                                Bab {{ $bookmark->hadith->chapter->chapter_number }}: {{ $bookmark->hadith->chapter->title }}
                            </p>
                        </div>
                        
                        <div class="flex space-x-2">
                            <a href="{{ route('hadiths.show', $bookmark->hadith->id) }}" 
                       class="text-blue-600 hover:text-blue-800">
                                Lihat Hadits
                            </a>
                            <form action="{{ route('bookmarks.destroy', $bookmark->id) }}" 
                          method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                class="text-red-600 hover:text-red-800"
                                onclick="return confirm('Apakah Anda yakin ingin menghapus bookmark ini?')">
                                    Hapus
                                </button>
                            </form>
                        </div>
                    </div>
                    
                    @if($bookmark->notes)
                    <div class="mt-4 p-4 bg-yellow-50 rounded-lg">
                        <h4 class="font-medium text-gray-900 mb-2">Catatan Bookmark:</h4>
                        <p class="text-gray-700">{{ $bookmark->notes }}</p>
                    </div>
                    @endif
                </div>
                @endforeach
            </div>
            
            <div class="mt-8">
                {{ $bookmarks->links() }}
            </div>
            @else
            <div class="bg-white border border-gray-200 rounded-lg shadow-sm p-12 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z" />
                </svg>
                <h3 class="mt-2 text-lg font-medium text-gray-900">Belum ada bookmark</h3>
                <p class="mt-1 text-gray-500">Anda belum menambahkan hadits apapun ke bookmark.</p>
                <div class="mt-6">
                    <a href="{{ route('chapters.index') }}" 
               class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-emerald-600 hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500">
                        Jelajahi Hadits
                    </a>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
```

## 14. Optimasi Performa dan Keamanan Aplikasi

### 14.1 Optimasi Database
```php
// config/database.php
'pgsql' => [
    'driver' => 'pgsql',
    'url' => env('DB_URL'),
    'host' => env('DB_HOST', '127.0.0.1'),
    'port' => env('DB_PORT', '5432'),
    'database' => env('DB_DATABASE', 'laravel'),
    'username' => env('DB_USERNAME', 'root'),
    'password' => env('DB_PASSWORD', ''),
    'charset' => 'utf8',
    'prefix' => '',
    'prefix_indexes' => true,
    'search_path' => 'public',
    'sslmode' => 'prefer',
    'options' => [
        PDO::ATTR_EMULATE_PREPARES => true,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_PERSISTENT => true, // Persistent connection
    ],
],
```

### 14.2 Implementasi Query Optimization
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Hadith extends Model
{
    use HasFactory;

    // ... existing code ...

    /**
     * Scope for eager loading common relationships
     */
    public function scopeWithCommonRelations($query)
    {
        return $query->with(['chapter', 'audioFile']);
    }

    /**
     * Get hadith by chapter with optimized query
     */
    public static function getByChapter($chapterId, $page = 1, $perPage = 10)
    {
        return static::where('chapter_id', $chapterId)
            ->withCommonRelations()
            ->orderBy('hadith_number')
            ->paginate($perPage, ['*'], 'page', $page);
    }

    /**
     * Search hadiths with optimized query
     */
    public static function searchHadiths($query, $page = 1, $perPage = 10)
    {
        return static::where(function ($q) use ($query) {
                $q->where('arabic_text', 'ILIKE', "%{$query}%")
                  ->orWhere('translation', 'ILIKE', "%{$query}%")
                  ->orWhere('interpretation', 'ILIKE', "%{$query}%");
            })
            ->withCommonRelations()
            ->paginate($perPage, ['*'], 'page', $page);
    }
}
```

### 14.3 Middleware untuk Keamanan
```php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SecurityHeaders
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);
        
        // Add security headers
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-XSS-Protection', '1; mode=block');
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('Content-Security-Policy', "default-src 'self'; script-src 'self' 'unsafe-inline' 'unsafe-eval'; style-src 'self' 'unsafe-inline'; font-src 'self' data:; img-src 'self' data:; connect-src 'self'; media-src 'self' data:; object-src 'none'; base-uri 'self'; form-action 'self'; frame-ancestors 'none';");
        
        return $response;
    }
}
```

### 14.4 Implementasi Rate Limiting
```php
// routes/web.php
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;

// Configure rate limiters
RateLimiter::for('search', function (Request $request) {
    return Limit::perMinute(30)->by($request->user()?->id ?: $request->ip());
});

RateLimiter::for('auth', function (Request $request) {
    return Limit::perMinute(5)->by($request->user()?->id ?: $request->ip());
});

// Apply rate limiting to routes
Route::middleware(['throttle:search'])->group(function () {
    Route::get('/search', [SearchController::class, 'search'])->name('search.results');
});

Route::middleware(['throttle:auth'])->group(function () {
    Route::post('/login', [LoginController::class, 'login']);
    Route::post('/register', [RegisteredUserController::class, 'store']);
    Route::post('/forgot-password', [PasswordResetLinkController::class, 'store']);
});
```

### 14.5 Implementasi HTTPS
```php
// app/Providers/AppServiceProvider.php
namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
{
    public function boot()
    {
        // Force HTTPS in production
        if (app()->environment('production')) {
            URL::forceScheme('https');
        }
    }
}
```

## 15. Pengujian Aplikasi dan Debugging

### 15.1 Feature Tests
```php
<?php

namespace Tests\Feature;

use App\Models\Chapter;
use App\Models\Hadith;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HadithTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_view_hadith()
    {
        $chapter = Chapter::factory()->create();
        $hadith = Hadith::factory()->create(['chapter_id' => $chapter->id]);
        
        $response = $this->get(route('hadiths.show', $hadith->id));
        
        $response->assertStatus(200);
        $response->assertViewIs('hadiths.show');
        $response->assertSee($hadith->arabic_text);
        $response->assertSee($hadith->translation);
    }
    
    public function test_user_can_bookmark_hadith()
    {
        $user = User::factory()->create();
        $chapter = Chapter::factory()->create();
        $hadith = Hadith::factory()->create(['chapter_id' => $chapter->id]);
        
        $response = $this->actingAs($user)
            ->post(route('bookmarks.store'), [
                'hadith_id' => $hadith->id,
                'notes' => 'Test bookmark note',
            ]);
            
        $response->assertRedirect();
        $this->assertDatabaseHas('bookmarks', [
            'user_id' => $user->id,
            'hadith_id' => $hadith->id,
            'notes' => 'Test bookmark note',
        ]);
    }
    
    public function test_user_can_search_hadiths()
    {
        $chapter = Chapter::factory()->create();
        $hadith = Hadith::factory()->create([
            'chapter_id' => $chapter->id,
            'translation' => 'This is a test hadith about the Prophet',
        ]);
        
        $response = $this->get(route('search.results', ['q' => 'Prophet']));
        
        $response->assertStatus(200);
        $response->assertViewIs('search.results');
        $response->assertSee($hadith->translation);
    }
}
```

### 15.2 Unit Tests
```php
<?php

namespace Tests\Unit;

use App\Services\UserEncryptionService;
use Tests\TestCase;

class UserEncryptionServiceTest extends TestCase
{
    protected $encryptionService;
    
    protected function setUp(): void
    {
        parent::setUp();
        
        $this->encryptionService = new UserEncryptionService();
    }
    
    public function test_encrypt_and_decrypt_string()
    {
        $originalString = 'test@example.com';
        
        $encrypted = $this->encryptionService->encrypt($originalString);
        $decrypted = $this->encryptionService->decrypt($encrypted);
        
        $this->assertEquals($originalString, $decrypted);
        $this->assertNotEquals($originalString, $encrypted);
    }
    
    public function test_decrypt_invalid_string_returns_null()
    {
        $invalidString = 'invalid-encrypted-string';
        
        $result = $this->encryptionService->decrypt($invalidString);
        
        $this->assertNull($result);
    }
}
```

### 15.3 Browser Tests dengan Laravel Dusk
```php
<?php

namespace Tests\Browser;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class HadithBrowserTest extends DuskTestCase
{
    use DatabaseMigrations;
    
    public function test_user_can_bookmark_hadith()
    {
        $user = User::factory()->create();
        $chapter = \App\Models\Chapter::factory()->create();
        $hadith = \App\Models\Hadith::factory()->create(['chapter_id' => $chapter->id]);
        
        $this->browse(function (Browser $browser) use ($user, $hadith) {
            $browser->loginAs($user)
                ->visit(route('hadiths.show', $hadith->id