<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreChapterRequest;
use App\Http\Requests\UpdateChapterRequest;
use App\Models\Chapter;
use App\Services\CacheService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ChaptersPageController extends Controller
{
    protected CacheService $cache;

    public function __construct(CacheService $cache)
    {
        // Pertahanan berlapis meski sudah ada group middleware di routes/web.php
        $this->middleware(['auth', 'ensure.active', 'role.admin']);
        $this->cache = $cache;
    }

    /**
     * Tampilkan daftar Chapters (SSR Admin) dengan pagination 15 dan sorting ASC berdasarkan chapter_number.
     * Dukungan filter q (opsional) pada title.
     */
    public function index(Request $request)
    {
        $query = Chapter::query()
            ->select(['id', 'title', 'description', 'chapter_number', 'created_at'])
            ->orderBy('chapter_number', 'asc');

        if ($request->filled('q')) {
            $q = trim((string) $request->input('q'));
            $driver = DB::connection()->getDriverName();

            // Tokenisasi untuk dukungan "kata mirip" (OR antar token)
            $tokens = preg_split('/\s+/', $q, -1, PREG_SPLIT_NO_EMPTY);
            $tokensLower = array_map(fn ($t) => mb_strtolower($t), $tokens);

            if ($driver === 'pgsql') {
                // Case-insensitive + mirip: ILIKE di title dan description untuk setiap token
                $query->where(function ($outer) use ($tokensLower) {
                    foreach ($tokensLower as $t) {
                        $outer->orWhereRaw('title ILIKE ?', ['%' . $t . '%'])
                              ->orWhereRaw('description ILIKE ?', ['%' . $t . '%']);
                    }
                });
            } else {
                // Case-insensitive lintas driver: LOWER(column) LIKE LOWER(?) di title dan description
                $query->where(function ($outer) use ($tokensLower) {
                    foreach ($tokensLower as $t) {
                        $outer->orWhereRaw('LOWER(title) LIKE ?', ['%' . $t . '%'])
                              ->orWhereRaw('LOWER(description) LIKE ?', ['%' . $t . '%']);
                    }
                });
            }
        }

        $chapters = $query->paginate(15)->appends($request->query());

        if (view()->exists('admin.chapters.index')) {
            return view('admin.chapters.index', compact('chapters'));
        }

        // Stub sementara jika view belum dibuat
        $html = '<!DOCTYPE html><html lang="id"><head><meta charset="utf-8"><title>Admin - Chapters</title><link rel="stylesheet" href="' . asset('css/app.css') . '"></head><body class="bg-gray-50 text-gray-900"><div class="max-w-7xl mx-auto px-4 py-8">';
        $html .= '<h1 class="text-2xl font-bold mb-4">Daftar Chapters (SSR)</h1>';
        $html .= '<p class="text-gray-600 mb-4">View Blade admin.chapters.index belum dibuat. Menampilkan stub sementara.</p>';
        $html .= '<p class="mb-4"><a href="' . route('admin.chapters.create') . '" class="text-emerald-700 hover:underline">+ Tambah Chapter</a></p>';
        $html .= '<table class="min-w-full bg-white border"><thead><tr><th class="px-3 py-2 border-b text-left">#</th><th class="px-3 py-2 border-b text-left">Nomor Bab</th><th class="px-3 py-2 border-b text-left">Judul</th><th class="px-3 py-2 border-b text-left">Aksi</th></tr></thead><tbody>';
        foreach ($chapters as $c) {
            $html .= '<tr>';
            $html .= '<td class="px-3 py-2 border-b">' . e($c->id) . '</td>';
            $html .= '<td class="px-3 py-2 border-b">' . e($c->chapter_number) . '</td>';
            $html .= '<td class="px-3 py-2 border-b">' . e($c->title) . '</td>';
            $html .= '<td class="px-3 py-2 border-b"><a class="text-emerald-700 hover:underline" href="' . route('admin.chapters.edit', $c->id) . '">Edit</a></td>';
            $html .= '</tr>';
        }
        $html .= '</tbody></table>';
        $html .= '<p class="text-sm text-gray-500 mt-3">Pagination: halaman ' . e($chapters->currentPage()) . ' dari ' . e($chapters->lastPage()) . '</p>';
        $html .= '<p class="mt-4"><a href="' . route('admin.index') . '" class="text-emerald-700 hover:underline">Kembali ke Dashboard Admin</a></p>';
        $html .= '</div></body></html>';

        return response($html);
    }

    /**
     * Tampilkan form pembuatan Chapter.
     */
    public function create()
    {
        if (view()->exists('admin.chapters.create')) {
            return view('admin.chapters.create');
        }

        // Stub sementara
        $html = '<!DOCTYPE html><html lang="id"><head><meta charset="utf-8"><title>Buat Chapter</title><link rel="stylesheet" href="' . asset('css/app.css') . '"></head><body class="bg-gray-50 text-gray-900"><div class="max-w-xl mx-auto px-4 py-8">';
        $html .= '<h1 class="text-2xl font-bold mb-4">Form Buat Chapter (Stub)</h1>';
        $html .= '<p class="text-gray-600">Silakan buat view admin.chapters.create untuk form lengkap.</p>';
        $html .= '<p class="mt-4"><a href="' . route('admin.chapters.index') . '" class="text-emerald-700 hover:underline">Kembali</a></p>';
        $html .= '</div></body></html>';
        return response($html);
    }

    /**
     * Simpan Chapter baru.
     */
    public function store(StoreChapterRequest $request)
    {
        $data = $request->validated();

        $chapter = new Chapter();
        $chapter->title = $data['title'];
        $chapter->description = $data['description'] ?? null;
        $chapter->chapter_number = $data['chapter_number'];
        $chapter->save();

        // Invalidate cache daftar dan detail chapter terkait
        $this->cache->clearChaptersCache();
        $this->cache->clearChapterWithHadithsCache($chapter->id);

        return redirect()
            ->route('admin.chapters.edit', $chapter->id)
            ->with('status', 'Chapter dibuat — Bab ' . $chapter->chapter_number . ': ' . $chapter->title);
    }

    /**
     * Tampilkan form edit Chapter.
     */
    public function edit(Chapter $chapter)
    {
        if (view()->exists('admin.chapters.edit')) {
            return view('admin.chapters.edit', compact('chapter'));
        }

        // Stub sementara
        $html = '<!DOCTYPE html><html lang="id"><head><meta charset="utf-8"><title>Edit Chapter</title><link rel="stylesheet" href="' . asset('css/app.css') . '"></head><body class="bg-gray-50 text-gray-900"><div class="max-w-xl mx-auto px-4 py-8">';
        $html .= '<h1 class="text-2xl font-bold mb-4">Form Edit Chapter (Stub)</h1>';
        $html .= '<p class="text-gray-600 mb-2">ID: ' . e($chapter->id) . '</p>';
        $html .= '<p class="text-gray-600 mb-2">Bab: ' . e($chapter->chapter_number) . '</p>';
        $html .= '<p class="text-gray-600">Judul: ' . e($chapter->title) . '</p>';
        $html .= '<p class="mt-4"><a href="' . route('admin.chapters.index') . '" class="text-emerald-700 hover:underline">Kembali</a></p>';
        $html .= '</div></body></html>';
        return response($html);
    }

    /**
     * Perbarui Chapter yang ada.
     */
    public function update(UpdateChapterRequest $request, Chapter $chapter)
    {
        $data = $request->validated();

        $changed = [];

        if (array_key_exists('title', $data)) {
            $chapter->title = $data['title'];
            $changed[] = 'title';
        }
        if (array_key_exists('description', $data)) {
            $chapter->description = $data['description'];
            $changed[] = 'description';
        }
        if (array_key_exists('chapter_number', $data)) {
            $chapter->chapter_number = $data['chapter_number'];
            $changed[] = 'chapter_number';
        }

        if (empty($changed)) {
            return back()->with('status', 'Tidak ada perubahan yang diterapkan');
        }

        $chapter->save();

        // Invalidate cache daftar dan detail chapter terkait
        $this->cache->clearChaptersCache();
        $this->cache->clearChapterWithHadithsCache($chapter->id);

        return redirect()
            ->route('admin.chapters.edit', $chapter->id)
            ->with('status', 'Chapter diperbarui — ' . implode(', ', $changed));
    }

    /**
     * Hapus Chapter beserta hadits (cascade oleh FK).
     */
    public function destroy(Chapter $chapter)
    {
        $id = $chapter->id;
        $label = 'Bab ' . $chapter->chapter_number . ': ' . $chapter->title;

        $chapter->delete();

        // Invalidate cache daftar dan detail chapter terkait
        $this->cache->clearChaptersCache();
        $this->cache->clearChapterWithHadithsCache($id);

        return redirect()
            ->route('admin.chapters.index')
            ->with('status', 'Chapter dihapus — ' . $label);
    }
}