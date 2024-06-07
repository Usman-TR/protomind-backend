<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProtocolResource extends JsonResource
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
            'agenda' => $this->agenda,
            'event_date' => $this->event_date,
            'status' => $this->status->label(),
            'director' => UserResource::make($this->director),
            'secretary' => UserResource::make($this->secretary),
        ];
    }
}
