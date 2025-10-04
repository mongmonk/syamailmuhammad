<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // PostgreSQL-specific optimizations
        if (DB::getDriverName() === 'pgsql') {
            // Enable trigram extension for fast ILIKE searches (if permitted)
            DB::statement("CREATE EXTENSION IF NOT EXISTS pg_trgm");

            // GIN index for full-text search on combined columns
            DB::statement("
                CREATE INDEX IF NOT EXISTS hadiths_fts_gin_idx
                ON hadiths
                USING gin (to_tsvector('simple',
                    coalesce(arabic_text,'') || ' ' ||
                    coalesce(translation,'') || ' ' ||
                    coalesce(interpretation,'')
                ));
            ");

            // Trigram GIN index for ILIKE on narration_source
            DB::statement("
                CREATE INDEX IF NOT EXISTS hadiths_narration_source_trgm_idx
                ON hadiths
                USING gin (narration_source gin_trgm_ops);
            ");
        }

        // Idempotent index creation:
        // - PostgreSQL: use CREATE INDEX IF NOT EXISTS via raw statements
        // - Other drivers: fallback to Schema builder
        if (DB::getDriverName() === 'pgsql') {
            DB::statement("CREATE INDEX IF NOT EXISTS hadiths_chapter_id_hadith_number_index ON hadiths (chapter_id, hadith_number)");
            DB::statement("CREATE INDEX IF NOT EXISTS hadiths_chapter_id_index ON hadiths (chapter_id)");
            DB::statement("CREATE INDEX IF NOT EXISTS hadiths_hadith_number_index ON hadiths (hadith_number)");

            DB::statement("CREATE INDEX IF NOT EXISTS chapters_chapter_number_index ON chapters (chapter_number)");

            DB::statement("CREATE INDEX IF NOT EXISTS bookmarks_user_hadith_index ON bookmarks (user_id, hadith_id)");

            DB::statement("CREATE INDEX IF NOT EXISTS user_notes_user_hadith_index ON user_notes (user_id, hadith_id)");

            DB::statement("CREATE INDEX IF NOT EXISTS search_history_user_created_at_index ON search_history (user_id, created_at)");
        } else {
            Schema::table('hadiths', function (Blueprint $table) {
                $table->index(['chapter_id', 'hadith_number'], 'hadiths_chapter_id_hadith_number_index');
                $table->index('chapter_id', 'hadiths_chapter_id_index');
            });


            Schema::table('bookmarks', function (Blueprint $table) {
                $table->index(['user_id', 'hadith_id'], 'bookmarks_user_hadith_index');
            });

            Schema::table('user_notes', function (Blueprint $table) {
                $table->index(['user_id', 'hadith_id'], 'user_notes_user_hadith_index');
            });

            Schema::table('search_history', function (Blueprint $table) {
                $table->index(['user_id', 'created_at'], 'search_history_user_created_at_index');
            });
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'pgsql') {
            DB::statement("DROP INDEX IF EXISTS hadiths_fts_gin_idx");
            DB::statement("DROP INDEX IF EXISTS hadiths_narration_source_trgm_idx");
            DB::statement("DROP INDEX IF EXISTS hadiths_chapter_id_hadith_number_index");
            DB::statement("DROP INDEX IF EXISTS hadiths_chapter_id_index");
            DB::statement("DROP INDEX IF EXISTS hadiths_hadith_number_index");
            DB::statement("DROP INDEX IF EXISTS chapters_chapter_number_index");
            DB::statement("DROP INDEX IF EXISTS bookmarks_user_hadith_index");
            DB::statement("DROP INDEX IF EXISTS user_notes_user_hadith_index");
            DB::statement("DROP INDEX IF EXISTS search_history_user_created_at_index");
            // Note: extension pg_trgm is left installed intentionally
        } else {
            Schema::table('hadiths', function (Blueprint $table) {
                $table->dropIndex('hadiths_chapter_id_hadith_number_index');
                $table->dropIndex('hadiths_chapter_id_index');
            });


            Schema::table('bookmarks', function (Blueprint $table) {
                $table->dropIndex('bookmarks_user_hadith_index');
            });

            Schema::table('user_notes', function (Blueprint $table) {
                $table->dropIndex('user_notes_user_hadith_index');
            });

            Schema::table('search_history', function (Blueprint $table) {
                $table->dropIndex('search_history_user_created_at_index');
            });
        }
    }
};