<?php

namespace App\Http\Controllers\Api;

use App\Enums\ProtocolStatusEnum;
use App\Enums\ProtocolTaskStatusEnum;
use App\Http\Controllers\Controller;
use App\Http\Filters\MeetingFilter;
use App\Http\Filters\TaskStatFilter;
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
     *     tags={"Stats"},
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

    /**
     * @OA\Get(
     *     path="/api/stats/secretary/meetings",
     *     summary="Get Secretary Meetings Statistics",
     *     description="Returns a list of meetings for the authenticated secretary.",
     *     tags={"Stats"},
     *     @OA\Parameter(
     *         name="start_date_at",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string", format="date"),
     *         description="Start date for filtering meetings."
     *     ),
     *     @OA\Parameter(
     *         name="end_date_at",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string", format="date"),
     *         description="End date for filtering meetings."
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="event_date", type="string", format="date-time", example="2024-07-06T12:00:00Z")
     *             )
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthorized")
     * )
     */
    public function getSecretaryMeetingsStat(MeetingFilter $filter): JsonResponse
    {
        $meetings = Meeting::filter($filter)
            ->select('id', 'event_date')
            ->where('secretary_id', auth()->id())
            ->orderBy('event_date')
            ->get();

        return ResponseService::success(
            $meetings
        );
    }

    /**
     * @OA\Get(
     *     path="/api/stats/secretary/{id}/entities",
     *     summary="Get Secretary Entities Statistics",
     *     description="Returns statistics for a specific secretary.",
     *     tags={"Stats"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="string"),
     *         description="Secretary ID"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="protocols",
     *                 type="object",
     *                 @OA\Property(property="in_process", type="integer", example=5),
     *                 @OA\Property(property="success", type="integer", example=10)
     *             ),
     *             @OA\Property(
     *                 property="meetings",
     *                 type="object",
     *                 @OA\Property(property="in_process", type="integer", example=3),
     *                 @OA\Property(property="success", type="integer", example=7)
     *             )
     *         )
     *     ),
     *     @OA\Response(response=404, description="Not Found"),
     *     @OA\Response(response=401, description="Unauthorized")
     * )
     */
    public function getSecretaryEntitiesStat(string $id): JsonResponse
    {
        $user = User::find($id);

        if(!$user) {
            return ResponseService::notFound();
        }

        $data = [
            "protocols" => [
                "in_process" => $user->protocols()->where('status', ProtocolStatusEnum::PROCESS->value)->count(),
                "success" => $user->protocols()->where('status', ProtocolStatusEnum::SUCCESS->value)->count(),
            ],
            "meetings" => [
                "in_process" => $user->meetings()->whereDate('event_date', '>=', Carbon::now()->startOfDay())->count(),
                "success" => $user->meetings()->whereDate('event_date', '<', Carbon::now()->startOfDay())->count(),
            ],
        ];

        return ResponseService::success($data);
    }

    /**
     * @OA\Get(
     *     path="/api/stats/secretary/{id}/tasks",
     *     summary="Get Secretary Tasks Statistics",
     *     description="Returns tasks statistics for a specific secretary.",
     *     tags={"Stats"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="string"),
     *         description="Secretary ID"
     *     ),
     *     @OA\Parameter(
     *         name="date",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string", format="date"),
     *         description="Filter tasks by date"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 @OA\Property(property="week", type="integer", example=1),
     *                 @OA\Property(property="in_process", type="integer", example=5),
     *                 @OA\Property(property="success", type="integer", example=10),
     *                 @OA\Property(property="expired", type="integer", example=2)
     *             )
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthorized")
     * )
     */
    public function getSecretaryTasksStat(string $id, TaskStatFilter $filter)
    {
        $tasks = ProtocolTask::whereHas('protocol.creator', function ($query) use ($id) {
            $query->where('id', $id);
        })->filter($filter)->get();

        $data = $tasks->groupBy(function ($task) {
            $carbonDate = Carbon::parse($task->created_at);
            return $carbonDate->weekOfMonth;
        })->map(function ($group) {
            return [
                'week' => $group->first()->created_at->weekOfMonth,
                'in_process' => $group->where('status', ProtocolTaskStatusEnum::PROCESS)->count(),
                'success' => $group->where('status', ProtocolTaskStatusEnum::SUCCESS)->count(),
                'expired' => $group->where('status', ProtocolTaskStatusEnum::EXPIRED)->count(),
            ];
        })->sortBy('week')->values();

        $minWeek = $data->min('week');
        $maxWeek = $data->max('week');
        $allWeeks = collect(range($minWeek, $maxWeek));

        $completeData = $allWeeks->map(function ($week) use ($data) {
            $weekData = $data->firstWhere('week', $week);
            if (!$weekData) {
                $weekData = [
                    'week' => $week,
                    'in_process' => 0,
                    'success' => 0,
                    'expired' => 0,
                ];
            }
            return $weekData;
        });

        return $completeData->sortBy('week')->values();
    }
}
