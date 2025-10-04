<?php

namespace App\Services;

use App\Models\Hadith;
use App\Models\SearchHistory;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as BaseCollection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SearchService
{
    /**
     * Pencarian dasar dengan dukungan PostgreSQL Full-Text Search (FTS) bila tersedia.
     * Kolom dicari: arabic_text, translation, interpretation; narration_source via LIKE.
     *
     * @param string $query
     * @param int $limit
     * @param int|null $chapterId
     * @param string|null $narrationSource
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function basicSearch(string $query, int $limit = 20, ?int $chapterId = null, ?string $narrationSource = null): Collection
    {
        $query = trim($query);
        // Normalisasi optional inputs
        $narrationSource = (is_null($narrationSource) || trim((string)$narrationSource) === '' ? null : trim((string)$narrationSource));

        $driver = DB::connection()->getDriverName();
        // Eager load chapter dan audioFile untuk mencegah N+1 saat komponen tampilan mengakses relasi
        $builder = Hadith::query()->with(['chapter', 'audioFile'])->select('hadiths.*');

        // Logging diagnostik
        Log::debug('SearchService.basicSearch.params', [
            'driver' => $driver,
            'query' => $query,
            'limit' => $limit,
            'chapter_id' => $chapterId,
            'narration_source' => $narrationSource,
            'types' => [
                'query' => gettype($query),
                'chapter_id' => gettype($chapterId),
                'narration_source' => gettype($narrationSource),
            ],
        ]);

        // Filter bab jika disediakan
        if ($chapterId !== null) {
            $builder->where('chapter_id', $chapterId);
        }

        // Filter sumber periwayatan jika disediakan
        if ($narrationSource !== null) {
            $builder->where('narration_source', 'like', '%' . $narrationSource . '%');
        }

        if ($driver === 'pgsql') {
            // PostgreSQL FTS menggunakan plainto_tsquery untuk pencarian natural
            // Gabungkan kolom menjadi satu tsvector (config "simple" agar netral untuk Arab/ID/EN)
            // Kelompokkan FTS dan sumber periwayatan agar tunduk pada filter lain (mis. chapter_id)
            $builder->where(function ($q) use ($query) {
                $q->whereRaw(
                    "to_tsvector('simple', coalesce(arabic_text,'') || ' ' || coalesce(translation,'') || ' ' || coalesce(interpretation,'')) @@ plainto_tsquery('simple', ?)",
                    [$query]
                )
                ->orWhere('narration_source', 'ilike', '%' . $query . '%');
            })
            ->addSelect(DB::raw($this->tsRankExpression($query) . ' AS relevance'))
            ->orderByDesc('relevance');
        } else {
            // Fallback untuk sqlite/mysql (tanpa FTS): LIKE-based
            $builder->where(function ($q) use ($query) {
                $q->where('arabic_text', 'like', '%' . $query . '%')
                    ->orWhere('translation', 'like', '%' . $query . '%')
                    ->orWhere('interpretation', 'like', '%' . $query . '%')
                    ->orWhere('narration_source', 'like', '%' . $query . '%');
            })
            ->orderByDesc($this->likeRelevanceExpression($query));
        }

        // Preview SQL dan bindings untuk debugging
        $sqlPreview = $builder->toSql();
        $bindings = $builder->getBindings();
        Log::debug('SearchService.basicSearch.sql', [
            'sql' => $sqlPreview,
            'bindings' => $bindings,
        ]);

        return $builder->limit($limit)->get();
    }

    /**
     * Pencarian lanjutan dengan bobot relevansi dan filter.
     * - Pada PostgreSQL: gunakan ts_rank untuk urutan relevansi.
     * - Pada fallback: gunakan CASE-based scoring sederhana.
     *
     * @param string $query
     * @param array{limit?:int,chapter_id?:int|null,narration_source?:string|null,mode?:string|null} $filters
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function advancedSearch(string $query, array $filters = []): Collection
    {
        $limit = (int)($filters['limit'] ?? 20);
        // Normalisasi input: trim query, dan ubah string kosong menjadi null
        $query = trim($query);
        $rawChapter = $filters['chapter_id'] ?? null;
        $chapterId = (is_null($rawChapter) || $rawChapter === '' ? null : (int)$rawChapter);
        $rawSource = $filters['narration_source'] ?? null;
        $narrationSource = (is_null($rawSource) || trim((string)$rawSource) === '' ? null : trim((string)$rawSource));
        $mode = $filters['mode'] ?? null; // future: support to_tsquery jika diperlukan

        $driver = DB::connection()->getDriverName();

        // Logging diagnostik untuk verifikasi asumsi
        Log::debug('SearchService.advancedSearch.filters', [
            'driver' => $driver,
            'query' => $query,
            'limit' => $limit,
            'chapter_id_raw' => $rawChapter,
            'chapter_id_normalized' => $chapterId,
            'narration_source_raw' => $rawSource,
            'narration_source_normalized' => $narrationSource,
            'mode' => $mode,
            'types' => [
                'query' => gettype($query),
                'chapter_id' => gettype($chapterId),
                'narration_source' => gettype($narrationSource),
            ],
        ]);

        // Eager load chapter dan audioFile agar komponen tampilan tidak memicu lazy-load per baris hasil
        $builder = Hadith::query()->with(['chapter', 'audioFile'])->select('hadiths.*');

        if ($chapterId !== null) {
            $builder->where('chapter_id', $chapterId);
        }
        if ($narrationSource !== null) {
            $builder->where('narration_source', 'like', '%' . $narrationSource . '%');
        }

        if ($driver === 'pgsql') {
            $tsQueryFn = ($mode === 'ts') ? 'to_tsquery' : 'plainto_tsquery';

            // Kelompokkan FTS dan sumber periwayatan dalam satu grup agar tunduk pada filter lain (mis. chapter_id)
            $builder->where(function ($q) use ($tsQueryFn, $query) {
                $q->whereRaw(
                    "to_tsvector('simple', coalesce(arabic_text,'') || ' ' || coalesce(translation,'') || ' ' || coalesce(interpretation,'')) @@ {$tsQueryFn}('simple', ?)",
                    [$query]
                )
                ->orWhere('narration_source', 'ilike', '%' . $query . '%');
            })
            // Tambahkan relevansi
            ->addSelect(DB::raw($this->tsRankExpression($query) . ' AS relevance'))
            ->orderByDesc('relevance');
        } else {
            $builder->where(function ($q) use ($query) {
                $q->where('arabic_text', 'like', '%' . $query . '%')
                    ->orWhere('translation', 'like', '%' . $query . '%')
                    ->orWhere('interpretation', 'like', '%' . $query . '%')
                    ->orWhere('narration_source', 'like', '%' . $query . '%');
            })
            ->addSelect(DB::raw($this->likeRelevanceExpression($query) . ' AS relevance'))
            ->orderByDesc('relevance');
        }

        // Preview SQL dan bindings untuk debugging
        $sqlPreview = $builder->toSql();
        $bindings = $builder->getBindings();
        Log::debug('SearchService.advancedSearch.sql', [
            'sql' => $sqlPreview,
            'bindings' => $bindings,
        ]);

        return $builder->limit($limit)->get();
    }

    /**
     * Ambil daftar query populer dari SearchHistory.
     *
     * @param int $limit
     * @return \Illuminate\Support\Collection
     */
    public function getPopularQueries(int $limit = 10): BaseCollection
    {
        return SearchHistory::query()
            ->select('query', DB::raw('COUNT(*) as total'))
            ->groupBy('query')
            ->orderByDesc('total')
            ->limit($limit)
            ->get();
    }

    /**
     * Ambil riwayat pencarian milik pengguna.
     *
     * @param int $userId
     * @param int $limit
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getUserHistory(int $userId, int $limit = 10): Collection
    {
        return SearchHistory::query()
            ->where('user_id', $userId)
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get();
    }

    /**
     * Ekspresi ts_rank untuk PostgreSQL guna mengurutkan hasil.
     * Menggunakan konfigurasi 'simple' agar tidak bias bahasa.
     *
     * @param string $query
     * @return string
     */
    protected function tsRankExpression(string $query): string
    {
        $escaped = str_replace("'", "''", $query);
        return "ts_rank(to_tsvector('simple', coalesce(arabic_text,'') || ' ' || coalesce(translation,'') || ' ' || coalesce(interpretation,'')), plainto_tsquery('simple', '{$escaped}'))";
    }

    /**
     * Ekspresi relevansi berbasis LIKE sebagai fallback untuk DB non-PostgreSQL.
     * Memberi bobot lebih pada translation dan arabic_text.
     *
     * @param string $query
     * @return string
     */
    protected function likeRelevanceExpression(string $query): string
    {
        // Gunakan %query% untuk pencocokan
        $q = str_replace(['%', '"'], ['\%', '\"'], $query);

        // CASE WHEN ... THEN weight
        return "
            (
                (CASE WHEN translation LIKE '%{$q}%' THEN 2 ELSE 0 END) +
                (CASE WHEN arabic_text LIKE '%{$q}%' THEN 2 ELSE 0 END) +
                (CASE WHEN interpretation LIKE '%{$q}%' THEN 1 ELSE 0 END) +
                (CASE WHEN narration_source LIKE '%{$q}%' THEN 1 ELSE 0 END)
            )
        ";
    }
}