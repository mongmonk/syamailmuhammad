<?php

namespace Tests\Feature;

use App\Models\AudioFile;
use App\Models\Hadith;
use App\Models\Chapter;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AudioControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Setup storage for testing
        Storage::fake('local');
    }
    
    public function test_get_audio_url_returns_json_with_correct_data()
    {
        // Create test data
        $chapter = Chapter::factory()->create();
        $hadith = Hadith::factory()->create(['chapter_id' => $chapter->id]);
        $audioFile = AudioFile::factory()->create(['hadith_id' => $hadith->id]);
        
        // Make request to get audio URL
        $response = $this->getJson("/audio/{$audioFile->id}/url");
        
        // Assert response status
        $response->assertStatus(200);
        
        // Assert response structure
        $response->assertJsonStructure([
            'id',
            'url',
            'duration',
            'file_size',
            'hadith_id'
        ]);
        
        // Assert response data
        $response->assertJson([
            'id' => $audioFile->id,
            'duration' => $audioFile->duration,
            'file_size' => $audioFile->file_size,
            'hadith_id' => $audioFile->hadith_id,
        ]);
        
        // Assert URL contains the correct route
        $url = $response->json('url');
        $this->assertStringContainsString("/audio/{$audioFile->id}/stream", $url);
    }
    
    public function test_stream_audio_returns_404_when_file_not_exists()
    {
        // Create test data
        $chapter = Chapter::factory()->create();
        $hadith = Hadith::factory()->create(['chapter_id' => $chapter->id]);
        $audioFile = AudioFile::factory()->create([
            'hadith_id' => $hadith->id,
            'file_path' => 'nonexistent/audio.mp3'
        ]);
        
        // Make request to stream audio
        $response = $this->get("/audio/{$audioFile->id}/stream");
        
        // Assert response status is 404
        $response->assertStatus(404);
    }
    
    public function test_stream_audio_returns_full_content_without_range_header()
    {
        // Create test data
        $chapter = Chapter::factory()->create();
        $hadith = Hadith::factory()->create(['chapter_id' => $chapter->id]);
        $audioFile = AudioFile::factory()->create(['hadith_id' => $hadith->id]);
        
        // Create a fake audio file
        $fakeAudioContent = 'fake audio content for testing';
        Storage::disk('local')->put($audioFile->file_path, $fakeAudioContent);
        
        // Make request to stream audio
        $response = $this->get("/audio/{$audioFile->id}/stream");
        
        // Assert response status is 200 (OK)
        $response->assertStatus(200);
        
        // Assert response headers
        $fileSize = strlen($fakeAudioContent);
        $response->assertHeader('Accept-Ranges', 'bytes');
        $response->assertHeader('Content-Range', "bytes 0-" . ($fileSize - 1) . "/$fileSize");
        $response->assertHeader('Content-Length', $fileSize);
    }
    
    public function test_stream_audio_handles_range_requests()
    {
        // Create test data
        $chapter = Chapter::factory()->create();
        $hadith = Hadith::factory()->create(['chapter_id' => $chapter->id]);
        $audioFile = AudioFile::factory()->create(['hadith_id' => $hadith->id]);
        
        // Create a fake audio file
        $fakeAudioContent = 'fake audio content for testing';
        Storage::disk('local')->put($audioFile->file_path, $fakeAudioContent);
        
        // Make request with range header
        $response = $this->withHeaders([
            'Range' => 'bytes=0-4'
        ])->get("/audio/{$audioFile->id}/stream");
        
        // Assert response status is 206 (Partial Content)
        $response->assertStatus(206);
        
        // Assert response headers
        $fileSize = strlen($fakeAudioContent);
        $response->assertHeader('Accept-Ranges', 'bytes');
        $response->assertHeader('Content-Range', 'bytes 0-4/' . $fileSize);
        $response->assertHeader('Content-Length', 5);
    }
    
    public function test_stream_audio_returns_416_for_invalid_range()
    {
        // Create test data
        $chapter = Chapter::factory()->create();
        $hadith = Hadith::factory()->create(['chapter_id' => $chapter->id]);
        $audioFile = AudioFile::factory()->create(['hadith_id' => $hadith->id]);
        
        // Create a fake audio file
        $fakeAudioContent = 'fake audio content for testing';
        Storage::disk('local')->put($audioFile->file_path, $fakeAudioContent);
        $fileSize = strlen($fakeAudioContent);
        
        // Make request with invalid range header (start > end)
        $response = $this->withHeaders([
            'Range' => 'bytes=10-5'
        ])->get("/audio/{$audioFile->id}/stream");
        
        // Assert response status is 416 (Requested Range Not Satisfiable)
        $response->assertStatus(416);
        
        // Make request with invalid range header (start > file size)
        $response = $this->withHeaders([
            'Range' => 'bytes=' . ($fileSize + 10) . '-' . ($fileSize + 20)
        ])->get("/audio/{$audioFile->id}/stream");
        
        // Assert response status is 416 (Requested Range Not Satisfiable)
        $response->assertStatus(416);
    }
    
    public function test_stream_audio_handles_end_of_file_range()
    {
        // Create test data
        $chapter = Chapter::factory()->create();
        $hadith = Hadith::factory()->create(['chapter_id' => $chapter->id]);
        $audioFile = AudioFile::factory()->create(['hadith_id' => $hadith->id]);
        
        // Create a fake audio file
        $fakeAudioContent = 'fake audio content for testing';
        Storage::disk('local')->put($audioFile->file_path, $fakeAudioContent);
        $fileSize = strlen($fakeAudioContent);
        
        // Make request with range that ends at file size
        $response = $this->withHeaders([
            'Range' => 'bytes=' . ($fileSize - 5) . '-'
        ])->get("/audio/{$audioFile->id}/stream");
        
        // Assert response status is 206 (Partial Content)
        $response->assertStatus(206);
        
        // Assert response headers
        $response->assertHeader('Accept-Ranges', 'bytes');
        $response->assertHeader('Content-Range', "bytes " . ($fileSize - 5) . "-" . ($fileSize - 1) . "/$fileSize");
        $response->assertHeader('Content-Length', 5);
    }
}