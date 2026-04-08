<?php

namespace Tests\Unit\Permissions;

use App\Enums\OrganizationRole;
use App\Enums\Permission;
use PHPUnit\Framework\TestCase;

class PermissionDefaultsTest extends TestCase
{
    public function test_owner_defaults_include_all_permissions(): void
    {
        $defaults = Permission::defaultsForRole(OrganizationRole::Owner);

        $this->assertCount(count(Permission::cases()), $defaults);
        foreach (Permission::cases() as $permission) {
            $this->assertContains($permission, $defaults, $permission->value);
        }
    }

    public function test_admin_defaults_exclude_delete_org(): void
    {
        $defaults = Permission::defaultsForRole(OrganizationRole::Admin);

        $this->assertNotContains(Permission::SettingsDeleteOrg, $defaults);
        $this->assertContains(Permission::SettingsManageRoles, $defaults);
    }

    public function test_member_defaults_are_limited(): void
    {
        $defaults = Permission::defaultsForRole(OrganizationRole::Member);

        $this->assertContains(Permission::TicketsCreate, $defaults);
        $this->assertNotContains(Permission::TicketsAssign, $defaults);
        $this->assertNotContains(Permission::SettingsManageRoles, $defaults);
    }

    public function test_agent_defaults_include_ticket_management(): void
    {
        $defaults = Permission::defaultsForRole(OrganizationRole::Agent);

        $this->assertContains(Permission::TicketsViewAll, $defaults);
        $this->assertContains(Permission::TicketsAssign, $defaults);
        $this->assertContains(Permission::DiscussionsViewInternalNotes, $defaults);
    }
}
