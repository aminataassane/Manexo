<?php

namespace App\Enums;

enum PlatformRole: string
{
    case SuperAdmin = 'super_admin';
    case PlatformAdmin = 'platform_admin';
    case PlatformObserver = 'platform_observer';

    public function label(): string
    {
        return match ($this) {
            self::SuperAdmin => __('platform_invitations.role_super_admin'),
            self::PlatformAdmin => __('platform_invitations.role_platform_admin'),
            self::PlatformObserver => __('platform_invitations.role_platform_observer'),
        };
    }

    /**
     * Can manage organizations, users, and support sessions (super_admin + platform_admin).
     */
    public function canManage(): bool
    {
        return in_array($this, [self::SuperAdmin, self::PlatformAdmin], true);
    }

    /**
     * Can administer platform-level settings, invitations, security, monitoring, backups (super_admin only).
     */
    public function canAdminister(): bool
    {
        return $this === self::SuperAdmin;
    }

    /**
     * Roles that can be invited by a super_admin.
     *
     * @return list<self>
     */
    public static function invitableRoles(): array
    {
        return [
            self::PlatformAdmin,
            self::PlatformObserver,
        ];
    }
}
