<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Post;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class PostsPageController extends Controller
{
    public function __construct()
    {
        // Middleware SSR: gunakan guard web untuk admin
        $this->middleware(['auth', 'ensure.active', 'role.admin']);
    }

    /**
     * Tampilkan daftar Post untuk admin (SSR).
     * Filter opsional:
     * - q: pencarian pada title (ILIKE, PostgreSQL)
     * - published: true/false untuk filter is_published
     */
    public function index(Request $request)
    {
        $query = Post::query()
            ->select(['id', 'title', 'slug', 'is_published', 'created_by', 'created_at'])
            ->orderByDesc('id');

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

        $posts = $query->paginate(15)->appends($request->query());

        return view('admin.posts.index', compact('posts'));
    }

    /**
     * Tampilkan form pembuatan post.
     */
    public function create()
    {
        return view('admin.posts.create');
    }

    /**
     * Simpan post baru.
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
        // Gunakan boolean() agar ketika checkbox tidak dicentang -> false (draft)
        $post->is_published = $request->boolean('is_published');
        $post->created_by = Auth::id();
        $post->save();

        return redirect()
            ->route('admin.posts.index')
            ->with('status', 'Post dibuat — ' . $post->title);
    }

    /**
     * Tampilkan form edit post.
     */
    public function edit(Post $post)
    {
        return view('admin.posts.edit', compact('post'));
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
            // boolean() memastikan "0" (unchecked) dibaca sebagai false (draft)
            $post->is_published = $request->boolean('is_published');
            $changed[] = 'is_published';
        }

        if (empty($changed)) {
            return back()->with('status', 'Tidak ada perubahan yang diterapkan');
        }

        $post->save();

        return redirect()
            ->route('admin.posts.index')
            ->with('status', 'Post diperbarui — ' . implode(', ', $changed));
    }

    /**
     * Hapus post.
     */
    public function destroy(Post $post)
    {
        $title = $post->title;
        $post->delete();

        return redirect()
            ->route('admin.posts.index')
            ->with('status', 'Post dihapus — ' . $title);
    }
}