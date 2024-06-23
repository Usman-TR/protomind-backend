<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="MeetingResource",
 *     type="object",
 *     title="Ресурс совещания",
 *     description="Ресурс, представляющий совещание",
 *     @OA\Property(
 *         property="id",
 *         type="integer",
 *         description="ID совещания"
 *     ),
 *     @OA\Property(
 *         property="theme",
 *         type="string",
 *         description="Тема совещания"
 *     ),
 *     @OA\Property(
 *         property="link",
 *         type="string",
 *         description="Ссылка на совещание"
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
 *         format="date-time",
 *         description="Время начала совещания"
 *     ),
 *     @OA\Property(
 *         property="event_end_time",
 *         type="string",
 *         format="date-time",
 *         description="Время окончания совещания"
 *     ),
 *     @OA\Property(
 *         property="members",
 *         type="array",
 *         description="Список участников совещания",
 *         @OA\Items(ref="#/components/schemas/MeetingMemberResource")
 *     ),
 *     @OA\Property(
 *         property="document_path",
 *         type="string",
 *         description="Путь к документу совещания",
 *         nullable=true
 *     )
 * )
 */
class MeetingResource extends JsonResource
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
            'theme' => $this->theme,
            'link' => $this->link,
            'event_date' => $this->event_date,
            'members' => MeetingMemberResource::collection($this->members),
            'document_path' => $this->getFirstMedia('document')?->getUrl() ?: null,
        ];
    }
}
