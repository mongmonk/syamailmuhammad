<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

/**
 * Manajemen Post oleh Admin
 * Endpoint (semua JSON):
 * - GET    /api/posts           : daftar post (paginate, opsional filter q, published)
 * - POST   /api/posts           : buat post baru (title, body, slug opsional, is_published)
 * - PATCH  /api/posts/{post}    : ubah isi post (title/body/slug/is_published)
 * - DELETE /api/posts/{post}    : hapus post
 *
 * Proteksi: middleware ['jwt','role.admin'] di constructor.
 */
class PostController extends Controller
{
    public function __construct()
    {
        // Konsisten dengan API auth berbasis JWT dan role admin
        $this->middleware(['jwt', 'role.admin']);
    }

    /**
     * Daftar post (paginate) dengan filter opsional:
     * - q: pencarian pada title
     * - published: true/false untuk filter is_published
     */
    public function index(Request $request)
    {
        $query = Post::query()->orderByDesc('id');

        if ($request->filled('q')) {
            $q = trim($request->input('q'));
            $query->where('title', 'ILIKE', '%' . str_replace('%', '\\%', $q) . '%');
        }

        if ($request->filled('published')) {
            $published = filter_var($request->input('published'), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
            if ($published !== null) {
                $query->where('is_published', $published);
            }
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
     * Buat post baru.
     * - slug opsional; jika tidak disediakan, digenerate dari title
     * - created_by diisi dari user yang login (JWT)
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:200'],
            'slug' => ['nullable', 'string', 'max:220', 'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/', 'unique:posts,slug'],
            'body' => ['required', 'string'],
            'is_published' => ['nullable', 'boolean'],
        ]);

        $slug = $data['slug'] ?? null;
        if (empty($slug)) {
            $slug = Str::slug($data['title']);
            // Pastikan unik jika kebetulan tabrakan
            $base = $slug;
            $i = 1;
            while (Post::where('slug', $slug)->exists()) {
                $slug = $base . '-' . $i;
                $i++;
            }
        }

        $post = new Post();
        $post->title = $data['title'];
        $post->slug = $slug;
        $post->body = $data['body'];
        $post->is_published = (bool) ($data['is_published'] ?? true);
        $post->created_by = Auth::id();
        $post->save();

        return response()->json([
            'message' => 'Post dibuat',
            'post' => $post->only(['id', 'title', 'slug', 'is_published', 'created_by', 'created_at']),
        ], 201);
    }

    /**
     * Perbarui post yang ada.
     */
    public function update(Request $request, Post $post)
    {
        $data = $request->validate([
            'title' => ['nullable', 'string', 'max:200'],
            'slug' => ['nullable', 'string', 'max:220', 'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/', Rule::unique('posts', 'slug')->ignore($post->id)],
            'body' => ['nullable', 'string'],
            'is_published' => ['nullable', 'boolean'],
        ]);

        $changed = [];

        if (array_key_exists('title', $data) && $data['title'] !== null) {
            $post->title = $data['title'];
            $changed[] = 'title';
        }
        if (array_key_exists('slug', $data) && $data['slug'] !== null) {
            $post->slug = $data['slug'];
            $changed[] = 'slug';
        }
        if (array_key_exists('body', $data) && $data['body'] !== null) {
            $post->body = $data['body'];
            $changed[] = 'body';
        }
        if (array_key_exists('is_published', $data) && $data['is_published'] !== null) {
            $post->is_published = (bool) $data['is_published'];
            $changed[] = 'is_published';
        }

        if (empty($changed)) {
            return response()->json([
                'code' => 'NO_CHANGES',
                'message' => 'Tidak ada perubahan yang diterapkan',
            ], 400);
        }

        $post->save();

        return response()->json([
            'message' => 'Post diperbarui',
            'changed' => $changed,
            'post' => $post->only(['id', 'title', 'slug', 'is_published', 'created_by', 'updated_at']),
        ]);
    }

    /**
     * Hapus post.
     */
    public function destroy(Post $post)
    {
        $post->delete();

        return response()->json([
            'message' => 'Post dihapus',
        ]);
    }
}