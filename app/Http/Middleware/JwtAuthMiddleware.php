<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Services\SecurityService;

class JwtAuthMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $token = null;
        $authHeader = $request->header('Authorization');

        if ($authHeader && str_starts_with($authHeader, 'Bearer ')) {
            $token = substr($authHeader, 7);
        } elseif ($request->cookie('token')) {
            $token = $request->cookie('token');
        } elseif (session()->has('jwt_token')) {
            $token = session('jwt_token');
        }

        if (!$token) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'error' => ['code' => 'UNAUTHORIZED', 'message' => 'Unauthorized']
                ], 401);
            }
            return redirect('/auth/login');
        }

        $user = SecurityService::verifyJwtToken($token);
        if (!$user) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'error' => ['code' => 'UNAUTHORIZED', 'message' => 'Invalid or expired token']
                ], 401);
            }
            return redirect('/auth/login');
        }

        $request->attributes->set('auth_user', (object) $user);

        return $next($request);
    }
}
