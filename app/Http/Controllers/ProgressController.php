<?php

namespace App\Http\Controllers;

use App\Models\UserReadingProgress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;

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
                return [
                    'type' => $p->resource_type,
                    'resource_id' => $p->resource_id,
                    'position' => $p->position,
                    'updated_at' => $p->updated_at?->toIso8601String(),
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
}