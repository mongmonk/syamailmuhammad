<?php

namespace App\Services;

use App\Models\User;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class JwtService
{
    private string $algo = 'HS256';

    /**
     * Issue a JWT token for the given user.
     * Includes standard claims and minimal user claims (sub, role, status).
     */
    public function issueToken(User $user, ?int $ttlSeconds = null): string
    {
        $now = time();
        $exp = $now + ($ttlSeconds ?? 3600 * 24); // default 24h

        $payload = [
            'iss' => config('app.url', 'http://localhost'),
            'aud' => config('app.url', 'http://localhost'),
            'iat' => $now,
            'nbf' => $now,
            'exp' => $exp,
            'sub' => $user->id,
            'role' => $user->role,
            'status' => $user->status,
        ];

        $key = $this->getKey();

        return JWT::encode($payload, $key, $this->algo);
    }

    /**
     * Decode a JWT token, returns payload as array.
     * Throws exception on invalid token.
     *
     * @return array<string, mixed>
     */
    public function decode(string $token): array
    {
        $key = $this->getKey();
        $decoded = JWT::decode($token, new Key($key, $this->algo));

        // Normalize to associative array
        return json_decode(json_encode($decoded), true);
    }

    /**
     * Get the signing key.
     * Priority: env('JWT_SECRET') -> decoded app.key if base64 -> app.key string.
     */
    private function getKey(): string
    {
        $jwtSecret = env('JWT_SECRET');
        if (is_string($jwtSecret) && $jwtSecret !== '') {
            return $jwtSecret;
        }

        $appKey = (string) config('app.key');
        if (str_starts_with($appKey, 'base64:')) {
            $base64 = substr($appKey, 7);
            $decoded = base64_decode($base64, true);
            if ($decoded !== false) {
                return $decoded;
            }
        }

        return $appKey;
    }
}