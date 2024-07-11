<?php

namespace App\Services;

use App\Models\Protocol;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\SimpleType\Jc;

class ProtocolDocumentService
{
    public function generateDocx(Protocol $protocol): string
    {
        $phpWord = new PhpWord();

        $section = $phpWord->addSection();

        $section->addText("ПРОТОКОЛ №{$protocol->user_protocol_number}", ['bold' => true], ['alignment' => Jc::CENTER]);
        $section->addText('заседания', ['bold' => true], ['alignment' => Jc::CENTER]);

        $section->addTextBreak(1);

        $meetingDate = $protocol->event_date->translatedFormat('«d» F Y г.');
        $meetingCity = $protocol->city;

        $dateCity = $section->addTextRun();
        $dateCity->addText($meetingDate, [], ['alignment' => Jc::START]);
        $dateCity->addText("\t\t\t\t\t\t\t\t\tг. {$meetingCity}", [], ['alignment' => Jc::END]);

        $startTime = $protocol->event_start_time->format('H ч.i мин');
        $section->addText("Время проведение заседания:    {$startTime}", [], ['alignment' => Jc::START]);

        $location = $protocol->location;
        $section->addText("Место проведения: {$location}", [], ['alignment' => Jc::START]);

        $section->addTextBreak(1);

        $membersCount = $protocol->members()->count();
        $section->addText("В заседании приняли участие {$membersCount} участников", [], ['alignment' => Jc::START]);

        $section->addTextBreak(1);

        $agenda = $protocol->agenda;
        $section->addText("Повестка дня заседания: {$agenda}", ['bold' => true], ['alignment' => Jc::START]);

        $section->addTextBreak(1);
        $section->addText("Ход заседания и принятые решения: ", ['bold' => true], ['alignment' => Jc::START]);
        $section->addTextBreak(1);

        foreach ($protocol->final_transcript as $item) {
            $key = mb_strtoupper($item['key']);
            $value = $item['value'];

            if(empty($value)) continue;

            $section->addText("{$key}:", [], ['alignment' => Jc::START]);
            $section->addText($value, [], ['alignment' => Jc::START]);
            $section->addTextBreak(1);
        }

        $filePath = storage_path('app/public/'. uniqid() .'.docx');

        $objWriter = IOFactory::createWriter($phpWord, 'Word2007');
        $objWriter->save($filePath);

        return $filePath;
    }
}
