<?php

namespace App\Http\Controllers;

use App\Models\UserReadingProgress;
use App\Models\Chapter;
use App\Models\Hadith;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;

class ProgressController extends Controller
{
    /**
     * Tampilkan progres baca milik pengguna yang login.
     * - Pending dan aktif diizinkan (dibatasi oleh middleware not.banned).
     * - Mengembalikan JSON bila diminta, atau menampilkan view.
     */
    public function index(Request $request): View|JsonResponse
    {
        $userId = Auth::id();

        $progress = UserReadingProgress::query()
            ->forUser((int) $userId)
            ->orderByDesc('updated_at')
            ->get()
            ->map(function (UserReadingProgress $p) {
                $type = $p->resource_type;
                $resourceId = (int) $p->resource_id;
                $position = (int) $p->position;

                if ($type === UserReadingProgress::TYPE_CHAPTER) {
                    $chapter = Chapter::query()->find($resourceId);
                    $label = $chapter ? ('Bab ' . $chapter->chapter_number) : ('Bab ' . $resourceId);
                    $url = route('chapters.show', ['chapter' => $resourceId]);
                } elseif ($type === UserReadingProgress::TYPE_HADITH) {
                    $hadith = Hadith::query()->find($resourceId);
                    $num = $hadith ? (int) $hadith->hadith_number : ($position > 0 ? $position : $resourceId);
                    $label = 'Hadits ' . $num;
                    $url = route('hadiths.show', ['hadith' => $resourceId]);
                } else {
                    $label = ucfirst((string) $type) . ' ' . $resourceId;
                    $url = null;
                }

                return [
                    'type' => $type,
                    'resource_id' => $resourceId,
                    'position' => $position,
                    'label' => $label,
                    'url' => $url,
                    'updated_at' => $p->updated_at?->toIso8601String(),
                    'updated_at_human' => $p->updated_at ? $p->updated_at->translatedFormat('d F Y H:i') : null,
                ];
            });

        if ($request->expectsJson()) {
            return response()->json([
                'data' => $progress,
            ]);
        }

        return view('progress.index', [
            'progress' => $progress,
        ]);
    }

    /**
     * Simpan atau perbarui progres baca untuk pengguna yang login.
     * - Validasi tipe sumber (chapter/hadith) dan resource_id yang ada.
     * - Upsert berdasarkan (user_id, resource_type, resource_id).
     * - Mengembalikan JSON bila diminta, atau redirect dengan flash message.
     */
    public function store(Request $request): JsonResponse|RedirectResponse
    {
        $userId = Auth::id();

        $validated = $request->validate([
            'type' => ['required', 'string', 'in:' . UserReadingProgress::TYPE_CHAPTER . ',' . UserReadingProgress::TYPE_HADITH],
            'resource_id' => ['required', 'integer', 'min:1'],
            'position' => ['required', 'integer', 'min:0'],
        ]);

        // Pastikan resource ada
        $resourceExists = match ($validated['type']) {
            UserReadingProgress::TYPE_CHAPTER => Chapter::query()->whereKey($validated['resource_id'])->exists(),
            UserReadingProgress::TYPE_HADITH => Hadith::query()->whereKey($validated['resource_id'])->exists(),
            default => false,
        };

        if (!$resourceExists) {
            $error = ['message' => 'Sumber progres tidak ditemukan'];
            if ($request->expectsJson()) {
                return response()->json($error, 422);
            }
            return redirect()
                ->back()
                ->withErrors($error);
        }

        $progress = UserReadingProgress::updateOrCreate(
            [
                'user_id' => (int) $userId,
                'resource_type' => $validated['type'],
                'resource_id' => (int) $validated['resource_id'],
            ],
            [
                'position' => (int) $validated['position'],
            ]
        );

        $payload = [
            'type' => $progress->resource_type,
            'resource_id' => $progress->resource_id,
            'position' => $progress->position,
            'updated_at' => $progress->updated_at?->toIso8601String(),
        ];

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Progres diperbarui',
                'data' => $payload,
            ]);
        }

        return redirect()
            ->route('progress.index')
            ->with('status', 'Progres diperbarui');
    }
}