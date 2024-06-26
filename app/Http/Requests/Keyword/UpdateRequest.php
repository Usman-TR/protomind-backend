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
 *          property="title",
 *          type="string",
 *          description="Заголовок ключевого слова"
 *      ),
 *      @OA\Property(
 *          property="phrase",
 *          type="string",
 *          description="Фраза ключевого слова"
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
            'title' => ['sometimes', 'string', 'max:255'],
            'phrase' => ['sometimes', 'string', 'max:255'],
        ];
    }
}
