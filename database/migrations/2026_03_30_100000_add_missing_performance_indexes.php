<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ticket_messages: user participation checks in whereUserParticipates() scope
        Schema::table('ticket_messages', function (Blueprint $table) {
            if (! $this->hasIndex('ticket_messages', 'ticket_messages_user_ticket_idx')) {
                $table->index(['user_id', 'ticket_id'], 'ticket_messages_user_ticket_idx');
            }
        });

        // ticket_assignees: responsible/collaborator filtering by role
        Schema::table('ticket_assignees', function (Blueprint $table) {
            if (! $this->hasIndex('ticket_assignees', 'ticket_assignees_ticket_role_idx')) {
                $table->index(['ticket_id', 'role'], 'ticket_assignees_ticket_role_idx');
            }
        });

        // users: status filtering for assignableInOrganization() scope
        Schema::table('users', function (Blueprint $table) {
            if (! $this->hasIndex('users', 'users_status_idx')) {
                $table->index(['status'], 'users_status_idx');
            }
        });

        // discussion_messages: user participation lookup
        Schema::table('discussion_messages', function (Blueprint $table) {
            if (! $this->hasIndex('discussion_messages', 'discussion_messages_user_thread_idx')) {
                $table->index(['user_id', 'thread_id'], 'discussion_messages_user_thread_idx');
            }
        });

        // discussion_threads: sorting by recency per organization
        Schema::table('discussion_threads', function (Blueprint $table) {
            if (! $this->hasIndex('discussion_threads', 'discussion_threads_org_updated_idx')) {
                $table->index(['organization_id', 'updated_at'], 'discussion_threads_org_updated_idx');
            }
        });

        // organization_memberships: function-based member lookups
        Schema::table('organization_memberships', function (Blueprint $table) {
            if (! $this->hasIndex('organization_memberships', 'org_memberships_org_function_idx')) {
                $table->index(['organization_id', 'organization_function_id'], 'org_memberships_org_function_idx');
            }
        });

        // organization_memberships: role-based filtering (assignable users)
        Schema::table('organization_memberships', function (Blueprint $table) {
            if (! $this->hasIndex('organization_memberships', 'org_memberships_org_role_idx')) {
                $table->index(['organization_id', 'role'], 'org_memberships_org_role_idx');
            }
        });
    }

    public function down(): void
    {
        $indexes = [
            'ticket_messages' => 'ticket_messages_user_ticket_idx',
            'ticket_assignees' => 'ticket_assignees_ticket_role_idx',
            'users' => 'users_status_idx',
            'discussion_messages' => 'discussion_messages_user_thread_idx',
            'discussion_threads' => 'discussion_threads_org_updated_idx',
            'organization_memberships' => ['org_memberships_org_function_idx', 'org_memberships_org_role_idx'],
        ];

        foreach ($indexes as $table => $names) {
            Schema::table($table, function (Blueprint $t) use ($table, $names) {
                foreach ((array) $names as $name) {
                    if ($this->hasIndex($table, $name)) {
                        $t->dropIndex($name);
                    }
                }
            });
        }
    }

    private function hasIndex(string $table, string $indexName): bool
    {
        return collect(Schema::getIndexes($table))->contains(fn ($idx) => $idx['name'] === $indexName);
    }
};
