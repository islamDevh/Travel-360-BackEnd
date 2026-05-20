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
            'email'         => 'nullable|string|email|required_if:registered_by,email',
            'phone'         => 'nullable|string|max:15|required_if:registered_by,phone',
        ];
    }
}
