<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Tambah kolom blind index (email_hash, phone_hash) dan backfill nilai untuk data existing.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('email_hash', 64)->nullable()->unique()->after('email');
            $table->string('phone_hash', 64)->nullable()->unique()->after('phone');
        });

        // Backfill blind index untuk baris yang sudah ada
        $appKey = (string) config('app.key', '');
        if (str_starts_with($appKey, 'base64:')) {
            $appKey = base64_decode(substr($appKey, 7));
        }

        $users = DB::table('users')->select('id', 'email', 'phone')->get();

        foreach ($users as $user) {
            $emailPlain = $user->email;
            $phonePlain = $user->phone;

            $emailNorm = null;
            if (!is_null($emailPlain) && $emailPlain !== '') {
                $emailNorm = Str::lower(trim($emailPlain));
            }

            $phoneNorm = null;
            if (!is_null($phonePlain) && $phonePlain !== '') {
                try {
                    $norm = \App\Support\PhoneUtil::normalize((string) $phonePlain);
                    $phoneNorm = $norm ?: null;
                } catch (\Throwable $e) {
                    // Jika normalisasi gagal, gunakan nilai apa adanya agar tetap konsisten
                    $phoneNorm = (string) $phonePlain;
                }
            }

            $emailHash = null;
            if (!is_null($emailNorm) && $emailNorm !== '') {
                $emailHash = hash_hmac('sha256', $emailNorm, $appKey);
            }

            $phoneHash = null;
            if (!is_null($phoneNorm) && $phoneNorm !== '') {
                $phoneHash = hash_hmac('sha256', $phoneNorm, $appKey);
            }

            DB::table('users')
                ->where('id', $user->id)
                ->update([
                    'email_hash' => $emailHash,
                    'phone_hash' => $phoneHash,
                ]);
        }
    }

    /**
     * Hapus kolom blind index.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Nama indeks mengikuti konvensi default Laravel: {table}_{column}_unique
            $table->dropUnique('users_email_hash_unique');
            $table->dropUnique('users_phone_hash_unique');
            $table->dropColumn(['email_hash', 'phone_hash']);
        });
    }
};