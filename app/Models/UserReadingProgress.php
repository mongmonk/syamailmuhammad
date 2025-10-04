<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserReadingProgress extends Model
{
    public const TYPE_CHAPTER = 'chapter';
    public const TYPE_HADITH = 'hadith';

    protected $table = 'user_reading_progress';

    protected $fillable = [
        'user_id',
        'resource_type',
        'resource_id',
        'position',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeForResource($query, string $type, int $resourceId)
    {
        return $query->where('resource_type', $type)
                     ->where('resource_id', $resourceId);
    }
}