<?php

namespace App\Services;

use App\Models\AudioFile;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AudioStreamingService
{
    /**
     * Get audio file URL with streaming support
     *
     * @param AudioFile $audioFile
     * @return string
     */
    public function getAudioUrl(AudioFile $audioFile): string
    {
        return route('audio.stream', ['audioFile' => $audioFile->id]);
    }
    
    /**
     * Generate audio file with range support for streaming
     *
     * @param AudioFile $audioFile
     * @param Request $request
     * @return StreamedResponse
     */
    public function streamAudio(AudioFile $audioFile, Request $request): StreamedResponse
    {
        $path = Storage::disk('local')->path($audioFile->file_path);
        
        if (!file_exists($path)) {
            Log::warning('Audio file not found', [
                'audio_file_id' => $audioFile->id,
                'path' => $path
            ]);
            abort(404, 'Audio file not found');
        }
        
        $fileSize = filesize($path);
        $mimeType = mime_content_type($path);

        // HTTP caching metadata
        $lastModified = filemtime($path);
        $etag = '"' . md5_file($path) . '"';

        // Conditional GET
        $ifNoneMatch = $request->header('If-None-Match');
        $ifModifiedSince = $request->header('If-Modified-Since');

        if (($ifNoneMatch && trim($ifNoneMatch) === $etag) ||
            ($ifModifiedSince && strtotime($ifModifiedSince) >= $lastModified)) {
            Log::info('Audio stream conditional 304', [
                'audio_file_id' => $audioFile->id,
                'etag' => $etag,
                'last_modified' => $lastModified,
                'if_none_match' => $ifNoneMatch,
                'if_modified_since' => $ifModifiedSince
            ]);
            return response('', 304)->withHeaders([
                'ETag' => $etag,
                'Last-Modified' => gmdate('D, d M Y H:i:s', $lastModified) . ' GMT',
                'Cache-Control' => 'public, max-age=86400, immutable',
            ]);
        }
        
        // Handle range requests for streaming
        $range = $request->header('Range');
        $start = 0;
        $end = $fileSize - 1;
        $length = $fileSize;
        $status = 200;
        
        if ($range) {
            Log::info('Audio stream range requested', [
                'audio_file_id' => $audioFile->id,
                'range_header' => $range,
                'file_size' => $fileSize
            ]);

            if (preg_match('/bytes=\h*(\d+)-(\d*)[\D.*]?/i', $range, $matches)) {
                $start = intval($matches[1]);
                if (!empty($matches[2])) {
                    $end = intval($matches[2]);
                }
                Log::info('Audio stream range parsed', [
                    'audio_file_id' => $audioFile->id,
                    'start' => $start,
                    'end' => $end,
                    'file_size' => $fileSize
                ]);
            }
            
            if ($start > $end || $start > $fileSize - 1 || $end >= $fileSize) {
                Log::warning('Audio stream invalid range', [
                    'audio_file_id' => $audioFile->id,
                    'start' => $start,
                    'end' => $end,
                    'file_size' => $fileSize
                ]);
                abort(416, 'Requested Range Not Satisfiable');
            }
            
            $length = $end - $start + 1;
            $status = 206;
        }
        
        $headers = [
            'Content-Type' => $mimeType,
            'Accept-Ranges' => 'bytes',
            'Content-Length' => $length,
            'Cache-Control' => 'public, max-age=86400, immutable',
            'ETag' => $etag,
            'Last-Modified' => gmdate('D, d M Y H:i:s', $lastModified) . ' GMT',
            // Untuk respons penuh (200 OK), Content-Range harus berakhir di fileSize-1
            'Content-Range' => $status == 206
                ? "bytes {$start}-{$end}/{$fileSize}"
                : "bytes 0-" . ($fileSize - 1) . "/{$fileSize}",
        ];
        
        Log::info('Audio stream response', [
            'audio_file_id' => $audioFile->id,
            'status' => $status,
            'content_range' => ($status == 206
                ? "bytes {$start}-{$end}/{$fileSize}"
                : "bytes 0-" . ($fileSize - 1) . "/{$fileSize}"),
            'content_length' => $length,
            'mime' => $mimeType
        ]);
        
        return response()->stream(function () use ($path, $start, $end) {
            $fp = fopen($path, 'rb');
            fseek($fp, $start);
            
            $bufferSize = 8192;
            $position = $start;
            
            while (!feof($fp) && $position <= $end) {
                $remaining = $end - $position + 1;
                $readSize = min($bufferSize, $remaining);
                
                echo fread($fp, $readSize);
                $position += $readSize;
                
                if (connection_status() != CONNECTION_NORMAL) {
                    break;
                }
                
                flush();
            }
            
            fclose($fp);
        }, $status, $headers);
    }
    
    /**
     * Get audio metadata for client
     *
     * @param AudioFile $audioFile
     * @return array
     */
    public function getAudioMetadata(AudioFile $audioFile): array
    {
        return [
            'id' => $audioFile->id,
            'url' => $this->getAudioUrl($audioFile),
            'duration' => $audioFile->duration,
            'file_size' => $audioFile->file_size,
            'hadith_id' => $audioFile->hadith_id,
        ];
    }
}