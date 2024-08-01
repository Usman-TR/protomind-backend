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

        $deadline = Carbon::parse($task->deadline)->startOfDay()->addDay();
        $now = Carbon::now();

        if ($now > $deadline) {
            $task->update(['status' => ProtocolTaskStatusEnum::EXPIRED->value]);
        } else {
            $delay = $deadline->diffInSeconds($now);
            UpdateProtocolTaskStatusJob::dispatch($task)->delay($now->addSeconds($delay));
        }

        return $task;
    }
}
