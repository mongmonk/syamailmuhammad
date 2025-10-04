<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Hash;
use App\Rules\PhoneNumber;
use App\Support\PhoneUtil;
use Illuminate\Support\Str;

/**
 * Manajemen pengguna oleh admin:
 * - GET /api/users        : daftar pengguna (paginated)
 * - POST /api/users       : buat pengguna baru (status/role opsional)
 * - PATCH /api/users/{user}: ubah status/role pengguna
 * - DELETE /api/users/{user}: hapus pengguna
 *
 * Proteksi: middleware ['auth','role.admin'] di constructor.
 * Respons: JSON dengan struktur seragam dan kode pesan.
 */
class UserController extends Controller
{
    public function __construct()
    {
        // Gunakan JWT untuk API admin agar konsisten dengan group rute /api
        $this->middleware(['jwt', 'role.admin']);
    }

    /**
     * Tampilkan daftar pengguna (paginated).
     */
    public function index(Request $request)
    {
        $users = User::query()
            ->select(['id', 'name', 'email', 'phone', 'status', 'role', 'created_at'])
            ->orderBy('id', 'desc')
            ->paginate(25);

        return response()->json([
            'data' => $users->items(),
            'meta' => [
                'current_page' => $users->currentPage(),
                'per_page' => $users->perPage(),
                'total' => $users->total(),
                'last_page' => $users->lastPage(),
            ],
        ]);
    }

    /**
     * Buat pengguna baru.
     * Admin dapat menentukan status dan role, default: pending & user.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:50'],
            'email' => ['nullable', 'string', 'email', 'max:75'],
            'phone' => ['required', 'string', new PhoneNumber()],
            'password' => ['required', Password::min(8)->letters()->numbers()],
            'status' => ['nullable', 'string', Rule::in([User::STATUS_PENDING, User::STATUS_ACTIVE, User::STATUS_BANNED])],
            'role' => ['nullable', 'string', Rule::in([User::ROLE_USER, User::ROLE_ADMIN])],
        ]);

        // Normalisasi nomor telepon dan cek unik menggunakan blind index setelah normalisasi
        $normalizedPhone = PhoneUtil::normalize($data['phone']);
        if ($normalizedPhone === null) {
            return response()->json([
                'phone' => ['Format nomor telepon tidak valid (gunakan 0..., 62..., atau +62...).']
            ], 422);
        }

        $appKey = (string) config('app.key', '');
        if (str_starts_with($appKey, 'base64:')) {
            $appKey = base64_decode(substr($appKey, 7));
        }
        $phoneHash = hash_hmac('sha256', $normalizedPhone, $appKey);

        if (User::where('phone_hash', $phoneHash)->exists()) {
            return response()->json([
                'phone' => ['Nomor telepon sudah digunakan.']
            ], 422);
        }

        // Cek unik email menggunakan blind index jika email disediakan
        if (!empty($data['email'])) {
            $normalizedEmail = Str::lower(trim((string) $data['email']));
            $emailHash = hash_hmac('sha256', $normalizedEmail, $appKey);

            if (User::where('email_hash', $emailHash)->exists()) {
                return response()->json([
                    'email' => ['Email sudah digunakan.']
                ], 422);
            }
        }

        $user = new User();
        $user->name = $data['name'];
        $user->email = $data['email'] ?? null;
        $user->phone = $normalizedPhone;
        // Hindari double-hash: cast [User.casts()](app/Models/User.php:53) sudah 'password' => 'hashed'
        $user->password = $data['password'];
        $user->status = $data['status'] ?? User::STATUS_PENDING;
        $user->role = $data['role'] ?? User::ROLE_USER;
        $user->save();

        return response()->json([
            'message' => 'Pengguna dibuat',
            'user' => $user->only(['id', 'name', 'email', 'phone', 'status', 'role']),
        ], 201);
    }

    /**
     * Perbarui status/role pengguna.
     * Hanya mengizinkan perubahan pada kolom status dan role.
     */
    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'status' => ['nullable', 'string', Rule::in([User::STATUS_PENDING, User::STATUS_ACTIVE, User::STATUS_BANNED])],
            'role' => ['nullable', 'string', Rule::in([User::ROLE_USER, User::ROLE_ADMIN])],
        ]);

        $changed = [];

        if (array_key_exists('status', $data) && $data['status'] !== null) {
            $user->status = $data['status'];
            $changed[] = 'status';
        }
        if (array_key_exists('role', $data) && $data['role'] !== null) {
            $user->role = $data['role'];
            $changed[] = 'role';
        }

        if (empty($changed)) {
            return response()->json([
                'code' => 'NO_CHANGES',
                'message' => 'Tidak ada perubahan yang diterapkan',
            ], 400);
        }

        $user->save();

        return response()->json([
            'message' => 'Pengguna diperbarui',
            'changed' => $changed,
            'user' => $user->only(['id', 'name', 'email', 'phone', 'status', 'role']),
        ]);
    }

    /**
     * Hapus pengguna.
     */
    public function destroy(User $user)
    {
        $user->delete();

        return response()->json([
            'message' => 'Pengguna dihapus',
        ]);
    }
}