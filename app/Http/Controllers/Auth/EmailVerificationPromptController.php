<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class EmailVerificationPromptController extends Controller
{
    /**
     * Tampilkan halaman prompt verifikasi email.
     * - Jika email sudah terverifikasi, arahkan ke halaman utama.
     */
    public function show(Request $request)
    {
        if ($request->user()?->hasVerifiedEmail()) {
            return redirect()->intended(route('home'));
        }

        return view('auth.verify-email');
    }
}