<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\SuperAdminAuditLog;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AuditExportController extends Controller
{
    public function __invoke(Request $request): StreamedResponse
    {
        $query = SuperAdminAuditLog::query()->with('user:id,name');

        if ($request->filled('actionFilter')) {
            $query->where('action', $request->input('actionFilter'));
        }

        if ($request->filled('targetTypeFilter')) {
            $query->where('target_type', $request->input('targetTypeFilter'));
        }

        if ($request->filled('dateFrom')) {
            $query->whereDate('created_at', '>=', $request->input('dateFrom'));
        }

        if ($request->filled('dateTo')) {
            $query->whereDate('created_at', '<=', $request->input('dateTo'));
        }

        $logs = $query->orderByDesc('created_at')->get();

        return response()->streamDownload(function () use ($logs) {
            $handle = fopen('php://output', 'w');

            // BOM for Excel UTF-8
            fwrite($handle, "\xEF\xBB\xBF");

            // Manexo branding header
            fputcsv($handle, ['Manexo — Journal d\'audit'], ';');
            fputcsv($handle, ['Exporté le ' . now()->format('d/m/Y H:i')], ';');
            fputcsv($handle, [], ';');

            fputcsv($handle, ['Date', 'Admin', 'Action', 'Target Type', 'Target ID', 'IP', 'Metadata'], ';');

            foreach ($logs as $log) {
                fputcsv($handle, [
                    $log->created_at->format('Y-m-d H:i:s'),
                    $log->user?->name ?? '',
                    $log->action,
                    $log->target_type ? class_basename($log->target_type) : '',
                    $log->target_id ?? '',
                    $log->ip_address,
                    $log->metadata ? json_encode($log->metadata, JSON_UNESCAPED_UNICODE) : '',
                ], ';');
            }

            fclose($handle);
        }, 'audit-log-' . date('Y-m-d') . '.csv', [
            'Content-Type' => 'text/csv; charset=utf-8',
        ]);
    }
}
