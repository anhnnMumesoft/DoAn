<?php

namespace App\Http\Requests;

use App\Rules\MatchOldPassword;
use App\Rules\MatchPasswordConfirmation;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class UpdateProfileRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = [
            'name' => 'required|min:6|max:30',
            'email' => ['required', 'email'],
            'number_phone' => 'required',
            'country' => '',
        ];

        // Nếu người dùng đã đăng nhập và nhập giá trị vào trường new_password, yêu cầu old_password
        if (Auth::check() && request()->filled('new_password')) {
            $rules['old_password'] = ['required', new MatchOldPassword];
        }

        // Nếu người dùng đã đăng nhập và nhập giá trị vào trường new_password, yêu cầu new_password và new_password_confirmation
        if (Auth::check() && request()->filled('new_password')) {
            $rules['new_password'] = 'required|confirmed|min:8|max:30';
            $rules['new_password_confirmation'] = 'required';
        }

        return $rules;
    }
}
