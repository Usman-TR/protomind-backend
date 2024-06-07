<?php

namespace App\Http\Controllers;

use App\Enums\ProtocolStatusEnum;
use App\Http\Requests\Protocol\StoreRequest;
use App\Http\Resources\ProtocolResource;
use App\Models\Protocol;
use App\Services\ResponseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProtocolController extends Controller
{

    public function index(): JsonResponse
    {
        $protocols = Protocol::all();

        return ResponseService::success(
            ProtocolResource::collection($protocols)
        );
    }

    public function store(StoreRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $validated['status'] = ProtocolStatusEnum::PROCESS;

        $protocol = Protocol::create($validated);

        return ResponseService::success(
            ProtocolResource::make($protocol)
        );
    }

    public function show(string $id): JsonResponse
    {
        $protocol = Protocol::find($id);

        if(!$protocol) {
            return ResponseService::notFound(message: 'Протокол не найден.');
        }

        return ResponseService::success(
            ProtocolResource::make($protocol)
        );
    }
}
