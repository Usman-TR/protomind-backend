<?php

namespace App\Jobs;

use App\Enums\ProtocolStageEnum;
use App\Events\VideoProcessedBroadcast;
use App\Http\Resources\ProtocolResource;
use App\Models\Protocol;
use App\Services\ProtocolService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class ProcessVideoJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        private Protocol        $protocol,
        private readonly string $filepath,
    )
    {
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $url = env('SPEECH_TRANSCRIBE_URL', 'http://127.0.0.1:8001');
        $response = Http::withOptions(['timeout' => 0])->post($url . '/transcribe/', [
            'filepath' => $this->filepath,
        ]);


        if($response->successful()) {
            $transcript = json_decode($response->body())->text;

            $finalTranscript = app(ProtocolService::class)->getFinalTranscript($transcript, $this->protocol->creator_id);
            $this->protocol->update([
               'stage' => ProtocolStageEnum::SUCCESS_VIDEO_PROCESS->value,
               'final_transcript' => $finalTranscript ?? null,
               'transcript' => $transcript ?? null,
            ]);

            broadcast(new VideoProcessedBroadcast($this->protocol));
        } else {
            $this->protocol->update([
                'stage' => ProtocolStageEnum::ERROR_VIDEO_PROCESS->value,
            ]);
        }

        unlink($this->filepath);

    }
}
