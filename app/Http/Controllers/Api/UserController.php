<?php

namespace App\Http\Controllers\Api;

use App\Enums\RolesEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\StoreRequest;
use App\Http\Requests\User\UpdateRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\ResponseService;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;

class UserController extends Controller
{

    /**
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $user = auth()->user();

        if($user->hasRole(RolesEnum::MANAGER->value)) {
            return ResponseService::success(UserResource::collection($user->secretaries));
        }

        if($user->hasRole(RolesEnum::ADMIN->value)) {
            return ResponseService::success(UserResource::collection(User::whereNot('id', $user->id)->get()));
        }

        return ResponseService::success();
    }

    public function __construct(private readonly UserService $userService)
    {
    }

    /**
     * @param StoreRequest $request
     * @return JsonResponse
     */
    public function store(StoreRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $this->userService->create($validated);

        return ResponseService::success();
    }

    /**
     * @param UpdateRequest $request
     * @param string $id
     * @return JsonResponse
     */
    public function update(UpdateRequest $request, string $id): JsonResponse
    {
        $user = User::find($id);

        if(!$user) {
            return ResponseService::badRequest(message: "Пользователь не найден.");
        }

        $validated = $request->validated();

        $user->update($validated);

        return ResponseService::success($user);
    }

    /**
     * @param string $id
     * @return JsonResponse
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
