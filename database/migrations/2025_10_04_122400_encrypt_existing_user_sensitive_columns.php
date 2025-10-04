<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Backfill: enkripsi email & phone yang masih plaintext,
     * sekalian normalkan dan set blind index (email_hash, phone_hash).
     */
    public function up(): void
    {
        // Ambil APP_KEY untuk HMAC blind index
        $appKey = (string) Config::get('app.key', '');
        if (str_starts_with($appKey, 'base64:')) {
            $appKey = base64_decode(substr($appKey, 7));
        }

        // Proses secara bertahap untuk menghindari memory bloat
        DB::table('users')
            ->orderBy('id')
            ->select(['id', 'email', 'phone'])
            ->chunkById(500, function ($rows) use ($appKey) {
                foreach ($rows as $row) {
                    $id = $row->id;

                    // EMAIL
                    $emailPlain = $row->email;
                    $emailNorm = null;
                    $emailHash = null;
                    $emailEncrypted = null;
                    $emailIsCipher = false;

                    if (!is_null($emailPlain) && $emailPlain !== '') {
                        // Deteksi apakah sudah terenkripsi (coba decrypt)
                        try {
                            $decrypted = Crypt::decryptString((string) $emailPlain);
                            // Jika decrypt berhasil, berarti sudah terenkripsi
                            $emailIsCipher = true;
                            $emailNorm = Str::lower(trim((string) $decrypted));
                        } catch (\Throwable $e) {
                            // Plaintext: normalisasi lalu enkripsi
                            $emailNorm = Str::lower(trim((string) $emailPlain));
                        }

                        if (!is_null($emailNorm) && $emailNorm !== '') {
                            $emailHash = hash_hmac('sha256', $emailNorm, $appKey);
                            if (!$emailIsCipher) {
                                // Enkripsi nilai ter-normalisasi
                                $emailEncrypted = Crypt::encryptString($emailNorm);
                            }
                        }
                    }

                    // PHONE
                    $phonePlain = $row->phone;
                    $phoneNorm = null;
                    $phoneHash = null;
                    $phoneEncrypted = null;
                    $phoneIsCipher = false;

                    if (!is_null($phonePlain) && $phonePlain !== '') {
                        // Deteksi apakah sudah terenkripsi (coba decrypt)
                        try {
                            $decrypted = Crypt::decryptString((string) $phonePlain);
                            $phoneIsCipher = true;
                            // Normalisasi kembali setelah decrypt
                            try {
                                $n = \App\Support\PhoneUtil::normalize((string) $decrypted);
                                $phoneNorm = $n ?: (string) $decrypted;
                            } catch (\Throwable $e) {
                                $phoneNorm = (string) $decrypted;
                            }
                        } catch (\Throwable $e) {
                            // Plaintext: normalisasi lalu enkripsi
                            try {
                                $n = \App\Support\PhoneUtil::normalize((string) $phonePlain);
                                $phoneNorm = $n ?: (string) $phonePlain;
                            } catch (\Throwable $e2) {
                                $phoneNorm = (string) $phonePlain;
                            }
                        }

                        if (!is_null($phoneNorm) && $phoneNorm !== '') {
                            $phoneHash = hash_hmac('sha256', $phoneNorm, $appKey);
                            if (!$phoneIsCipher) {
                                $phoneEncrypted = Crypt::encryptString($phoneNorm);
                            }
                        }
                    }

                    // Bangun payload update: hanya tulis kolom yang berubah
                    $update = [];

                    // Blind index selalu diset sesuai nilai norm terakhir
                    if (!is_null($emailPlain) && $emailPlain !== '') {
                        $update['email_hash'] = $emailHash;
                    } else {
                        $update['email_hash'] = null;
                    }

                    if (!is_null($phonePlain) && $phonePlain !== '') {
                        $update['phone_hash'] = $phoneHash;
                    } else {
                        $update['phone_hash'] = null;
                    }

                    // Jika sebelumnya plaintext, tulis ciphertext baru
                    if (!$emailIsCipher && !is_null($emailEncrypted)) {
                        $update['email'] = $emailEncrypted;
                    }
                    if (!$phoneIsCipher && !is_null($phoneEncrypted)) {
                        $update['phone'] = $phoneEncrypted;
                    }

                    if (!empty($update)) {
                        DB::table('users')->where('id', $id)->update($update);
                    }
                }
            });
    }

    public function down(): void
    {
        // Operasi ini tidak dapat di-rollback ke plaintext dengan aman.
        // Hanya reset blind index (bukan dekripsi data).
        // Jika diperlukan, kolom hash dapat di-nulllkan.
        DB::table('users')->update([
            'email_hash' => null,
            'phone_hash' => null,
        ]);
    }
};