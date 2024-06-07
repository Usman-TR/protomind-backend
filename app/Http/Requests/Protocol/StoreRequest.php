<?php

namespace App\Http\Requests\Protocol;

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
            'theme' => ['required', 'string', 'max:255'],
            'agenda' => ['required', 'string', 'max:255'],
            'secretary_id' => ['required', 'integer'],
            'director_id' => ['required', 'integer'],
            'event_date' => ['required', 'date_format:Y-m-d'],
        ];
    }
}
