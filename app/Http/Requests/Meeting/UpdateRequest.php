<?php

namespace App\Http\Requests\Meeting;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @OA\Schema(
 *     schema="MeetingUpdateRequest",
 *     type="object",
 *     title="Запрос на обновление совещания",
 *     description="Запрос, содержащий данные для обновления совещания",
 *     @OA\Property(
 *         property="theme",
 *         type="string",
 *         description="Тема совещания",
 *         maxLength=255,
 *         nullable=true
 *     ),
 *     @OA\Property(
 *         property="document",
 *         type="string",
 *         format="binary",
 *         description="Документ совещания",
 *         nullable=true
 *     ),
 *     @OA\Property(
 *         property="link",
 *         type="string",
 *         description="Ссылка на совещание",
 *         maxLength=255,
 *         nullable=true
 *     ),
 *     @OA\Property(
 *         property="event_date",
 *         type="string",
 *         format="date",
 *         description="Дата совещания",
 *         nullable=true
 *     ),
 *     @OA\Property(
 *         property="event_start_time",
 *         type="string",
 *         format="date-time",
 *         description="Время начала совещания в формате часы-минуты",
 *         nullable=true
 *     ),
 *     @OA\Property(
 *         property="event_end_time",
 *         type="string",
 *         format="time",
 *         description="Время окончания совещания в формате часы-минуты",
 *         nullable=true
 *     ),
 *     @OA\Property(
 *         property="members",
 *         type="array",
 *         description="Список участников совещания",
 *         @OA\Items(
 *             type="object",
 *             @OA\Property(
 *                 property="member_id",
 *                 type="integer",
 *                 description="ID участника"
 *             ),
 *             @OA\Property(
 *                 property="should_notify",
 *                 type="boolean",
 *                 description="Уведомлять ли участника"
 *             )
 *         ),
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
            'theme' => ['sometimes', 'string', 'max:255'],
            'document' => ['sometimes', 'file'],
            'link' => ['sometimes', 'string', 'max:255'],
            'event_date' => ['sometimes', 'date', 'date_format:Y-m-d'],
            'event_start_time' => ['sometimes', 'date_format:H:i'],
            'event_end_time' => ['sometimes', 'date_format:H:i'],
            'members' => ['sometimes', 'array'],
            'members.*.member_id' => ['integer', 'exists:users,id'],
            'members.*.should_notify' => ['boolean'],
        ];
    }
}
