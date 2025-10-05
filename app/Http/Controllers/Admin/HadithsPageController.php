<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreHadithRequest;
use App\Http\Requests\UpdateHadithRequest;
use App\Models\Chapter;
use App\Models\Hadith;
use App\Models\AudioFile;
use App\Services\CacheService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use App\Jobs\ExtractAudioMetadata;

class HadithsPageController extends Controller
{
    protected CacheService $cache;

    public function __construct(CacheService $cache)
    {
        // Pertahanan berlapis meskipun sudah dalam route group admin
        $this->middleware(['auth', 'ensure.active', 'role.admin']);
        $this->cache = $cache;
    }

    /**
     * Tampilkan daftar Hadits (SSR Admin) dengan:
     * - Pagination 15
     * - Sorting ASC pada hadith_number
     * - Filter opsional: chapter_id, q (cari di arabic_text/translation)
     */
    public function index(Request $request)
    {
        $query = Hadith::query()
            ->with(['chapter:id,chapter_number,title'])
            ->select(['id', 'chapter_id', 'hadith_number', 'arabic_text', 'translation', 'created_at'])
            ->orderBy('hadith_number', 'asc');

        if ($request->filled('chapter_id')) {
            $cid = (int) $request->input('chapter_id');
            $query->where('chapter_id', $cid);
        }

        if ($request->filled('q')) {
            $q = trim((string) $request->input('q'));
            $driver = DB::connection()->getDriverName();

            // Tokenisasi untuk dukungan "kata mirip" (OR antar token), case-insensitive
            $tokens = preg_split('/\s+/', $q, -1, PREG_SPLIT_NO_EMPTY);
            $tokensLower = array_map(fn ($t) => mb_strtolower($t), $tokens);

            $query->where(function ($sub) use ($tokensLower, $driver) {
                foreach ($tokensLower as $t) {
                    if ($driver === 'pgsql') {
                        // PostgreSQL: gunakan ILIKE (case-insensitive) di beberapa kolom
                        $sub->orWhereRaw('arabic_text ILIKE ?', ['%' . $t . '%'])
                            ->orWhereRaw('translation ILIKE ?', ['%' . $t . '%'])
                            ->orWhereRaw('footnotes ILIKE ?', ['%' . $t . '%']);
                    } else {
                        // Lintas driver: bandingkan dengan LOWER(column) LIKE LOWER(?)
                        $sub->orWhereRaw('LOWER(arabic_text) LIKE ?', ['%' . $t . '%'])
                            ->orWhereRaw('LOWER(translation) LIKE ?', ['%' . $t . '%'])
                            ->orWhereRaw('LOWER(footnotes) LIKE ?', ['%' . $t . '%']);
                    }
                }
            });
        }

        $hadiths = $query->paginate(15)->appends($request->query());

        // Data pendukung filter
        $chapters = Chapter::select(['id', 'chapter_number', 'title'])
            ->orderBy('chapter_number', 'asc')
            ->get();

        if (view()->exists('admin.hadiths.index')) {
            return view('admin.hadiths.index', compact('hadiths', 'chapters'));
        }

        // Stub sementara jika view belum ada
        $html = '<!DOCTYPE html><html lang="id"><head><meta charset="utf-8"><title>Admin - Hadiths</title><link rel="stylesheet" href="' . asset('css/app.css') . '"></head><body class="bg-gray-50 text-gray-900"><div class="max-w-7xl mx-auto px-4 py-8">';
        $html .= '<h1 class="text-2xl font-bold mb-4">Daftar Hadits (SSR)</h1>';
        $html .= '<p class="text-gray-600 mb-4">View Blade admin.hadiths.index belum dibuat. Menampilkan stub sementara.</p>';
        $html .= '<p class="mb-4"><a href="' . route('admin.hadiths.create') . '" class="text-emerald-700 hover:underline">+ Tambah Hadits</a></p>';
        $html .= '<table class="min-w-full bg-white border"><thead><tr><th class="px-3 py-2 border-b text-left">ID</th><th class="px-3 py-2 border-b text-left">Bab</th><th class="px-3 py-2 border-b text-left">No. Hadits</th><th class="px-3 py-2 border-b text-left">Preview Terjemahan</th><th class="px-3 py-2 border-b text-left">Aksi</th></tr></thead><tbody>';
        foreach ($hadiths as $h) {
            $chapterLabel = $h->chapter ? ('Bab ' . e($h->chapter->chapter_number) . ' — ' . e($h->chapter->title)) : '-';
            $preview = mb_strimwidth((string) $h->translation, 0, 80, '...');
            $html .= '<tr>';
            $html .= '<td class="px-3 py-2 border-b">' . e($h->id) . '</td>';
            $html .= '<td class="px-3 py-2 border-b">' . $chapterLabel . '</td>';
            $html .= '<td class="px-3 py-2 border-b">' . e($h->hadith_number) . '</td>';
            $html .= '<td class="px-3 py-2 border-b text-gray-700">' . e($preview) . '</td>';
            $html .= '<td class="px-3 py-2 border-b"><a class="text-emerald-700 hover:underline" href="' . route('admin.hadiths.edit', $h->id) . '">Edit</a></td>';
            $html .= '</tr>';
        }
        $html .= '</tbody></table>';
        $html .= '<p class="text-sm text-gray-500 mt-3">Pagination: halaman ' . e($hadiths->currentPage()) . ' dari ' . e($hadiths->lastPage()) . '</p>';
        $html .= '<p class="mt-4"><a href="' . route('admin.index') . '" class="text-emerald-700 hover:underline">Kembali ke Dashboard Admin</a></p>';
        $html .= '</div></body></html>';

        return response($html);
    }

