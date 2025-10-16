<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Casts\EncryptedString;
use App\Traits\SanitizesSaw;

class UserNote extends Model
{
    use HasFactory;
    use SanitizesSaw;

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

    /**
     * Atribut yang akan otomatis disanitasi saat menyimpan:
     * - Mengganti "Nabi|Muhammad|Rasulullah SAW" menjadi "Nabi|Muhammad|Rasulullah ﷺ"
     * Catatan: Disanitasi sebelum dienkripsi oleh EncryptedString cast.
     */
    protected array $sawSanitize = [
        'note_content',
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
