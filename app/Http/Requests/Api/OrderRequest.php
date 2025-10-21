<?php

namespace App\Http\Requests\Api;

use App\Models\Category;
use Illuminate\Foundation\Http\FormRequest;

class OrderRequest extends FormRequest
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
            'category_id' => ['required', 'exists:categories,id'],
            'measurements' => ['required'],
            'measurements.*' => ['required'],
            'comment' => ['nullable','sometimes', 'string', 'max:50000'],
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $category = Category::find($this->input('category_id'));

            if ($category) {
                $requiredSizes = $category->requiredSizes()->pluck('id')->toArray();

                foreach ($requiredSizes as $sizeId) {
                    if (!isset($this->input('measurements')[$sizeId])) {
                        $validator->errors()->add('measurements.' . $sizeId, 'This measurement is required for the selected category.');
                    }
                }
            }
        });
    }
}
