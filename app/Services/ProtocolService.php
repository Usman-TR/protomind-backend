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
        if(isset($data['final_transcript']) &&
            $data['final_transcript'] && is_null($protocol->final_transcript)) {
            $data['stage'] = ProtocolStageEnum::FINAL->value;
        }

        if(isset($data['execute']) && $data['execute']) {
            $data['status'] = ProtocolStatusEnum::SUCCESS->value;
        }

        $protocol->update($data);
    }

    public function getFinalTranscript(Protocol $protocol): void
    {

    }
}
