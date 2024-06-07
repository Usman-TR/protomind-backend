<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProtocolMember\StoreRequest;
use App\Http\Resources\ProtocolMemberResource;
use App\Models\Protocol;
use App\Models\ProtocolMember;
use App\Services\ResponseService;
use Illuminate\Http\JsonResponse;

class ProtocolMemberController extends Controller
{
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

    public function store(StoreRequest $request, string $protocolId): JsonResponse
    {
        $validated = $request->validated();

        $protocol = Protocol::find($protocolId);

        if(!$protocol) {
            ResponseService::notFound(message: 'Протокол не найден.');
        }

        $validated['protocol_id'] = $protocolId;

        $member = ProtocolMember::create($validated);

        return ResponseService::success($member);
    }

    public function destroy(string $id): JsonResponse
    {
        ProtocolMember::destroy($id);

        return ResponseService::success(message: 'Участник удален.');
    }
}
