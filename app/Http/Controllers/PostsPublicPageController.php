<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\GalleryItem;
use Illuminate\Http\Request;

class PostsPublicPageController extends Controller
{
    /**
     * Halaman galeri publik (SSR).
     * - Menampilkan item aktif & terbit terbaru
     * - Pencarian sederhana berdasarkan caption
     * - Filter berdasarkan tag (opsional)
     * - Pagination
     */
    public function index(Request $request)
    {
        $query = GalleryItem::query()
            ->select([
                'id',
                'slug',
                'caption',
                'alt_text',
                'variants',
                'published_at',
                'created_at',
            ])
            ->where('is_active', true)
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->orderByDesc('published_at')
            ->orderByDesc('id');

        if ($request->filled('q')) {
            $q = trim((string) $request->input('q'));
            $like = '%' . str_replace('%', '\\%', $q) . '%';
            $query->where('caption', 'like', $like);
        }

        if ($request->filled('tag')) {
            $tag = trim((string) $request->input('tag'));
            if ($tag !== '') {
                $query->whereJsonContains('tags', $tag);
            }
        }

        $items = $query->paginate(24)->appends($request->query());

        return view('posts.index', [
            'items' => $items,
        ]);
    }

    /**
     * Halaman detail galeri (gambar resolusi lebih tinggi + caption).
     */
    public function show(string $slug)
    {
        $item = GalleryItem::query()
            ->where('slug', $slug)
            ->where('is_active', true)
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->first();

        if (! $item) {
            abort(404, 'Gambar tidak ditemukan');
        }

        return view('posts.show', [
            'item' => $item,
        ]);
    }
}