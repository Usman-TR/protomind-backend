<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="KeywordResource",
 *     type="object",
 *     title="Keyword Resource",
 *     properties={
 *         @OA\Property(
 *             property="id",
 *             type="integer",
 *             description="ID ключевого слова"
 *         ),
 *         @OA\Property(
 *             property="title",
 *             type="string",
 *             description="Заголовок"
 *         ),
 *         @OA\Property(
 *             property="phrase",
 *             type="string",
 *             description="Фраза или слово"
 *         )
 *     }
 * )
 */

class KeywordResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'phrase' => $this->phrase,
        ];
    }
}
