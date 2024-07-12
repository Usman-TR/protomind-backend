<?php

namespace App\Jobs;

use App\Enums\ProtocolTaskStatusEnum;
use App\Models\ProtocolTask;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class UpdateProtocolTaskStatusJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        private readonly ProtocolTask $task,
        private readonly int $userId
    )
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        if ($this->task->status !== ProtocolTaskStatusEnum::SUCCESS->value &&
            Carbon::parse($this->task->deadline) < Carbon::now()) {
            $this->task->updateQuietly(['status' => ProtocolTaskStatusEnum::EXPIRED->value]);

            $this->task->statusChanges()
                ->where('user_id', $this->userId)
                ->where('created_at', '>=', now()->startOfWeek())
                ->where('created_at', '<=', now()->endOfWeek())
                ->delete();

            $this->task->statusChanges()->create([
                'user_id' => $this->userId,
                'status' => ProtocolTaskStatusEnum::EXPIRED->value,
            ]);
        }
    }
}
