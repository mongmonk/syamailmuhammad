<?php

namespace App\Casts;

use App\Services\UserEncryptionService;
use App\Support\PhoneUtil;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;

class EncryptedString implements CastsAttributes
{
    /**
     * Cast the given value (decrypt if encrypted, otherwise return as-is).
     *
     * @param  array<string, mixed>  $attributes
     * @return string|null
     */
    public function get(Model $model, string $key, mixed $value, array $attributes)
    {
        $encryptionService = App::make(UserEncryptionService::class);

        if ($value === null || $value === '') {
            return $value;
        }

        // Hindari DecryptException untuk data legacy plaintext
        if (! $encryptionService->isEncrypted((string) $value)) {
            return $value;
        }

        return $encryptionService->decrypt((string) $value);
    }

    /**
     * Prepare the given value for storage (normalize, hash blind index, then encrypt).
     *
     * @param  array<string, mixed>  $attributes
     * @return string|array|null
     */
    public function set(Model $model, string $key, mixed $value, array $attributes)
    {
        $encryptionService = App::make(UserEncryptionService::class);

        if ($value === null || $value === '') {
            // PERBAIKAN: Kembalikan null bukan string kosong untuk menghindari unique constraint violation
            if ($key === 'phone') {
                return [
                    'phone' => null,
                    'phone_hash' => null,
                ];
            } elseif ($key === 'email') {
                return [
                    'email' => null,
                    'email_hash' => null,
                ];
            }
            return null;
        }

        // Normalisasi sebelum enkripsi dan hashing
        $normalized = (string) $value;

        if ($key === 'phone') {
            $normalized = PhoneUtil::normalize((string) $value);
            if ($normalized === null || $normalized === '') {
                return [
                    'phone' => $value,
                    'phone_hash' => null,
                ];
            }
        } elseif ($key === 'email') {
            $normalized = Str::lower(trim((string) $value));
        }

        // Hitung blind index menggunakan HMAC-SHA256 + APP_KEY
        $appKey = (string) Config::get('app.key', '');
        if (str_starts_with($appKey, 'base64:')) {
            $appKey = base64_decode(substr($appKey, 7));
        }
        $blindIndex = hash_hmac('sha256', $normalized, $appKey);

        // Enkripsi nilai ter-normalisasi
        $encrypted = $encryptionService->encrypt($normalized);

        // Kembalikan array agar Eloquent menyimpan kolom utama + hash bersama-sama
        if ($key === 'phone') {
            return [
                'phone' => $encrypted,
                'phone_hash' => $blindIndex,
            ];
        } elseif ($key === 'email') {
            return [
                'email' => $encrypted,
                'email_hash' => $blindIndex,
            ];
        }

        return $encrypted;
    }
}