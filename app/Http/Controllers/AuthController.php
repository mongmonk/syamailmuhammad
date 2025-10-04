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
                'email' => ['nullable', 'string', 'email', 'max:75', 'unique:users,email'],
                'phone' => ['required', 'string', new PhoneNumber(), 'unique:users,phone'],
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

            // Cek unik berdasarkan nomor yang sudah dinormalisasi
            if (User::where('phone', $normalizedPhone)->exists()) {
                if ($request->wantsJson()) {
                    return response()->json([
                        'phone' => ['Nomor telepon sudah digunakan.']
                    ], 422);
                }
                return back()
                    ->withErrors(['phone' => 'Nomor telepon sudah digunakan.'])
                    ->withInput();
            }

            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'] ?? null,
                'phone' => $normalizedPhone,
                'password' => Hash::make($validated['password']),
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
            'remember' => ['nullable', 'boolean'],
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

        $credentials = [
            'phone' => $normalizedPhone,
            'password' => $request->input('password'),
        ];
        $remember = (bool) $request->input('remember', false);

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();
            $user = Auth::user();

            // Jika status banned, tolak login dan jangan buat sesi.
            if ($user->isBanned()) {
                Auth::logout();
                if ($request->wantsJson()) {
                    return response()->json([
                        'code' => 'USER_STATUS_BANNED',
                        'message' => trans('errors.USER_STATUS_BANNED'),
                    ], 403);
                }
                return abort(403, trans('errors.USER_STATUS_BANNED'));
            }

            // Pending diperbolehkan login untuk akses pribadi; batasan endpoint aktif di middleware.
            if ($request->wantsJson()) {
                return response()->json([
                    'message' => 'Login berhasil',
                    'user' => $user->only(['id', 'name', 'email', 'phone', 'status', 'role']),
                ]);
            }

            return redirect()->intended(route('home'))
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

        /** @var \App\Models\User|null $user */
        $user = User::where('phone', $normalizedPhone)->first();

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

        // Issue JWT token
        /** @var \App\Services\JwtService $jwt */
        $jwt = app(\App\Services\JwtService::class);
        $ttlSeconds = 3600 * 24; // 24 jam
        $token = $jwt->issueToken($user, $ttlSeconds);

        return response()->json([
            'token' => $token,
            'token_type' => 'Bearer',
            'expires_in' => $ttlSeconds,
            'user' => $user->only(['id', 'name', 'email', 'phone', 'status', 'role']),
        ], 200);
    }
}