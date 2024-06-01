<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VerifyTokenAdmin
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

        $user = Auth::guard('sanctum')->user();
        if (!in_array($user->roleId, ['R4', 'R1'])) {
            return response()->json([
                'status' => false,
                'errMessage' => 'Bạn không có đủ quyền',
                'refresh' => true,
            ], 403); // Use 403 for access denied
        }

        return $next($request);
    }
}
