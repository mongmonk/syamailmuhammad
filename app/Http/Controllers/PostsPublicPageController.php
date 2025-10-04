<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Post;

class PostsPublicPageController extends Controller
{
    /**
     * Daftar post terbit (publik, SSR).
     * Filter opsional: q (judul).
     */
    public function index(Request $request)
    {
        $query = Post::query()
            ->select(['id', 'title', 'slug', 'created_at'])
            ->published()
            ->orderByDesc('id');

        if ($request->filled('q')) {
            $q = trim($request->input('q'));
            $query->where('title', 'ILIKE', '%' . str_replace('%', '\\%', $q) . '%');
        }

        $posts = $query->paginate(12)->appends($request->query());

        return view('posts.index', compact('posts'));
    }

    /**
     * Detail post terbit berdasarkan slug (publik, SSR).
     */
    public function show(string $slug)
    {
        $post = Post::query()
            ->published()
            ->where('slug', $slug)
            ->first();

        if (! $post) {
            abort(404, 'Post tidak ditemukan');
        }

        return view('posts.show', compact('post'));
    }
}