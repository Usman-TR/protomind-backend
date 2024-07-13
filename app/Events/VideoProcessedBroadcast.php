<?php

namespace App\Events;

use App\Http\Resources\ProtocolResource;
use App\Models\Protocol;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class VideoProcessedBroadcast implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    private ProtocolResource $protocol;
    /**
     * Create a new event instance.
     */
    public function __construct(Protocol $protocol)
    {
        $this->protocol = ProtocolResource::make($protocol);
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('secretary_protocol.' . $this->protocol->id),
        ];
    }

    public function broadcastWith(): array
    {
        return [
            'protocol' => $this->protocol,
        ];
    }

    public function broadcastAs(): string
    {
        return 'VideoProcessed';
    }
}
