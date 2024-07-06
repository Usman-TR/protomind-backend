<?php

namespace App\Services;

use App\Enums\ProtocolTaskStatusEnum;
use App\Jobs\UpdateProtocolTaskStatusJob;
use App\Models\ProtocolTask;
use Carbon\Carbon;

class ProtocolTaskService
{
    public function create(array $data): ProtocolTask
    {
        $data['status'] = ProtocolTaskStatusEnum::PROCESS->value;

        $task = ProtocolTask::create($data);

        if (Carbon::now() > Carbon::parse($task->deadline)) {
            $task->update(['status' => ProtocolTaskStatusEnum::EXPIRED->value]);
        } else {
            $delay = Carbon::parse($task->deadline)->diffInSeconds(Carbon::now());
            UpdateProtocolTaskStatusJob::dispatch($task)->delay(Carbon::now()->addSeconds($delay));
        }

        return $task;
    }
}
