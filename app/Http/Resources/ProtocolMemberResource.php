<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="ProtocolMemberResource",
 *     type="object",
 *     title="Участник протокола",
 *     description="Ресурс участника протокола",
 *     @OA\Property(
 *         property="id",
 *         type="integer",
 *         description="ID участника протокола"
 *     ),
 *     @OA\Property(
 *         property="protocol",
 *         ref="#/components/schemas/ProtocolResource"
 *     ),
 *     @OA\Property(
 *         property="member",
 *         ref="#/components/schemas/UserResource"
 *     )
 * )
 */
class ProtocolMemberResource extends JsonResource
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
            'protocol' => ProtocolResource::make($this->protocol),
            'member' => UserResource::make($this->member),
        ];
    }
}
