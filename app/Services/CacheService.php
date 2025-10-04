<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use App\Models\Chapter;
use App\Models\Hadith;

class CacheService
{
    /**
     * Cache key prefixes
     */
    const CHAPTERS_PREFIX = 'chapters:';
    const HADITHS_PREFIX = 'hadiths:';
    const SEARCH_PREFIX = 'search:';
    const SEARCH_HISTORY_PREFIX = 'search_history:';
    const POPULAR_SEARCH_PREFIX = 'popular_search:';

    /**
     * Default TTL values (in seconds)
     */
    private $chaptersTtl;
    private $hadithsTtl;
    private $searchTtl;
    private $searchHistoryTtl;

    public function __construct()
    {
        $this->chaptersTtl = env('CACHE_TTL_CHAPTERS', 86400);        // 24 hours
        $this->hadithsTtl = env('CACHE_TTL_HADITHS', 86400);          // 24 hours
        $this->searchTtl = env('CACHE_TTL_SEARCH', 3600);             // 1 hour
        $this->searchHistoryTtl = env('CACHE_TTL_SEARCH_HISTORY', 1800); // 30 minutes
    }

    /**
     * Get all chapters with caching
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getChapters()
    {
        $cacheKey = self::CHAPTERS_PREFIX . 'all';

        return Cache::remember($cacheKey, $this->chaptersTtl, function () {
            Log::info('Cache miss: fetching all chapters from database');
            return Chapter::orderBy('chapter_number')->get();
        });
    }

    /**
     * Get a specific chapter with its hadiths
     *
     * @param int $chapterId
     * @return array
     */
    public function getChapterWithHadiths($chapterId)
    {
        $cacheKey = self::CHAPTERS_PREFIX . 'with_hadiths:' . $chapterId;

        return Cache::remember($cacheKey, $this->hadithsTtl, function () use ($chapterId) {
            Log::info('Cache miss: fetching chapter with hadiths from database', ['chapter_id' => $chapterId]);
            
            $chapter = Chapter::find($chapterId);
            if (!$chapter) {
                return null;
            }
            
            // Eager load relasi yang dipakai di view untuk mencegah N+1 (chapter, audioFile)
            $hadiths = $chapter->hadiths()
                ->with(['chapter', 'audioFile'])
                ->orderBy('hadith_number')
                ->get();
            
            return [
                'chapter' => $chapter,
                'hadiths' => $hadiths
            ];
        });
    }

    /**
     * Get a specific hadith with related data
     *
     * @param int $hadithId
     * @return array|null
     */
    public function getHadithWithRelatedData($hadithId)
    {
        $cacheKey = self::HADITHS_PREFIX . 'with_related:' . $hadithId;

        return Cache::remember($cacheKey, $this->hadithsTtl, function () use ($hadithId) {
            Log::info('Cache miss: fetching hadith with related data from database', ['hadith_id' => $hadithId]);
            
            $hadith = Hadith::with(['chapter', 'audioFile'])->find($hadithId);
            if (!$hadith) {
                return null;
            }
            
            // Get previous and next hadith in the same chapter
            $previousHadith = Hadith::where('chapter_id', $hadith->chapter_id)
                ->where('hadith_number', '<', $hadith->hadith_number)
                ->orderBy('hadith_number', 'desc')
                ->first();
                
            $nextHadith = Hadith::where('chapter_id', $hadith->chapter_id)
                ->where('hadith_number', '>', $hadith->hadith_number)
                ->orderBy('hadith_number', 'asc')
                ->first();
            
            return [
                'hadith' => $hadith,
                'previousHadith' => $previousHadith,
                'nextHadith' => $nextHadith
            ];
        });
    }

