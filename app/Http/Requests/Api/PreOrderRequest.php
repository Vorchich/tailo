<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class PreOrderRequest extends FormRequest
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
            'category_id' => ['nullable', 'sometimes', 'exists:categories,id'],
            'sizes' => ['required'],
            'sizes.*' => ['required', 'exists:sizes,id'],
            'comment' => ['nullable','sometimes', 'string', 'max:50000'],
        ];
    }
}
