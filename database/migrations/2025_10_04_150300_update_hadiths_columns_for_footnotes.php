<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Ubah struktur tabel hadiths sesuai spesifikasi:
        // - Rename kolom "interpretation" menjadi "footnotes"
        // - Hapus kolom "narration_source"
        DB::statement('ALTER TABLE hadiths RENAME COLUMN interpretation TO footnotes');
        DB::statement('ALTER TABLE hadiths DROP COLUMN narration_source');
    }

    public function down(): void
    {
        // Rollback perubahan:
        // - Rename kembali "footnotes" menjadi "interpretation"
        // - Tambahkan kembali kolom "narration_source" sebagai varchar (nullable)
        DB::statement('ALTER TABLE hadiths RENAME COLUMN footnotes TO interpretation');
        DB::statement('ALTER TABLE hadiths ADD COLUMN narration_source varchar');
    }
};