<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class PermissionRequest extends FormRequest
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
            'permissible_type' => ['required', 'string','in:Notepad,NotepadFolder,Text,Media'],
            'permissible_id' => ['required', 'integer'],
            'can_edit' => ['sometimes', 'boolean'],
        ];
    }
}
