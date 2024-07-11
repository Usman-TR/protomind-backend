<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Protocol;
use App\Services\ProtocolDocumentService;
use App\Services\ResponseService;
use Barryvdh\DomPDF\Facade\Pdf;
use Symfony\Component\HttpFoundation\Response;

class ProtocolDocumentController extends Controller
{
    public function __construct(
        private readonly ProtocolDocumentService $service
    )
    {
    }

    /**
     * @OA\Get(
     *     path="/api/protocols/{id}/documents/pdf",
     *     operationId="generatePdf",
     *     tags={"ProtocolDocuments"},
     *     summary="Сгенерировать PDF протокола",
     *     description="Метод для генерации PDF версии протокола",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID протокола",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Успешная операция",
     *         @OA\MediaType(
     *             mediaType="application/pdf",
     *             @OA\Schema(type="string", format="binary")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Протокол не найден",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Протокол не найден."
     *             )
     *         )
     *     )
     * )
     */

    public function generatePdf(string $id): Response
    {
        $protocol = Protocol::find($id);

        if(!$protocol) {
            return ResponseService::notFound(message: 'Протокол не найден.');
        }

        return Pdf::loadView('documents.generate-pdf',
            [
                'user_protocol_number' => $protocol->user_protocol_number,
                'event_date' => $protocol->event_date->translatedFormat('«d» F Y г.'),
                'city' => $protocol->city,
                'event_start_time' => $protocol->event_start_time->format('H ч.i мин'),
                'location' => $protocol->location,
                'members_count' => $protocol->members()->count(),
                'agenda' => $protocol->agenda,
                'final_transcript' => json_decode($protocol->final_transcript, true),
            ]
            )
            ->setPaper('A4', 'portrait')
            ->download($protocol->theme);
    }

    /**
     * @OA\Get(
     *     path="/api/protocols/{id}/documents/docx",
     *     operationId="generateDocx",
     *     tags={"ProtocolDocuments"},
     *     summary="Сгенерировать DOCX протокола",
     *     description="Метод для генерации DOCX версии протокола",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID протокола",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Успешная операция",
     *         @OA\MediaType(
     *             mediaType="application/vnd.openxmlformats-officedocument.wordprocessingml.document",
     *             @OA\Schema(type="string", format="binary")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Протокол не найден",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Протокол не найден."
     *             )
     *         )
     *     )
     * )
     */
    public function generateDocx(string $id): Response
    {
        $protocol = Protocol::find($id);

        if (!$protocol) {
            return response()->json(['message' => 'Протокол не найден.'], 404);
        }

        $filePath = $this->service->generateDocx($protocol);

        return response()->download($filePath)->deleteFileAfterSend(true);
    }

}
