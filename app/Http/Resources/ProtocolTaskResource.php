<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

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
