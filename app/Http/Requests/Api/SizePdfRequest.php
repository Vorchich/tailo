<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class SizePdfRequest extends FormRequest
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
            'notepad_id' => ['sometimes', 'integer'],
            'category_id' => ['sometimes'],
            'sizes' => ['required', 'array'],
            'sizes.*' => ['required', 'string', 'max:190'],
        ];
    }
}
