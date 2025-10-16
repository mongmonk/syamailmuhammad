<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Database\Factories\ChapterFactory;
use App\Traits\SanitizesSaw;

class Chapter extends Model
{
    use HasFactory;
    use SanitizesSaw;

    protected $fillable = [
        'title',
        'description',
        'chapter_number',
    ];

    /**
     * Daftar atribut yang akan otomatis disanitasi:
     * - Mengganti "Nabi|Muhammad|Rasulullah SAW" menjadi "Nabi|Muhammad|Rasulullah ﷺ"
     */
    protected array $sawSanitize = [
        'title',
        'description',
    ];

    public function hadiths(): HasMany
    {
        return $this->hasMany(Hadith::class);
    }
}
