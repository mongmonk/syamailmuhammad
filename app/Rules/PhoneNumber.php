<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use App\Support\PhoneUtil;

final class PhoneNumber implements ValidationRule
{
    /**
     * Validasi nomor telepon Indonesia fleksibel; menerima:
     * - 0XXXXXXXXX (lokal, diawali 0)
     * - 62XXXXXXXXX (internasional tanpa '+')
     * - +62XXXXXXXXX (internasional dengan '+')
     *
     * Disimpan/divalidasi sebagai 62XXXXXXXXX (hanya digit, tanpa '+').
     * Contoh input: 082232236630 -> normalisasi: 6282232236630
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!is_string($value)) {
            $fail('Format nomor telepon tidak valid (gunakan 0..., 62..., atau +62...).');
            return;
        }

        $normalized = PhoneUtil::normalize($value);

        if ($normalized === null) {
            $fail('Format nomor telepon tidak valid (gunakan 0..., 62..., atau +62...).');
            return;
        }

        // Validasi setelah normalisasi:
        // - Harus diawali '62'
        // - Digit setelah '62' tidak boleh diawali '0'
        // - Panjang total 9-15 digit cukup longgar untuk nomor umum
        if (!preg_match('/^62[1-9]\d{7,13}$/', $normalized)) {
            $fail('Format nomor telepon tidak valid (contoh: 0822XXXXXXX atau +62812XXXXXXX).');
            return;
        }
    }
}