<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class StorageProxyController extends Controller
{
    /**
     * Proxy file dari disk 'public' agar dapat diakses tanpa bergantung pada symlink OS/webserver.
     * Keamanan:
     * - Hanya izinkan path di bawah 'gallery/' (blokir 'private/' dan traversal).
     * - Hanya baca dari disk publik.
     * Caching:
     * - Cache-Control panjang (1 tahun) + Last-Modified, dukung If-Modified-Since.
     */
    public function serve(string $path, Request $request)
    {
        $path = ltrim($path, '/');

        // Blokir path tidak valid
        if ($path === '' || str_contains($path, '..') || str_starts_with($path, 'private/')) {
            abort(403);
        }

        // Batasi hanya ke folder galeri (hindari ekspos selain varian gambar)
        if (!str_starts_with($path, 'gallery/')) {
            abort(403);
        }

        $disk = Storage::disk('public');

        if (!$disk->exists($path)) {
            abort(404);
        }

        // Ambil metadata
        try {
            $mime = $disk->mimeType($path) ?: 'application/octet-stream';
        } catch (\Throwable $e) {
            $mime = 'application/octet-stream';
        }

        try {
            $lastModified = $disk->lastModified($path);
        } catch (\Throwable $e) {
            $lastModified = time();
        }

        // If-Modified-Since handling
        $ifModifiedSince = $request->headers->get('If-Modified-Since');
        if ($ifModifiedSince) {
            $since = strtotime($ifModifiedSince);
            if ($since !== false && $since >= $lastModified) {
                return response('', 304, [
                    'Cache-Control' => 'public, max-age=31536000, immutable',
                    'Last-Modified' => gmdate('D, d M Y H:i:s', $lastModified) . ' GMT',
                ]);
            }
        }

        // Stream konten agar hemat memori
        $stream = $disk->readStream($path);
        if ($stream === false) {
            abort(500, 'Gagal membaca berkas.');
        }

        return response()->stream(function () use ($stream) {
            fpassthru($stream);
            if (is_resource($stream)) {
                fclose($stream);
            }
        }, 200, [
            'Content-Type' => $mime,
            'Cache-Control' => 'public, max-age=31536000, immutable',
            'Last-Modified' => gmdate('D, d M Y H:i:s', $lastModified) . ' GMT',
            'X-Accel-Buffering' => 'no',
        ]);
    }
}