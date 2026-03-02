<?php

namespace App\Traits;

use App\Enums\OrganizationRole;
use App\Enums\Permission;
use App\Helpers\CacheHelper;
use App\Models\OrganizationRolePermission;
use Illuminate\Support\Facades\Cache;

trait HasOrganizationPermissions
{
    /** In-memory cache per request: [orgId:role => [permission => true]] */
    private array $_permCache = [];

    /**
     * Get the user's role in the given organization.
     * Uses the middleware pivot when available to avoid extra queries.
     */
    public function orgRole(?int $orgId = null): ?string
    {
        $orgId = $orgId ?: (int) session('current_organization_id');
        if (! $orgId) {
            return null;
        }

        // Try middleware-injected pivot first (avoids DB query)
        $org = request()->attributes->get('currentOrganization');
        if ($org && (int) $org->id === $orgId && $org->pivot?->role) {
            return (string) $org->pivot->role;
        }

        $membership = $this->organizations()->whereKey($orgId)->first();

        return $membership?->pivot?->role;
    }

    /**
     * Check if the user has a specific permission in the given organization.
     */
    public function hasPermission(Permission|string $permission, ?int $orgId = null): bool
    {
        $orgId = $orgId ?: (int) session('current_organization_id');
        if (! $orgId) {
            return false;
        }

        $role = $this->orgRole($orgId);
        if (! $role) {
            return false;
        }

        // Owner always bypasses
        if ($role === OrganizationRole::Owner->value) {
            return true;
        }

        $permValue = $permission instanceof Permission ? $permission->value : $permission;
        $perms = $this->loadPermissions($orgId, $role);

        return isset($perms[$permValue]);
    }

    /**
     * Check if the user has any of the given permissions.
     *
     * @param array<Permission|string> $permissions
     */
    public function hasAnyPermission(array $permissions, ?int $orgId = null): bool
    {
        foreach ($permissions as $permission) {
            if ($this->hasPermission($permission, $orgId)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Load permissions for an org+role combination (with caching).
     *
     * @return array<string, true>
     */
    private function loadPermissions(int $orgId, string $role): array
    {
        $cacheKey = "{$orgId}:{$role}";

        if (isset($this->_permCache[$cacheKey])) {
            return $this->_permCache[$cacheKey];
        }

        $perms = Cache::remember(
            CacheHelper::rolePermissionsKey($orgId, $role),
            300,
            function () use ($orgId, $role) {
                return OrganizationRolePermission::query()
                    ->where('organization_id', $orgId)
                    ->where('role', $role)
                    ->pluck('permission')
                    ->flip()
                    ->map(fn () => true)
                    ->all();
            }
        );

        $this->_permCache[$cacheKey] = $perms;

        return $perms;
    }
}
