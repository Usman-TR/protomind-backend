<?php

namespace App\Http\Requests\ProtocolTask;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
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
            'responsible_id' => ['required', 'integer', 'exists:users,id'],
            'essence' => ['required', 'string', 'max:65000'],
            'deadline' => ['required', 'date_format:Y-m-d'],
        ];
    }
}
