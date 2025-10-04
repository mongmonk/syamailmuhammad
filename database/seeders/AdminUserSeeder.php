<?php

namespace Database\Seeders;

use App\Models\User;
use App\Support\PhoneUtil;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Config;

class AdminUserSeeder extends Seeder
{
    /**
     * Seed admin user:
     * Phone: 081234567890
     * Password: Bintang6$k.
     */
    public function run(): void
    {
        $name = 'Administrator';
        $email = null;
        $rawPhone = '081234567890';
        $password = 'Bintang6$k.';

        // Normalisasi nomor telepon (akan masuk ke cast juga, tapi diperlukan untuk blind index lookup idempotent)
        $normalizedPhone = PhoneUtil::normalize($rawPhone) ?? $rawPhone;

        // HMAC blind index menggunakan APP_KEY (selaras dengan [EncryptedString.set()](app/Casts/EncryptedString.php:35))
        $appKey = (string) Config::get('app.key', '');
        if (\str_starts_with($appKey, 'base64:')) {
            $appKey = \base64_decode(\substr($appKey, 7));
        }
        $phoneHash = \hash_hmac('sha256', $normalizedPhone, $appKey);

        // Idempotent: cari berdasarkan phone_hash
        /** @var \App\Models\User|null $existing */
        $existing = User::query()->where('phone_hash', $phoneHash)->first();

        if (! $existing) {
            // Fallback: cari admin yang nomor telepon terdekripsi sama dengan nomor yang sudah dinormalisasi
            // Catatan: $u->phone akan terdekripsi oleh cast [EncryptedString.get()](app/Casts/EncryptedString.php:21)
            $candidates = User::query()->where('role', User::ROLE_ADMIN)->get();
            foreach ($candidates as $u) {
                if ((string) $u->phone === (string) $normalizedPhone) {
                    $existing = $u;
                    break;
                }
            }
        }

        if ($existing) {
            // Update via Eloquent agar cast enkripsi & hashed password tetap berjalan
            $existing->name = $name;
            $existing->email = $email;
            $existing->phone = $normalizedPhone; // akan ternormalisasi + terenkripsi oleh cast
            $existing->password = $password;     // cast 'hashed' akan meng-hash otomatis
            $existing->status = User::STATUS_ACTIVE;
            $existing->role = User::ROLE_ADMIN;
            $existing->save();
        } else {
            // Create via Eloquent agar cast berjalan
            User::create([
                'name' => $name,
                'email' => $email,
                'phone' => $normalizedPhone, // cast akan normalisasi + enkripsi + set phone_hash
                'password' => $password,     // cast 'hashed' akan meng-hash otomatis
                'status' => User::STATUS_ACTIVE,
                'role' => User::ROLE_ADMIN,
            ]);
        }
    }
}