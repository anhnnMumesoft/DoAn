<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VerifyTokenUser
{
    public function handle(Request $request, Closure $next)
    {
        // Authenticate the user based on Sanctum token
        if (!Auth::guard('sanctum')->check()) {
            return response()->json([
                'status' => false,
                'message' => "You're not authenticated!",
                'refresh' => true,
            ], 401);
        }

        // Optionally, you can perform additional checks or set user context here

        return $next($request);
    }
}
