<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Database\Factories\AudioFileFactory;

class AudioFile extends Model
{
    use HasFactory;

    protected $fillable = [
        'hadith_id',
        'file_path',
        'duration',
        'file_size',
    ];

    public function hadith(): BelongsTo
    {
        return $this->belongsTo(Hadith::class);
    }
}
