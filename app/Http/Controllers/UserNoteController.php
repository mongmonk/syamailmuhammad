<?php

namespace App\Http\Controllers;

use App\Models\Hadith;
use App\Models\UserNote;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserNoteController extends Controller
{
    /**
     * Tampilkan daftar catatan milik user.
     */
    public function index(): \Illuminate\View\View
    {
        $notes = Auth::user()
            ->notes()
            ->with(['hadith.chapter'])
            ->latest()
            ->paginate(12);

        return view('notes.index', [
            'notes' => $notes,
        ]);
    }

    /**
     * Tampilkan (JSON) catatan user untuk hadits tertentu.
     * Digunakan oleh front-end untuk prefill modal edit catatan.
     */
    public function show(Hadith $hadith): JsonResponse
    {
        $note = UserNote::where('user_id', Auth::id())
            ->where('hadith_id', $hadith->id)
            ->firstOrFail();

        return response()->json([
            'id' => $note->id,
            'hadith_id' => $note->hadith_id,
            'note_content' => $note->note_content,
            'updated_at' => $note->updated_at,
        ]);
    }

    /**
     * Simpan catatan baru untuk hadits.
     * Unik per (user, hadith) - jika sudah ada, akan di-update.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'hadith_id' => ['required', 'exists:hadiths,id'],
            'note_content' => ['required', 'string'],
        ]);

        $note = UserNote::updateOrCreate(
            [
                'user_id' => Auth::id(),
                'hadith_id' => $validated['hadith_id'],
            ],
            [
                'note_content' => $validated['note_content'],
            ]
        );

        return response()->json([
            'success' => true,
            'note' => [
                'id' => $note->id,
                'hadith_id' => $note->hadith_id,
                'note_content' => $note->note_content,
            ],
        ]);
    }

    /**
     * Perbarui catatan yang sudah ada untuk hadits tertentu.
     */
    public function update(Hadith $hadith, Request $request): JsonResponse
    {
        $validated = $request->validate([
            'note_content' => ['required', 'string'],
        ]);

        $note = UserNote::where('user_id', Auth::id())
            ->where('hadith_id', $hadith->id)
            ->firstOrFail();

        $note->note_content = $validated['note_content'];
        $note->save();

        return response()->json([
            'success' => true,
            'note' => [
                'id' => $note->id,
                'hadith_id' => $note->hadith_id,
                'note_content' => $note->note_content,
            ],
        ]);
    }

    /**
     * Hapus catatan milik user untuk hadits tertentu.
     */
    public function destroy(Hadith $hadith): JsonResponse
    {
        $deleted = UserNote::where('user_id', Auth::id())
            ->where('hadith_id', $hadith->id)
            ->delete();

        return response()->json([
            'success' => (bool) $deleted,
        ]);
    }
}