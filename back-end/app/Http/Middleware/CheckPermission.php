<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CheckPermission
{
    public function handle($request, Closure $next, $permissions)
    {
        // Phân tách chuỗi tham số thành mảng và đảm bảo rằng nó chứa ít nhất hai phần tử
        $parts = explode('_', $permissions, 2);

        // Kiểm tra xem mảng có đủ phần tử không
        if (count($parts) < 2) {
            // Xử lý trường hợp không đủ phần tử, ví dụ: trả về lỗi hoặc redirect
            return redirect('/')->with('error', 'Invalid permissions format.');
        }

        // Gán giá trị cho $action và $type từ mảng $parts
        [$action, $type] = $parts;

        // Loại bỏ khoảng trắng xung quanh các tham số (nếu có)
        $action = trim($action);
        $type = trim($type);

        // Tiếp tục kiểm tra quyền
        if (!Auth::check() || !$request->user()->hasPermissionTo($action, $type)) {
            return redirect('/')->with('error', 'You do not have permission to access this page!');
        }

        return $next($request);
    }
}
