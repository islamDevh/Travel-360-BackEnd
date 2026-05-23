<?php

namespace App\Http\Requests\API;

use Illuminate\Foundation\Http\FormRequest;

class StoreGuideAppRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'user_id' => auth()->id(),
        ]);
    }

    public function rules(): array
    {
        return [
            'user_id'                => ['required', 'integer', 'exists:users,id'],
            'phone'                  => ['required', 'string', 'max:20'],
            'image'                  => ['required', 'image', 'max:8192'],
            'experience'             => ['required', 'string'],
            'years_experience'       => ['required', 'integer', 'min:0'],
            'cv'                     => ['required', 'file', 'mimes:pdf,doc,docx', 'max:20480'],
            'lang'                   => ['required', 'array', 'min:1'],
            'lang.*'                 => ['required', 'integer', 'exists:languages,id'],
            'has_car'                => ['nullable', 'boolean'],
            'car_type'               => ['required_if:has_car,true', 'nullable', 'string', 'max:100'],
            'driving_license'        => ['required_if:has_car,true', 'nullable', 'image', 'max:8192'],
            'driving_license_expiry' => ['required_if:has_car,true', 'nullable', 'date', 'after:today'],
            'car_number'             => ['required_if:has_car,true', 'nullable', 'string', 'max:50'],
            'country'                => ['required', 'string', 'max:100'],
            'area'                   => ['required', 'string', 'max:100'],
        ];
    }
}
