<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Database\Factories\ChapterFactory;

class Chapter extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'chapter_number',
    ];

    public function hadiths(): HasMany
    {
        return $this->hasMany(Hadith::class);
    }
}
