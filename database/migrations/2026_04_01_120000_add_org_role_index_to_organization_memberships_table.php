<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Accélère User::assignableInOrganization (filtre organization_id + role IN owner/admin/agent).
     */
    public function up(): void
    {
        Schema::table('organization_memberships', function (Blueprint $table) {
            $table->index(['organization_id', 'role'], 'organization_memberships_org_id_role_index');
        });
    }

    public function down(): void
    {
        Schema::table('organization_memberships', function (Blueprint $table) {
            $table->dropIndex('organization_memberships_org_id_role_index');
        });
    }
};
