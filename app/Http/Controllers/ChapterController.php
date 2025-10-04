<?php

namespace App\Http\Controllers;

use App\Models\Chapter;
use App\Services\CacheService;
use Illuminate\Http\Request;
use App\Support\FootnoteParser;

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

        /** @var \App\Support\FootnoteParser $parser */
        $parser = app(FootnoteParser::class);

        $chapterModel = $chapterWithHadiths['chapter'];
        $hadiths = $chapterWithHadiths['hadiths'];

        // Proses footnotes untuk setiap hadits pada bab
        $hadiths = $hadiths->map(function ($h) use ($parser) {
            $parsed = $parser->process($h->interpretation);
            $h->interpretation_rendered = $parsed['content'];
            $h->footnotes = $parsed['footnotes'];
            return $h;
        });

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
                        'interpretation' => $h->interpretation,
                        'footnotes' => $h->footnotes ?? [],
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