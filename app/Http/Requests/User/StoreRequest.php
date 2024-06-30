<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;


/**
 * @OA\Schema(
 *     schema="UserStoreRequest",
 *     type="object",
 *     title="Запрос на создание пользователя",
 *     description="Запрос, содержащий данные для создания пользователя",
 *     @OA\Property(
 *         property="full_name",
 *         type="string",
 *         description="Полное имя пользователя",
 *         maxLength=255,
 *         nullable=true
 *     ),
 *     @OA\Property(
 *         property="email",
 *         type="string",
 *         format="email",
 *         description="Электронная почта пользователя",
 *         maxLength=255,
 *         nullable=true
 *     ),
 *     @OA\Property(
 *         property="password",
 *         type="string",
 *         description="Пароль пользователя",
 *         minLength=6,
 *         nullable=true
 *     ),
 *     @OA\Property(
 *         property="department",
 *         type="string",
 *         description="Отдел пользователя",
 *         maxLength=255,
 *         nullable=true
 *     ),
 *     @OA\Property(
 *         property="external",
 *         type="boolean",
 *         description="Внешний пользователь",
 *         nullable=true
 *     ),
 *     @OA\Property(
 *           property="avatar",
 *           type="string",
 *           format="binary",
 *           nullable=true,
 *           description="Avatar image of the user"
 *       )
 * )
 */

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
            'full_name' => ['required_if:external,false', 'nullable', 'max:255'],
            'email' => ['required_if:external,false', 'nullable', 'email', 'max:255', 'unique:users'],
            'password' => ['required_if:external,false', 'nullable', 'min:6'],
            'department' => ['required_if:external,false', 'nullable', 'max:255'],
            'external' => ['sometimes', 'boolean'],
            'avatar' => ['sometimes', 'image'],
        ];
    }
}
