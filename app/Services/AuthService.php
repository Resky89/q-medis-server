<?php

namespace App\Services;

use App\Models\JwtSession;
use App\Models\User;
use Illuminate\Support\Str;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTFactory;

class AuthService
{
    private function hashToken(string $plain): string
    {
        $key = config('app.key');
        if (Str::startsWith($key, 'base64:')) {
            $key = base64_decode(substr($key, 7));
        }
        return hash_hmac('sha256', $plain, $key);
    }

    private function issueTokens(User $user, ?string $userAgent = null, ?string $ip = null): array
    {
        $claims = [
            'uid' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role ?? null,
            'avatar' => $user->avatar ?? null,
        ];
        $accessToken = JWTAuth::claims($claims)->fromUser($user);
        $expiresAt = now()->addDays((int) env('JWT_REFRESH_TTL_DAYS', 30));

        // Create refresh token as JWT with custom claims (no 'sub' so it can't be used as access token)
        $rid = (string) Str::uuid();
        $refreshTtlMinutes = ((int) env('JWT_REFRESH_TTL_DAYS', 30)) * 24 * 60;
        $refreshPayload = JWTFactory::customClaims([
            'typ' => 'refresh',
            'jti' => $rid,
            'sub' => $user->id,
            'uid' => $user->id,
        ])->setTTL($refreshTtlMinutes)->make();
        $plainRefresh = JWTAuth::encode($refreshPayload)->get();
        // Store only a hash of the jti for revocation checks
        $refreshHash = $this->hashToken($rid);

        JwtSession::create([
            'user_id' => $user->id,
            'refresh_token' => $refreshHash,
            'expires_at' => $expiresAt,
            'user_agent' => $userAgent,
            'ip_address' => $ip,
        ]);

        return [
            'access_token' => $accessToken,
            'token_type' => 'bearer',
            'expires_in' => (int) config('jwt.ttl', 60) * 60,
            'refresh_token' => $plainRefresh,
            'refresh_expires_at' => $expiresAt,
            'user' => $user,
        ];
    }

    public function attempt(string $email, string $password, string $userAgent = null, string $ip = null): array|false
    {
        $credentials = ['email' => $email, 'password' => $password];
        if (! $token = auth('api')->attempt($credentials)) {
            return false;
        }

        $user = auth('api')->user();
        return $this->issueTokens($user, $userAgent, $ip);
    }

    public function refresh(string $refreshToken, string $userAgent = null, string $ip = null): array|false
    {
        // Decode and validate refresh JWT (signature & exp)
        try {
            $payload = JWTAuth::setToken($refreshToken)->getPayload();
        } catch (\Throwable $e) {
            return false;
        }

        if (($payload['typ'] ?? null) !== 'refresh') {
            return false;
        }

        $rid = $payload['jti'] ?? null;
        if (! $rid) {
            return false;
        }

        $hash = $this->hashToken($rid);
        $session = JwtSession::where('refresh_token', $hash)
            ->whereNull('revoked_at')
            ->where('expires_at', '>', now())
            ->first();

        if (! $session) {
            return false;
        }

        $user = User::find($session->user_id);
        if (! $user) {
            return false;
        }

        $claims = [
            'uid' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role ?? null,
            'avatar' => $user->avatar ?? null,
        ];
        $accessToken = JWTAuth::claims($claims)->fromUser($user);

        $session->revoked_at = now();
        $session->save();

        // Issue new refresh JWT
        $newRid = (string) Str::uuid();
        $expiresAt = now()->addDays((int) env('JWT_REFRESH_TTL_DAYS', 30));
        $refreshTtlMinutes = ((int) env('JWT_REFRESH_TTL_DAYS', 30)) * 24 * 60;
        $newPayload = JWTFactory::customClaims([
            'typ' => 'refresh',
            'jti' => $newRid,
            'uid' => $user->id,
        ])->setTTL($refreshTtlMinutes)->make();
        $newPlainRefresh = JWTAuth::encode($newPayload)->get();
        $newHash = $this->hashToken($newRid);

        JwtSession::create([
            'user_id' => $user->id,
            'refresh_token' => $newHash,
            'expires_at' => $expiresAt,
            'user_agent' => $userAgent,
            'ip_address' => $ip,
        ]);

        return [
            'access_token' => $accessToken,
            'token_type' => 'bearer',
            'expires_in' => (int) config('jwt.ttl', 60) * 60,
            'refresh_token' => $newPlainRefresh,
            'refresh_expires_at' => $expiresAt,
        ];
    }

    public function logout(?string $refreshToken = null): void
    {
        try {
            JWTAuth::parseToken()->invalidate(true);
        } catch (\Throwable $e) {
        }

        if ($refreshToken) {
            try {
                $payload = JWTAuth::setToken($refreshToken)->getPayload();
                $rid = $payload['jti'] ?? null;
                if ($rid) {
                    $hash = $this->hashToken($rid);
                    JwtSession::where('refresh_token', $hash)->update(['revoked_at' => now()]);
                }
            } catch (\Throwable $e) {
                // ignore invalid refresh token
            }
        }
    }

    public function loginViaGoogle(string $googleId, ?string $email, ?string $name, ?string $avatar, string $userAgent = null, string $ip = null): array|false
    {
        $user = null;

        // Try to find existing user by email or google_id
        if ($email) {
            $user = User::where('email', $email)->first();
        }
        if (! $user) {
            $user = User::where('google_id', $googleId)->first();
        }

        // Auto-create user if not found
        if (! $user) {
            if (! $email) {
                return false; // Email is required for new users
            }

            $user = User::create([
                'name' => $name ?? 'User',
                'email' => $email,
                'google_id' => $googleId,
                'avatar' => $avatar,
                'role' => 'petugas', 
                'password' => bcrypt(Str::random(32)),
            ]);
        } else {
            // Update existing user's Google info if needed
            $updates = [];
            if (! $user->google_id) $updates['google_id'] = $googleId;
            if ($name && $user->name !== $name) $updates['name'] = $name;
            if ($avatar && $user->avatar !== $avatar) $updates['avatar'] = $avatar;
            if ($updates) $user->update($updates);
        }

        return $this->issueTokens($user, $userAgent, $ip);
    }
}
