<?php

namespace App\Http\Requests\ProtocolKeyword;

use Illuminate\Foundation\Http\FormRequest;

class StoreUpdateRequest extends FormRequest
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
            'keywords' => ['required', 'array'],
            'keywords.*.id' => ['sometimes', 'exists:protocol_keywords,id'],
            'keywords.*.title' => ['required', 'string'],
            'keywords.*.phrase' => ['required', 'string'],
        ];
    }
}
