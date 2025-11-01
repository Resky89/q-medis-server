<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RefreshRequest;
use App\Http\Resources\UserResource;
use App\Services\AuthService;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends BaseController
{
    public function __construct(private readonly AuthService $authService)
    {
    }

    public function login(LoginRequest $request)
    {
        $result = $this->authService->attempt(
            $request->input('email'),
            $request->input('password'),
            $request->userAgent(),
            $request->ip(),
        );

        if ($result === false) {
            return $this->error('invalid credentials', 401);
        }

        $data = [
            'access_token' => $result['access_token'],
            'refresh_token' => $result['refresh_token'],
        ];

        return $this->success($data, 'login success');
    }

    
    public function me()
    {
        return $this->success(new UserResource(auth('api')->user()), 'profile retrieved');
    }

    
    public function refresh(RefreshRequest $request)
    {
        $result = $this->authService->refresh(
            $request->input('refresh_token'),
            $request->userAgent(),
            $request->ip(),
        );

        if ($result === false) {
            return $this->error('invalid refresh token', 401);
        }

        $data = [
            'access_token' => $result['access_token'],
            'refresh_token' => $result['refresh_token'],
        ];

        return $this->success($data, 'token refreshed');
    }

    
    public function logout(RefreshRequest $request)
    {
        $this->authService->logout($request->input('refresh_token'));
        return $this->success(null, 'logged out');
    }

    public function googleRedirect()
    {
        return Socialite::driver('google')->stateless()->redirect();
    }

    public function googleCallback(Request $request)
    {
        $googleUser = Socialite::driver('google')->stateless()->user();

        $result = $this->authService->loginViaGoogle(
            $googleUser->getId(),
            $googleUser->getEmail(),
            $googleUser->getName(),
            $googleUser->getAvatar(),
            $request->userAgent(),
            $request->ip(),
        );

        // Validate and get return URL
        $returnUrl = $this->validateReturnUrl($request->query('return_url'));

        if ($result === false) {
            // Redirect to frontend login with error (rare case: Google didn't provide email)
            $loginUrl = $returnUrl ?: $this->getDefaultUrl('/auth/login');
            return redirect($loginUrl . (str_contains($loginUrl, '?') ? '&' : '?') . 'error=google_login_failed');
        }

        // Redirect to frontend callback with tokens
        $callbackUrl = $returnUrl ?: $this->getDefaultUrl('/auth/google/callback');
        
        return redirect($callbackUrl . (str_contains($callbackUrl, '?') ? '&' : '?') . http_build_query([
            'access_token' => $result['access_token'],
            'refresh_token' => $result['refresh_token'],
        ]));
    }

    /**
     * Validate return URL against whitelist
     */
    private function validateReturnUrl(?string $url): ?string
    {
        if (!$url) {
            return null;
        }

        // Get allowed frontend URLs from config
        $allowedUrls = array_filter([
            config('app.frontend_url'),
            config('app.url'), // Allow same domain
        ]);

        // Parse the URL
        $parsedUrl = parse_url($url);
        if (!$parsedUrl || !isset($parsedUrl['scheme'], $parsedUrl['host'])) {
            return null;
        }

        $urlBase = $parsedUrl['scheme'] . '://' . $parsedUrl['host'];
        if (isset($parsedUrl['port'])) {
            $urlBase .= ':' . $parsedUrl['port'];
        }

        // Check if URL base matches any allowed URL
        foreach ($allowedUrls as $allowedUrl) {
            if (str_starts_with($url, rtrim($allowedUrl, '/'))) {
                return $url;
            }
        }

        return null;
    }

    /**
     * Get default frontend URL with path
     */
    private function getDefaultUrl(string $path = ''): string
    {
        $baseUrl = config('app.frontend_url', config('app.url', 'http://localhost:5173'));
        return rtrim($baseUrl, '/') . $path;
    }
}
