<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use App\Models\Bookmark;
use App\Models\Hadith;

class BookmarkController extends Controller
{
    /**
     * Tampilkan daftar bookmark milik user.
     */
    public function index(): \Illuminate\View\View
    {
        $bookmarks = Auth::user()
            ->bookmarks()
            ->with(['hadith.chapter'])
            ->latest()
            ->paginate(12);

        return view('bookmarks.index', [
            'bookmarks' => $bookmarks,
        ]);
    }

    /**
     * Tambah atau perbarui bookmark (idempotent untuk kombinasi user/hadith).
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'hadith_id' => ['required', 'exists:hadiths,id'],
            'notes' => ['nullable', 'string'],
        ]);

        $bookmark = Bookmark::updateOrCreate(
            [
                'user_id' => Auth::id(),
                'hadith_id' => $validated['hadith_id'],
            ],
            [
                'notes' => $validated['notes'] ?? null,
            ]
        );

        return response()->json([
            'success' => true,
            'bookmark' => [
                'id' => $bookmark->id,
                'notes' => $bookmark->notes,
            ],
        ]);
    }

    /**
     * Edit catatan pada bookmark.
     */
    public function update(Hadith $hadith, Request $request): JsonResponse
    {
        $validated = $request->validate([
            'notes' => ['nullable', 'string'],
        ]);

        $bookmark = Bookmark::where('user_id', Auth::id())
            ->where('hadith_id', $hadith->id)
            ->firstOrFail();

        $bookmark->notes = $validated['notes'] ?? null;
        $bookmark->save();

        return response()->json([
            'success' => true,
            'bookmark' => [
                'id' => $bookmark->id,
                'notes' => $bookmark->notes,
            ],
        ]);
    }

    /**
     * Hapus bookmark.
     */
    public function destroy(Hadith $hadith): JsonResponse
    {
        $deleted = Bookmark::where('user_id', Auth::id())
            ->where('hadith_id', $hadith->id)
            ->delete();

        return response()->json([
            'success' => (bool) $deleted,
        ]);
    }
}