<?php

namespace App\Observers;

use App\Models\Protocol;

class ProtocolObserver
{
    public function creating(Protocol $protocol): void
    {
        $lastProtocol = Protocol::where('creator_id', $protocol->creator_id)
            ->orderBy('user_protocol_number', 'desc')
            ->first();

        $protocol->user_protocol_number = $lastProtocol ? $lastProtocol->user_protocol_number + 1 : 1;
    }
}