    /**
     * Tampilkan form pembuatan Hadits.
     */
    public function create()
    {
        $chapters = Chapter::select(['id', 'chapter_number', 'title'])
            ->orderBy('chapter_number', 'asc')
            ->get();

        if (view()->exists('admin.hadiths.create')) {
            return view('admin.hadiths.create', compact('chapters'));
        }

        // Stub
        $html = '<!DOCTYPE html><html lang="id"><head><meta charset="utf-8"><title>Buat Hadits</title><link rel="stylesheet" href="' . asset('css/app.css') . '"></head><body class="bg-gray-50 text-gray-900"><div class="max-w-xl mx-auto px-4 py-8">';
        $html .= '<h1 class="text-2xl font-bold mb-4">Form Buat Hadits (Stub)</h1>';
        $html .= '<p class="text-gray-600">Silakan buat view admin.hadiths.create untuk form lengkap.</p>';
        $html .= '<p class="mt-4"><a href="' . route('admin.hadiths.index') . '" class="text-emerald-700 hover:underline">Kembali</a></p>';
        $html .= '</div></body></html>';
        return response($html);
    }

    /**
     * Simpan Hadits baru.
     */
    public function store(StoreHadithRequest $request)
    {
        $data = $request->validated();

        $hadith = new Hadith();
        $hadith->chapter_id = $data['chapter_id'];
        $hadith->arabic_text = $data['arabic_text'];
        $hadith->translation = $data['translation'];
        $hadith->footnotes = $data['footnotes'] ?? null;
        $hadith->hadith_number = $data['hadith_number'];
        $hadith->save();

        // Upload audio file jika ada
        if ($request->hasFile('audio_file')) {
            $file = $request->file('audio_file');
            $storedPath = $file->store('audio', 'local'); // disimpan di storage/app/private/audio
            $size = $file->getSize();

            // Logging untuk validasi asumsi (durasi belum dihitung)
            $absolutePath = Storage::disk('local')->path($storedPath);
            $hasGetID3 = class_exists(\getID3::class);
            $hasFFMpeg = class_exists(\FFMpeg\FFMpeg::class);

            Log::info('Audio upload received (store)', [
                'hadith_id' => $hadith->id,
                'stored_path' => $storedPath,
                'absolute_path' => $absolutePath,
                'size_bytes' => $size,
                'has_getid3' => $hasGetID3,
                'has_ffmpeg' => $hasFFMpeg,
            ]);

            // Hitung durasi sinkron menggunakan getID3
            $durationSeconds = null;
            if (class_exists(\getID3::class)) {
                $getID3 = new \getID3();
                $info = $getID3->analyze($absolutePath);
                if (isset($info['playtime_seconds'])) {
                    $durationSeconds = (int) round((float) $info['playtime_seconds']);
                }
            }

            Log::info('Audio upload analyzed (store)', [
                'hadith_id' => $hadith->id,
                'stored_path' => $storedPath,
                'absolute_path' => $absolutePath,
                'size_bytes' => $size,
                'computed_duration' => $durationSeconds,
            ]);

            $created = AudioFile::create([
                'hadith_id' => $hadith->id,
                'file_path' => $storedPath,
                'duration' => $durationSeconds,
                'file_size' => $size,
            ]);
        }

        // Invalidate cache terkait
        $this->cache->clearChapterWithHadithsCache($hadith->chapter_id);
        $this->cache->clearHadithCache($hadith->id);

        return redirect()
            ->route('admin.hadiths.index')
            ->with('status', 'Hadits dibuat — Bab ' . $hadith->chapter_id . ' No. ' . $hadith->hadith_number);
    }

