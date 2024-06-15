<?php

namespace App\Http\Requests\ProtocolTask;

use App\Enums\ProtocolTaskStatusEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

/**
 * @OA\Schema(
 *     schema="ProtocolTaskUpdateRequest",
 *     type="object",
 *     title="Запрос на обновление задачи протокола",
 *     description="Запрос, содержащий данные для обновления задачи протокола",
 *     @OA\Property(
 *         property="responsible_id",
 *         type="integer",
 *         description="ID ответственного пользователя",
 *         nullable=true
 *     ),
 *     @OA\Property(
 *         property="essence",
 *         type="string",
 *         description="Суть задачи",
 *         maxLength=65000,
 *         nullable=true
 *     ),
 *     @OA\Property(
 *         property="deadline",
 *         type="string",
 *         format="date",
 *         description="Срок выполнения задачи",
 *         example="2023-06-01",
 *         nullable=true
 *     ),
 *     @OA\Property(
 *         property="status",
 *         type="string",
 *         description="Статус задачи",
 *         nullable=true
 *     )
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
            'status' => ['sometimes', new Enum(ProtocolTaskStatusEnum::class)],
        ];
    }
}
