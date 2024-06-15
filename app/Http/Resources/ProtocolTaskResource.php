<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="ProtocolTaskResource",
 *     type="object",
 *     title="Задача протокола",
 *     description="Ресурс задачи протокола",
 *     @OA\Property(
 *         property="id",
 *         type="integer",
 *         description="ID задачи протокола"
 *     ),
 *     @OA\Property(
 *         property="protocol",
 *         ref="#/components/schemas/ProtocolResource"
 *     ),
 *     @OA\Property(
 *         property="responsible",
 *         ref="#/components/schemas/UserResource"
 *     ),
 *     @OA\Property(
 *         property="essence",
 *         type="string",
 *         description="Суть задачи"
 *     ),
 *     @OA\Property(
 *         property="deadline",
 *         type="string",
 *         format="date",
 *         description="Срок выполнения задачи"
 *     ),
 *     @OA\Property(
 *         property="status",
 *         type="string",
 *         description="Статус задачи"
 *     )
 * )
 */

class ProtocolTaskResource extends JsonResource
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
            'responsible' => UserResource::make($this->responsible),
            'essence' => $this->essence,
            'deadline' => $this->deadline,
            'status' => $this->status->label(),
        ];
    }
}
