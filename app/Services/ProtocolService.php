<?php

namespace App\Services;

use App\Enums\ProtocolStatusEnum;
use App\Jobs\ProcessVideoJob;
use App\Models\Keyword;
use App\Models\Protocol;
use App\Models\User;
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

        return $protocol;
    }

    public function processTranscript(Protocol $protocol): void
    {
        ProcessVideoJob::dispatch($protocol);
    }

    public function convertToWav($filePath): ?string
    {
        $media = FFMpeg::open($filePath);

        $hasAudio = false;
        foreach ($media->getStreams() as $stream) {
            if ($stream->isAudio()) {
                $hasAudio = true;
                break;
            }
        }

        if (!$hasAudio) {
            return null;
        }

        $outputPath = 'wav/' . uniqid() . '.wav';

        $media->export()
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
        $protocol->update($data);
    }

    public function getFinalTranscript(?string $transcript, string $userId, ?array $currentFinalTranscript = null, ?Keyword $updatedKeyword = null): array
    {
        $keywords = User::find($userId)->keywords;

        $positions = [];
        $allKeywords = [];
        $oldTitle = $updatedKeyword?->getOriginal('title');
        $newTitle = $updatedKeyword?->title;

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
        foreach ($allKeywords as $keyword) {
            $finalArr[] = [
                'key' => $keyword,
                'value' => $result[$keyword]
            ];
        }

        if ($currentFinalTranscript) {
            if ($updatedKeyword) {
                foreach ($currentFinalTranscript as &$item) {
                    if ($item['key'] === $oldTitle) {
                        $item['key'] = $newTitle;
                    }
                }
            }

            $existingKeys = array_column($currentFinalTranscript, 'key');

            $finalArr = array_filter($finalArr, function($item) use ($existingKeys) {
                return !in_array($item['key'], $existingKeys);
            });

            $mergedArr = array_merge($currentFinalTranscript, $finalArr);

            return array_values($mergedArr);
        }

        return $finalArr;
    }
}
