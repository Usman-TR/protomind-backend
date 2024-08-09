<?php

namespace App\Http\Controllers\Api;

use App\Enums\RolesEnum;
use App\Http\Controllers\Controller;
use App\Http\Filters\UserFilter;
use App\Http\Requests\User\StoreRequest;
use App\Http\Requests\User\UpdateRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\ResponseService;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{

    public function __construct(private readonly UserService $userService)
    {
    }

    /**
     * @OA\Get(
     *     path="/api/users",
     *     operationId="indexUsers",
     *     tags={"Users"},
     *     summary="Получить список пользователей",
     *     description="Метод для отображения списка пользователей с фильтрацией",
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="Поиск по полному имени",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="login",
     *         in="query",
     *         description="Поиск по логину",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="email",
     *         in="query",
     *         description="Поиск по электронной почте",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *          name="role",
     *          in="query",
     *          description="Получает юзеров только заданной роли",
     *          @OA\Schema(
     *              type="string",
     *              enum={"manager", "external", "secretary"}
     *          )
     *      ),
     *     @OA\Response(
     *         response=200,
     *         description="Успешная операция",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/UserResource")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     )
     * )
     */
    public function index(Request $request, UserFilter $filter): JsonResponse
    {
        $queryParams = $request->all();

        $users = $this->userService->getAll($queryParams, $filter);

        return ResponseService::success(
            UserResource::collection($users)->response()->getData(true)
        );
    }

    /**
     * @OA\Post(
     *     path="/api/users",
     *     operationId="storeUser",
     *     tags={"Users"},
     *     summary="Создать нового пользователя",
     *     description="Метод для создания нового пользователя",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/UserStoreRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Успешная операция",
     *         @OA\JsonContent(ref="#/components/schemas/UserResource")
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Ошибочный запрос",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Ошибка при создании пользователя"
     *             )
     *         )
     *     )
     * )
     */
    public function store(StoreRequest $request): JsonResponse
    {
        $validated = $request->validated();

        try {
            $user = $this->userService->create($validated);

            return ResponseService::success(
                UserResource::make($user)
            );
        } catch (\Exception $e) {
            return ResponseService::badRequest(message: $e->getMessage());
        }
    }

    /**
     * @OA\Put(
     *     path="/api/users/{id}",
     *     operationId="updateUser",
     *     tags={"Users"},
     *     summary="Обновить пользователя",
     *     description="Метод для обновления данных пользователя",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID пользователя",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/UserUpdateRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Успешная операция",
     *         @OA\JsonContent(ref="#/components/schemas/UserResource")
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Ошибочный запрос",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Пользователь не найден."
     *             )
     *         )
     *     )
     * )
     */
    public function update(UpdateRequest $request, string $id): JsonResponse
    {
        $user = User::withTrashed()->find($id);

        if(!$user) {
            return ResponseService::badRequest(message: "Пользователь не найден.");
        }

        $validated = $request->validated();

        if(isset($validated['avatar']) && $validated['avatar']) {
            $user->clearMediaCollection('avatar');
            $user->addMediaFromRequest('avatar')->toMediaCollection('avatar');
        }

        if (empty($validated['password'])) {
            unset($validated['password']);
        } else {
            $validated['password'] = Hash::make($validated['password']);
        }

        $user->update($validated);

        return ResponseService::success(
            UserResource::make($user->fresh())
        );
    }

    /**
     * @OA\Get(
     *     path="/api/users/{id}",
     *     operationId="showUser",
     *     tags={"Users"},
     *     summary="Получить пользователя",
     *     description="Метод для отображения данных пользователя",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID пользователя",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Успешная операция",
     *         @OA\JsonContent(ref="#/components/schemas/UserResource")
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Ошибочный запрос",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Пользователь не найден."
     *             )
     *         )
     *     )
     * )
     */
    public function show(string $id): JsonResponse
    {
        $user = User::find($id);

        if(!$user) {
            return ResponseService::badRequest(message: "Пользователь не найден.");
        }

        return ResponseService::success(UserResource::make($user));
    }
}
