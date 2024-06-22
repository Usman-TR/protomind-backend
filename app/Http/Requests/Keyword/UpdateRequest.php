<?php

namespace App\Http\Requests\Keyword;

use Illuminate\Foundation\Http\FormRequest;


/**
 * @OA\Schema(
 *     schema="KeywordUpdateRequest",
 *     type="object",
 *     title="Запрос на обновление ключевых слов",
 *     description="Запрос, содержащий список ключевых слов для обновления",
 *     @OA\Property(
 *         property="keywords",
 *         type="array",
 *         description="Список ключевых слов",
 *         @OA\Items(
 *             type="object",
 *             @OA\Property(
 *                  property="id",
 *                  type="integer",
 *                  description="ID"
 *              ),
 *             @OA\Property(
 *                 property="title",
 *                 type="string",
 *                 description="Заголовок ключевого слова"
 *             ),
 *             @OA\Property(
 *                 property="phrase",
 *                 type="string",
 *                 description="Фраза ключевого слова"
 *             )
 *         )
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
            'keywords' => ['required', 'array'],
            'keywords.*.id' => ['requried', 'exists:protocol_keywords,id'],
            'keywords.*.title' => ['required', 'string'],
            'keywords.*.phrase' => ['required', 'string'],
        ];
    }
}