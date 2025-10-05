<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Validation\Rule;
use App\Services\AuditLogger;

class UsersPageController extends Controller
{
    public function __construct()
    {
        // Middleware SSR (route group sudah pakai ['auth','ensure.active','role.admin'])
        // Tetap didefinisikan di controller untuk pertahanan berlapis.
        $this->middleware(['auth', 'ensure.active', 'role.admin']);
    }

    public function index(Request $request)
    {
        // Filter opsional: q (nama), status, role
        $query = User::query()
            ->select(['id', 'name', 'email', 'phone', 'status', 'role', 'created_at'])
            ->orderByDesc('id');

        if ($request->filled('q')) {
            $q = trim($request->input('q'));
            $query->where('name', 'ILIKE', '%' . str_replace('%', '\\%', $q) . '%');
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('role')) {
            $query->where('role', $request->input('role'));
        }

        $users = $query->paginate(15)->appends($request->query());

        // Jika view Blade tersedia, gunakan
        if (view()->exists('admin.users.index')) {
            return view('admin.users.index', compact('users'));
        }

        // Stub HTML sementara jika view belum dibuat
        $html = '<!DOCTYPE html><html lang="id"><head><meta charset="utf-8"><title>Admin - Pengguna</title><link rel="stylesheet" href="' . asset('css/app.css') . '"></head><body class="bg-gray-50 text-gray-900"><div class="max-w-7xl mx-auto px-4 py-8"><h1 class="text-2xl font-bold mb-4">Daftar Pengguna (SSR)</h1><p class="text-gray-600 mb-4">View Blade admin.users.index belum dibuat. Tampilkan stub sementara.</p><table class="min-w-full bg-white border"><thead><tr><th class="px-3 py-2 border-b">ID</th><th class="px-3 py-2 border-b">Nama</th><th class="px-3 py-2 border-b">Phone</th><th class="px-3 py-2 border-b">Status</th><th class="px-3 py-2 border-b">Role</th></tr></thead><tbody>';

        foreach ($users as $u) {
            $html .= '<tr><td class="px-3 py-2 border-b">' . e($u->id) . '</td><td class="px-3 py-2 border-b">' . e($u->name) . '</td><td class="px-3 py-2 border-b">' . e($u->phone) . '</td><td class="px-3 py-2 border-b">' . e($u->status) . '</td><td class="px-3 py-2 border-b">' . e($u->role) . '</td></tr>';
        }

        $html .= '</tbody></table><p class="text-sm text-gray-500 mt-3">Pagination: halaman ' . e($users->currentPage()) . ' dari ' . e($users->lastPage()) . '</p><p class="mt-4"><a href="' . route('admin.index') . '" class="text-emerald-700 hover:underline">Kembali ke Dashboard Admin</a></p></div></body></html>';

        return response($html);
    }
    /**
     * Form buat pengguna baru oleh admin (SSR).
     */
    public function create()
    {
        return view('admin.users.create');
    }

    /**
     * Simpan pengguna baru (SSR).
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:50'],
            'email' => ['nullable', 'string', 'email', 'max:75'],
            'phone' => ['required', 'string', new \App\Rules\PhoneNumber()],
            'password' => ['required', \Illuminate\Validation\Rules\Password::min(8)->letters()->numbers()],
            'status' => ['nullable', 'string', \Illuminate\Validation\Rule::in([User::STATUS_PENDING, User::STATUS_ACTIVE, User::STATUS_BANNED])],
            'role' => ['nullable', 'string', \Illuminate\Validation\Rule::in([User::ROLE_USER, User::ROLE_ADMIN])],
        ]);

        // Normalisasi nomor telepon
        $normalizedPhone = \App\Support\PhoneUtil::normalize($data['phone']);
        if ($normalizedPhone === null) {
            return back()
                ->withErrors(['phone' => 'Format nomor telepon tidak valid (gunakan 0..., 62..., atau +62...).'])
                ->withInput();
        }

        // Blind index untuk cek unik
        $appKey = (string) config('app.key', '');
        if (str_starts_with($appKey, 'base64:')) {
            $appKey = base64_decode(substr($appKey, 7));
        }
        $phoneHash = hash_hmac('sha256', $normalizedPhone, $appKey);
        if (User::where('phone_hash', $phoneHash)->exists()) {
            return back()
                ->withErrors(['phone' => 'Nomor telepon sudah digunakan.'])
                ->withInput();
        }

        if (!empty($data['email'])) {
            $normalizedEmail = \Illuminate\Support\Str::lower(trim((string) $data['email']));
            $emailHash = hash_hmac('sha256', $normalizedEmail, $appKey);
            if (User::where('email_hash', $emailHash)->exists()) {
                return back()
                    ->withErrors(['email' => 'Email sudah digunakan.'])
                    ->withInput();
            }
        }

        // Buat user baru (password otomatis di-hash oleh cast)
        $user = new User();
        $user->name = $data['name'];
        $user->email = $data['email'] ?? null;
        $user->phone = $normalizedPhone;
        $user->password = $data['password']; // [User.casts()](app/Models/User.php:53) => 'password' => 'hashed'
        $user->status = $data['status'] ?? User::STATUS_PENDING;
        $user->role = $data['role'] ?? User::ROLE_USER;
        $user->save();

        return redirect()
            ->route('admin.users.index')
            ->with('status', 'Pengguna berhasil dibuat.');
    }

    /**
     * Tampilkan form edit status/role pengguna.
     */
    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    /**
     * Perbarui status/role pengguna (SSR).
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
            return back()->with('status', 'Tidak ada perubahan yang diterapkan');
        }

        $user->save();

        // Tampilkan detail perubahan beserta nilai baru agar lebih informatif
        $details = [];
        if (in_array('status', $changed, true)) {
            $details[] = 'Status: ' . $user->status;
        }
        if (in_array('role', $changed, true)) {
            $details[] = 'Role: ' . $user->role;
        }

        $message = 'Pengguna diperbarui';
        if (! empty($details)) {
            $message .= ' — ' . implode(', ', $details);
        }

        // Audit: admin memperbarui user
        app(AuditLogger::class)->allow(
            'admin.user.update',
            'user',
            (string) $user->id,
            null,
            [
                'changed' => $changed,
                'new' => [
                    'status' => $user->status,
                    'role' => $user->role,
                ],
            ],
            $request
        );
        
        return redirect()
            ->route('admin.users.index', $request->query())
            ->with('status', $message);
    }
}