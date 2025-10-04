<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureUserActive
{
    /**
     * Enforce that the authenticated user is active.
     * Returns structured JSON or aborts with appropriate HTTP status for web.
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

        // Pending (not active) -> 403
        if (!$user->isActive()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'code' => 'USER_STATUS_NOT_ACTIVE',
                    'message' => trans('errors.USER_STATUS_NOT_ACTIVE'),
                ], 403);
            }
            abort(403, trans('errors.USER_STATUS_NOT_ACTIVE'));
        }

        return $next($request);
    }
}