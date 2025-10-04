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
    public function show(Chapter $chapter)
    {
        $chapterWithHadiths = $this->cacheService->getChapterWithHadiths($chapter->id);
        
        if (!$chapterWithHadiths) {
            abort(404);
        }
        
        return view('chapters.show', [
            'chapter' => $chapterWithHadiths['chapter'],
            'hadiths' => $chapterWithHadiths['hadiths']
        ]);
    }
}