<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ticket_assignees', function (Blueprint $table) {
            $table->string('role', 20)->default('collaborator')->after('assigned_by');
        });

        // Promote existing primary assignees to 'responsible'
        DB::statement("
            UPDATE ticket_assignees
            SET role = 'responsible'
            WHERE EXISTS (
                SELECT 1 FROM tickets
                WHERE tickets.id = ticket_assignees.ticket_id
                  AND tickets.assigned_to = ticket_assignees.user_id
            )
        ");
    }

    public function down(): void
    {
        Schema::table('ticket_assignees', function (Blueprint $table) {
            $table->dropColumn('role');
        });
    }
};
