<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Helpers\ResponseFormatter;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        $user = auth('api')->user();
        if (! $user) {
            return ResponseFormatter::error('unauthenticated', 401);
        }

        if ($roles && ! in_array((string) $user->role, $roles, true)) {
            return ResponseFormatter::error('forbidden', 403);
        }

        return $next($request);
    }
}
