<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Filters\MeetingFilter;
use App\Http\Requests\Meeting\StoreRequest;
use App\Http\Requests\Meeting\UpdateRequest;
use App\Http\Resources\MeetingResource;
use App\Models\Meeting;
use App\Services\MeetingService;
use App\Services\ResponseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MeetingController extends Controller
{
    public function __construct(
        private readonly MeetingService $service
    )
    {
    }

    /**
     * @OA\Get(
     *     path="/api/meetings",
     *     operationId="indexMeetings",
     *     tags={"Meetings"},
     *     summary="Показать список совещаний",
     *     description="Метод для отображения списка совещаний",
     *     @OA\Parameter(
     *          name="start_date_at",
     *          in="query",
     *          required=false,
     *          @OA\Schema(
     *              type="string",
     *              format="date",
     *              example="2024-01-01"
     *          ),
     *          description="Начальная дата в формате год-месяц-день"
     *      ),
     *      @OA\Parameter(
     *          name="end_date_at",
     *          in="query",
     *          required=false,
     *          @OA\Schema(
     *              type="string",
     *              format="date",
     *              example="2024-12-31"
     *          ),
     *          description="Конечная дата в формате год-месяц-день"
     *      ),
     *      @OA\Parameter(
     *          name="date",
     *          in="query",
     *          required=false,
     *          @OA\Schema(
     *              type="string",
     *              format="date",
     *              example="2024-06-28"
     *          ),
     *          description="Фильтр по конкретной дате в формате год-месяц-день"
     *      ),
     *     @OA\Response(
     *         response=200,
     *         description="Успешная операция",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/MeetingResource")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     )
     * )
     */
    public function index(MeetingFilter $filter): JsonResponse
    {
        $meetings = auth()->user()->meetings()->filter($filter)->get();

        return ResponseService::success(
            MeetingResource::collection($meetings)
        );
    }

    /**
     * @OA\Post(
     *     path="/api/meetings",
     *     operationId="storeMeeting",
     *     tags={"Meetings"},
     *     summary="Создать новое совещание",
     *     description="Метод для создания нового совещания",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/MeetingStoreRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Успешная операция",
     *         @OA\JsonContent(ref="#/components/schemas/MeetingResource")
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Ошибочный запрос",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Ошибка при создании совещания"
     *             )
     *         )
     *     )
     * )
     */
    public function store(StoreRequest $request): JsonResponse
    {
        $validated = $request->validated();

        try {
            $meeting = $this->service->create($validated);

            return ResponseService::success(
                MeetingResource::make($meeting)
            );
        } catch (\Exception $e) {
            return ResponseService::badRequest($e->getMessage());
        }
    }

    /**
     * @OA\Get(
     *     path="/api/meetings/{id}",
     *     operationId="showMeeting",
     *     tags={"Meetings"},
     *     summary="Показать совещание",
     *     description="Метод для отображения данных совещания",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID совещания",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Успешная операция",
     *         @OA\JsonContent(ref="#/components/schemas/MeetingResource")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Не найдено",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Совещание не найдено."
     *             )
     *         )
     *     )
     * )
     */
    public function show(string $id): JsonResponse
    {
        $meeting = Meeting::find($id);

        if(!$meeting) {
            return ResponseService::notFound(message: 'Совещание не найдено.');
        }

        return ResponseService::success($meeting);
    }

    /**
     * @OA\Put(
     *     path="/api/meetings/{id}",
     *     operationId="updateMeeting",
     *     tags={"Meetings"},
     *     summary="Обновить совещание",
     *     description="Метод для обновления совещания",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID совещания",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/MeetingUpdateRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Успешная операция",
     *         @OA\JsonContent(ref="#/components/schemas/MeetingResource")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Не найдено",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Совещание не найдено."
     *             )
     *         )
     *     )
     * )
     */
    public function update(UpdateRequest $request, string $id): JsonResponse
    {
        $validated = $request->validated();

        $meeting = Meeting::find($id);

        if(!$meeting) {
            return ResponseService::notFound(message: 'Совещание не найдено.');
        }

        $this->service->update($meeting, $validated);

        return ResponseService::success(
            MeetingResource::make($meeting)
        );
    }

    /**
     * @OA\Delete(
     *     path="/api/meetings/{id}",
     *     operationId="destroyMeeting",
     *     tags={"Meetings"},
     *     summary="Удалить совещание",
     *     description="Метод для удаления совещания",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID совещания",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
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
     *                 example="Совещание не найдено."
     *             )
     *         )
     *     )
     * )
     */
    public function destroy(string $id): JsonResponse
    {
        $meeting = Meeting::find($id);

        if(!$meeting) {
            return ResponseService::notFound(message: 'Совещание не найдено.');
        }

        $meeting->delete();

        return ResponseService::success();
    }
}
