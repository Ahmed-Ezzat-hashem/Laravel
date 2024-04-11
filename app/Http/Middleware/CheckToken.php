<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class CheckToken
{
    public function handle(Request $request, Closure $next)
    {
        // Check if the request contains a valid token
        if (!$request->bearerToken()) {
            return response()->json([
                'error' => 'Token not provided',
            ], 401);
        }

        // Verify the token
        if (!Auth::guard('api')->check()) {
            return response()->json(['error' => 'Invalid token'], 401);
        }

        return $next($request);
    }

    private function isPublicRoute($request)
    {
        // Define your public routes here
        $publicRoutes = [
            'login-username',
            'login-email',
            'login-phone',
            'password/forgot-password-sms',
            'password/reset-sms',
            'password/forgot-password',
            'password/otp',
            // Add more public routes as needed
        ];

        // Check if the current route is a public route
        return in_array($request->route()->getName(), $publicRoutes);
    }
}
