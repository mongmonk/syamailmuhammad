<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureRoleAdmin
{
    /**
     * Ensure the authenticated user has admin role.
     * Returns structured JSON for API requests or aborts for web requests.
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
        if (method_exists($user, 'isBanned') && $user->isBanned()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'code' => 'USER_STATUS_BANNED',
                    'message' => trans('errors.USER_STATUS_BANNED'),
                ], 403);
            }
            abort(403, trans('errors.USER_STATUS_BANNED'));
        }

        // Not admin -> 403
        if (!method_exists($user, 'isAdmin') || !$user->isAdmin()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'code' => 'FORBIDDEN_ADMIN_ONLY',
                    'message' => trans('errors.FORBIDDEN_ADMIN_ONLY'),
                ], 403);
            }
            abort(403, trans('errors.FORBIDDEN_ADMIN_ONLY'));
        }

        return $next($request);
    }
}