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
        $data['status'] = ProtocolStatusEnum::PROCESS;
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
        if(isset($data['final_transcript']) &&
            $data['final_transcript'] && is_null($protocol->final_transcript)) {
            $data['stage'] = ProtocolStageEnum::FINAL->value;
        }

        if(isset($data['execute']) && $data['execute']) {
            $data['status'] = ProtocolStatusEnum::SUCCESS->value;
        }

        $protocol->update($data);
    }

    public function saveFinalTranscript(Protocol $protocol): void
    {
        $finalTranscript = $this->extractSectionsByKeywords($protocol->transcript);

        $protocol->update([
           'final_transcript' => $finalTranscript
        ]);
    }

    private function extractSectionsByKeywords($text): string
    {
        $text = strtolower(trim($text));

        $keywords = array_map('strtolower', Keyword::all()->pluck('phrase')->toArray());
        $keywordSet = array_flip($keywords);

        $words = explode(' ', $text);

        $sections = [];
        $currentSection = '';
        $writing = false;

        foreach ($words as $word) {
            $trimmedWord = trim($word);

            if (isset($keywordSet[$trimmedWord])) {
                if ($currentSection !== '') {
                    $sections[] = trim($currentSection);
                }
                $currentSection = '';
                $writing = true;
            }

            if ($writing) {
                $currentSection .= ' ' . $trimmedWord;
            }
        }

        return implode(' ', $sections);
    }
}
