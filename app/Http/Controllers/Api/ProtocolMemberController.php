<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProtocolMember\StoreRequest;
use App\Http\Resources\ProtocolMemberResource;
use App\Models\Protocol;
use App\Models\ProtocolMember;
use App\Services\ResponseService;
use Illuminate\Http\JsonResponse;

class ProtocolMemberController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/protocols/{protocolId}/members",
     *     operationId="indexProtocolMembers",
     *     tags={"ProtocolMembers"},
     *     summary="Показать участников протокола",
     *     description="Метод для отображения списка участников протокола",
     *     @OA\Parameter(
     *         name="protocolId",
     *         in="path",
     *         required=true,
     *         description="ID протокола",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Успешная операция",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/ProtocolMemberResource")
     *         )
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
    public function index(string $protocolId): JsonResponse
    {
        $protocol = Protocol::find($protocolId);

        if(!$protocol) {
            ResponseService::notFound(message: 'Протокол не найден.');
        }

        return ResponseService::success(
            ProtocolMemberResource::collection($protocol->members)
        );
    }

    /**
     * @OA\Post(
     *     path="/api/protocols/{protocolId}/members",
     *     operationId="storeProtocolMember",
     *     tags={"ProtocolMembers"},
     *     summary="Добавить участника в протокол",
     *     description="Метод для добавления нового участника в протокол",
     *     @OA\Parameter(
     *         name="protocolId",
     *         in="path",
     *         required=true,
     *         description="ID протокола",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/ProtocolMemberStoreRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Успешная операция",
     *         @OA\JsonContent(ref="#/components/schemas/ProtocolMemberResource")
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
        $validated = $request->validated();

        $protocol = Protocol::find($protocolId);

        if(!$protocol) {
            ResponseService::notFound(message: 'Протокол не найден.');
        }

        $validated['protocol_id'] = $protocolId;

        $member = ProtocolMember::create($validated);

        return ResponseService::success(ProtocolMemberResource::make($member));
    }

    /**
     * @OA\Delete(
     *     path="/api/protocols/members/{id}",
     *     operationId="destroyProtocolMember",
     *     tags={"ProtocolMembers"},
     *     summary="Удалить участника протокола",
     *     description="Метод для удаления участника протокола",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID участника протокола",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Успешная операция",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Участник удален."
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Не найдено",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Участник протокола не найден."
     *             )
     *         )
     *     )
     * )
     */
    public function destroy(string $id): JsonResponse
    {
        ProtocolMember::destroy($id);

        return ResponseService::success(message: 'Участник удален.');
    }
}
