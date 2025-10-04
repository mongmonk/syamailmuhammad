<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureUserNotBanned
{
    /**
     * Block access for banned users on any authenticated endpoint.
     * If unauthenticated, return 401.
     * If banned, return 403 with structured error for JSON requests or abort for web.
     */
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        // Unauthenticated -> 401
        if (!$user) {
            if ($request->expectsJson()) {
                return response()->json([
                    'code' => 'UNAUTHENTICATED',
                    'message' => trans('errors.UNAUTHENTICATED'),
                ], 401);
            }
            abort(401, trans('errors.UNAUTHENTICATED'));
        }

        // Banned -> 403
        if ($user->isBanned()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'code' => 'USER_STATUS_BANNED',
                    'message' => trans('errors.USER_STATUS_BANNED'),
                ], 403);
            }
            abort(403, trans('errors.USER_STATUS_BANNED'));
        }

        return $next($request);
    }
}