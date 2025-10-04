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

            // Validate subject
            $userId = (int) ($payload['sub'] ?? 0);
            if ($userId <= 0) {
                return response()->json([
                    'code' => 'UNAUTHENTICATED',
                    'message' => trans('errors.TOKEN_SUB_MISSING'),
                ], 401);
            }

            // Validate token type (must be access for protected API routes)
            $typ = $payload['typ'] ?? 'access';
            if ($typ !== 'access') {
                return response()->json([
                    'code' => 'UNAUTHENTICATED',
                    'message' => trans('errors.TOKEN_INVALID'),
                ], 401);
            }

            // Validate JTI (revocation)
            $jti = $payload['jti'] ?? null;
            if (empty($jti) || $jwt->isRevoked($jti)) {
                return response()->json([
                    'code' => 'UNAUTHENTICATED',
                    'message' => trans('errors.TOKEN_REVOKED'),
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

            // Attach user and payload to current request context
            Auth::setUser($user);
            $request->attributes->set('jwt_payload', $payload);
        } catch (\Firebase\JWT\ExpiredException $e) {
            return response()->json([
                'code' => 'UNAUTHENTICATED',
                'message' => trans('errors.TOKEN_EXPIRED'),
            ], 401);
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