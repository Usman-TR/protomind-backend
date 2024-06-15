<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Keyword\StoreRequest;
use App\Http\Requests\Keyword\UpdateRequest;
use App\Http\Resources\KeywordResource;
use App\Models\Keyword;
use App\Services\KeywordService;
use App\Services\ResponseService;
use Illuminate\Http\JsonResponse;

class KeywordController extends Controller
{
    public function __construct(
        private readonly KeywordService $service
    )
    {
    }

    /**
     * Display a listing of the resource.
     *
     * @OA\Get(
     *      path="/api/keywords",
     *      operationId="indexKeywords",
     *      tags={"Keywords"},
     *      summary="Выводит все ключевые слова, отсортированные по времени",
     *      description="Метод для отображения списка ключевых слов",
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(ref="#/components/schemas/KeywordResource")
     *          )
     *      )
     *  )
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $keywords = Keyword::latest()->get();

        return ResponseService::success(KeywordResource::make($keywords));
    }

    /**
     * @OA\Post(
     *     path="/api/keywords",
     *     operationId="storeKeyword",
     *     tags={"Keywords"},
     *     summary="Создание нового ключевого слова",
     *     description="Метод для создания нового ключевого слова",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/KeywordStoreRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Успешная операция",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/KeywordResource")
     *         )
     *     )
     * )
     *
     * @param StoreRequest $request
     * @return JsonResponse
     */
    public function store(StoreRequest $request): JsonResponse
    {
        $keywords = $this->service->create($request->validated());

        return ResponseService::success(KeywordResource::collection($keywords));
    }

    /**
     * @OA\Put(
     *     path="/api/keywords/{id}",
     *     operationId="updateKeyword",
     *     tags={"Keywords"},
     *     summary="Обновление существующего ключевого слова",
     *     description="Метод для обновления существующего ключевого слова",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/KeywordUpdateRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/KeywordResource")
     *     )
     * )
     *
     * @param UpdateRequest $request
     * @return JsonResponse
     */
    public function update(UpdateRequest $request): JsonResponse
    {
        $keywords = $this->service->update($request->validated());

        return ResponseService::success(KeywordResource::make($keywords));
    }

    /**
     * @OA\Delete(
     *     path="/api/keywords/{id}",
     *     operationId="destroyKeyword",
     *     tags={"Keywords"},
     *     summary="Удаление существующего ключевого слова",
     *     description="Метод для удаления ключевого слова",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
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
     *         description="Not Found",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Ничего не найдено."
     *             )
     *         )
     *     )
     * )
     *
     * @param string $id
     * @return JsonResponse
     */
    public function destroy(string $id): JsonResponse
    {
        $keyword = Keyword::find($id);

        if(!$keyword) {
            return ResponseService::notFound(message: 'Ничего не найдено.');
        }

        $keyword->delete();

        return ResponseService::success();
    }
}
