<?php

namespace App\Http\Controllers;

use App\Models\Hadith;
use App\Services\CacheService;
use Illuminate\Http\Request;
use App\Support\FootnoteParser;
use Illuminate\Support\Facades\Log;

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
    public function show(Request $request, Hadith $hadith)
    {
        $hadithWithRelatedData = $this->cacheService->getHadithWithRelatedData($hadith->id);
        
        if (!$hadithWithRelatedData) {
            abort(404);
        }
    
        // Parse footnotes dari kolom footnotes (hasil rename dari interpretation)
        /** @var \App\Support\FootnoteParser $parser */
        $parser = app(FootnoteParser::class);
        $currentHadith = $hadithWithRelatedData['hadith'];
        $rawFootnotes = $currentHadith->footnotes ?? '';
        $parsed = $parser->process($rawFootnotes);

        // Gunakan daftar footnotes; bila parser tidak menemukan penanda, fallback ke satu item dari teks
        $currentHadith->footnotes = $parsed['footnotes'];
        if (empty($currentHadith->footnotes) && !empty(trim((string) $rawFootnotes))) {
            $currentHadith->footnotes = [
                ['index' => 1, 'content' => trim(preg_replace('/\s+/', ' ', (string) $rawFootnotes))],
            ];
        }

        // Logging untuk validasi jumlah footnote yang dihasilkan
        Log::info('syamail.footnotes_generated', [
            'hadith_id' => $currentHadith->id,
            'count' => count($parsed['footnotes']),
            'has_markers' => count($parsed['footnotes']) > 0,
        ]);
        
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
    
        // JSON payload termasuk footnotes sesuai spesifikasi
        if ($request->expectsJson()) {
            $prev = $hadithWithRelatedData['previousHadith'] ?? null;
            $next = $hadithWithRelatedData['nextHadith'] ?? null;
    
            return response()->json([
                'hadith' => [
                    'id' => $currentHadith->id,
                    'chapter_id' => $currentHadith->chapter_id ?? null,
                    'hadith_number' => $currentHadith->hadith_number,
                    'arabic_text' => $currentHadith->arabic_text,
                    'translation' => $currentHadith->translation,
                    'footnotes' => $currentHadith->footnotes ?? [],
                ],
                'previousHadith' => $prev ? [
                    'id' => $prev->id,
                    'hadith_number' => $prev->hadith_number,
                    'chapter_id' => $prev->chapter_id ?? null,
                ] : null,
                'nextHadith' => $next ? [
                    'id' => $next->id,
                    'hadith_number' => $next->hadith_number,
                    'chapter_id' => $next->chapter_id ?? null,
                ] : null,
            ]);
        }
        
        return view('hadiths.show', [
            'hadith' => $currentHadith,
            'previousHadith' => $hadithWithRelatedData['previousHadith'],
            'nextHadith' => $hadithWithRelatedData['nextHadith'],
            'bookmark' => $bookmark,
            'userNote' => $userNote
        ]);
    }
}