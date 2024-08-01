<?php

namespace App\Observers;

use App\Models\ProtocolTask;
use App\Models\ProtocolTaskStatusChange;

class ProtocolTaskObserver
{
    public function created(ProtocolTask $protocolTask): void
    {
        $protocolTask->statusChanges()->create([
            'user_id' => auth()->id(),
            'status' => $protocolTask->status,
        ]);
    }
    public function updated(ProtocolTask $protocolTask): void
    {
        if($protocolTask->isDirty('status')) {
            $protocolTask->statusChanges()
                ->where('user_id', auth()->id())
                ->where('created_at', '>=', now()->startOfWeek())
                ->where('created_at', '<=', now()->endOfWeek())
                ->delete();

            $protocolTask->statusChanges()->create([
                'user_id' => auth()->id() ?? $protocolTask->protocol->secretary_id,
                'status' => $protocolTask->status,
            ]);
        }
    }

}
