<?php

namespace App\Jobs;

use App\Models\AudioFile;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ExtractAudioMetadata implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $audioFileId;

    public int $tries = 3;

    public int $backoff = 10;

    public function __construct(int $audioFileId)
    {
        $this->audioFileId = $audioFileId;
        $this->onQueue('media');
    }

    public function handle(): void
    {
        $audio = AudioFile::find($this->audioFileId);
        if (!$audio) {
            Log::warning('ExtractAudioMetadata: audio file record not found', [
                'audio_file_id' => $this->audioFileId,
            ]);
            return;
        }

        $path = Storage::disk('local')->path($audio->file_path);

        if (!is_file($path)) {
            Log::warning('ExtractAudioMetadata: file not found on disk', [
                'audio_file_id' => $audio->id,
                'file_path' => $audio->file_path,
                'absolute_path' => $path,
            ]);
            return;
        }

        try {
            $hasGetID3 = class_exists(\getID3::class);
            if (!$hasGetID3) {
                Log::error('ExtractAudioMetadata: getID3 class not found');
                return;
            }

            $getID3 = new \getID3();
            $info = $getID3->analyze($path);

            $durationSeconds = null;
            if (isset($info['playtime_seconds'])) {
                $durationSeconds = (int) round((float) $info['playtime_seconds']);
            }

            $fileSize = @filesize($path);
            if ($fileSize === false && isset($info['filesize'])) {
                $fileSize = (int) $info['filesize'];
            }

            $updates = [];
            if ($durationSeconds !== null) {
                $updates['duration'] = $durationSeconds;
            }
            if ($fileSize !== null) {
                $updates['file_size'] = (int) $fileSize;
            }

            if (!empty($updates)) {
                $audio->fill($updates);
                $audio->save();

                Log::info('ExtractAudioMetadata: updated audio metadata', [
                    'audio_file_id' => $audio->id,
                    'duration' => $updates['duration'] ?? null,
                    'file_size' => $updates['file_size'] ?? null,
                ]);
            } else {
                Log::warning('ExtractAudioMetadata: no metadata extracted', [
                    'audio_file_id' => $audio->id,
                    'info_keys' => array_keys($info ?? []),
                ]);
            }
        } catch (\Throwable $e) {
            Log::error('ExtractAudioMetadata: exception while analyzing audio', [
                'audio_file_id' => $audio->id ?? $this->audioFileId,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }
}