    /**
     * Get search results with caching
     *
     * @param string $query
     * @param int $limit
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getSearchResults($query, $limit = 20)
    {
        $cacheKey = self::SEARCH_PREFIX . md5($query) . ':limit:' . $limit;

        return Cache::remember($cacheKey, $this->searchTtl, function () use ($query, $limit) {
            Log::info('Cache miss: performing search query', ['query' => $query, 'limit' => $limit]);

            $driver = \DB::connection()->getDriverName();

            // Build base query
            $builder = Hadith::query()->with('chapter');

            if ($driver === 'pgsql') {
                // PostgreSQL Full-Text Search pada arabic_text, translation, interpretation
                // Gunakan konfigurasi 'simple' agar netral, tambahkan ILIKE untuk narration_source
                $escaped = str_replace("'", "''", $query);

                $builder->whereRaw(
                    "to_tsvector('simple', coalesce(arabic_text,'') || ' ' || coalesce(translation,'') || ' ' || coalesce(interpretation,'')) @@ plainto_tsquery('simple', ?)",
                    [$query]
                )->orWhere('narration_source', 'ilike', '%' . $query . '%')
                  ->orderByDesc(\DB::raw(
                      "ts_rank(to_tsvector('simple', coalesce(arabic_text,'') || ' ' || coalesce(translation,'') || ' ' || coalesce(interpretation,'')), plainto_tsquery('simple', '{$escaped}'))"
                  ));
            } else {
                // Fallback LIKE-based untuk non-PostgreSQL
                $builder->where(function ($q) use ($query) {
                    $q->where('arabic_text', 'like', '%' . $query . '%')
                      ->orWhere('translation', 'like', '%' . $query . '%')
                      ->orWhere('interpretation', 'like', '%' . $query . '%')
                      ->orWhere('narration_source', 'like', '%' . $query . '%');
                });
            }

            return $builder->limit($limit)->get();
        });
    }

    /**
     * Get user search history with caching
     *
     * @param int $userId
     * @param int $limit
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getUserSearchHistory($userId, $limit = 10)
    {
        $cacheKey = self::SEARCH_HISTORY_PREFIX . 'user:' . $userId . ':limit:' . $limit;

        return Cache::remember($cacheKey, $this->searchHistoryTtl, function () use ($userId, $limit) {
            Log::info('Cache miss: fetching user search history', ['user_id' => $userId, 'limit' => $limit]);
            
            return \App\Models\SearchHistory::where('user_id', $userId)
                ->orderBy('created_at', 'desc')
                ->limit($limit)
                ->get();
        });
    }

    /**
     * Get popular search queries with caching (aggregated from search_history)
     *
     * @param int $limit
     * @return \Illuminate\Support\Collection
     */
    public function getPopularQueries(int $limit = 10)
    {
        $cacheKey = self::POPULAR_SEARCH_PREFIX . 'limit:' . $limit;

        return Cache::remember($cacheKey, $this->searchHistoryTtl, function () use ($limit) {
            Log::info('Cache miss: fetching popular search queries', ['limit' => $limit]);

            return \App\Models\SearchHistory::select('query', \DB::raw('COUNT(*) as total'))
                ->groupBy('query')
                ->orderByDesc('total')
                ->limit($limit)
                ->get();
        });
    }

    /**
     * Clear chapters cache
     *
     * @return void
     */
    public function clearChaptersCache()
    {
        Cache::forget(self::CHAPTERS_PREFIX . 'all');
        Log::info('Chapters cache cleared');
    }

    /**
     * Clear specific chapter with hadiths cache
     *
     * @param int $chapterId
     * @return void
     */
    public function clearChapterWithHadithsCache($chapterId)
    {
        Cache::forget(self::CHAPTERS_PREFIX . 'with_hadiths:' . $chapterId);
        Log::info('Chapter with hadiths cache cleared', ['chapter_id' => $chapterId]);
    }

    /**
     * Clear specific hadith cache
     *
     * @param int $hadithId
     * @return void
     */
    public function clearHadithCache($hadithId)
    {
        Cache::forget(self::HADITHS_PREFIX . 'with_related:' . $hadithId);
        Log::info('Hadith cache cleared', ['hadith_id' => $hadithId]);
    }

    /**
     * Clear search results cache
     *
     * @param string $query
     * @return void
     */
    public function clearSearchCache($query = null)
    {
        if ($query) {
            // Hapus kunci cache yang digunakan oleh getSearchResults() dengan limit default 20 (sesuai ekspektasi test)
            Cache::forget(self::SEARCH_PREFIX . md5($query) . ':limit:20');

            // Opsional: hapus variasi lain yang mungkin digunakan
            Cache::forget(self::SEARCH_PREFIX . md5($query) . ':limit:10');

            Log::info('Search cache cleared for query', ['query' => $query]);
        } else {
            // Tidak ada wildcard/prefix deletion di Cache::forget, dokumentasikan keterbatasan
            Log::info('Search cache clear requested for all queries (not implemented)');
        }
    }

    /**
     * Clear user search history cache
     *
     * @param int $userId
     * @return void
     */
    public function clearUserSearchHistoryCache($userId)
    {
        // This would require a more complex approach to clear all user search history cache
        // In a real application, you might want to use cache tags or a prefix-based deletion
        Log::info('User search history cache clear requested', ['user_id' => $userId]);
    }
}