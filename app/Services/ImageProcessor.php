<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use RuntimeException;

class ImageProcessor
{
    /**
     * Maksimal ukuran file (bytes): 5 MB
     */
    private const MAX_BYTES = 5 * 1024 * 1024;

    /**
     * MIME type yang diizinkan.
     */
    private const ALLOWED_MIMES = [
        'image/jpeg',
        'image/png',
        'image/webp',
    ];

    /**
     * Target varian lebar (px)
     */
    private const VARIANTS = [
        'thumb'  => 320,
        'medium' => 640,
        'large'  => 1280,
        'max'    => 1920,
    ];

    /**
     * Kualitas kompresi default.
     * - JPEG: 82
     * - WEBP: 82
     */
    private const QUALITY = 82;

    private ImageManager $manager;

    public function __construct()
    {
        // Gunakan GD agar dependency minimal (Imagick opsional).
        $this->manager = new ImageManager(new Driver());
    }

    /**
     * Validasi dan proses uploaded image menjadi varian responsive.
     * - Validasi: MIME, ukuran, real mime (finfo).
     * - Proses: orientasi (jika ada), kompres, strip metadata (dengan re-encode),
     *           hasilkan varian thumb/medium/large/max tidak melebihi lebar asli.
     * - Simpan ke disk 'public' di path: gallery/YYYY/MM/{slug}/{size}.webp
     * - Kembalikan metadata & path relatif varian.
     *
     * @return array{
     *   original_filename: string,
     *   mime: string,
     *   original_width: int,
     *   original_height: int,
     *   variants: array{thumb?:string,medium?:string,large?:string,max?:string}
     * }
     *
     * @throws ValidationException
     * @throws RuntimeException
     */
    public function process(UploadedFile $file, string $slug): array
    {
        // Validasi ukuran
        if ($file->getSize() === false || $file->getSize() === null) {
            throw ValidationException::withMessages([
                'file' => 'Ukuran file tidak valid.',
            ]);
        }
        if ($file->getSize() > self::MAX_BYTES) {
            throw ValidationException::withMessages([
                'file' => 'Ukuran file melebihi 5 MB.',
            ]);
        }

        // Validasi mime (client + real)
        $clientMime = (string) $file->getClientMimeType();
        if (!in_array($clientMime, self::ALLOWED_MIMES, true)) {
            throw ValidationException::withMessages([
                'file' => 'Tipe file tidak didukung. Hanya JPG, PNG, atau WebP.',
            ]);
        }
        $realMime = $this->detectRealMime($file);
        if (!in_array($realMime, self::ALLOWED_MIMES, true)) {
            throw ValidationException::withMessages([
                'file' => 'Deteksi tipe file gagal/berbahaya. Hanya JPG, PNG, atau WebP.',
            ]);
        }

        // Baca gambar dari stream
        \Illuminate\Support\Facades\Log::info('ImageProcessor: processing upload', [
            'slug' => $slug,
            'client_mime' => $clientMime,
            'real_mime' => $realMime,
            'size_bytes' => $file->getSize(),
        ]);
        $image = $this->manager->read($file->getPathname());
        
        // Perbaiki orientasi jika metadata ada, lalu re-encode untuk strip metadata
        // Catatan: Re-encode ke format sumber dahulu sebelum membuat varian
        $format = $this->mapMimeToFormat($realMime);

        // orientasi jika ada (v3 auto-orient via ->orientate() tidak ada; gunakan rotate berbasis EXIF manual)
        // Untuk GD driver, read() akan menghasilkan gambar tanpa metadata saat re-encode.
        // Kita putar sesuai EXIF sebelum re-encode agar orientasi benar lalu metadata dibuang.
        $rotation = $this->detectOrientationRotation($file->getPathname(), $realMime);
        if ($rotation !== 0) {
            try {
                $image = $image->rotate($rotation);
                \Illuminate\Support\Facades\Log::info('ImageProcessor: applied EXIF orientation rotation', [
                    'slug' => $slug,
                    'rotation' => $rotation,
                ]);
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning('ImageProcessor: failed to rotate image for orientation', [
                    'slug' => $slug,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        // Re-encode agar metadata non-esensial terbuang
        $encoded = $this->encodeImage($image, $format, self::QUALITY);
        
        // Buat image fresh dari encoded (tanpa metadata)
        $cleanBytes = $encoded->toString();
        $image = $this->manager->read($cleanBytes);
        
        $originalWidth = $image->width();
        $originalHeight = $image->height();

        // Normalisasi nama file asli (sanitasi)
        $originalFilename = $this->sanitizeFilename($file->getClientOriginalName() ?: 'upload');

        // Siapkan folder simpan
        $baseDir = $this->baseDirForSlug($slug);

        // Generate varian sesuai batas lebar asli (no upscale)
        $variants = [];
        foreach (self::VARIANTS as $key => $targetWidth) {
            $width = min($targetWidth, $originalWidth);
            // Skip jika target tidak mengubah apa-apa dan sudah ada varian dengan lebar sama (hindari duplikat)
            if ($width <= 0) {
                continue;
            }

            // Baca ulang dari bytes bersih untuk setiap varian (hindari clone() yang tidak ada di v3)
            // Gunakan resize dengan height null untuk mempertahankan aspect ratio.
            $variantImage = $this->manager->read($cleanBytes)->resize($width, null);

            // Catat dimensi aktual setelah resize
            $variantWidth = $variantImage->width();
            $variantHeight = $variantImage->height();

            // Simpan semua varian sebagai WebP agar hemat bandwith
            $encodedVariant = $variantImage->toWebp(self::QUALITY);

            $path = $baseDir . '/' . $key . '.webp';
            $this->storeToPublic($path, $encodedVariant->toString());

            $variants[$key] = $path;

            \Illuminate\Support\Facades\Log::info('ImageProcessor: variant generated', [
                'slug' => $slug,
                'variant' => $key,
                'target_width' => $targetWidth,
                'saved_width' => $width,
                'final_width' => $variantWidth,
                'final_height' => $variantHeight,
                'path' => $path,
            ]);
        }

        // Pastikan minimal ada satu varian
        if (empty($variants)) {
            // fallback: simpan original sebagai max
            $encodedOriginalWebp = $image->toWebp(self::QUALITY);
            $path = $baseDir . '/max.webp';
            $this->storeToPublic($path, $encodedOriginalWebp->toString());
            $variants['max'] = $path;

            \Illuminate\Support\Facades\Log::warning('ImageProcessor: no variants generated, using fallback original as max', [
                'slug' => $slug,
                'original_width' => $originalWidth,
                'original_height' => $originalHeight,
                'path' => $path,
            ]);
        }

        return [
            'original_filename' => $originalFilename,
            'mime' => $realMime,
            'original_width' => $originalWidth,
            'original_height' => $originalHeight,
            'variants' => $variants,
        ];
    }

    /**
     * Hapus semua varian pada slug tertentu dari storage publik.
     */
    public function cleanupVariants(string $slug): void
    {
        $baseDir = $this->baseDirForSlug($slug);
        // Hapus seluruh direktori slug
        Storage::disk('public')->deleteDirectory($baseDir);
    }

    /**
     * Ulang proses penggantian gambar (reupload) untuk slug yang sama:
     * - bersihkan varian lama
     * - proses file baru
     */
    public function replace(UploadedFile $file, string $slug): array
    {
        $this->cleanupVariants($slug);

        return $this->process($file, $slug);
    }

    /**
     * Deteksi real MIME menggunakan finfo.
     */
    private function detectRealMime(UploadedFile $file): string
    {
        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->file($file->getPathname()) ?: '';
        return strtolower($mime);
    }

    /**
     * Pemetaan mime ke format Intervention.
     */
    private function mapMimeToFormat(string $mime): string
    {
        return match ($mime) {
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/webp' => 'webp',
            default => throw ValidationException::withMessages([
                'file' => 'Tipe file tidak didukung.',
            ]),
        };
    }

    /**
     * Encode image ke format tertentu dengan kualitas yang ditentukan.
     */
    private function encodeImage(\Intervention\Image\Interfaces\ImageInterface $image, string $format, int $quality)
    {
        return match ($format) {
            'jpg', 'jpeg' => $image->toJpg($quality),
            'png' => $image->toPng(), // PNG quality berbeda (lossless); biarkan default
            'webp' => $image->toWebp($quality),
            default => throw new RuntimeException('Format encoding tidak dikenal: ' . $format),
        };
    }

    /**
     * Simpan konten biner ke disk 'public'.
     */
    private function storeToPublic(string $path, string $binary): void
    {
        $ok = Storage::disk('public')->put($path, $binary, ['visibility' => 'public']);
        if (!$ok) {
            throw new RuntimeException('Gagal menyimpan berkas gambar: ' . $path);
        }
    }

    /**
     * Direktori dasar berdasarkan slug: gallery/YYYY/MM/{slug}
     */
    private function baseDirForSlug(string $slug): string
    {
        $y = date('Y');
        $m = date('m');
        return "gallery/{$y}/{$m}/{$slug}";
    }

    /**
     * Sanitasi nama file agar aman.
     */
    private function sanitizeFilename(string $name): string
    {
        // Hapus path jika ada
        $name = basename($name);
        // Ganti spasi & karakter non-aman
        $name = preg_replace('/[^A-Za-z0-9._-]+/', '-', $name) ?? 'file';
        // Hindari dotfiles
        $name = ltrim($name, '.');
        // Batasi panjang
        if (strlen($name) > 180) {
            $ext = pathinfo($name, PATHINFO_EXTENSION);
            $base = pathinfo($name, PATHINFO_FILENAME);
            $base = substr($base, 0, 160);
            $name = $ext ? ($base . '.' . $ext) : $base;
        }
        // Cegah double extensions berbahaya (php.jpg.php)
        $parts = explode('.', $name);
        if (count($parts) > 2) {
            $ext = array_pop($parts);
            $base = implode('-', $parts);
            $name = $base . ($ext ? ('.' . $ext) : '');
        }
        // Fallback
        if ($name === '' || $name === '.' || $name === '..') {
            $name = 'file';
        }
        return strtolower($name);
    }

    /**
     * Deteksi rotasi berdasarkan EXIF Orientation (JPEG saja).
     * Mengembalikan derajat rotasi: 0, 90, -90, 180.
     */
    private function detectOrientationRotation(string $path, string $realMime): int
    {
        try {
            if ($realMime !== 'image/jpeg') {
                return 0;
            }
            if (!function_exists('exif_read_data')) {
                return 0;
            }
            $exif = @exif_read_data($path);
            if (!$exif || empty($exif['Orientation'])) {
                return 0;
            }
            return match ((int) ($exif['Orientation'] ?? 0)) {
                3 => 180,
                6 => -90, // 6 = 90° CW -> -90 jika positif dianggap CCW
                8 => 90,  // 8 = 90° CCW
                default => 0,
            };
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('ImageProcessor: EXIF read failed', [
                'path' => $path,
                'error' => $e->getMessage(),
            ]);
            return 0;
        }
    }
}