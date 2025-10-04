<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Casts\EncryptedString;

class UserNote extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'hadith_id',
        'note_content',
    ];

    protected $casts = [
        'note_content' => EncryptedString::class,
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function hadith(): BelongsTo
    {
        return $this->belongsTo(Hadith::class);
    }
}
