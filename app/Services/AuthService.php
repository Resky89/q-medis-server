<?php

namespace App\Services;

use App\Models\JwtSession;
use App\Models\User;
use Illuminate\Support\Str;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

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
        $accessToken = JWTAuth::fromUser($user);
        $plainRefresh = bin2hex(random_bytes(64));
        $refreshHash = $this->hashToken($plainRefresh);
        $expiresAt = now()->addDays((int) env('JWT_REFRESH_TTL_DAYS', 30));

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
        $hash = $this->hashToken($refreshToken);
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

        $accessToken = JWTAuth::fromUser($user);

        $session->revoked_at = now();
        $session->save();

        $newPlainRefresh = bin2hex(random_bytes(64));
        $newHash = $this->hashToken($newPlainRefresh);
        $expiresAt = now()->addDays((int) env('JWT_REFRESH_TTL_DAYS', 30));

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
            $hash = $this->hashToken($refreshToken);
            JwtSession::where('refresh_token', $hash)->update(['revoked_at' => now()]);
        }
    }

    public function loginViaGoogle(string $googleId, ?string $email, ?string $name, ?string $avatar, string $userAgent = null, string $ip = null): array
    {
        $user = null;

        if ($email) {
            $user = User::where('email', $email)->first();
        }
        if (! $user) {
            $user = User::where('google_id', $googleId)->first();
        }

        if ($user) {
            $updates = [];
            if (! $user->google_id) $updates['google_id'] = $googleId;
            if ($name && $user->name !== $name) $updates['name'] = $name;
            if ($avatar && $user->avatar !== $avatar) $updates['avatar'] = $avatar;
            if ($updates) $user->update($updates);
        } else {
            $user = User::create([
                'name' => $name ?: 'Google User',
                'email' => $email ?: ("google_{$googleId}@example.local"),
                'password' => Str::password(),
                'google_id' => $googleId,
                'avatar' => $avatar,
                'role' => 'petugas',
            ]);
        }

        return $this->issueTokens($user, $userAgent, $ip);
    }
}
