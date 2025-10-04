<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\Request;

class PostPublicController extends Controller
{
    /**
     * Daftar post yang dipublikasikan (publik).
     * Filter opsional:
     * - q: pencarian pada title (LIKE)
     * Paginate: 25 per halaman.
     */
    public function index(Request $request)
    {
        $query = Post::query()->published()->orderByDesc('id');

        if ($request->filled('q')) {
            $q = trim($request->input('q'));
            // Gunakan LIKE agar portabel lintas DB; untuk Postgres bisa dioptimalkan ke ILIKE bila diperlukan
            $like = '%' . str_replace('%', '\\%', $q) . '%';
            $query->where('title', 'like', $like);
        }

        $posts = $query->paginate(25);

        return response()->json([
            'data' => $posts->items(),
            'meta' => [
                'current_page' => $posts->currentPage(),
                'per_page' => $posts->perPage(),
                'total' => $posts->total(),
                'last_page' => $posts->lastPage(),
            ],
        ]);
    }

    /**
     * Detail post yang dipublikasikan berdasarkan slug (publik).
     */
    public function show(string $slug)
    {
        $post = Post::query()->published()->where('slug', $slug)->first();

        if (! $post) {
            return response()->json([
                'code' => 'NOT_FOUND',
                'message' => 'Post tidak ditemukan',
            ], 404);
        }

        return response()->json([
            'post' => $post->only(['id', 'title', 'slug', 'body', 'created_by', 'created_at']),
        ]);
    }
}