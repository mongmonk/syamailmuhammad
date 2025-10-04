<?php

namespace App\Services;

use App\Models\User;
use App\Models\JwtToken;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;

class JwtService
{
    private string $algo = 'HS256';

    /**
     * Issue a JWT token for the given user and persist token metadata (jti) to DB.
     * Type: 'access' or 'refresh'
     */
    public function issueToken(User $user, ?int $ttlSeconds = null, string $type = 'access', ?string $ip = null, ?string $userAgent = null): string
    {
        $now = time();
        $exp = $now + ($ttlSeconds ?? 3600 * 24); // default 24h
        $jti = (string) Str::uuid();

        $payload = [
            'iss' => config('app.url', 'http://localhost'),
            'aud' => config('app.url', 'http://localhost'),
            'iat' => $now,
            'nbf' => $now,
            'exp' => $exp,
            'sub' => $user->id,
            'role' => $user->role,
            'status' => $user->status,
            'jti' => $jti,
            'typ' => $type,
        ];

        $key = $this->getKey();

        $token = JWT::encode($payload, $key, $this->algo);

        // Persist token record for revocation/expiry checks
        JwtToken::create([
            'jti' => $jti,
            'user_id' => $user->id,
            'type' => $type === 'refresh' ? 'refresh' : 'access',
            'expires_at' => Carbon::createFromTimestamp($exp),
            'revoked_at' => null,
            'ip' => $ip,
            'user_agent' => $userAgent,
        ]);

        return $token;
    }

    /**
     * Issue access and refresh tokens together.
     * Returns array with access_token, refresh_token and their TTLs.
     */
    public function issueAccessAndRefreshTokens(User $user, ?int $accessTtlSeconds = null, ?int $refreshTtlSeconds = null, ?string $ip = null, ?string $userAgent = null): array
    {
        $accessTtl = $accessTtlSeconds ?? (3600 * 24);        // 24h default
        $refreshTtl = $refreshTtlSeconds ?? (3600 * 24 * 14); // 14 days default

        $access = $this->issueToken($user, $accessTtl, 'access', $ip, $userAgent);
        $refresh = $this->issueToken($user, $refreshTtl, 'refresh', $ip, $userAgent);

        return [
            'access_token' => $access,
            'access_expires_in' => $accessTtl,
            'refresh_token' => $refresh,
            'refresh_expires_in' => $refreshTtl,
            'token_type' => 'Bearer',
        ];
    }

    /**
     * Decode a JWT token, returns payload as array.
     * Throws exception on invalid/expired token.
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
     * Revoke a token by JTI.
     */
    public function revokeByJti(string $jti): bool
    {
        $record = JwtToken::where('jti', $jti)->first();
        if (! $record) {
            return false;
        }
        if ($record->revoked_at) {
            return true;
        }
        $record->revoked_at = Carbon::now();
        $record->save();
        return true;
    }

    /**
     * Check if token JTI is revoked (or missing record).
     */
    public function isRevoked(string $jti): bool
    {
        $record = JwtToken::where('jti', $jti)->first();
        return ! $record || $record->revoked_at !== null;
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