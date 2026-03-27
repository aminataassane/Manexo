<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            // Covers: org + archived filter + updated_at sort (main ticket list)
            $table->index(['organization_id', 'archived_at', 'updated_at'], 'tickets_org_archived_updated_idx');
            // Covers: org + status + created_at (reports, counters)
            $table->index(['organization_id', 'status', 'created_at'], 'tickets_org_status_created_idx');
            // Covers: org + group filter
            $table->index(['organization_id', 'ticket_group_id'], 'tickets_org_group_idx');
            // Covers: SLA queries
            $table->index(['organization_id', 'sla_policy_id'], 'tickets_org_sla_idx');
        });

        Schema::table('ticket_assignees', function (Blueprint $table) {
            if (! $this->hasIndex('ticket_assignees', 'ticket_assignees_user_ticket_idx')) {
                $table->index(['user_id', 'ticket_id'], 'ticket_assignees_user_ticket_idx');
            }
        });

        Schema::table('ticket_participants', function (Blueprint $table) {
            if (! $this->hasIndex('ticket_participants', 'ticket_participants_user_ticket_idx')) {
                $table->index(['user_id', 'ticket_id'], 'ticket_participants_user_ticket_idx');
            }
        });

        Schema::table('ticket_checklist_items', function (Blueprint $table) {
            if (! $this->hasIndex('ticket_checklist_items', 'checklist_items_ticket_done_idx')) {
                $table->index(['ticket_id', 'is_done'], 'checklist_items_ticket_done_idx');
            }
        });

        Schema::table('organization_memberships', function (Blueprint $table) {
            if (! $this->hasIndex('organization_memberships', 'org_memberships_org_user_idx')) {
                $table->index(['organization_id', 'user_id'], 'org_memberships_org_user_idx');
            }
        });
    }

    public function down(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->dropIndex('tickets_org_archived_updated_idx');
            $table->dropIndex('tickets_org_status_created_idx');
            $table->dropIndex('tickets_org_group_idx');
            $table->dropIndex('tickets_org_sla_idx');
        });

        Schema::table('ticket_assignees', function (Blueprint $table) {
            if ($this->hasIndex('ticket_assignees', 'ticket_assignees_user_ticket_idx')) {
                $table->dropIndex('ticket_assignees_user_ticket_idx');
            }
        });

        Schema::table('ticket_participants', function (Blueprint $table) {
            if ($this->hasIndex('ticket_participants', 'ticket_participants_user_ticket_idx')) {
                $table->dropIndex('ticket_participants_user_ticket_idx');
            }
        });

        Schema::table('ticket_checklist_items', function (Blueprint $table) {
            if ($this->hasIndex('ticket_checklist_items', 'checklist_items_ticket_done_idx')) {
                $table->dropIndex('checklist_items_ticket_done_idx');
            }
        });

        Schema::table('organization_memberships', function (Blueprint $table) {
            if ($this->hasIndex('organization_memberships', 'org_memberships_org_user_idx')) {
                $table->dropIndex('org_memberships_org_user_idx');
            }
        });
    }

    private function hasIndex(string $table, string $indexName): bool
    {
        return collect(Schema::getIndexes($table))->contains(fn ($idx) => $idx['name'] === $indexName);
    }
};
