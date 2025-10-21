<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class ProfileRequest extends FormRequest
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
            'firstName' => ['sometimes', 'string', 'max:100'],
            'lastName' => ['sometimes', 'string', 'max:100'],
            'email' => ['sometimes', 'email', 'max:100'],
            'role_seamstress' => ['boolean', 'sometimes', 'nullable'],
            'role_customer' => ['boolean', 'sometimes', 'nullable'],
            'image' => ['sometimes', 'image', 'max:8000'],
            'profile_description' => ['nullable', 'sometimes', 'string', 'max:8000'],
        ];
    }
}
