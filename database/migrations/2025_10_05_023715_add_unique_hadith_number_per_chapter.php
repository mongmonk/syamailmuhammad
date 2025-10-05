<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add unique constraint for hadith_number within a chapter.
     * Compatible with SQLite for tests, and MySQL/PostgreSQL for production.
     */
    public function up(): void
    {
        Schema::table('hadiths', function (Blueprint $table) {
            // Enforce uniqueness of hadith_number per chapter
            $table->unique(['chapter_id', 'hadith_number'], 'hadiths_chapter_id_hadith_number_unique');
        });
    }

    /**
     * Rollback the unique constraint.
     */
    public function down(): void
    {
        Schema::table('hadiths', function (Blueprint $table) {
            $table->dropUnique('hadiths_chapter_id_hadith_number_unique');
        });
    }
};