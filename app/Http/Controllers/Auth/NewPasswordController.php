<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\Rules\Password as PasswordRule;

class NewPasswordController extends Controller
{
    /**
     * Tampilkan form reset password.
     */
    public function create(Request $request, ?string $token = null)
    {
        return view('auth.reset-password', [
            'token' => $token ?? $request->route('token'),
            'email' => $request->query('email'),
        ]);
    }

    /**
     * Proses reset password menggunakan token yang dikirim via email.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'token' => ['required', 'string'],
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmed', PasswordRule::min(8)->letters()->numbers()],
        ]);

        $status = Password::reset(
            $validated,
            function (User $user, string $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        if ($request->wantsJson()) {
            return response()->json([
                'status' => __($status),
            ], $status === Password::PASSWORD_RESET ? 200 : 422);
        }

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('login')->with('status', __($status))
            : back()->withErrors(['email' => __($status)]);
    }
}