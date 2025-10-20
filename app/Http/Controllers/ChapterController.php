<?php

namespace App\Http\Controllers;

use App\Models\Chapter;
use App\Services\CacheService;
use Illuminate\Http\Request;

class ChapterController extends Controller
{
    protected $cacheService;

    public function __construct(CacheService $cacheService)
    {
        $this->cacheService = $cacheService;
    }

    /**
     * Display a listing of the chapters.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $chapters = $this->cacheService->getChapters();
        return view('chapters.index', compact('chapters'));
    }

    /**
     * Display the specified chapter and its hadiths.
     *
     * @param  \App\Models\Chapter  $chapter
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, Chapter $chapter)
    {
        $chapterWithHadiths = $this->cacheService->getChapterWithHadiths($chapter->id);
        
        if (!$chapterWithHadiths) {
            abort(404);
        }

        $chapterModel = $chapterWithHadiths['chapter'];
        $hadiths = $chapterWithHadiths['hadiths'];

        // Footnotes langsung digunakan tanpa parsing (seperti di HadithController)
        // Akan ditampilkan dengan nl2br di view

        // Jika JSON diminta, sertakan footnotes pada payload
        if ($request->expectsJson()) {
            return response()->json([
                'chapter' => [
                    'id' => $chapterModel->id,
                    'chapter_number' => $chapterModel->chapter_number,
                    'title' => $chapterModel->title,
                    'description' => $chapterModel->description,
                ],
                'hadiths' => $hadiths->map(function ($h) {
                    return [
                        'id' => $h->id,
                        'hadith_number' => $h->hadith_number,
                        'arabic_text' => $h->arabic_text,
                        'translation' => $h->translation,
                        'footnotes' => $h->footnotes ?? null,
                    ];
                })->values(),
            ]);
        }
        
        return view('chapters.show', [
            'chapter' => $chapterModel,
            'hadiths' => $hadiths,
        ]);
    }
}