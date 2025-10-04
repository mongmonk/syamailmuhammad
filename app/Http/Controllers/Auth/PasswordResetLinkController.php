<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;

class PasswordResetLinkController extends Controller
{
    /**
     * Tampilkan form lupa password.
     */
    public function create()
    {
        return view('auth.forgot-password');
    }

    /**
     * Kirimkan tautan reset password ke email pengguna.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
        ]);

        $status = Password::sendResetLink(
            ['email' => $validated['email']]
        );

        if ($request->wantsJson()) {
            return response()->json([
                'status' => __($status),
            ], $status === Password::RESET_LINK_SENT ? 200 : 422);
        }

        return $status === Password::RESET_LINK_SENT
            ? back()->with('status', __($status))
            : back()->withErrors(['email' => __($status)]);
    }
}