<?php

namespace App\Http\Requests\Protocol;

use Illuminate\Foundation\Http\FormRequest;


/**
 * @OA\Schema(
 *     schema="ProtocolUpdateRequest",
 *     type="object",
 *     title="Запрос на обновление протокола",
 *     description="Запрос, содержащий данные для обновления протокола",
 *     @OA\Property(
 *         property="theme",
 *         type="string",
 *         description="Тема протокола",
 *         maxLength=255,
 *         nullable=true
 *     ),
 *     @OA\Property(
 *         property="agenda",
 *         type="string",
 *         description="Повестка дня",
 *         maxLength=255,
 *         nullable=true
 *     ),
 *     @OA\Property(
 *         property="secretary_id",
 *         type="integer",
 *         description="ID секретаря",
 *         nullable=true
 *     ),
 *     @OA\Property(
 *         property="director_id",
 *         type="integer",
 *         description="ID директора",
 *         nullable=true
 *     ),
 *     @OA\Property(
 *         property="event_date",
 *         type="string",
 *         format="date",
 *         description="Дата события",
 *         example="2023-06-01",
 *         nullable=true
 *     ),
 *     @OA\Property(
 *         property="video",
 *         type="string",
 *         format="binary",
 *         description="Видео файл",
 *         example="video.mp4",
 *         nullable=true
 *     ),
 *     @OA\Property(
 *         property="final_transcript",
 *         type="string",
 *         description="Окончательный протокол",
 *         nullable=true
 *     ),
 *     @OA\Property(
 *         property="execute",
 *         type="boolean",
 *         description="Выполнить",
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
            'agenda' => ['sometimes', 'string', 'max:255'],
            'secretary_id' => ['sometimes', 'integer'],
            'director_id' => ['sometimes', 'integer'],
            'event_date' => ['sometimes', 'date_format:Y-m-d'],
            'video' => ['sometimes', 'file', 'mimes:mp4,avi,mov,wmv,mkv,flv,m4v,webm,ogg'],
            'execute' => ['sometimes', 'boolean'],
        ];
    }
}
