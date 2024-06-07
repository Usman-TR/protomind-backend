<?php

namespace App\Http\Controllers;

use App\Enums\ProtocolTaskStatusEnum;
use App\Http\Requests\ProtocolTask\StoreRequest;
use App\Http\Requests\ProtocolTask\UpdateRequest;
use App\Http\Resources\ProtocolTaskResource;
use App\Models\Protocol;
use App\Models\ProtocolTask;
use App\Services\ResponseService;
use Illuminate\Http\JsonResponse;

class ProtocolTaskController extends Controller
{
    public function store(StoreRequest $request, string $protocolId): JsonResponse
    {
        $protocol = Protocol::find($protocolId);

        if(!$protocol) {
            return ResponseService::notFound(message: 'Протокол не найден.');
        }

        $validated = $request->validated();
        $validated['protocol_id'] = $protocolId;
        $validated['status'] = ProtocolTaskStatusEnum::PROCESS;

        $task = ProtocolTask::create($validated);

        return ResponseService::success(
            ProtocolTaskResource::make($task)
        );
    }

    public function update(UpdateRequest $request, string $protocolId): JsonResponse
    {
        $task = ProtocolTask::find($protocolId);

        if(!$task) {
            return ResponseService::notFound(message: 'Задача не найдена.');
        }

        $task->update($request->validated());

        return ResponseService::success(
            ProtocolTaskResource::make($task)
        );
    }
}
