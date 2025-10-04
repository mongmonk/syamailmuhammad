<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Database\Factories\HadithFactory;

class Hadith extends Model
{
    use HasFactory;

    protected $fillable = [
        'chapter_id',
        'arabic_text',
        'translation',
        'interpretation',
        'narration_source',
        'hadith_number',
    ];

    public function chapter(): BelongsTo
    {
        return $this->belongsTo(Chapter::class);
    }

    public function audioFile(): HasOne
    {
        return $this->hasOne(AudioFile::class);
    }

    public function bookmarks(): HasMany
    {
        return $this->hasMany(Bookmark::class);
    }

    public function userNotes(): HasMany
    {
        return $this->hasMany(UserNote::class);
    }
}
