<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\SendLinkChangePasswordRequest;
use App\Http\Requests\ChangePassword\CheckTokenRequest;
use App\Http\Requests\ChangePassword\ResetRequest;
use App\Services\ResetPasswordService;
use App\Services\ResponseService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;

class ChangePasswordController extends Controller
{
    public function __construct(
        private readonly ResetPasswordService $service
    )
    {
    }

    /**
     * @OA\Post(
     *     path="/api/change-password/check-token",
     *     summary="Проверяет существует ли токен по email и token",
     *     tags={"ChangePassword"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/ChangePasswordCheckTokenRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Token is valid",
     *         @OA\JsonContent()
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Token not found",
     *         @OA\JsonContent()
     *     )
     * )
     */
    public function checkToken(CheckTokenRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $tokenData = DB::table('password_reset_tokens')
            ->where('email', $validated['email'])
            ->first();

        if (!$tokenData) {
            return ResponseService::notFound(message: 'Токен не найден');
        }

        if (!Hash::check($validated['token'], $tokenData->token)) {
            return ResponseService::notFound(message: 'Неверный токен');
        }

        $createdAt = Carbon::parse($tokenData->created_at);
        if (Carbon::now()->diffInMinutes($createdAt) > config('auth.passwords.users.expire', 60)) {
            return ResponseService::notFound(message: 'Срок действия токена истек');
        }

        return ResponseService::success(message: 'Токен действителен');

    }


    /**
     * @OA\Post(
     *     path="/api/change-password/send-link",
     *     summary="Отправляет ссылку на почту",
     *     tags={"ChangePassword"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/SendLinkChangePasswordRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Reset link sent",
     *         @OA\JsonContent()
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad request",
     *         @OA\JsonContent()
     *     )
     * )
     */
    public function sendResetLinkEmail(SendLinkChangePasswordRequest $request): JsonResponse
    {
        $validated = $request->validated();

        try {
            $status = $this->service->sendLink($validated['email']);

            return ResponseService::success(message: __($status));
        } catch (\Exception $e) {
            return ResponseService::badRequest(message: $e->getMessage());
        }
    }

    /**
     * @OA\Post(
     *     path="/api/change-password/reset",
     *     summary="Меняет пароль",
     *     tags={"ChangePassword"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/ChangePasswordResetRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Password reset successful",
     *         @OA\JsonContent()
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad request",
     *         @OA\JsonContent()
     *     )
     * )
     */
    public function reset(ResetRequest $request): JsonResponse
    {
        $status = $this->service->resetPassword(
            $request->only('email', 'password', 'password_confirmation', 'token')
        );

        if ($status == Password::PASSWORD_RESET) {
            return ResponseService::success(message: __($status));
        }

        return ResponseService::badRequest(message: __($status));
    }
}
