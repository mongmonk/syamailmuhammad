<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\SanitizesSaw;

class Post extends Model
{
    use HasFactory;
    use SanitizesSaw;

    protected $fillable = [
        'title',
        'slug',
        'body',
        'is_published',
        'created_by',
    ];

    protected $casts = [
        'is_published' => 'boolean',
    ];

    /**
     * Atribut yang akan otomatis disanitasi saat menyimpan:
     * - Mengganti "Nabi|Muhammad|Rasulullah SAW" menjadi "Nabi|Muhammad|Rasulullah ﷺ"
     */
    protected array $sawSanitize = [
        'title',
        'body',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }
}