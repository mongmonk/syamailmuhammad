<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GalleryItem;
use App\Services\ImageProcessor;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PostsPageController extends Controller
{
    public function __construct(private readonly ImageProcessor $images)
    {
        // Middleware SSR admin (guard web). Security headers sudah di group route.
        $this->middleware(['auth', 'ensure.active', 'role.admin']);
    }

    /**
     * Manajemen Galeri (SSR) pada /admin/posts.
     * Fitur:
     * - Daftar item dengan pencarian (caption) & filter status aktif
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
                'is_active',
                'published_at',
                'created_at',
            ])
            ->orderByDesc('id');

        if ($request->filled('q')) {
            $q = trim((string) $request->input('q'));
            // Gunakan LIKE agar portabel
            $like = '%' . str_replace('%', '\\%', $q) . '%';
            $query->where('caption', 'like', $like);
        }

        if ($request->filled('tag')) {
            $tag = trim((string) $request->input('tag'));
            if ($tag !== '') {
                $query->whereJsonContains('tags', $tag);
            }
        }

        if ($request->filled('active')) {
            $active = filter_var($request->input('active'), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
            if ($active !== null) {
                $query->where('is_active', $active);
            }
        } else {
            // Backward compat: jika ada ?published=true/false
            if ($request->filled('published')) {
                $active = filter_var($request->input('published'), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
                if ($active !== null) {
                    $query->where('is_active', $active);
                }
            }
        }

        $items = $query->paginate(24)->appends($request->query());

        return view('admin.posts.index', [
            'items' => $items,
        ]);
    }

    /**
     * Form unggah beberapa gambar (multi).
     */
    public function create()
    {
        return view('admin.posts.create');
    }

    /**
     * Unggah satu/lebih gambar:
     * - Validasi: JPG/PNG/WebP, maks 5MB per file
     * - Proses varian (320/640/1280/1920) via ImageProcessor
     * - Simpan metadata GalleryItem
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'images' => ['required', 'array', 'min:1'],
            'images.*' => ['file', 'mimetypes:image/jpeg,image/png,image/webp', 'max:5120'], // 5MB
            'caption' => ['nullable', 'string', 'max:150'],
            'alt_text' => ['nullable', 'string', 'max:180'],
            'tags' => ['nullable', 'string'], // comma separated
            'is_active' => ['nullable', 'boolean'],
        ]);

        $files = $request->file('images', []);
        $isActive = $request->boolean('is_active', true);
        $defaultCaption = $data['caption'] ?? null;
        $defaultAlt = $data['alt_text'] ?? null;
        $tags = $this->parseTags($data['tags'] ?? null);

        $created = 0;

        foreach ($files as $file) {
            // Slug dari nama file (fallback random)
            $base = Str::slug(pathinfo((string) $file->getClientOriginalName(), PATHINFO_FILENAME));
            if ($base === '') {
                $base = 'img-' . Str::lower(Str::random(6));
            }
            $slug = $this->uniqueSlug($base);

            // Proses varian
            $meta = $this->images->process($file, $slug);

            // Simpan metadata item
            $item = new GalleryItem();
            $item->slug = $slug;
            $item->original_filename = $meta['original_filename'];
            $item->mime = $meta['mime'];
            $item->original_width = (int) $meta['original_width'];
            $item->original_height = (int) $meta['original_height'];
            $item->variants = $meta['variants'];
            $item->caption = $defaultCaption;
            $item->alt_text = $defaultAlt ?: $defaultCaption;
            $item->tags = $tags;
            $item->is_active = $isActive;
            $item->published_at = $isActive ? now() : null;
            $item->save();

            $created++;
        }

        return redirect()
            ->route('admin.posts.index')
            ->with('status', $created . ' gambar berhasil diunggah.');
    }

    /**
     * Form edit item (caption/tags/status) & penggantian gambar.
     */
    public function edit(GalleryItem $post)
    {
        // Param {post} tetap, tapi modelnya GalleryItem
        return view('admin.posts.edit', [
            'item' => $post,
        ]);
    }

    /**
     * Perbarui caption, tags, status aktif, atau ganti gambar (re-process).
     */
    public function update(Request $request, GalleryItem $post)
    {
        $data = $request->validate([
            'caption' => ['nullable', 'string', 'max:150'],
            'alt_text' => ['nullable', 'string', 'max:180'],
            'tags' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
            'image' => ['nullable', 'file', 'mimetypes:image/jpeg,image/png,image/webp', 'max:5120'],
        ]);

        $changed = [];

        // Ganti gambar (opsional)
        if ($request->hasFile('image')) {
            $meta = $this->images->replace($request->file('image'), $post->slug);
            $post->original_filename = $meta['original_filename'];
            $post->mime = $meta['mime'];
            $post->original_width = (int) $meta['original_width'];
            $post->original_height = (int) $meta['original_height'];
            $post->variants = $meta['variants'];
            $changed[] = 'image';
        }

        if (array_key_exists('caption', $data)) {
            $post->caption = $data['caption'];
            $changed[] = 'caption';
        }
        if (array_key_exists('alt_text', $data)) {
            $post->alt_text = $data['alt_text'];
            $changed[] = 'alt_text';
        }
        if (array_key_exists('tags', $data)) {
            $post->tags = $this->parseTags($data['tags']);
            $changed[] = 'tags';
        }
        if (array_key_exists('is_active', $data)) {
            $newActive = $request->boolean('is_active');
            if ($newActive && ! $post->is_active) {
                $post->published_at = now();
            } elseif (! $newActive) {
                $post->published_at = null;
            }
            $post->is_active = $newActive;
            $changed[] = 'is_active';
        }

        if (empty($changed)) {
            return back()->with('status', 'Tidak ada perubahan yang diterapkan.');
        }

        $post->save();

        return redirect()
            ->route('admin.posts.index')
            ->with('status', 'Item diperbarui: ' . implode(', ', $changed));
    }

    /**
     * Hapus item galeri dan bersihkan file varian di storage.
     */
    public function destroy(GalleryItem $post)
    {
        $slug = $post->slug;
        $title = $post->caption ?: $post->slug;

        // Bersihkan varian file
        $this->images->cleanupVariants($slug);

        // Hapus record
        $post->delete();

        return redirect()
            ->route('admin.posts.index')
            ->with('status', 'Item dihapus — ' . $title);
    }

    /**
     * Buat slug unik.
     */
    private function uniqueSlug(string $base): string
    {
        $slug = $base;
        $i = 1;
        while (GalleryItem::where('slug', $slug)->exists()) {
            $slug = $base . '-' . $i;
            $i++;
        }
        return $slug;
    }

    /**
     * Parse tags: string "tag1, tag2" -> array unik tertrim.
     *
     * @return array<string>
     */
    private function parseTags(?string $raw): ?array
    {
        if ($raw === null) {
            return null;
        }
        $parts = array_map(
            fn ($s) => trim((string) $s),
            explode(',', $raw)
        );
        $parts = array_values(array_filter($parts, fn ($s) => $s !== ''));
        if (empty($parts)) {
            return null;
        }
        // batasi 20 tag agar wajar
        $parts = array_slice(array_values(array_unique($parts)), 0, 20);
        return $parts;
    }
}