<?php

namespace App\Http\Controllers\Api;

use App\Enums\ProtocolTaskStatusEnum;
use App\Http\Controllers\Controller;
use App\Http\Filters\ProtocolTaskFilter;
use App\Http\Requests\ProtocolTask\StoreRequest;
use App\Http\Requests\ProtocolTask\UpdateRequest;
use App\Http\Resources\ProtocolTaskResource;
use App\Jobs\UpdateProtocolTaskStatusJob;
use App\Models\Protocol;
use App\Models\ProtocolTask;
use App\Services\ProtocolTaskService;
use App\Services\ResponseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProtocolTaskController extends Controller
{

    public function __construct(
        private readonly ProtocolTaskService $service,
    )
    {
    }

    /**
     * @OA\Get(
     *     path="/api/protocols/tasks",
     *     operationId="getAllSecretaryTasks",
     *     summary="Get all tasks for the secretary",
     *     tags={"ProtocolTasks"},
     *     @OA\Parameter(
     *         name="limit",
     *         in="query",
     *         description="Number of tasks per page",
     *         required=false,
     *         @OA\Schema(type="integer", default=15)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(ref="#/components/schemas/ProtocolTaskResource")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Unauthenticated.")
     *         )
     *     )
     * )
     */
    public function index(Request $request, ProtocolTaskFilter $filter): JsonResponse
    {
        $limit = $request->query('limit', config('constants.paginator.limit'));

        $tasks = auth()->user()->protocolTasks()
            ->filter($filter)
            ->orderByRaw("CASE
                WHEN protocol_tasks.status = 'expired' THEN 1
                WHEN protocol_tasks.status = 'process' THEN 2
                WHEN protocol_tasks.status = 'success' THEN 3
                ELSE 4 END"
            )
            ->paginate($limit);

        return ResponseService::success(
            ProtocolTaskResource::collection($tasks)->response()->getData(true)
        );
    }

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

        $task = $this->service->create($validated);

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

    /**
     * @OA\Delete(
     *     path="/api/protocols/tasks/{taskId}",
     *     operationId="deleteProtocolTask",
     *     tags={"ProtocolTasks"},
     *     summary="Удалить задачу из протокола",
     *     description="Метод для удаления задачи из протокола",
     *     @OA\Parameter(
     *         name="taskId",
     *         in="path",
     *         required=true,
     *         description="ID задачи",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Успешная операция",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Задача успешно удалена."
     *             )
     *         )
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
    public function destroy(string $protocolId): JsonResponse
    {
        $task = ProtocolTask::find($protocolId);

        if(!$task) {
            return ResponseService::notFound(message: 'Задача не найдена.');
        }

        $task->delete();

        return ResponseService::success();
    }

}
