<?php

namespace App\Http\Requests\API;

use Illuminate\Foundation\Http\FormRequest;

class ForgotPasswordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'registered_by' => 'required|in:email,phone',
            'email'         => 'required_if:registered_by,email|string|email|max:255',
            'phone'         => 'required_if:registered_by,phone|string|max:15',
        ];
    }
}
