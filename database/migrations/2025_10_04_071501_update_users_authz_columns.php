<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Update users table to meet new authz spec:
     * - email: nullable (unique remains)
     * - phone: NOT NULL and unique (login credential)
     * - status: pending/active/banned (default pending)
     * - role: user/admin (default user)
     * - indexes and CHECK constraints for Postgres
     */
    public function up(): void
    {
        $driver = Schema::getConnection()->getDriverName();

        if ($driver !== 'sqlite') {
            // Nullability changes via PostgreSQL-compatible ALTER statements
            DB::statement("ALTER TABLE users ALTER COLUMN email DROP NOT NULL;");
            // Backfill nilai phone untuk baris lama yang masih NULL agar aman sebelum SET NOT NULL
            // Gunakan pola '+999000' || id agar unik dan kecil kemungkinan berbenturan
            DB::statement("UPDATE users SET phone = '+999000' || id WHERE phone IS NULL;");
            DB::statement("ALTER TABLE users ALTER COLUMN phone SET NOT NULL;");
        } else {
            // SQLite tidak mendukung ALTER COLUMN DROP/SET NOT NULL secara langsung.
            // Biarkan constraint NOT NULL apa adanya; validasi aplikasi akan memastikan konsistensi.
        }

        Schema::table('users', function (Blueprint $table) {
            // New columns
            $table->string('status', 10)->default('pending');
            $table->string('role', 10)->default('user');

            // Indexes for faster filtering
            $table->index('status');
            $table->index('role');
        });

        if ($driver !== 'sqlite') {
            // CHECK constraints to emulate enum behavior in PostgreSQL
            DB::statement("ALTER TABLE users ADD CONSTRAINT users_status_check CHECK (status IN ('pending','active','banned'));");
            DB::statement("ALTER TABLE users ADD CONSTRAINT users_role_check CHECK (role IN ('user','admin'));");
        }
    }

    /**
     * Revert changes to original schema:
     * - email: NOT NULL
     * - phone: nullable
     * - drop status/role columns and their indexes & constraints
     */
    public function down(): void
    {
        $driver = Schema::getConnection()->getDriverName();

        if ($driver !== 'sqlite') {
            // Drop constraints if exist (idempotent)
            DB::statement("ALTER TABLE users DROP CONSTRAINT IF EXISTS users_status_check;");
            DB::statement("ALTER TABLE users DROP CONSTRAINT IF EXISTS users_role_check;");
        }

        Schema::table('users', function (Blueprint $table) {
            // Drop indexes and columns
            $table->dropIndex(['status']);
            $table->dropIndex(['role']);
            $table->dropColumn('status');
            $table->dropColumn('role');
        });

        if ($driver !== 'sqlite') {
            // Revert nullability
            DB::statement("ALTER TABLE users ALTER COLUMN email SET NOT NULL;");
            DB::statement("ALTER TABLE users ALTER COLUMN phone DROP NOT NULL;");
        }
    }
};