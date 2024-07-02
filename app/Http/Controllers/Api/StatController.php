<?php

namespace App\Http\Controllers\Api;

use App\Enums\ProtocolStatusEnum;
use App\Enums\ProtocolTaskStatusEnum;
use App\Http\Controllers\Controller;
use App\Models\Meeting;
use App\Models\Protocol;
use App\Models\ProtocolTask;
use App\Models\User;
use App\Services\ResponseService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;

class StatController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/manager/all",
     *     summary="Get manager entities statistics",
     *     tags={"Manager"},
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(
     *                     property="protocols",
     *                     type="object",
     *                     @OA\Property(property="in_process", type="integer", example=5),
     *                     @OA\Property(property="success", type="integer", example=10)
     *                 ),
     *                 @OA\Property(
     *                     property="meetings",
     *                     type="object",
     *                     @OA\Property(property="in_process", type="integer", example=3),
     *                     @OA\Property(property="success", type="integer", example=7)
     *                 ),
     *                 @OA\Property(
     *                     property="tasks",
     *                     type="object",
     *                     @OA\Property(property="success", type="integer", example=8),
     *                     @OA\Property(property="expired", type="integer", example=2),
     *                     @OA\Property(property="process", type="integer", example=6)
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad request",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Bad request.")
     *         )
     *     )
     * )
     */
    public function getManagerEntitiesStat(): JsonResponse
    {
        $secretaryIds = User::find(auth()->id())->secretaries->pluck('id')->toArray();

        if(!$secretaryIds) {
            return ResponseService::badRequest();
        }

        $protocolCounts = Protocol::whereIn('secretary_id', $secretaryIds)
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status');

        $meetingsInProcessCount = Meeting::whereIn('secretary_id', $secretaryIds)
            ->whereDate('event_date', '>=', Carbon::now()->startOfDay())
            ->count();

        $meetingsSuccessCount = Meeting::whereIn('secretary_id', $secretaryIds)
            ->whereDate('event_date', '<', Carbon::now()->startOfDay())
            ->count();

        $taskCounts = ProtocolTask::whereHas('protocol', function ($query) use ($secretaryIds) {
            $query->whereIn('secretary_id', $secretaryIds);
        })
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status');

        $data = [
            "protocols" => [
                "in_process" => $protocolCounts[ProtocolStatusEnum::PROCESS->value] ?? 0,
                "success" => $protocolCounts[ProtocolStatusEnum::SUCCESS->value] ?? 0,
            ],
            "meetings" => [
                "in_process" => $meetingsInProcessCount,
                "success" => $meetingsSuccessCount,
            ],
            'tasks' => [
                "success" => $taskCounts[ProtocolTaskStatusEnum::SUCCESS->value] ?? 0,
                "expired" => $taskCounts[ProtocolTaskStatusEnum::EXPIRED->value] ?? 0,
                "process" => $taskCounts[ProtocolTaskStatusEnum::PROCESS->value] ?? 0,
            ]
        ];

        return ResponseService::success($data);
    }
}
