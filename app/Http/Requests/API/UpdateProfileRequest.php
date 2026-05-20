<?php

namespace App\Http\Requests\API;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $user = auth()->user();

        $this->merge([
            'full_name' => ($this->first_name ?? $user->first_name) . ' ' . ($this->last_name ?? $user->last_name),
        ]);
    }

    public function rules(): array
    {
        $userId = auth()->id();

        return [
            'first_name' => 'required|string|max:255',
            'last_name'  => 'required|string|max:255',
            'full_name'  => 'required|string|max:255',
            'gender'     => 'required|in:male,female',
            'email'      => ['nullable', 'email', 'max:255', Rule::unique('users')->ignore($userId)],
            'phone'      => ['nullable', 'string', 'max:20', Rule::unique('users')->ignore($userId)],
            'image'      => 'nullable|image|mimes:jpeg,png,jpg,gif|max:8192',
        ];
    }
}
