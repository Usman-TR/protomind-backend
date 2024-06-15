<?php

namespace App\Http\Controllers\Api;

use App\Enums\ProtocolTaskStatusEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\ProtocolTask\StoreRequest;
use App\Http\Requests\ProtocolTask\UpdateRequest;
use App\Http\Resources\ProtocolTaskResource;
use App\Models\Protocol;
use App\Models\ProtocolTask;
use App\Services\ResponseService;
use Illuminate\Http\JsonResponse;

class ProtocolTaskController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/protocols/{protocolId}/tasks",
     *     operationId="storeProtocolTask",
     *     tags={"ProtocolTasks"},
     *     summary="Создать новую задачу в протоколе",
     *     description="Метод для создания новой задачи в протоколе",
     *     @OA\Parameter(
     *         name="protocolId",
     *         in="path",
     *         required=true,
     *         description="ID протокола",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/ProtocolTaskStoreRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Успешная операция",
     *         @OA\JsonContent(ref="#/components/schemas/ProtocolTaskResource")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Протокол не найден",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Протокол не найден."
     *             )
     *         )
     *     )
     * )
     */
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

    /**
     * @OA\Put(
     *     path="/api/protocols/{protocolId}/tasks/{taskId}",
     *     operationId="updateProtocolTask",
     *     tags={"ProtocolTasks"},
     *     summary="Обновить задачу в протоколе",
     *     description="Метод для обновления задачи в протоколе",
     *     @OA\Parameter(
     *         name="protocolId",
     *         in="path",
     *         required=true,
     *         description="ID протокола",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="taskId",
     *         in="path",
     *         required=true,
     *         description="ID задачи",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/ProtocolTaskUpdateRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Успешная операция",
     *         @OA\JsonContent(ref="#/components/schemas/ProtocolTaskResource")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Задача не найдена",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Задача не найдена."
     *             )
     *         )
     *     )
     * )
     */
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
