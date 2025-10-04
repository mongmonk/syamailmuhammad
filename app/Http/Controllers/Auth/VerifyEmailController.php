<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\Request;

class VerifyEmailController extends Controller
{
    /**
     * Verifikasi email menggunakan tautan bertanda (signed).
     * Middleware: auth, signed, throttle:6,1
     */
    public function verify(Request $request)
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->route('login');
        }

        // Jika sudah terverifikasi, arahkan ke halaman utama
        if ($user->hasVerifiedEmail()) {
            return redirect()->intended(route('home'))
                ->with('status', __('Email Anda sudah terverifikasi.'));
        }

        $id = (int) $request->route('id');
        $hash = (string) $request->route('hash');

        // Pastikan tautan sesuai dengan pengguna yang login
        if ($user->getKey() !== $id) {
            return redirect()->route('login');
        }

        // Validasi hash terhadap email pengguna
        if (! hash_equals(sha1($user->getEmailForVerification()), $hash)) {
            return redirect()->route('verification.notice')
                ->withErrors(['email' => __('Tautan verifikasi tidak valid atau kedaluwarsa.')]);
        }

        // Tandai email sebagai terverifikasi
        if ($user->markEmailAsVerified()) {
            event(new Verified($user));
        }

        return redirect()->intended(route('home'))
            ->with('status', __('Email Anda telah berhasil diverifikasi.'));
    }
}