<?php

namespace App\Http\Middleware;

use App\Models\User;
use App\Services\JwtService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class JwtAuthenticate
{
    /**
     * Authenticate request using JWT Bearer token.
     * - Requires Authorization: Bearer <token>
     * - Sets Auth::setUser($user) on success
     * - Blocks banned users
     * - Returns structured JSON errors for API
     */
    public function handle(Request $request, Closure $next)
    {
        $authHeader = $request->header('Authorization', '');

        if (! str_starts_with($authHeader, 'Bearer ')) {
            return response()->json([
                'code' => 'UNAUTHENTICATED',
                'message' => trans('errors.TOKEN_MISSING'),
            ], 401);
        }

        $token = substr($authHeader, 7);

        try {
            /** @var JwtService $jwt */
            $jwt = app(JwtService::class);
            $payload = $jwt->decode($token);

            $userId = (int) ($payload['sub'] ?? 0);
            if ($userId <= 0) {
                return response()->json([
                    'code' => 'UNAUTHENTICATED',
                    'message' => trans('errors.TOKEN_SUB_MISSING'),
                ], 401);
            }

            /** @var User|null $user */
            $user = User::find($userId);
            if (! $user) {
                return response()->json([
                    'code' => 'UNAUTHENTICATED',
                    'message' => trans('errors.USER_NOT_FOUND'),
                ], 401);
            }

            // Banned user
            if (method_exists($user, 'isBanned') && $user->isBanned()) {
                return response()->json([
                    'code' => 'USER_STATUS_BANNED',
                    'message' => trans('errors.USER_STATUS_BANNED'),
                ], 403);
            }

            // Attach user to current request's auth context
            Auth::setUser($user);
        } catch (\Throwable $e) {
            return response()->json([
                'code' => 'UNAUTHENTICATED',
                'message' => trans('errors.TOKEN_INVALID'),
                'detail' => app()->hasDebugModeEnabled() ? $e->getMessage() : null,
            ], 401);
        }

        return $next($request);
    }
}