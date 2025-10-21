<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTextRequest extends FormRequest
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
            'name' => ['nullable', 'sometimes', 'string', 'max:255',
                Rule::unique('notepads', 'name')->where(function ($query) {
                    return $query->where('user_id', auth()->id());
                })->ignore(request()->notepad)],
            'text' => ['nullable', 'sometimes', 'string', 'max:50000']
        ];
    }
}
