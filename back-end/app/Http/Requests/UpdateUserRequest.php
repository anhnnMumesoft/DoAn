<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
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
        return [
            'name' => 'required|min:6|max:30',
            'email' => 'required|string|email|max:255|unique:users,email,' . $this->input('userId'),
            'number_phone' => 'required',
            'role_id' => 'required|exists:_roles,id',
            'is_active' => 'required|boolean',
            'email_verified_at'=>'nullable|date'
        ];
    }
}
