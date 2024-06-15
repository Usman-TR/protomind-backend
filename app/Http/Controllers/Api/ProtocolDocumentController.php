<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Protocol;
use App\Services\ProtocolService;
use App\Services\ResponseService;
use Barryvdh\DomPDF\Facade\Pdf;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\PhpWord;
use Symfony\Component\HttpFoundation\Response;

class ProtocolDocumentController extends Controller
{
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
                ['text' => $protocol->final_transcript]
            )
            ->setPaper('A4', 'landscape')
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

        $phpWord = new PhpWord();

        $section = $phpWord->addSection();

        $section->addText($protocol->final_transcript);

        $filePath = storage_path('app/public/'. uniqid() .'.docx');

        $objWriter = IOFactory::createWriter($phpWord, 'Word2007');
        $objWriter->save($filePath);

        return response()->download($filePath)->deleteFileAfterSend(true);
    }

}
