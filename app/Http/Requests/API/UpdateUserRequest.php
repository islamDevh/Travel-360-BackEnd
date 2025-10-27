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
            'first_name' => 'nullable|string|max:255',
            'last_name'  => 'nullable|string|max:255',
            'email'      => ['nullable', 'email', 'max:255', Rule::unique('users')->ignore($user_id)],
            'phone'      => ['nullable', 'string', 'max:20', Rule::unique('users')->ignore($user_id)],
            'gender'     => 'nullable|in:male,female',
            'image'      => 'nullable|image|mimes:jpeg,png,jpg,gif|max:8192',
        ];
    }
}
