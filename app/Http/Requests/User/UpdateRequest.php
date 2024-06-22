<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;


/**
 * @OA\Schema(
 *     schema="UserUpdateRequest",
 *     type="object",
 *     title="Запрос на обновление пользователя",
 *     description="Запрос, содержащий данные для обновления пользователя",
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
 *         property="login",
 *         type="string",
 *         description="Логин пользователя",
 *         maxLength=255,
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
 *         property="is_active",
 *         type="boolean",
 *         description="Статус активности пользователя",
 *         nullable=true
 *     ),
 *     @OA\Property(
 *          property="avatar",
 *          type="string",
 *          format="binary",
 *          nullable=true,
 *          description="Avatar image of the user"
 *      )
 * )
 */

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
            'full_name' => ['sometimes', 'max:255'],
            'email' => ['sometimes', 'email', 'max:255', 'unique:users,email,' . $this->route('id')],
            'login' => ['sometimes', 'max:255', 'unique:users,login,' . $this->route('id')],
            'department' => ['sometimes', 'max:255'],
            'is_active' => ['sometimes', 'boolean'],
            'avatar' => ['sometimes', 'image'],
        ];
    }
}
