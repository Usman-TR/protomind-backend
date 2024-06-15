<?php

namespace App\Http\Requests\ProtocolMember;

use Illuminate\Foundation\Http\FormRequest;


/**
 * @OA\Schema(
 *     schema="ProtocolMemberStoreRequest",
 *     type="object",
 *     title="Запрос на добавление участника протокола",
 *     description="Запрос, содержащий данные для добавления участника протокола",
 *     @OA\Property(
 *         property="member_id",
 *         type="integer",
 *         description="ID участника (пользователя)"
 *     )
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
            'member_id' => ['required', 'integer', 'exists:users,id', 'unique:protocol_members,member_id'],
        ];
    }
}
