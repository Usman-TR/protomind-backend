<?php

namespace App\Jobs;

use App\Enums\ProtocolStageEnum;
use App\Events\VideoProcessedBroadcast;
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
        $response = Http::withOptions(['timeout' => 0])->post('http://127.0.0.1:8000/transcribe', [
            'filepath' => $this->filepath,
        ]);


        if($response->successful()) {
            $this->protocol->update([
               'stage' => ProtocolStageEnum::SUCCESS_VIDEO_PROCESS->value,
               'transcript' => json_decode($response->body())->text,
            ]);

            event(new VideoProcessedBroadcast($this->protocol));
            app(ProtocolService::class)->saveFinalTranscript($this->protocol);
        } else {
            $this->protocol->update([
                'stage' => ProtocolStageEnum::ERROR_VIDEO_PROCESS->value,
            ]);
        }

        unlink($this->filepath);

    }
}
