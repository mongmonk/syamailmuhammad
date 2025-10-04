<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use App\Models\AudioFile;

class AudioStreamingTest extends TestCase
{
    use RefreshDatabase;

    public function test_stream_returns_404_when_file_missing(): void
    {
        $audioFile = AudioFile::factory()->create([
            'file_path' => 'audio/does_not_exist.mp3',
            'file_size' => 12345,
        ]);

        $response = $this->get(route('audio.stream', ['audioFile' => $audioFile->id]));

        $response->assertStatus(404);
    }

    public function test_stream_returns_200_full_without_range(): void
    {
        Storage::fake('local');
        Storage::disk('local')->put('audio/full.mp3', str_repeat('A', 4096));

        $audioFile = AudioFile::factory()->create([
            'file_path' => 'audio/full.mp3',
            'file_size' => 4096,
        ]);

        $response = $this->get(route('audio.stream', ['audioFile' => $audioFile->id]));

        $response->assertStatus(200);
        $response->assertHeader('Accept-Ranges', 'bytes');
        $response->assertHeader('Content-Length', '4096');
        $response->assertHeader('Content-Range', 'bytes 0-4095/4096');
    }

    public function test_stream_returns_206_partial_with_range(): void
    {
        Storage::fake('local');
        Storage::disk('local')->put('audio/partial.mp3', str_repeat('B', 4096));

        $audioFile = AudioFile::factory()->create([
            'file_path' => 'audio/partial.mp3',
            'file_size' => 4096,
        ]);

        $response = $this->get(route('audio.stream', ['audioFile' => $audioFile->id]), [
            'Range' => 'bytes=0-1023',
        ]);

        $response->assertStatus(206);
        $response->assertHeader('Accept-Ranges', 'bytes');
        $response->assertHeader('Content-Length', '1024');
        $response->assertHeader('Content-Range', 'bytes 0-1023/4096');
    }

    public function test_stream_returns_416_on_invalid_range(): void
    {
        Storage::fake('local');
        Storage::disk('local')->put('audio/invalid.mp3', str_repeat('C', 1024));

        $audioFile = AudioFile::factory()->create([
            'file_path' => 'audio/invalid.mp3',
            'file_size' => 1024,
        ]);

        $response = $this->get(route('audio.stream', ['audioFile' => $audioFile->id]), [
            'Range' => 'bytes=5000-6000',
        ]);

        $response->assertStatus(416);
    }
}