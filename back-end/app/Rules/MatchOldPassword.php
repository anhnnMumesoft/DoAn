<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class MatchOldPassword implements Rule
{
    public function passes($attribute, $value)
    {
        $user = User::find(Auth::id()); // Lấy thông tin người dùng hiện tại
        return Hash::check($value, $user->password);
    }

    public function message()
    {
        return 'The old password does not match the current password.';
    }

    public function validate($attribute, $value, $parameters, $validator)
    {
        // Thực hiện kiểm tra
        $passes = $this->passes($attribute, $value);

        if (!$passes) {
            // Nếu không thành công, tạo thông báo lỗi và gửi nó bằng Closure
            $fail('The old password does not match the current password.');
        }
    }
}
