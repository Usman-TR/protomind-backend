<?php

namespace App\Http\Requests\ChangePassword;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @OA\Schema(
 *     schema="ChangePasswordResetRequest",
 *     type="object",
 *     required={"token", "email", "password", "password_confirmation"},
 *     @OA\Property(property="token", type="string", example="some-reset-token"),
 *     @OA\Property(property="email", type="string", format="email", example="user@example.com"),
 *     @OA\Property(property="password", type="string", format="password", example="newpassword"),
 *     @OA\Property(property="password_confirmation", type="string", format="password", example="newpassword")
 * )
 */
class ResetRequest extends FormRequest
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
            'token' => ['required'],
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ];
    }
}
