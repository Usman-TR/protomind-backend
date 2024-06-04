<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends FormRequest
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
            'full_name' => 'sometimes|max:255',
            'email' => 'sometimes|email|max:255|unique:users,email,' . $this->route('id'),
            'login' => 'sometimes|max:255|unique:users,login,' . $this->route('id'),
            'department' => 'sometimes|max:255',
            'is_active' => 'sometimes|boolean',
        ];
    }
}
