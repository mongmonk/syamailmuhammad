<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AuditPageController extends Controller
{
    public function __construct()
    {
        // Pertahanan berlapis (route group juga sudah memakai ['auth','ensure.active','role.admin'])
        $this->middleware(['auth', 'ensure.active', 'role.admin']);
    }

    /**
     * Tampilkan halaman Audit Logs dengan filter dan paginasi (SSR).
     */
    public function index(Request $request)
    {
        // Ambil dan sanitasi parameter filter
        $actorParam = $request->input('actor');
        $actorId = $actorParam !== null
            ? filter_var($actorParam, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]])
            : null;

        $action = $this->sanitizeString($request->input('action'), 100);
        $resourceType = $this->sanitizeString($request->input('resource_type'), 100);
        $resourceId = $this->sanitizeString($request->input('resource_id'), 100);
        $status = $request->input('status');
        $reason = $this->sanitizeString($request->input('reason'), 100);
        $from = $this->sanitizeDateString($request->input('from'));
        $to = $this->sanitizeDateString($request->input('to'));

        $query = AuditLog::query()
            ->with(['actor:id,name'])
            ->orderByDesc('id');

        // Terapkan scopes untuk konsistensi dan reuse
        $query
            ->byActor($actorId)
            ->byAction($action)
            ->byResource($resourceType, $resourceId)
            ->byStatus($status)
            ->byReason($reason)
            ->betweenDates($from, $to);

        $logs = $query->paginate(20)->appends($request->query());

        // Kirim nilai filter ke view untuk mempertahankan input
        $filters = [
            'actor' => $actorId,
            'action' => $action,
            'resource_type' => $resourceType,
            'resource_id' => $resourceId,
            'status' => $status,
            'reason' => $reason,
            'from' => $from,
            'to' => $to,
        ];

        // Logging akses halaman index audit
        Log::channel('security')->info('Admin audit index accessed', [
            'user_id' => $request->user()?->id,
            'route' => 'admin.audit.index',
            'filters' => $filters,
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return view('admin.audit.index', compact('logs', 'filters'));
    }

    /**
     * Ekspor Audit Logs ke CSV berdasarkan filter yang sama.
     */
    public function export(Request $request): StreamedResponse
    {
        // Ambil dan sanitasi parameter filter (selaras dengan index)
        $actorParam = $request->input('actor');
        $actorId = $actorParam !== null
            ? filter_var($actorParam, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]])
            : null;

        $action = $this->sanitizeString($request->input('action'), 100);
        $resourceType = $this->sanitizeString($request->input('resource_type'), 100);
        $resourceId = $this->sanitizeString($request->input('resource_id'), 100);
        $status = $request->input('status');
        $reason = $this->sanitizeString($request->input('reason'), 100);
        $from = $this->sanitizeDateString($request->input('from'));
        $to = $this->sanitizeDateString($request->input('to'));

        $query = AuditLog::query()
            ->with(['actor:id,name'])
            ->orderBy('id'); // stabil untuk ekspor

        $query
            ->byActor($actorId)
            ->byAction($action)
            ->byResource($resourceType, $resourceId)
            ->byStatus($status)
            ->byReason($reason)
            ->betweenDates($from, $to);

        $filename = 'audit_logs_' . date('Ymd_His') . '.csv';

        // Hitung total baris dan tulis log akses export
        $total = (clone $query)->count();
        Log::channel('security')->info('Admin audit export requested', [
            'user_id' => $request->user()?->id,
            'route' => 'admin.audit.export',
            'filters' => [
                'actor' => $actorId,
                'action' => $action,
                'resource_type' => $resourceType,
                'resource_id' => $resourceId,
                'status' => $status,
                'reason' => $reason,
                'from' => $from,
                'to' => $to,
            ],
            'total' => $total,
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return response()->streamDownload(function () use ($query) {
            $out = fopen('php://output', 'w');

            // Header CSV
            fputcsv($out, [
                'id',
                'actor_id',
                'actor_name',
                'action',
                'resource_type',
                'resource_id',
                'status',
                'reason_code',
                'ip',
                'user_agent',
                'redacted_meta',
                'created_at',
            ]);

            // Stream per chunk agar hemat memori
            $query->chunk(500, function ($rows) use ($out) {
                foreach ($rows as $log) {
                    fputcsv($out, [
                        $log->id,
                        $log->actor_id,
                        optional($log->actor)->name,
                        $log->action,
                        $log->resource_type,
                        $log->resource_id,
                        $log->status,
                        $log->reason_code,
                        $log->ip,
                        $log->user_agent,
                        $log->redacted_meta ? json_encode($log->redacted_meta) : '',
                        optional($log->created_at)->format('Y-m-d H:i:s'),
                    ]);
                }
            });

            fflush($out);
            fclose($out);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    /**
     * Batasi string dan hilangkan spasi berlebih.
     */
    private function sanitizeString(?string $value, int $max = 255): ?string
    {
        if ($value === null) {
            return null;
        }
        $trimmed = trim($value);
        if ($trimmed === '') {
            return null;
        }
        return mb_substr($trimmed, 0, $max);
    }

    /**
     * Validasi string tanggal sederhana (biarkan fleksibel: tanggal atau datetime).
     * Pengguna dapat memasok "YYYY-MM-DD" atau "YYYY-MM-DD HH:MM[:SS]".
     */
    private function sanitizeDateString(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }
        $trimmed = trim($value);
        if ($trimmed === '') {
            return null;
        }
        // Tidak melakukan parsing ketat; serahkan ke query builder.
        // Tetap batasi panjang agar aman.
        return mb_substr($trimmed, 0, 32);
    }
}