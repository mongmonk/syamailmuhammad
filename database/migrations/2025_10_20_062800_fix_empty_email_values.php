<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Fix empty email values that cause unique constraint violations.
     * Convert empty strings to NULL to prevent duplicate key errors.
     */
    public function up(): void
    {
        // Fix existing data: convert empty email strings to NULL
        DB::statement("UPDATE users SET email = NULL, email_hash = NULL WHERE email = ''");
        
        // Log the fix for audit purposes
        $fixedCount = DB::table('users')->where('email', '')->count();
        if ($fixedCount > 0) {
            \Log::info("Fixed {$fixedCount} users with empty email strings converted to NULL");
        }
    }

    /**
     * Reverse the migration (not recommended as it will re-introduce the bug).
     */
    public function down(): void
    {
        // This would re-introduce the bug, but included for completeness
        // DB::statement("UPDATE users SET email = '', email_hash = NULL WHERE email IS NULL");
    }
};