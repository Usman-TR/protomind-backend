<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ChangePasswordRequest;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\SendLinkChangePasswordRequest;
use App\Http\Resources\AuthResource;
use App\Http\Resources\UserResource;
use App\Services\AuthService;
use App\Services\ResponseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;


class AuthController extends Controller
{


    /**
     * @OA\Post(
     *     path="/api/login",
     *     operationId="login",
     *     tags={"Auth"},
     *     description="Метод для авторизации пользователя",
     *     summary="Метод для авторизации пользователя",
     *     @OA\RequestBody(
     *       @OA\MediaType(
     *           mediaType="application/json",
     *           @OA\Schema(
     *              @OA\Property(property="email",type="string",example="example@242_test.com"),
     *              @OA\Property(property="password",type="string",example="242_test"),
     *           ),
     *       ),
     *     ),
     *     @OA\Response(response="200",
     *          description="OK",
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                 @OA\Property(
     *                     property="token",
     *                     type="string",
     *                             example="eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9yJpc3MiOiJodHRwOi8vbG9jYWxob3N0L2FwaS9sb2dpbiIsImlhdCI6MTcwNjUxMDU4MCwiZXhwIjoxNzA2NTE0MTgwLCJuYmYiOjE3MDY1MTA1ODAsImp0aSI6IlpHb3ZxekZLd3ppa3VoVjciLCJzdWIiOiIyIiwicHJ2IjoiMjNiZDVjODk0OWY2MDBhZGIzOWU3MDFjNDAwODcyZGI3YTU5NzZmNyJ9.YJ3frVsQSqnOg6fIZ7nwwE6AogmXfwEA-me-g9wDvBg"
     *                 ),
     *                 @OA\Property(
     *                     property="expires_in",
     *                     type="int64",
     *                     example=3600
     *                 )
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response="401",
     *          description="Unauthorized",
     *      )
     * )
     *
     * @param LoginRequest $request
     * @return JsonResponse
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $credentials = [
            "email" => $validated["email"],
            "password" => $validated["password"]
        ];

        $token = Auth::attempt($credentials);

        if (!$token) return ResponseService::unauthorized(message: __('auth.failed'));

        $user = Auth::user();

        return ResponseService::success(
            AuthResource::make($user, $token)
        );
    }

    /**
     * @OA\Post(
     *     path="/api/logout",
     *     operationId="logout",
     *     tags={"Auth"},
     *     description="Метод для выхода из системы",
     *     summary="Метод для выхода из системы",
     *     security={{"bearer_token":{}}},
     *     @OA\Response(
     *          response="200",
     *          description="OK",
     *      ),
     *      @OA\Response(
     *          response="401",
     *          description="Unauthorized",
     *      )
     * )
     *
     * @return JsonResponse
     */
    public function logout(): JsonResponse
    {
        Auth::logout();

        return ResponseService::success();
    }

    /**
     * @OA\Post(
     *     path="/api/refresh",
     *     operationId="refresh",
     *     tags={"Auth"},
     *     description="Метод для обновления jwt токенов пользователя",
     *     summary="Метод для обновления jwt токенов пользователя",
     *     security={{"bearer_token":{}}},
     *     @OA\Response(response="200",
     *          description="OK",
     *          @OA\MediaType(
     *               mediaType="application/json",
     *               @OA\Schema(
     *                  @OA\Property(
     *                      property="token",
     *                      type="string",
     *                              example="eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9yJpc3MiOiJodHRwOi8vbG9jYWxob3N0L2FwaS9sb2dpbiIsImlhdCI6MTcwNjUxMDU4MCwiZXhwIjoxNzA2NTE0MTgwLCJuYmYiOjE3MDY1MTA1ODAsImp0aSI6IlpHb3ZxekZLd3ppa3VoVjciLCJzdWIiOiIyIiwicHJ2IjoiMjNiZDVjODk0OWY2MDBhZGIzOWU3MDFjNDAwODcyZGI3YTU5NzZmNyJ9.YJ3frVsQSqnOg6fIZ7nwwE6AogmXfwEA-me-g9wDvBg"
     *                  ),
     *                  @OA\Property(
     *                      property="expires_in",
     *                      type="int64",
     *                      example=3600
     *                  )
     *               )
     *           )
     *      ),
     *      @OA\Response(
     *          response="401",
     *          description="Unauthorized",
     *      )
     * )
     *
     * @return JsonResponse
     */
    public function refresh(): JsonResponse
    {
        $user = Auth::user();
        $token = Auth::refresh();

        return ResponseService::success(
            AuthResource::make($user, $token)
        );
    }

    /**
     * @OA\Get(
     *     path="/api/me",
     *     operationId="me",
     *     tags={"Auth"},
     *     description="Метод для получения информации о текущем авторизованном пользователе/проверки авторизации",
     *     summary="Метод для получения информации о текущем авторизованном пользователе/проверки авторизации",
     *     security={{"bearer_token":{}}},
     *     @OA\Response(response="200",
     *          description="OK",
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                 @OA\Property(
     *                     property="user",
     *                     ref="#/components/schemas/User"
     *                 ),
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response="401",
     *          description="Unauthorized",
     *      )
     * )
     *
     * @return JsonResponse
     */
    public function me(): JsonResponse
    {
        $user = Auth::user();

        return ResponseService::success(
            UserResource::make($user)
        );
    }
}
