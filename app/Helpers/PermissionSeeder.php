<?php

namespace App\Helpers;

use App\Enums\OrganizationRole;
use App\Enums\Permission;
use App\Models\OrganizationRolePermission;
use App\Models\RoleDefinition;

class PermissionSeeder
{
    /**
     * Seed default permissions for an organization (idempotent via upsert).
     */
    public static function seedForOrganization(int $orgId): void
    {
        self::seedRoleDefinitions($orgId);

        $rows = [];
        $now = now();

        foreach (OrganizationRole::cases() as $role) {
            foreach (Permission::defaultsForRole($role) as $permission) {
                $rows[] = [
                    'organization_id' => $orgId,
                    'role' => $role->value,
                    'permission' => $permission->value,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
        }

        OrganizationRolePermission::query()->upsert(
            $rows,
            ['organization_id', 'role', 'permission'],
            ['updated_at'],
        );
    }

    /**
     * Seed the 4 default role definitions for an organization (idempotent via upsert).
     */
    public static function seedRoleDefinitions(int $orgId): void
    {
        $defaults = [
            ['name' => 'Propriétaire', 'slug' => 'owner'],
            ['name' => 'Administrateur', 'slug' => 'admin'],
            ['name' => 'Agent', 'slug' => 'agent'],
            ['name' => 'Membre', 'slug' => 'member'],
        ];

        $now = now();
        $rows = [];

        foreach ($defaults as $d) {
            $rows[] = [
                'organization_id' => $orgId,
                'name' => $d['name'],
                'slug' => $d['slug'],
                'is_default' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        RoleDefinition::query()->upsert(
            $rows,
            ['organization_id', 'slug'],
            ['updated_at'],
        );
    }
}
