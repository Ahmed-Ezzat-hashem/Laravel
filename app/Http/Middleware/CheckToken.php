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
        // Check for a bearer token in the request
        $token = $request->bearerToken();

        // Verify the token using the 'api' guard
        if (!$request->bearerToken()) {
            return response()->json([
                'error' => 'Token not provided',
            ], 401);
        }

        // Verify the token
        if (!Auth::guard('api')->check()) {
            return response()->json(['error' => 'Invalid token. pls login again'], 401);
        }

        // Set the authenticated user
        Auth::setUser(Auth::guard('api')->user());

        // Proceed to the next middleware or controller
        return $next($request);
    }

}
