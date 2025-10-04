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
        Schema::create('audio_files', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hadith_id')->constrained()->onDelete('cascade');
            $table->string('file_path');
            $table->integer('duration')->nullable(); // dalam detik
            $table->integer('file_size')->nullable(); // dalam bytes
            $table->timestamps();
            
            // Memastikan hanya satu file audio per hadith
            $table->unique('hadith_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audio_files');
    }
};
