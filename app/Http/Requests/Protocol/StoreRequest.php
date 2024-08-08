<?php

namespace App\Http\Requests\Protocol;

use Illuminate\Foundation\Http\FormRequest;


/**
 * @OA\Schema(
 *     schema="ProtocolStoreRequest",
 *     type="object",
 *     title="Запрос на создание протокола",
 *     description="Запрос, содержащий данные для создания протокола",
 *     @OA\Property(
 *         property="theme",
 *         type="string",
 *         description="Тема протокола",
 *         maxLength=255
 *     ),
 *     @OA\Property(
 *         property="agenda",
 *         type="string",
 *         description="Повестка дня",
 *         maxLength=255
 *     ),
 *     @OA\Property(
 *         property="secretary_id",
 *         type="integer",
 *         description="ID секретаря"
 *     ),
 *     @OA\Property(
 *         property="director_id",
 *         type="integer",
 *         description="ID директора"
 *     ),
 *     @OA\Property(
 *         property="event_date",
 *         type="string",
 *         format="date",
 *         description="Дата события",
 *         example="2023-06-01"
 *     ),
 *     @OA\Property(
 *         property="video",
 *         type="string",
 *         format="binary",
 *         description="Видео файл",
 *         example="video.mp4"
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
            'theme' => ['required', 'string', 'max:255'],
            'agenda' => ['required', 'string', 'max:255'],
            'secretary_id' => ['required', 'integer'],
            'director_id' => ['required', 'integer'],
            'event_date' => ['required', 'date_format:Y-m-d'],
        ];
    }
}
