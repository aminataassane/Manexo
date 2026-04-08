<?php

namespace Tests\Unit\Domain;

use App\Enums\PlatformRole;
use App\Models\User;
use PHPUnit\Framework\TestCase;

class UserPlatformAccessTest extends TestCase
{
    public function test_has_platform_access_when_platform_role_set(): void
    {
        $user = new User;
        $user->platform_role = PlatformRole::PlatformObserver;

        $this->assertTrue($user->hasPlatformAccess());
    }

    public function test_has_platform_access_when_super_admin_flag(): void
    {
        $user = new User;
        $user->is_super_admin = true;

        $this->assertTrue($user->hasPlatformAccess());
    }

    public function test_can_platform_manage_for_super_admin_and_platform_admin(): void
    {
        $super = new User;
        $super->is_super_admin = true;
        $this->assertTrue($super->canPlatformManage());

        $admin = new User;
        $admin->platform_role = PlatformRole::PlatformAdmin;
        $this->assertTrue($admin->canPlatformManage());

        $observer = new User;
        $observer->platform_role = PlatformRole::PlatformObserver;
        $this->assertFalse($observer->canPlatformManage());
    }

    public function test_can_platform_administer_only_super_admin_role_or_flag(): void
    {
        $super = new User;
        $super->platform_role = PlatformRole::SuperAdmin;
        $this->assertTrue($super->canPlatformAdminister());

        $admin = new User;
        $admin->platform_role = PlatformRole::PlatformAdmin;
        $this->assertFalse($admin->canPlatformAdminister());

        $legacy = new User;
        $legacy->is_super_admin = true;
        $this->assertTrue($legacy->canPlatformAdminister());
    }
}
