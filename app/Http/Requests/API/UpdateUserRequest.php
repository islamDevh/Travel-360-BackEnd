<?php

namespace App\Http\Requests\API;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $user_id = auth()->id();

        return [
            'first_name'       => 'sometimes|string|max:255',
            'last_name'        => 'sometimes|string|max:255',
            'name'             => 'sometimes|string|max:255',
            'email'            => ['sometimes', 'email', 'max:255', Rule::unique('users')->ignore($user_id)],
            'phone'            => ['sometimes', 'string', 'max:20', Rule::unique('users')->ignore($user_id)],
        ];
    }

    public function messages(): array
    {
        return [
            'email.unique' => 'This email is already taken',
            'phone.unique' => 'This phone number is already taken',
            'password.confirmed' => 'Password confirmation does not match',
            'current_password.required_with' => 'Current password is required when changing password',
        ];
    }
}
