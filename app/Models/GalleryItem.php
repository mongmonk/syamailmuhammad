<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Facades\Storage;

class GalleryItem extends Model
{
    use HasFactory;

    protected $table = 'gallery_items';

    protected $fillable = [
        'slug',
        'original_filename',
        'mime',
        'original_width',
        'original_height',
        'variants',
        'caption',
        'alt_text',
        'tags',
        'is_active',
        'published_at',
    ];

    protected $casts = [
        'variants' => 'array',
        'tags' => 'array',
        'is_active' => 'boolean',
        'published_at' => 'datetime',
    ];

    /**
     * Scope item aktif.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope item published (published_at <= now()).
     */
    public function scopePublished($query)
    {
        return $query
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now());
    }

    /**
     * Ambil path varian berdasarkan ukuran (thumb|medium|large|max).
     */
    public function variantPath(string $size): ?string
    {
        $variants = $this->variants ?? [];
        return $variants[$size] ?? null;
    }

    /**
     * Ambil URL publik varian berdasarkan ukuran (disk 'public').
     */
    public function variantUrl(string $size): ?string
    {
        $path = $this->variantPath($size);
        return $path ? Storage::disk('public')->url($path) : null;
    }

    /**
     * Accessor alt text dengan fallback ke caption jika kosong.
     */
    public function altText(): Attribute
    {
        return Attribute::make(
            get: fn ($value, $attributes) => $value ?: ($attributes['caption'] ?? null),
        );
    }

    /**
     * Event: set alt_text default sama dengan caption bila kosong.
     */
    protected static function booted(): void
    {
        static::saving(function (self $item): void {
            $rawAlt = $item->getAttribute('alt_text');
            $rawCaption = $item->getAttribute('caption');
            if (empty($rawAlt) && !empty($rawCaption)) {
                $item->setAttribute('alt_text', $rawCaption);
            }
        });
    }
}