    /**
     * Tampilkan form edit Hadits.
     */
    public function edit(Hadith $hadith)
    {
        $chapters = Chapter::select(['id', 'chapter_number', 'title'])
            ->orderBy('chapter_number', 'asc')
            ->get();

        if (view()->exists('admin.hadiths.edit')) {
            return view('admin.hadiths.edit', compact('hadith', 'chapters'));
        }

        // Stub
        $html = '<!DOCTYPE html><html lang="id"><head><meta charset="utf-8"><title>Edit Hadits</title><link rel="stylesheet" href="' . asset('css/app.css') . '"></head><body class="bg-gray-50 text-gray-900"><div class="max-w-xl mx-auto px-4 py-8">';
        $html .= '<h1 class="text-2xl font-bold mb-4">Form Edit Hadits (Stub)</h1>';
        $html .= '<p class="text-gray-600 mb-2">ID: ' . e($hadith->id) . '</p>';
        $html .= '<p class="text-gray-600 mb-2">Bab: ' . e($hadith->chapter_id) . '</p>';
        $html .= '<p class="text-gray-600">Nomor: ' . e($hadith->hadith_number) . '</p>';
        $html .= '<p class="mt-4"><a href="' . route('admin.hadiths.index') . '" class="text-emerald-700 hover:underline">Kembali</a></p>';
        $html .= '</div></body></html>';
        return response($html);
    }

