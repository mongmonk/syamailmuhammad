<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuditLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'actor_id',
        'action',
        'resource_type',
        'resource_id',
        'status',
        'reason_code',
        'redacted_meta',
        'ip',
        'user_agent',
    ];

    protected $casts = [
        'redacted_meta' => 'array',
    ];

    public function actor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'actor_id');
    }

    // Scopes untuk filter umum pada halaman Audit
    public function scopeByActor($query, ?int $actorId)
    {
        if ($actorId) {
            $query->where('actor_id', $actorId);
        }
        return $query;
    }

    public function scopeByAction($query, ?string $action)
    {
        if (!empty($action)) {
            $query->where('action', $action);
        }
        return $query;
    }

    public function scopeByResource($query, ?string $type, ?string $id)
    {
        if (!empty($type)) {
            $query->where('resource_type', $type);
        }
        if (!empty($id)) {
            $query->where('resource_id', $id);
        }
        return $query;
    }

    public function scopeByStatus($query, ?string $status)
    {
        if (in_array($status, ['allow', 'deny'], true)) {
            $query->where('status', $status);
        }
        return $query;
    }

    public function scopeByReason($query, ?string $reasonCode)
    {
        if (!empty($reasonCode)) {
            $query->where('reason_code', $reasonCode);
        }
        return $query;
    }

    public function scopeBetweenDates($query, ?string $from, ?string $to)
    {
        if (!empty($from)) {
            $query->where('created_at', '>=', $from);
        }
        if (!empty($to)) {
            $query->where('created_at', '<=', $to);
        }
        return $query;
    }
}