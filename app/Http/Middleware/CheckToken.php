<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class CheckToken
{
    public function handle($request, Closure $next)
    {
        // Check if the request contains a valid token
        if (!$request->bearerToken()) {
            return response()->json([
                'error' => 'Token not provided',
                'token' => $request->bearerToken(),
            ], 401);
        }

        // Verify the token
        if (!Auth::guard('api')->check()) {
            return response()->json(['error' => 'Invalid token',
            'token' => $request->bearerToken(),
        ], 401);
        }

        return $next($request);
    }
}
