<?php

namespace Tests\Unit;

use App\Services\AudioStreamingService;
use App\Models\AudioFile;
use App\Models\Hadith;
use App\Models\Chapter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use ReflectionClass;

class AudioStreamingServiceTest extends TestCase
{
    use RefreshDatabase;
    protected AudioStreamingService $audioStreamingService;
    
    protected function setUp(): void
    {
        parent::setUp();
        
        $this->audioStreamingService = new AudioStreamingService();
        
        // Setup storage for testing
        Storage::fake('local');
    }
    
    public function test_get_audio_url_returns_correct_route()
    {
        // Create test data
        $chapter = Chapter::factory()->create();
        $hadith = Hadith::factory()->create(['chapter_id' => $chapter->id]);
        $audioFile = AudioFile::factory()->create(['hadith_id' => $hadith->id]);
        
        // Get audio URL
        $url = $this->audioStreamingService->getAudioUrl($audioFile);
        
        // Assert that URL contains the correct route
        $this->assertStringContainsString('/audio/' . $audioFile->id . '/stream', $url);
    }
    
    public function test_get_audio_metadata_returns_correct_data()
    {
        // Create test data
        $chapter = Chapter::factory()->create();
        $hadith = Hadith::factory()->create(['chapter_id' => $chapter->id]);
        $audioFile = AudioFile::factory()->create(['hadith_id' => $hadith->id]);
        
        // Get audio metadata
        $metadata = $this->audioStreamingService->getAudioMetadata($audioFile);
        
        // Assert metadata contains correct data
        $this->assertEquals($audioFile->id, $metadata['id']);
        $this->assertEquals($audioFile->duration, $metadata['duration']);
        $this->assertEquals($audioFile->file_size, $metadata['file_size']);
        $this->assertEquals($audioFile->hadith_id, $metadata['hadith_id']);
        $this->assertStringContainsString('/audio/' . $audioFile->id . '/stream', $metadata['url']);
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
        
        $request = new Request();
        
        // Expect exception when file doesn't exist
        $this->expectException(\Symfony\Component\HttpKernel\Exception\NotFoundHttpException::class);
        
        // Try to stream non-existent file
        $this->audioStreamingService->streamAudio($audioFile, $request);
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
        
        // Create request with range header
        $request = new Request();
        $request->headers->set('Range', 'bytes=0-4');
        
        // Stream audio
        $response = $this->audioStreamingService->streamAudio($audioFile, $request);
        
        // Assert response status is 206 (Partial Content)
        $this->assertEquals(206, $response->getStatusCode());
        
        // Assert response headers
        $this->assertEquals('bytes 0-4/' . strlen($fakeAudioContent), $response->headers->get('Content-Range'));
        $this->assertEquals('bytes', $response->headers->get('Accept-Ranges'));
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
        
        // Create request without range header
        $request = new Request();
        
        // Stream audio
        $response = $this->audioStreamingService->streamAudio($audioFile, $request);
        
        // Assert response status is 200 (OK)
        $this->assertEquals(200, $response->getStatusCode());
        
        // Assert response headers
        $fileSize = strlen($fakeAudioContent);
        $this->assertEquals("bytes 0-" . ($fileSize - 1) . "/$fileSize", $response->headers->get('Content-Range'));
        $this->assertEquals('bytes', $response->headers->get('Accept-Ranges'));
    }
}