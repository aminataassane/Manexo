<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            // Composite index for the heavy stats/counts query:
            // WHERE organization_id = ? AND archived_at IS NULL  + FILTER on status
            $table->index(['organization_id', 'archived_at', 'status'], 'tickets_org_archived_status_idx');
        });

        Schema::table('ticket_assignees', function (Blueprint $table) {
            // Speeds up EXISTS subqueries: WHERE ticket_id = ? AND user_id = ?
            // (unique already covers this, but add explicit index on user_id for reverse lookups)
            $table->index('user_id', 'ticket_assignees_user_id_idx');
        });

        Schema::table('form_responses', function (Blueprint $table) {
            // Speeds up EXISTS subquery: WHERE ticket_id = tickets.id
            $table->index('ticket_id', 'form_responses_ticket_id_idx');
        });
    }

    public function down(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->dropIndex('tickets_org_archived_status_idx');
        });

        Schema::table('ticket_assignees', function (Blueprint $table) {
            $table->dropIndex('ticket_assignees_user_id_idx');
        });

        Schema::table('form_responses', function (Blueprint $table) {
            $table->dropIndex('form_responses_ticket_id_idx');
        });
    }
};
