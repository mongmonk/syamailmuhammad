<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('hadiths', function (Blueprint $table) {
            $table->id();
            $table->foreignId('chapter_id')->constrained()->onDelete('cascade');
            $table->text('arabic_text');
            $table->text('translation');
            $table->text('interpretation')->nullable();
            $table->string('narration_source');
            $table->integer('hadith_number');
            $table->timestamps();
            
            // Index untuk hadith_number untuk pencarian cepat
            $table->index('hadith_number');
            
            // Catatan:
            // Index full-text dihilangkan di migrasi ini agar kompatibel dengan SQLite saat pengujian.
            // Untuk produksi PostgreSQL/MySQL, buat indeks GIN/FTS pada kolom gabungan via migrasi terpisah opsional.
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hadiths');
    }
};
