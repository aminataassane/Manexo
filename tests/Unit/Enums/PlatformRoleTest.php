<?php

namespace Tests\Unit\Enums;

use App\Enums\PlatformRole;
use PHPUnit\Framework\TestCase;

class PlatformRoleTest extends TestCase
{
    public function test_can_manage_only_super_and_platform_admin(): void
    {
        $this->assertTrue(PlatformRole::SuperAdmin->canManage());
        $this->assertTrue(PlatformRole::PlatformAdmin->canManage());
        $this->assertFalse(PlatformRole::PlatformObserver->canManage());
    }

    public function test_can_administer_only_super_admin(): void
    {
        $this->assertTrue(PlatformRole::SuperAdmin->canAdminister());
        $this->assertFalse(PlatformRole::PlatformAdmin->canAdminister());
        $this->assertFalse(PlatformRole::PlatformObserver->canAdminister());
    }

    public function test_invitable_roles_excludes_super_admin(): void
    {
        $invitable = PlatformRole::invitableRoles();

        $this->assertCount(2, $invitable);
        $this->assertContains(PlatformRole::PlatformAdmin, $invitable);
        $this->assertContains(PlatformRole::PlatformObserver, $invitable);
        $this->assertNotContains(PlatformRole::SuperAdmin, $invitable);
    }
}
