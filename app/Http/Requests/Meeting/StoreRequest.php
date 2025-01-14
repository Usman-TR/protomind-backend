<?php

namespace App\Http\Requests\Meeting;

use Illuminate\Foundation\Http\FormRequest;


/**
 * @OA\Schema(
 *     schema="MeetingStoreRequest",
 *     type="object",
 *     title="Запрос на создание совещания",
 *     description="Запрос, содержащий данные для создания совещания",
 *     @OA\Property(
 *         property="document",
 *         type="string",
 *         format="binary",
 *         nullable=true,
 *         description="Документ совещания"
 *     ),
 *     @OA\Property(
 *         property="theme",
 *         type="string",
 *         description="Тема совещания",
 *         maxLength=255
 *     ),
 *     @OA\Property(
 *         property="link",
 *         type="string",
 *         description="Ссылка на совещание",
 *         maxLength=255
 *     ),
 *     @OA\Property(
 *         property="event_date",
 *         type="string",
 *         format="date",
 *         description="Дата совещания"
 *     ),
 *     @OA\Property(
 *         property="event_start_time",
 *         type="string",
 *         format="time",
 *         description="Время начала совещания"
 *     ),
 *     @OA\Property(
 *         property="event_end_time",
 *         type="string",
 *         format="time",
 *         description="Время окончания совещания в формате часы-минуты"
 *     ),
 *     @OA\Property(
 *         property="members",
 *         type="array",
 *         description="Список участников совещания в формате часы-минуты",
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
 *         )
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
            'document' => ['nullable', 'file'],
            'theme' => ['required', 'string', 'max:255'],
            'link' => ['required', 'string', 'max:255'],
            'event_date' => ['required', 'date', 'date_format:Y-m-d'],
            'event_start_time' => ['required', 'date_format:H:i'],
            'event_end_time' => ['required', 'date_format:H:i'],
            'members' => ['array'],
            'members.*.member_id' => ['required', 'integer', 'exists:users,id'],
            'members.*.should_notify' => ['required', 'boolean'],
        ];
    }
}
