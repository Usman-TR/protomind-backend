<?php

namespace App\Http\Controllers\Api;

use App\Enums\ProtocolStatusEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\Protocol\StoreRequest;
use App\Http\Requests\Protocol\UpdateRequest;
use App\Http\Resources\ProtocolResource;
use App\Models\Protocol;
use App\Services\ProtocolService;
use App\Services\ResponseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProtocolController extends Controller
{

    public function __construct(
        private readonly ProtocolService $service,
    )
    {
    }

    /**
     * @OA\Get(
     *     path="/api/protocols",
     *     operationId="indexProtocols",
     *     tags={"Protocols"},
     *     summary="Показать список протоколов",
     *     description="Метод для отображения списка протоколов",
     *     @OA\Response(
     *         response=200,
     *         description="Успешная операция",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/ProtocolResource")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     )
     * )
     */

    public function index(Request $request): JsonResponse
    {
        $limit = $request->query('limit', config('constants.paginator.limit'));

        $protocols = auth()->user()->protocols()
            ->orderByRaw("CASE WHEN status = ? THEN 0 ELSE 1 END", [ProtocolStatusEnum::PROCESS->value])
            ->paginate($limit);

        return ResponseService::success(
            ProtocolResource::collection($protocols)->response()->getData(true),
        );
    }

    /**
     * @OA\Post(
     *     path="/api/protocols",
     *     operationId="storeProtocol",
     *     tags={"Protocols"},
     *     summary="Создать новый протокол",
     *     description="Метод для создания нового протокола",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/ProtocolStoreRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Успешная операция",
     *         @OA\JsonContent(ref="#/components/schemas/ProtocolResource")
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Ошибочный запрос",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Ошибка при создании протокола"
     *             )
     *         )
     *     )
     * )
     */

    public function store(StoreRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $protocol = $this->service->create($validated);

        return ResponseService::success(
            ProtocolResource::make($protocol)
        );
    }

    /**
     * @OA\Get(
     *     path="/api/protocols/{id}",
     *     operationId="showProtocol",
     *     tags={"Protocols"},
     *     summary="Показать протокол",
     *     description="Метод для отображения данных протокола",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID протокола",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Успешная операция",
     *         @OA\JsonContent(ref="#/components/schemas/ProtocolResource")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Не найдено",
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

    public function show(string $id): JsonResponse
    {
        $protocol = auth()->user()->protocols()->where('id', $id)->first();

        if(!$protocol) {
            return ResponseService::notFound(message: 'Протокол не найден.');
        }

        return ResponseService::success(
            ProtocolResource::make($protocol)
        );
    }


    /**
     * @OA\Put(
     *     path="/api/protocols/{id}",
     *     operationId="updateProtocol",
     *     tags={"Protocols"},
     *     summary="Обновить протокол",
     *     description="Метод для обновления данных протокола",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID протокола",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/ProtocolUpdateRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Успешная операция",
     *         @OA\JsonContent(ref="#/components/schemas/ProtocolResource")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Не найдено",
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
    public function update(UpdateRequest $request, string $id): JsonResponse
    {
        $protocol = Protocol::find($id);

        if(!$protocol) {
            return ResponseService::notFound(message: 'Протокол не найден.');
        }

        $validated = $request->validated();

        $this->service->update($protocol, $validated);

        return ResponseService::success(
            ProtocolResource::make($protocol)
        );
    }


    /**
     * @OA\Delete(
     *     path="/api/protocols/{id}",
     *     operationId="destroyProtocol",
     *     tags={"Protocols"},
     *     summary="Удалить протокол",
     *     description="Метод для удаления протокола",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID протокола",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *     description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                  property="message",
     *                  type="string",
     *                  example="success"
     *              )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Не найдено",
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
    public function destroy(string $id): JsonResponse
    {
        $protocol = Protocol::find($id);

        if(!$protocol) {
            return ResponseService::notFound(message: 'Протокол не найден.');
        }

        $protocol->delete();

        return ResponseService::success();
    }
}
