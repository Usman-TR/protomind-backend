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
            'theme' => $this->theme,
            'agenda' => $this->agenda,
            'event_date' => $this->event_date,
            'stage' => $this->stage,
            'status' => $this->status->value,
            'director' => UserResource::make($this->director),
            'secretary' => UserResource::make($this->secretary),
            'transcript' => $this->transcript,
            'final_transcript' => $this->final_transcript,
            'video_path' => asset($this->getFirstMediaUrl('video')),
        ];
    }
}
