<?php

namespace App\Traits;

use App\Models\Scopes\OrganizationScope;
use Illuminate\Database\Eloquent\Model;

/**
 * Trait for models that belong to an organization.
 *
 * - Boots the OrganizationScope (auto WHERE organization_id = session org)
 * - Auto-fills organization_id on creating if not already set
 * - Provides scopeWithoutOrganizationScope() for platform-admin cross-org queries
 */
trait BelongsToOrganization
{
    public static function bootBelongsToOrganization(): void
    {
        static::addGlobalScope(new OrganizationScope());

        static::creating(function (Model $model) {
            if (empty($model->organization_id)) {
                $orgId = session('current_organization_id');
                if ($orgId !== null) {
                    $model->organization_id = $orgId;
                }
            }
        });
    }

    /**
     * Query without the organization scope (for platform admin / cross-org queries).
     */
    public static function withoutOrganizationScope()
    {
        return static::withoutGlobalScope(OrganizationScope::class);
    }
}
