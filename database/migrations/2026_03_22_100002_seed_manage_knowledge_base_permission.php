<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $now = now();
        $orgIds = DB::table('organizations')->pluck('id');

        $rows = [];
        foreach ($orgIds as $orgId) {
            foreach (['owner', 'admin'] as $role) {
                $rows[] = [
                    'organization_id' => $orgId,
                    'role' => $role,
                    'permission' => 'settings.manage_knowledge_base',
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
        }

        if (! empty($rows)) {
            DB::table('organization_role_permissions')->upsert(
                $rows,
                ['organization_id', 'role', 'permission'],
                ['updated_at'],
            );
        }
    }

    public function down(): void
    {
        DB::table('organization_role_permissions')
            ->where('permission', 'settings.manage_knowledge_base')
            ->delete();
    }
};
