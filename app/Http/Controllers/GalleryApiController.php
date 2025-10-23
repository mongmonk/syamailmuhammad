<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\GalleryItem;
use Illuminate\Http\Request;

class GalleryApiController extends Controller
{
    /**
     * GET /api/gallery
     * Daftar item galeri publik dengan pagination dan filter sederhana.
     * Query:
     * - q   : cari pada caption (LIKE)
     * - tag : filter berdasarkan salah satu tag
     * - page: halaman (default Laravel)
     *
     * Response:
     * {
     *   data: [
     *     {
     *       id, slug, caption, alt_text, published_at,
     *       variants: { thumb, medium, large, max }
     *     }, ...
     *   ],
     *   meta: { current_page, per_page, total, last_page }
     * }
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
                'is_active',
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

        $data = [];
        foreach ($items->items() as $it) {
            $data[] = [
                'id' => $it->id,
                'slug' => $it->slug,
                'caption' => $it->caption,
                'alt_text' => $it->alt_text,
                'published_at' => optional($it->published_at)->toIso8601String(),
                'variants' => [
                    'thumb' => $it->variantUrl('thumb'),
                    'medium' => $it->variantUrl('medium'),
                    'large' => $it->variantUrl('large'),
                    'max' => $it->variantUrl('max'),
                ],
            ];
        }

        return response()->json([
            'data' => $data,
            'meta' => [
                'current_page' => $items->currentPage(),
                'per_page' => $items->perPage(),
                'total' => $items->total(),
                'last_page' => $items->lastPage(),
            ],
        ]);
    }
}