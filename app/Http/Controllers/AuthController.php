<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\UserEncryptionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Auth\Events\Registered;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    protected $encryptionService;

    public function __construct(UserEncryptionService $encryptionService)
    {
        $this->encryptionService = $encryptionService;
    }

    /**
     * Register a new user
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'name' => ['required', 'string', 'max:50'],
                'email' => ['required', 'string', 'email', 'max:75', 'unique:users,email'],
                'phone' => ['nullable', 'string', 'max:14', 'unique:users,phone'],
                'password' => ['required', 'confirmed', Password::min(8)->letters()->numbers()],
            ],
            [
                'name.required' => 'Nama wajib diisi.',
                'name.string' => 'Nama harus berupa teks.',
                'name.max' => 'Nama tidak boleh lebih dari :max karakter.',

                'email.required' => 'Email wajib diisi.',
                'email.email' => 'Email harus berupa alamat email yang valid.',
                'email.max' => 'Email tidak boleh lebih dari :max karakter.',
                'email.unique' => 'Email sudah digunakan.',

                'phone.string' => 'Nomor telepon harus berupa teks.',
                'phone.max' => 'Nomor telepon tidak boleh lebih dari :max karakter.',
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
            $validatedData = $validator->validated();
    
            $user = User::create([
                'name' => $validatedData['name'],
                'email' => $validatedData['email'],
                'phone' => $validatedData['phone'] ?? null,
                'password' => Hash::make($validatedData['password']),
            ]);
    
            // Trigger email verification notification if configured
            event(new Registered($user));
    
            // Login the user (session-based)
            Auth::login($user);
            $request->session()->regenerate();
    
            if ($request->wantsJson()) {
                return response()->json([
                    'message' => 'Registrasi berhasil',
                    'user' => $user->only(['id', 'name', 'email', 'phone']),
                ], 201);
            }
    
            return redirect()->intended(route('home'))
                ->with('status', 'Registrasi berhasil. Jika diperlukan, silakan verifikasi email Anda.');
        } catch (\Exception $e) {
            if ($request->wantsJson()) {
                return response()->json([
                    'message' => 'Registrasi gagal',
                    'error' => $e->getMessage(),
                ], 500);
            }
            return back()->withErrors(['general' => 'Registrasi gagal: '.$e->getMessage()])->withInput();
        }
    }

    /**
     * Login user
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
            'remember' => ['nullable', 'boolean'],
        ]);
    
        if ($validator->fails()) {
            if ($request->wantsJson()) {
                return response()->json($validator->errors(), 422);
            }
            return back()->withErrors($validator)->withInput();
        }
    
        $credentials = $request->only('email', 'password');
        $remember = (bool) $request->input('remember', false);
    
        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();
            $user = Auth::user();
    
            if ($request->wantsJson()) {
                return response()->json([
                    'message' => 'Login berhasil',
                    'user' => $user->only(['id', 'name', 'email', 'phone']),
                ]);
            }
    
            return redirect()->intended(route('home'))
                ->with('status', 'Login berhasil');
        }
    
        // Security logging untuk percobaan login gagal
        Log::channel('security')->warning('Login gagal', [
            'email' => $request->input('email'),
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);
    
        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Kredensial tidak valid',
            ], 401);
        }
    
        return back()->withErrors([
            'email' => 'Kredensial tidak valid',
        ])->withInput();
    }

}