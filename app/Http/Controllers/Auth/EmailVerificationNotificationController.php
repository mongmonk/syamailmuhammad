<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class EmailVerificationNotificationController extends Controller
{
    /**
     * Kirim ulang email verifikasi untuk pengguna yang belum terverifikasi.
     */
    public function store(Request $request)
    {
        if ($request->user()?->hasVerifiedEmail()) {
            return redirect()->intended(route('home'));
        }

        $request->user()?->sendEmailVerificationNotification();

        if ($request->wantsJson()) {
            return response()->json([
                'status' => __('verification.link_sent'),
            ], 200);
        }

        return back()->with('status', __('verification.link_sent'));
    }
}