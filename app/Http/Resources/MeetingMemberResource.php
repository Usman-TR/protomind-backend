<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;


/**
 * @OA\Schema(
 *     schema="MeetingMemberResource",
 *     type="object",
 *     title="Ресурс участника совещания",
 *     description="Ресурс, представляющий участника совещания",
 *     @OA\Property(
 *         property="id",
 *         type="integer",
 *         description="ID участника совещания"
 *     ),
 *     @OA\Property(
 *         property="member",
 *         ref="#/components/schemas/UserResource",
 *         description="Данные пользователя участника"
 *     ),
 *     @OA\Property(
 *         property="email_sent",
 *         type="boolean",
 *         description="Флаг отправки email уведомления"
 *     )
 * )
 */
class MeetingMemberResource extends JsonResource
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
            'member' => UserResource::make($this->member),
            'email_sent' => $this->email_sent,
        ];
    }
}
