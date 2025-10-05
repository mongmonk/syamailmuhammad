<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AuditLogger
{
    /**
     * Tulis satu entri audit.
     *
     * @param string $action        Nama aksi, mis. "search", "note.store", "admin.user.update"
     * @param string $status        "allow" atau "deny"
     * @param string|null $resourceType  Jenis resource, mis. "hadith", "user"
     * @param string|null $resourceId    ID resource, mis. "42"
     * @param string|null $reasonCode    Kode alasan kebijakan, mis. "POLICY_X" (opsional)
     * @param array|null $redactedMeta   Metadata non-sensitif (akan dicast json)
     * @param Request|null $request      Request untuk mengambil IP dan User-Agent (opsional)
     */
    public function log(
        string $action,
        string $status,
        ?string $resourceType = null,
        ?string $resourceId = null,
        ?string $reasonCode = null,
        ?array $redactedMeta = null,
        ?Request $request = null
    ): void {
        // Normalisasi status
        $status = $status === 'deny' ? 'deny' : 'allow';

        try {
            $req = $request ?? request();

            $actorId = optional(Auth::user())->id;
            $ip = $req ? $req->ip() : null;
            $userAgent = $req ? $req->userAgent() : null;

            AuditLog::create([
                'actor_id' => $actorId,
                'action' => mb_substr($action, 0, 100),
                'resource_type' => $resourceType ? mb_substr($resourceType, 0, 100) : null,
                'resource_id' => $resourceId ? mb_substr($resourceId, 0, 100) : null,
                'status' => $status,
                'reason_code' => $reasonCode ? mb_substr($reasonCode, 0, 100) : null,
                'redacted_meta' => $redactedMeta,
                'ip' => $ip,
                'user_agent' => $userAgent ? mb_substr($userAgent, 0, 512) : null,
            ]);
        } catch (\Throwable $e) {
            Log::channel('security')->error('Audit log write failed', [
                'error' => $e->getMessage(),
                'action' => $action,
                'status' => $status,
                'resource_type' => $resourceType,
                'resource_id' => $resourceId,
                'reason_code' => $reasonCode,
            ]);
        }
    }

    /**
     * Shortcut untuk status "allow".
     */
    public function allow(
        string $action,
        ?string $resourceType = null,
        ?string $resourceId = null,
        ?string $reasonCode = null,
        ?array $redactedMeta = null,
        ?Request $request = null
    ): void {
        $this->log($action, 'allow', $resourceType, $resourceId, $reasonCode, $redactedMeta, $request);
    }

    /**
     * Shortcut untuk status "deny".
     */
    public function deny(
        string $action,
        ?string $resourceType = null,
        ?string $resourceId = null,
        ?string $reasonCode = null,
        ?array $redactedMeta = null,
        ?Request $request = null
    ): void {
        $this->log($action, 'deny', $resourceType, $resourceId, $reasonCode, $redactedMeta, $request);
    }
}