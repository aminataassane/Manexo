<?php

namespace App\Policies;

use App\Enums\Permission;
use App\Models\Organization;
use App\Models\User;

class OrganizationPolicy
{
    public function manageSettings(User $user, Organization $organization): bool
    {
        return $user->hasPermission(Permission::SettingsManageBranding)
            || $user->hasPermission(Permission::SettingsManageCategories)
            || $user->hasPermission(Permission::SettingsManagePriorities)
            || $user->hasPermission(Permission::SettingsManageFunctions)
            || $user->hasPermission(Permission::SettingsManageRoles);
    }

    public function manageTeam(User $user, Organization $organization): bool
    {
        return $user->hasAnyPermission([
            Permission::TeamInvite,
            Permission::TeamEditRole,
            Permission::TeamRemove,
        ]);
    }

    public function manageBranding(User $user, Organization $organization): bool
    {
        return $user->hasPermission(Permission::SettingsManageBranding);
    }

    public function deleteOrganization(User $user, Organization $organization): bool
    {
        return $user->hasPermission(Permission::SettingsDeleteOrg);
    }
}
