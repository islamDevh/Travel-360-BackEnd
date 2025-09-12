<?php

namespace App\Http\Requests\API;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Password;

class RegisterUserRequest extends FormRequest
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
            'registered_by' => 'required|in:email,phone',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'nullable|string|email|unique:users,email|required_if:registered_by,email',
            'phone' => 'nullable|string|max:15|unique:users,phone|required_if:registered_by,phone',
            'password' => 'required|string|confirmed|min:8',
        ];
    }

    public function messages(): array
    {
        return [
            'registered_by.required' => 'The registration type is required.',
            'registered_by.in' => 'The registration type must be either email or phone.',
        ];
    }
}
