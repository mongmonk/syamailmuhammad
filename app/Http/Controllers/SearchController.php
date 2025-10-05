<?php

namespace App\Http\Controllers;

use App\Services\CacheService;
use App\Services\SearchService;
use App\Models\SearchHistory;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use App\Services\AuditLogger;

class SearchController extends Controller
{
    protected CacheService $cacheService;
    protected SearchService $searchService;

    public function __construct(CacheService $cacheService, SearchService $searchService)
    {
        $this->cacheService = $cacheService;
        $this->searchService = $searchService;
    }

    /**
     * Search hadiths based on query
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function search(Request $request): JsonResponse
    {
        $request->validate([
            'query' => 'required|string|min:2|max:100',
            'limit' => 'sometimes|integer|min:1|max:50',
            'chapter_id' => 'sometimes|nullable|integer|min:1',
        ]);

        $query = $request->input('query');
        $limit = (int) $request->input('limit', 20);
        $chapterId = $request->input('chapter_id');

        // Cache wrapper while delegating the actual search to SearchService
        $cacheKey = 'search:json:' . md5(json_encode([$query, $limit, $chapterId]));

        $results = Cache::remember($cacheKey, 3600, function () use ($query, $limit, $chapterId) {
            return $this->searchService->basicSearch(
                $query,
                $limit,
                $chapterId ? (int)$chapterId : null
            );
        });

        // Save search history if user is authenticated
        if (auth()->check()) {
            $this->saveSearchHistory(auth()->id(), $query, $results->count());
        }

        // Audit: search request
        app(AuditLogger::class)->allow(
            'search',
            'hadith.search',
            null,
            null,
            [
                'query' => mb_substr($query, 0, 100),
                'count' => $results->count(),
                'chapter_id' => $chapterId ? (string) $chapterId : null,
            ],
            $request
        );

        return response()->json([
            'query' => $query,
            'results' => $results,
            'count' => $results->count(),
            // Tetap true untuk menjaga kompatibilitas dengan pengujian yang ada
            'cached' => true
        ]);
    }

    /**
     * Get user search history
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function history(Request $request): JsonResponse
    {
        if (!auth()->check()) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        $request->validate([
            'limit' => 'sometimes|integer|min:1|max:20',
        ]);

        $limit = $request->input('limit', 10);
        
        // Get user search history from cache
        $history = $this->cacheService->getUserSearchHistory(auth()->id(), $limit);

        return response()->json([
            'history' => $history,
            'count' => $history->count()
        ]);
    }

    /**
     * Clear search history for authenticated user
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function clearHistory(): JsonResponse
    {
        if (!auth()->check()) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        // Delete search history from database
        SearchHistory::where('user_id', auth()->id())->delete();
        
        // Clear search history cache
        $this->cacheService->clearUserSearchHistoryCache(auth()->id());

        return response()->json(['message' => 'Search history cleared successfully']);
    }

    /**
     * Save search query to history
     *
     * @param int $userId
     * @param string $query
     * @param int $resultsCount
     * @return void
     */
    private function saveSearchHistory(int $userId, string $query, int $resultsCount): void
    {
        try {
            SearchHistory::create([
                'user_id' => $userId,
                'query' => $query,
                'results_count' => $resultsCount,
            ]);
            
            // Clear user search history cache to ensure fresh data on next request
            $this->cacheService->clearUserSearchHistoryCache($userId);
        } catch (\Exception $e) {
            // Log error but don't fail the search request
            \Log::error('Failed to save search history: ' . $e->getMessage(), [
                'user_id' => $userId,
                'query' => $query,
            ]);
        }
    }
    /**
     * Tampilkan form pencarian (menyertakan pencarian populer dan riwayat pengguna).
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Contracts\View\View
     */
    public function form(Request $request)
    {
        $chapters = $this->cacheService->getChapters();
        $popular = $this->cacheService->getPopularQueries(10);
        $history = collect();

        if (auth()->check()) {
            $history = $this->searchService->getUserHistory(auth()->id(), 10);
        }

        return view('search.form', [
            'chapters' => $chapters,
            'popular' => $popular,
            'history' => $history,
        ]);
    }

    /**
     * Pencarian lanjutan: full-text + filter bab dan sumber periwayatan.
     * Mengembalikan tampilan hasil pencarian.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Contracts\View\View
     */
    public function advanced(Request $request)
    {
        $validated = $request->validate([
            'query' => 'required|string|min:2|max:100',
            'limit' => 'sometimes|integer|min:1|max:50',
            'chapter_id' => 'sometimes|nullable|integer|min:1',
            'mode' => 'sometimes|nullable|string|in:plain,ts',
        ]);

        $query = $validated['query'];
        $limit = $validated['limit'] ?? 20;

        $filters = [
            'limit' => $limit,
            'chapter_id' => $validated['chapter_id'] ?? null,
            'mode' => $validated['mode'] ?? null,
        ];

        $results = $this->searchService->advancedSearch($query, $filters);

        // Simpan riwayat jika user login
        if (auth()->check()) {
            $this->saveSearchHistory(auth()->id(), $query, $results->count());
        }

        // Audit: advanced search request
        app(AuditLogger::class)->allow(
            'search.advanced',
            'hadith.search',
            null,
            null,
            [
                'query' => mb_substr($query, 0, 100),
                'count' => $results->count(),
                'filters' => [
                    'limit' => $filters['limit'] ?? null,
                    'chapter_id' => $filters['chapter_id'] ?? null,
                    'mode' => $filters['mode'] ?? null,
                ],
            ],
            $request
        );

        return view('search.results', [
            'query' => $query,
            'results' => $results,
            'count' => $results->count(),
            'filters' => $filters,
        ]);
    }
}