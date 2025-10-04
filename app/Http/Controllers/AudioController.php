<?php

namespace App\Http\Controllers;

use App\Models\AudioFile;
use App\Services\AudioStreamingService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class AudioController extends Controller
{
    protected AudioStreamingService $audioStreamingService;

    /**
     * AudioController constructor.
     *
     * @param AudioStreamingService $audioStreamingService
     */
    public function __construct(AudioStreamingService $audioStreamingService)
    {
        $this->audioStreamingService = $audioStreamingService;
    }

    /**
     * Stream audio file with range support
     *
     * @param AudioFile $audioFile
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function stream(AudioFile $audioFile, Request $request)
    {
        return $this->audioStreamingService->streamAudio($audioFile, $request);
    }

    /**
     * Get audio URL and metadata
     *
     * @param AudioFile $audioFile
     * @return JsonResponse
     */
    public function getAudioUrl(AudioFile $audioFile): JsonResponse
    {
        $metadata = $this->audioStreamingService->getAudioMetadata($audioFile);
        
        return response()->json($metadata);
    }
}