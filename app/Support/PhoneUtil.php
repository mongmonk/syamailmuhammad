<?php

namespace App\Support;

final class PhoneUtil
{
    /**
     * Normalisasi nomor telepon ke format kanonik Indonesia tanpa tanda plus:
     * - Terima input:
     *   - 0XXXXXXXXX  (lokal, leading 0)
     *   - 62XXXXXXXXX (internasional tanpa +)
     *   - +62XXXXXXXX (internasional dengan +)
     * - Hasil normalisasi: 62XXXXXXXXX (hanya digit, tanpa +)
     *
     * Catatan:
     * - Hanya melakukan normalisasi ringan, tidak melakukan validasi panjang/keabsahan.
     * - Validasi tetap dilakukan oleh Validation Rule (mis. PhoneNumberFlexible).
     */
    public static function normalize(?string $input): ?string
    {
        if ($input === null) {
            return null;
        }

        // Hilangkan spasi
        $v = preg_replace('/\s+/', '', $input) ?? '';

        // Hilangkan semua karakter non digit kecuali tanda + (sementara)
        $v = preg_replace('/[^\d+]/', '', $v) ?? '';

        // Hilangkan leading '+'
        if (str_starts_with($v, '+')) {
            $v = substr($v, 1);
        }

        // Jika diawali '0', ganti menjadi '62'
        if (str_starts_with($v, '0')) {
            $v = '62' . substr($v, 1);
        }

        // Biarkan jika sudah '62...' atau bentuk lain (mis. sudah 62XXXXXXXX)
        return $v;
    }
}