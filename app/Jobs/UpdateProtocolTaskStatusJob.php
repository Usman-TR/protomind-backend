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
        private readonly ProtocolTask $task
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
            Carbon::parse($this->task->deadline)->startOfDay()->addDay() < Carbon::now()) {
            $this->task->update(['status' => ProtocolTaskStatusEnum::EXPIRED->value]);
        }
    }
}
