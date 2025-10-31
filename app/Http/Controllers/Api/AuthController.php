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

        if ($result === false) {
            return $this->error('account not registered', 404);
        }

        $result['user'] = new UserResource($result['user']);
        $result['refresh_expires_at'] = $result['refresh_expires_at']->toISOString();

        return $this->success($result, 'google login success');
    }
}

