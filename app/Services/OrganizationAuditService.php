<?php

namespace App\Services;

use App\Models\OrganizationAuditLog;

class OrganizationAuditService
{
    /**
     * Log an action within the current organization context.
     */
    public static function log(
        string $action,
        ?string $targetType = null,
        ?int $targetId = null,
        ?array $metadata = null,
    ): OrganizationAuditLog {
        $orgId = (int) session('current_organization_id');

        return OrganizationAuditLog::create([
            'organization_id' => $orgId,
            'user_id' => auth()->id(),
            'action' => $action,
            'target_type' => $targetType,
            'target_id' => $targetId,
            'metadata' => $metadata,
            'ip_address' => request()->ip(),
            'created_at' => now(),
        ]);
    }
}
