<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class IsSuperAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Kiểm tra nếu người dùng đã đăng nhập và có vai trò là super admin
        if (Auth::check() && Auth::user()->role->name == 'Super Admin') {
            return $next($request);
        }

        // Nếu không phải super admin, chuyển hướng người dùng về trang chủ hoặc trang không có quyền
        return redirect('/home')->with('error', 'You do not have permission to access this page.');
    }
}
