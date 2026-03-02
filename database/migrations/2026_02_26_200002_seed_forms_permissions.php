<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $now = now();

        // For each org, seed the new forms permissions for owner + admin (all 3) and agent (view_responses only)
        $orgIds = DB::table('organizations')->pluck('id');

        $rows = [];
        foreach ($orgIds as $orgId) {
            // Owner gets all (already via "all cases" logic, but seed explicitly for consistency)
            foreach (['forms.manage', 'forms.assign', 'forms.view_responses'] as $perm) {
                $rows[] = [
                    'organization_id' => $orgId,
                    'role' => 'owner',
                    'permission' => $perm,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }

            // Admin gets all 3
            foreach (['forms.manage', 'forms.assign', 'forms.view_responses'] as $perm) {
                $rows[] = [
                    'organization_id' => $orgId,
                    'role' => 'admin',
                    'permission' => $perm,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }

            // Agent gets view_responses
            $rows[] = [
                'organization_id' => $orgId,
                'role' => 'agent',
                'permission' => 'forms.view_responses',
                'created_at' => $now,
                'updated_at' => $now,
            ];
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
            ->whereIn('permission', ['forms.manage', 'forms.assign', 'forms.view_responses'])
            ->delete();
    }
};
