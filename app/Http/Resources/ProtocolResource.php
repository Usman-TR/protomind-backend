<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;


/**
 * @OA\Schema(
 *     schema="ProtocolResource",
 *     type="object",
 *     allOf={
 *         @OA\Schema(ref="#/components/schemas/Protocol")
 *     }
 * )
 */

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
            'protocol_number' => $this->user_protocol_number,
            'theme' => $this->theme,
            'agenda' => $this->agenda,
            'event_date' => $this->event_date,
            'stage' => $this->stage,
            'status' => $this->status->value,
            'director' => UserResource::make($this->director),
            'secretary' => UserResource::make($this->secretary),
            'transcript' => $this->transcript,
            'final_transcript' => $this->final_transcript,
            'location' => $this->location,
            'event_start_time' => $this->event_start_time,
            'city' => $this->city,
            'video_path' => asset($this->getFirstMediaUrl('video')),
        ];
    }
}
