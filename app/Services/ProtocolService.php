<?php

namespace App\Services;

use App\Enums\ProtocolStageEnum;
use App\Enums\ProtocolStatusEnum;
use App\Jobs\ProcessVideoJob;
use App\Models\Keyword;
use App\Models\Protocol;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use ProtoneMedia\LaravelFFMpeg\Support\FFMpeg;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileDoesNotExist;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileIsTooBig;

class ProtocolService
{
    /**
     * @param array $data
     * @return Protocol
     */
    public function create(array $data): Protocol
    {
        $data['status'] = ProtocolStatusEnum::PROCESS->value;
        $data['stage'] = ProtocolStageEnum::NO_VIDEO->value;
        $data['creator_id'] = auth()->id();

        return Protocol::create($data);
    }

    /**
     * @param Protocol $protocol
     * @return void
     */
    public function processTranscript(Protocol $protocol): void
    {
        ProcessVideoJob::dispatch($protocol);
    }

    /**
     * @param $filePath
     * @return string|null
     */
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

    /**
     * @param Protocol $protocol
     * @param array $data
     * @return void
     */
    public function update(Protocol $protocol, array $data): void
    {
        $protocol->update($data);
    }

    /**
     * @param string|null $transcript
     * @param string $userId
     * @param array|null $currentFinalTranscript
     * @param Keyword|null $updatedKeyword
     * @return array
     */
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

    /**
     * @param Protocol $protocol
     * @param array $data
     * @return array{chunk_index: int, total_chunks: int}
     */
    public function handleChunk(Protocol $protocol, array $data): array
    {
        $chunkIndex = (int)$data['chunk_index'];
        $totalChunks = (int)$data['total_chunks'];
        $video = ($data['video'])->get();

        $filename = $this->generateFilename($chunkIndex, $totalChunks);
        $fullPath = $this->getChunkPath($protocol) . '/' . $filename;

        Storage::put($fullPath, $video);

        if ($chunkIndex === $totalChunks - 1) {
            $this->compileChunksAndAttachToProtocol($protocol, $totalChunks);
        }

        return [
            'chunk_index' => $chunkIndex,
            'total_chunks' => $totalChunks,
        ];
    }

    /**
     * @param $chunkIndex
     * @param $totalChunks
     * @return string
     */
    private function generateFilename($chunkIndex, $totalChunks): string
    {
        $currentIndex = $chunkIndex + 1;
        return "{$currentIndex}_{$totalChunks}.part";
    }

    /**
     * @param Protocol $protocol
     * @return string
     */
    private function getChunkPath(Protocol $protocol): string
    {
        return  'temp/chunks/protocol_' . $protocol->id;
    }

    /**
     * @param Protocol $protocol
     * @param $totalChunks
     * @return void
     */
    private function compileChunksAndAttachToProtocol(Protocol $protocol, $totalChunks): void
    {
        $tempOutputPath = storage_path('app/public/temp') . "/video_{$protocol->id}.mp4";

        // Очищаем временный файл перед началом объединения
        file_put_contents($tempOutputPath, '');

        // Объединяем чанки
        for ($i = 0; $i < $totalChunks; $i++) {
            $chunkFilename = $this->generateFilename($i, $totalChunks);
            $chunkPath = Storage::path($this->getChunkPath($protocol) . '/' . $chunkFilename);

            if (file_exists($chunkPath)) {
                // Используем FILE_APPEND для добавления данных к файлу
                file_put_contents($tempOutputPath, file_get_contents($chunkPath), FILE_APPEND);

                // Удаляем обработанный чанк
                unlink($chunkPath);
            }
        }

        // Проверяем, что все чанки были объединены
        if (filesize($tempOutputPath) > 0) {
            try {
                $protocol
                    ->addMedia($tempOutputPath)
                    ->toMediaCollection('video');
                \Log::info("Protocol:{$protocol->id}. Video successfully compiled and attached.");
            } catch (FileDoesNotExist|FileIsTooBig $e) {
                \Log::error("Protocol:{$protocol->id}. Error attaching media: {$e->getMessage()}");
            }
        }

        $protocol->update(['status' => ProtocolStatusEnum::PROCESS->value]);

        // Удаляем директорию с чанками
        Storage::deleteDirectory($this->getChunkPath($protocol));
    }
}
