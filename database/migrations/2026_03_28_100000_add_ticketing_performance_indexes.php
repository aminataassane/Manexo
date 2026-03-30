<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Cursor-based pagination for ticket messages (ticket_id + id DESC)
        DB::statement('
            CREATE INDEX IF NOT EXISTS idx_ticket_messages_ticket_id_id
            ON ticket_messages (ticket_id, id DESC)
        ');

        // Tickets by group + status (groups overview page with top tickets)
        DB::statement('
            CREATE INDEX IF NOT EXISTS idx_tickets_group_status
            ON tickets (ticket_group_id, status)
            WHERE archived_at IS NULL AND deleted_at IS NULL
        ');

        // Tickets by organization + status (main ticket list)
        DB::statement('
            CREATE INDEX IF NOT EXISTS idx_tickets_org_status
            ON tickets (organization_id, status)
            WHERE deleted_at IS NULL
        ');
    }

    public function down(): void
    {
        DB::statement('DROP INDEX IF EXISTS idx_ticket_messages_ticket_id_id');
        DB::statement('DROP INDEX IF EXISTS idx_tickets_group_status');
        DB::statement('DROP INDEX IF EXISTS idx_tickets_org_status');
    }
};
