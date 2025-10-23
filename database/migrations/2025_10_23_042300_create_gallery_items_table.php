<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gallery_items', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('slug', 220)->unique();
            $table->string('original_filename', 255);
            $table->string('mime', 100);
            $table->unsignedInteger('original_width')->nullable();
            $table->unsignedInteger('original_height')->nullable();
            $table->json('variants'); // path varian ukuran (thumb, medium, large, max), relatif terhadap disk publik
            $table->string('caption', 150)->nullable();
            $table->string('alt_text', 180)->nullable(); // default diisi sama seperti caption saat penyimpanan
            $table->json('tags')->nullable(); // array string tag opsional
            $table->boolean('is_active')->default(true)->index();
            $table->timestamp('published_at')->nullable()->index();
            $table->timestamps();

            $table->index(['is_active', 'published_at'], 'idx_gallery_active_published');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gallery_items');
    }
};