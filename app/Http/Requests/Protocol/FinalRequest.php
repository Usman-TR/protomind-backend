<?php

namespace App\Http\Requests\Protocol;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @OA\Schema(
 *     schema="ProtocolFinalRequest",
 *     type="object",
 *     required={"final_transcript", "location", "city"},
 *     @OA\Property(
 *         property="final_transcript",
 *         type="array",
 *         @OA\Items(
 *             type="object",
 *             @OA\Property(
 *                 property="key",
 *                 type="string"
 *             ),
 *             @OA\Property(
 *                 property="value",
 *                 type="string"
 *             )
 *         )
 *     ),
 *     @OA\Property(
 *         property="location",
 *         type="string"
 *     ),
 *     @OA\Property(
 *         property="city",
 *         type="string"
 *     ),
 *     @OA\Property(
 *         property="event_start_time",
 *         type="string",
 *         format="date-time",
 *         example="14:30"
 *     )
 * )
 */

class FinalRequest extends FormRequest
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
            'final_transcript' => ['required', 'array'],
            'final_transcript.*.key' => ['sometimes'],
            'final_transcript.*.value' => ['sometimes'],
            'location' => ['required', 'string'],
            'city' => ['required', 'string'],
            'event_start_time' => ['sometimes', 'date_format:H:i'],
        ];
    }
}
