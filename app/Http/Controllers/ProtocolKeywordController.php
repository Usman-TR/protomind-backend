<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProtocolKeyword\StoreUpdateRequest;
use App\Http\Resources\ProtocolKeywordResource;
use App\Models\Protocol;
use App\Models\ProtocolKeyword;
use App\Services\ProtocolKeywordService;
use App\Services\ResponseService;
use Illuminate\Http\JsonResponse;

class ProtocolKeywordController extends Controller
{
    public function __construct(
        private readonly ProtocolKeywordService $protocolKeywordService
    )
    {
    }

    public function index(string $protocolId): JsonResponse
    {
        $protocol = Protocol::find($protocolId);

        if(!$protocol) {
            return ResponseService::notFound(message: 'Протокол не найден.');
        }

        return ResponseService::success(
            ProtocolKeywordResource::collection($protocol->keywords)
        );
    }

    public function store(StoreUpdateRequest $request, string $protocolId): JsonResponse
    {
        $protocol = Protocol::find($protocolId);

        if(!$protocol) {
            return ResponseService::notFound(message: 'Протокол не найден.');
        }

        $validated = $request->validated();

        $newKeywords = $this->protocolKeywordService->create($validated, $protocolId);

        return ResponseService::success(
            ProtocolKeywordResource::collection($newKeywords)
        );
    }

    public function update(StoreUpdateRequest $request, string $protocolId): JsonResponse
    {
        $protocol = Protocol::find($protocolId);

        if(!$protocol) {
            return ResponseService::notFound(message: 'Протокол не найден.');
        }

        $validated = $request->validated();

        $newKeywords = $this->protocolKeywordService->update($validated);

        return ResponseService::success(
            ProtocolKeywordResource::collection($newKeywords)
        );
    }

    public function destroy(string $id): JsonResponse
    {
        ProtocolKeyword::destroy($id);

        return ResponseService::success();
    }
}
