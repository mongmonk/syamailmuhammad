<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_reading_progress', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();

            // Sumber progres: chapter atau hadith
            $table->string('resource_type', 20);
            $table->unsignedBigInteger('resource_id');

            // Posisi progres (mis. indeks ayat/hadith atau offset)
            $table->integer('position')->default(0);

            $table->timestamps();

            // Unik per user+sumber
            $table->unique(['user_id', 'resource_type', 'resource_id'], 'uniq_user_reading_progress');

            // Index untuk query umum
            $table->index(['resource_type', 'resource_id'], 'idx_reading_resource');
        });

        // Enum-like CHECK constraint untuk Postgres (idempotent-ish)
        try {
            DB::statement("ALTER TABLE user_reading_progress ADD CONSTRAINT user_reading_progress_type_check CHECK (resource_type IN ('chapter','hadith'));");
        } catch (\Throwable $e) {
            // Abaikan jika DB bukan Postgres atau constraint sudah ada
        }
    }

    public function down(): void
    {
        try {
            DB::statement("ALTER TABLE user_reading_progress DROP CONSTRAINT IF EXISTS user_reading_progress_type_check;");
        } catch (\Throwable $e) {
            // noop
        }

        Schema::dropIfExists('user_reading_progress');
    }
};