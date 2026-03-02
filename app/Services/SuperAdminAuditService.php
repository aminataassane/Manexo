<?php

namespace App\Services;

use App\Models\SuperAdminAuditLog;
use Illuminate\Support\Facades\Auth;

class SuperAdminAuditService
{
    public static function log(string $action, ?string $targetType = null, ?int $targetId = null, ?array $metadata = null): SuperAdminAuditLog
    {
        return SuperAdminAuditLog::create([
            'user_id' => Auth::id(),
            'action' => $action,
            'target_type' => $targetType,
            'target_id' => $targetId,
            'metadata' => $metadata,
            'ip_address' => request()->ip() ?? '0.0.0.0',
        ]);
    }
}
