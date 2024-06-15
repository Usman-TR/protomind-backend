<?php

namespace App\Http\Requests\ProtocolTask;

use Illuminate\Foundation\Http\FormRequest;


/**
 * @OA\Schema(
 *     schema="ProtocolTaskStoreRequest",
 *     type="object",
 *     title="Запрос на создание задачи протокола",
 *     description="Запрос, содержащий данные для создания задачи протокола",
 *     @OA\Property(
 *         property="responsible_id",
 *         type="integer",
 *         description="ID ответственного пользователя"
 *     ),
 *     @OA\Property(
 *         property="essence",
 *         type="string",
 *         description="Суть задачи",
 *         maxLength=65000
 *     ),
 *     @OA\Property(
 *         property="deadline",
 *         type="string",
 *         format="date",
 *         description="Срок выполнения задачи",
 *         example="2023-06-01"
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
            'responsible_id' => ['required', 'integer', 'exists:users,id'],
            'essence' => ['required', 'string', 'max:65000'],
            'deadline' => ['required', 'date_format:Y-m-d'],
        ];
    }
}
