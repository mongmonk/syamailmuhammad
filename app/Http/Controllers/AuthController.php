<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\UserEncryptionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rules\Password;
use App\Rules\PhoneNumber;
use App\Support\PhoneUtil;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    protected $encryptionService;

    public function __construct(UserEncryptionService $encryptionService)
    {
        $this->encryptionService = $encryptionService;
    }

    /**
     * Register a new user using phone as primary credential.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function register(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'name' => ['required', 'string', 'max:50'],
                'email' => ['nullable', 'string', 'email', 'max:75'],
                'phone' => ['required', 'string', new PhoneNumber()],
                'password' => ['required', 'confirmed', Password::min(8)->letters()->numbers()],
            ],
            [
                'name.required' => 'Nama wajib diisi.',
                'name.string' => 'Nama harus berupa teks.',
                'name.max' => 'Nama tidak boleh lebih dari :max karakter.',

                'email.email' => 'Email harus berupa alamat email yang valid.',
                'email.max' => 'Email tidak boleh lebih dari :max karakter.',
                'email.unique' => 'Email sudah digunakan.',

                'phone.required' => 'Nomor telepon wajib diisi.',
                'phone.string' => 'Nomor telepon harus berupa teks.',
                'phone.regex' => 'Format nomor telepon tidak valid (gunakan format E.164, mis. +62812XXXXXXX).',
                'phone.unique' => 'Nomor telepon sudah digunakan.',

                'password.required' => 'Kata sandi wajib diisi.',
                'password.confirmed' => 'Konfirmasi kata sandi tidak cocok.',
                'password.min' => 'Kata sandi minimal :min karakter.',
                'password.letters' => 'Kata sandi harus mengandung setidaknya satu huruf.',
                'password.numbers' => 'Kata sandi harus mengandung setidaknya satu angka.',
            ],
            [
                'name' => 'nama',
                'email' => 'email',
                'phone' => 'nomor telepon',
                'password' => 'kata sandi',
                'password_confirmation' => 'konfirmasi kata sandi',
            ]
        );

        if ($validator->fails()) {
            if ($request->wantsJson()) {
                return response()->json($validator->errors(), 422);
            }
            return back()->withErrors($validator)->withInput();
        }

        try {
            $validated = $validator->validated();

            // Normalisasi nomor telepon ke format penyimpanan "62XXXXXXXXXX"
            $normalizedPhone = PhoneUtil::normalize($validated['phone']);
            if ($normalizedPhone === null) {
                if ($request->wantsJson()) {
                    return response()->json([
                        'phone' => ['Format nomor telepon tidak valid (gunakan 0..., 62..., atau +62...).']
                    ], 422);
                }
                return back()
                    ->withErrors(['phone' => 'Format nomor telepon tidak valid (gunakan 0..., 62..., atau +62...).'])
                    ->withInput();
            }

            // Cek unik menggunakan blind index berdasarkan nomor yang sudah dinormalisasi
            $appKey = (string) config('app.key', '');
            if (str_starts_with($appKey, 'base64:')) {
                $appKey = base64_decode(substr($appKey, 7));
            }
            $phoneHash = hash_hmac('sha256', $normalizedPhone, $appKey);

            if (User::where('phone_hash', $phoneHash)->exists()) {
                if ($request->wantsJson()) {
                    return response()->json([
                        'phone' => ['Nomor telepon sudah digunakan.']
                    ], 422);
                }
                return back()
                    ->withErrors(['phone' => 'Nomor telepon sudah digunakan.'])
                    ->withInput();
            }

            // Cek unik email menggunakan blind index jika email disediakan
            $normalizedEmail = null;
            if (!empty($validated['email'])) {
                $normalizedEmail = Str::lower(trim((string) $validated['email']));
                $emailHash = hash_hmac('sha256', $normalizedEmail, $appKey);
                if (User::where('email_hash', $emailHash)->exists()) {
                    if ($request->wantsJson()) {
                        return response()->json([
                            'email' => ['Email sudah digunakan.']
                        ], 422);
                    }
                    return back()
                        ->withErrors(['email' => 'Email sudah digunakan.'])
                        ->withInput();
                }
            }

            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'] ?? null,
                'phone' => $normalizedPhone,
                // Hindari double-hash: cast [User.casts()](app/Models/User.php:53) sudah 'password' => 'hashed'
                'password' => $validated['password'],
                'status' => User::STATUS_PENDING,
                'role' => User::ROLE_USER,
            ]);

            // Login the user (session-based). Pending tetap diizinkan login untuk akses pribadi.
            Auth::login($user);
            $request->session()->regenerate();

            if ($request->wantsJson()) {
                return response()->json([
                    'message' => 'Registrasi berhasil',
                    'user' => $user->only(['id', 'name', 'email', 'phone', 'status', 'role']),
                ], 201);
            }

            return redirect()->intended(route('home'))
                ->with('status', 'Registrasi berhasil.');
        } catch (\Exception $e) {
            if ($request->wantsJson()) {
                return response()->json([
                    'message' => 'Registrasi gagal',
                    'error' => $e->getMessage(),
                ], 500);
            }
            return back()->withErrors(['general' => 'Registrasi gagal: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Login user using phone + password.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone' => ['required', 'string', new PhoneNumber()],
            'password' => ['required', 'string'],
            'remember' => ['sometimes', 'accepted'],
        ]);

        if ($validator->fails()) {
            if ($request->wantsJson()) {
                return response()->json($validator->errors(), 422);
            }
            return back()->withErrors($validator)->withInput();
        }

        // Normalisasi nomor telepon input agar cocok dengan format penyimpanan
        $normalizedPhone = PhoneUtil::normalize($request->input('phone'));
        if ($normalizedPhone === null) {
            if ($request->wantsJson()) {
                return response()->json([
                    'phone' => ['Format nomor telepon tidak valid (gunakan 0..., 62..., atau +62...).']
                ], 422);
            }
            return back()
                ->withErrors(['phone' => 'Format nomor telepon tidak valid (gunakan 0..., 62..., atau +62...).'])
                ->withInput();
        }

        $remember = $request->boolean('remember');

        // Cari user menggunakan blind index (phone_hash) lalu verifikasi password
        $appKey = (string) config('app.key', '');
        if (str_starts_with($appKey, 'base64:')) {
            $appKey = base64_decode(substr($appKey, 7));
        }
        $phoneHash = hash_hmac('sha256', $normalizedPhone, $appKey);

        /** @var \App\Models\User|null $user */
        $user = User::where('phone_hash', $phoneHash)->first();

        if ($user && Hash::check($request->input('password'), $user->password)) {
            // Jika status banned, tolak login dan jangan buat sesi.
            if ($user->isBanned()) {
                if ($request->wantsJson()) {
                    return response()->json([
                        'code' => 'USER_STATUS_BANNED',
                        'message' => trans('errors.USER_STATUS_BANNED'),
                    ], 403);
                }
                return abort(403, trans('errors.USER_STATUS_BANNED'));
            }

            Auth::login($user, $remember);
            $request->session()->regenerate();

            // Pending diperbolehkan login untuk akses pribadi; batasan endpoint aktif di middleware.
            if ($request->wantsJson()) {
                return response()->json([
                    'message' => 'Login berhasil',
                    'user' => $user->only(['id', 'name', 'email', 'phone', 'status', 'role']),
                ]);
            }

            return redirect()->intended(route('dashboard'))
                ->with('status', 'Login berhasil');
        }

        // Security logging untuk percobaan login gagal
        Log::channel('security')->warning('Login gagal', [
            'phone' => $request->input('phone'),
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'code' => 'UNAUTHENTICATED',
                'message' => trans('errors.CREDENTIALS_INVALID'),
            ], 401);
        }

        return back()->withErrors([
            'phone' => 'Kredensial tidak valid',
        ])->withInput();
    }
    /**
     * API Login: return JWT Bearer token using phone + password.
     * - If banned: 403 USER_STATUS_BANNED
     * - If pending: token tetap diberikan untuk akses pribadi; middleware EnsureUserActive akan membatasi endpoint yang butuh status aktif
     * - If invalid credentials: 401 UNAUTHENTICATED
     */
    public function loginApi(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone' => ['required', 'string', new PhoneNumber()],
            'password' => ['required', 'string'],
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $phoneInput = $request->input('phone');
        $password = $request->input('password');

        // Normalisasi nomor telepon input agar cocok dengan format penyimpanan
        $normalizedPhone = PhoneUtil::normalize($phoneInput);
        if ($normalizedPhone === null) {
            return response()->json([
                'phone' => ['Format nomor telepon tidak valid (gunakan 0..., 62..., atau +62...).']
            ], 422);
        }

        // Cari user menggunakan blind index (phone_hash)
        $appKey = (string) config('app.key', '');
        if (str_starts_with($appKey, 'base64:')) {
            $appKey = base64_decode(substr($appKey, 7));
        }
        $phoneHash = hash_hmac('sha256', $normalizedPhone, $appKey);

        /** @var \App\Models\User|null $user */
        $user = User::where('phone_hash', $phoneHash)->first();

        if (! $user || ! Hash::check($password, $user->password)) {
            // Security logging untuk percobaan login gagal (API)
            Log::channel('security')->warning('API Login gagal', [
                'phone' => $phoneInput,
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            return response()->json([
                'code' => 'UNAUTHENTICATED',
                'message' => trans('errors.CREDENTIALS_INVALID'),
            ], 401);
        }

        if ($user->isBanned()) {
            return response()->json([
                'code' => 'USER_STATUS_BANNED',
                'message' => trans('errors.USER_STATUS_BANNED'),
            ], 403);
        }

        // Issue access and refresh tokens
        /** @var \App\Services\JwtService $jwt */
        $jwt = app(\App\Services\JwtService::class);
        $tokens = $jwt->issueAccessAndRefreshTokens(
            $user,
            3600 * 24,         // access TTL 24 jam
            3600 * 24 * 14,    // refresh TTL 14 hari
            $request->ip(),
            $request->userAgent()
        );

        return response()->json([
            'access_token' => $tokens['access_token'],
            'access_expires_in' => $tokens['access_expires_in'],
            'refresh_token' => $tokens['refresh_token'],
            'refresh_expires_in' => $tokens['refresh_expires_in'],
            'token_type' => $tokens['token_type'],
            'user' => $user->only(['id', 'name', 'email', 'phone', 'status', 'role']),
        ], 200);
    }

    /**
     * Tukar refresh token menjadi access token baru.
     * Header: Authorization: Bearer <refresh_jwt>
     */
    public function refreshToken(Request $request)
    {
        $authHeader = $request->header('Authorization', '');
        if (! str_starts_with($authHeader, 'Bearer ')) {
            return response()->json([
                'code' => 'UNAUTHENTICATED',
                'message' => trans('errors.TOKEN_MISSING'),
            ], 401);
        }

        $refreshToken = substr($authHeader, 7);

        try {
            /** @var \App\Services\JwtService $jwt */
            $jwt = app(\App\Services\JwtService::class);
            $payload = $jwt->decode($refreshToken);

            // Harus refresh token
            $typ = $payload['typ'] ?? 'access';
            if ($typ !== 'refresh') {
                return response()->json([
                    'code' => 'UNAUTHENTICATED',
                    'message' => trans('errors.TOKEN_INVALID'),
                ], 401);
            }

            // Cek JTI revoked
            $jti = $payload['jti'] ?? null;
            if (empty($jti) || $jwt->isRevoked($jti)) {
                return response()->json([
                    'code' => 'UNAUTHENTICATED',
                    'message' => trans('errors.TOKEN_REVOKED'),
                ], 401);
            }

            // Validasi user
            $userId = (int) ($payload['sub'] ?? 0);
            if ($userId <= 0) {
                return response()->json([
                    'code' => 'UNAUTHENTICATED',
                    'message' => trans('errors.TOKEN_SUB_MISSING'),
                ], 401);
            }

            /** @var \App\Models\User|null $user */
            $user = User::find($userId);
            if (! $user) {
                return response()->json([
                    'code' => 'UNAUTHENTICATED',
                    'message' => trans('errors.USER_NOT_FOUND'),
                ], 401);
            }

            if ($user->isBanned()) {
                return response()->json([
                    'code' => 'USER_STATUS_BANNED',
                    'message' => trans('errors.USER_STATUS_BANNED'),
                ], 403);
            }

            // Keluarkan access token baru (refresh tetap valid hingga kadaluarsa atau di-revoke)
            $ttlSeconds = 3600 * 24; // 24 jam
            $accessToken = $jwt->issueToken($user, $ttlSeconds, 'access', $request->ip(), $request->userAgent());

            return response()->json([
                'token' => $accessToken,
                'token_type' => 'Bearer',
                'expires_in' => $ttlSeconds,
            ], 200);
        } catch (\Firebase\JWT\ExpiredException $e) {
            return response()->json([
                'code' => 'UNAUTHENTICATED',
                'message' => trans('errors.TOKEN_EXPIRED'),
            ], 401);
        } catch (\Throwable $e) {
            return response()->json([
                'code' => 'UNAUTHENTICATED',
                'message' => trans('errors.TOKEN_INVALID'),
                'detail' => app()->hasDebugModeEnabled() ? $e->getMessage() : null,
            ], 401);
        }
    }

    /**
     * Revoke token berdasarkan JTI.
     * - Body JSON opsional: { "jti": "<uuid>" }
     * - Jika tidak ada jti di body, gunakan Authorization Bearer token untuk mengambil JTI.
     */
    public function revokeToken(Request $request)
    {
        /** @var \App\Services\JwtService $jwt */
        $jwt = app(\App\Services\JwtService::class);

        $jti = (string) ($request->input('jti') ?? '');

        if ($jti === '') {
            $authHeader = $request->header('Authorization', '');
            if (str_starts_with($authHeader, 'Bearer ')) {
                $raw = substr($authHeader, 7);
                try {
                    $payload = $jwt->decode($raw);
                    $jti = (string) ($payload['jti'] ?? '');
                } catch (\Firebase\JWT\ExpiredException $e) {
                    return response()->json([
                        'code' => 'UNAUTHENTICATED',
                        'message' => trans('errors.TOKEN_EXPIRED'),
                    ], 401);
                } catch (\Throwable $e) {
                    return response()->json([
                        'code' => 'UNAUTHENTICATED',
                        'message' => trans('errors.TOKEN_INVALID'),
                        'detail' => app()->hasDebugModeEnabled() ? $e->getMessage() : null,
                    ], 401);
                }
            }
        }

        if ($jti === '') {
            return response()->json([
                'code' => 'BAD_REQUEST',
                'message' => 'Parameter jti diperlukan atau sertakan Authorization: Bearer token.',
            ], 400);
        }

        // Idempotent: 200 meskipun sudah di-revoke atau tidak ditemukan
        $jwt->revokeByJti($jti);

        return response()->json([
            'message' => 'revoked',
        ], 200);
    }
}