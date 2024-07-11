<?php

namespace App\Services;

use App\Enums\ProtocolStageEnum;
use App\Enums\ProtocolStatusEnum;
use App\Jobs\ProcessVideoJob;
use App\Models\Keyword;
use App\Models\Protocol;
use Illuminate\Support\Facades\Storage;
use ProtoneMedia\LaravelFFMpeg\Support\FFMpeg;

class ProtocolService
{
    public function create(array $data): Protocol
    {
        $data['status'] = ProtocolStatusEnum::PROCESS->value;
        $data['creator_id'] = auth()->id();

        $protocol = Protocol::create($data);

        $protocol->addMedia($data['video'])->toMediaCollection('video');

        $this->processTranscript($protocol);

        return $protocol;
    }

    private function processTranscript(Protocol $protocol): void
    {
        $relativePath = str_replace(config('app.url') . '/storage', '', $protocol->getFirstMediaUrl('video'));

        $wavPath = $this->convertToWav($relativePath);

        ProcessVideoJob::dispatch($protocol, $wavPath, ProtocolService::class);
    }

    private function convertToWav($filePath): string
    {
        $outputPath = 'wav/' . uniqid() . '.wav';

        FFMpeg::open($filePath)
            ->export()
            ->toDisk(env('FILESYSTEM_DISK'))
            ->inFormat((new \FFMpeg\Format\Audio\Wav)
                ->setAudioCodec('pcm_s16le')
                ->setAudioChannels(1)
                ->setAudioKiloBitrate(256))
            ->save($outputPath);

        return Storage::path($outputPath);
    }

    public function update(Protocol $protocol, array $data): void
    {
        if(isset($data['execute']) && $data['execute']) {
            $data['status'] = ProtocolStatusEnum::SUCCESS->value;
        }

        $protocol->update($data);
    }

    public function getFinalTranscript(string $text): array
    {
        $keywords = auth()->user()->keywords;
        $transcript = $text;

        $positions = [];
        $allKeywords = [];

        foreach ($keywords as $keyword) {
            $title = $keyword->title;
            $phrase = $keyword->phrase;
            $allKeywords[] = $title;

            $pos = mb_stripos($transcript, $phrase);
            if ($pos !== false) {
                $positions[$title] = $pos;
            }
        }

        asort($positions);

        $result = array_fill_keys($allKeywords, '');

        $values = array_values($positions);
        $keys = array_keys($positions);

        for ($i = 0; $i < count($positions); $i++) {
            $key = $keys[$i];
            $start = $values[$i];
            $end = $values[$i + 1] ?? mb_strlen($transcript);

            $result[$key] = mb_substr($transcript, $start, $end - $start);
        }

        $finalArr = [];
        $finalArrKeys = array_keys($result);
        $finalArrValues = array_values($result);

        for ($i = 0; $i < count($result); $i++) {
            $finalArr[$i]['key'] = $finalArrKeys[$i];
            $finalArr[$i]['value'] = $finalArrValues[$i];
        }

        return $finalArr;
    }
}
