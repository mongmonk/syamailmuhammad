<?php

namespace App\Http\Controllers;

use App\Models\Hadith;
use App\Services\CacheService;
use Illuminate\Http\Request;

class HadithController extends Controller
{
    protected $cacheService;

    public function __construct(CacheService $cacheService)
    {
        $this->cacheService = $cacheService;
    }

    /**
     * Display the specified hadith.
     *
     * @param  \App\Models\Hadith  $hadith
     * @return \Illuminate\Http\Response
     */
    public function show(Hadith $hadith)
    {
        $hadithWithRelatedData = $this->cacheService->getHadithWithRelatedData($hadith->id);
        
        if (!$hadithWithRelatedData) {
            abort(404);
        }
        
        // Get user's bookmark and notes if authenticated
        $bookmark = null;
        $userNote = null;
        
        if (auth()->check()) {
            $bookmark = auth()->user()->bookmarks()
                ->where('hadith_id', $hadith->id)
                ->first();
                
            $userNote = auth()->user()->notes()
                ->where('hadith_id', $hadith->id)
                ->first();
        }
        
        return view('hadiths.show', [
            'hadith' => $hadithWithRelatedData['hadith'],
            'previousHadith' => $hadithWithRelatedData['previousHadith'],
            'nextHadith' => $hadithWithRelatedData['nextHadith'],
            'bookmark' => $bookmark,
            'userNote' => $userNote
        ]);
    }
}