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
            'email' => ['nullable', 'string', 'email', 'max:75', 'unique:users,email'],
            'phone' => ['required', 'string', new PhoneNumber(), 'unique:users,phone'],
            'password' => ['required', Password::min(8)->letters()->numbers()],
            'status' => ['nullable', 'string', Rule::in([User::STATUS_PENDING, User::STATUS_ACTIVE, User::STATUS_BANNED])],
            'role' => ['nullable', 'string', Rule::in([User::ROLE_USER, User::ROLE_ADMIN])],
        ]);

        // Normalisasi nomor telepon dan cek unik setelah normalisasi
        $normalizedPhone = PhoneUtil::normalize($data['phone']);
        if ($normalizedPhone === null) {
            return response()->json([
                'phone' => ['Format nomor telepon tidak valid (gunakan 0..., 62..., atau +62...).']
            ], 422);
        }
        if (User::where('phone', $normalizedPhone)->exists()) {
            return response()->json([
                'phone' => ['Nomor telepon sudah digunakan.']
            ], 422);
        }

        $user = new User();
        $user->name = $data['name'];
        $user->email = $data['email'] ?? null;
        $user->phone = $normalizedPhone;
        $user->password = Hash::make($data['password']);
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