    /**
     * Perbarui Hadits yang ada.
     */
    public function update(UpdateHadithRequest $request, Hadith $hadith)
    {
        $data = $request->validated();

        $changed = [];

        if (array_key_exists('chapter_id', $data)) {
            $hadith->chapter_id = $data['chapter_id'];
            $changed[] = 'chapter_id';
        }
        if (array_key_exists('arabic_text', $data)) {
            $hadith->arabic_text = $data['arabic_text'];
            $changed[] = 'arabic_text';
        }
        if (array_key_exists('translation', $data)) {
            $hadith->translation = $data['translation'];
            $changed[] = 'translation';
        }
        if (array_key_exists('footnotes', $data)) {
            $hadith->footnotes = $data['footnotes'];
            $changed[] = 'footnotes';
        }
        if (array_key_exists('hadith_number', $data)) {
            $hadith->hadith_number = $data['hadith_number'];
            $changed[] = 'hadith_number';
        }

        if (empty($changed)) {
            return back()->with('status', 'Tidak ada perubahan yang diterapkan');
        }

        $hadith->save();

        // Upload/replace audio file jika ada
        if ($request->hasFile('audio_file')) {
            $file = $request->file('audio_file');
            $storedPath = $file->store('audio', 'local'); // disimpan di storage/app/private/audio
            $size = $file->getSize();

            // Logging untuk validasi asumsi (durasi belum dihitung)
            $absolutePath = Storage::disk('local')->path($storedPath);
            $hasGetID3 = class_exists(\getID3::class);
            $hasFFMpeg = class_exists(\FFMpeg\FFMpeg::class);

            Log::info('Audio upload received (update)', [
                'hadith_id' => $hadith->id,
                'stored_path' => $storedPath,
                'absolute_path' => $absolutePath,
                'size_bytes' => $size,
                'has_getid3' => $hasGetID3,
                'has_ffmpeg' => $hasFFMpeg,
                'existing_audio_id' => optional($hadith->audioFile)->id,
            ]);

            $existing = $hadith->audioFile;
            if ($existing) {
                // Hapus file lama jika ada
                if ($existing->file_path) {
                    Storage::disk('local')->delete($existing->file_path);
                }
                // Hitung durasi sinkron menggunakan getID3
                $durationSeconds = null;
                if (class_exists(\getID3::class)) {
                    $getID3 = new \getID3();
                    $info = $getID3->analyze($absolutePath);
                    if (isset($info['playtime_seconds'])) {
                        $durationSeconds = (int) round((float) $info['playtime_seconds']);
                    }
                }

                $existing->file_path = $storedPath;
                $existing->file_size = $size;
                $existing->duration = $durationSeconds;
                $existing->save();

                Log::info('Audio file replaced', [
                    'audio_file_id' => $existing->id,
                    'new_path' => $storedPath,
                    'size_bytes' => $size,
                    'computed_duration' => $durationSeconds,
                ]);

                // Durasi dihitung sinkron; tidak perlu dispatch job di sini
            } else {
                // Hitung durasi sinkron menggunakan getID3
                $durationSeconds = null;
                if (class_exists(\getID3::class)) {
                    $getID3 = new \getID3();
                    $info = $getID3->analyze($absolutePath);
                    if (isset($info['playtime_seconds'])) {
                        $durationSeconds = (int) round((float) $info['playtime_seconds']);
                    }
                }

                $created = AudioFile::create([
                    'hadith_id' => $hadith->id,
                    'file_path' => $storedPath,
                    'duration' => $durationSeconds,
                    'file_size' => $size,
                ]);

                Log::info('Audio file created', [
                    'audio_file_id' => $created->id,
                    'path' => $storedPath,
                    'size_bytes' => $size,
                    'computed_duration' => $durationSeconds,
                ]);

                // Durasi dihitung sinkron; tidak perlu dispatch job di sini
            }

            $changed[] = 'audio_file';
        }

        // Invalidate cache terkait
        $this->cache->clearHadithCache($hadith->id);
        $this->cache->clearChapterWithHadithsCache($hadith->chapter_id);

        return redirect()
            ->route('admin.hadiths.index', $request->query())
            ->with('status', 'Hadits diperbarui — ' . implode(', ', $changed));
    }

    /**
     * Hapus Hadits.
     */
    public function destroy(Hadith $hadith)
    {
        $id = $hadith->id;
        $chapterId = $hadith->chapter_id;
        $label = 'Bab ' . $chapterId . ' No. ' . $hadith->hadith_number;

        $hadith->delete();

        // Invalidate cache terkait
        $this->cache->clearHadithCache($id);
        $this->cache->clearChapterWithHadithsCache($chapterId);

        return redirect()
            ->route('admin.hadiths.index')
            ->with('status', 'Hadits dihapus — ' . $label);
    }
}