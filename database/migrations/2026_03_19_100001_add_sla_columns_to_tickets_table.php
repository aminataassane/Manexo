<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->foreignId('sla_policy_id')->nullable()->constrained('sla_policies')->nullOnDelete();
            $table->timestamp('sla_first_response_deadline')->nullable();
            $table->timestamp('sla_resolution_deadline')->nullable();
            $table->timestamp('sla_first_response_met_at')->nullable();
            $table->timestamp('sla_resolution_met_at')->nullable();
            $table->boolean('sla_first_response_breached')->default(false);
            $table->boolean('sla_resolution_breached')->default(false);
            $table->timestamp('sla_paused_at')->nullable();
            $table->unsignedInteger('sla_paused_seconds')->default(0);
        });

        // Partial index for first response deadline (unresolved tickets)
        DB::statement('
            CREATE INDEX idx_tickets_sla_first_response
            ON tickets (organization_id, sla_first_response_deadline)
            WHERE sla_first_response_deadline IS NOT NULL
              AND sla_first_response_met_at IS NULL
              AND sla_first_response_breached = false
              AND deleted_at IS NULL
        ');

        // Partial index for resolution deadline (unresolved tickets)
        DB::statement('
            CREATE INDEX idx_tickets_sla_resolution
            ON tickets (organization_id, sla_resolution_deadline)
            WHERE sla_resolution_deadline IS NOT NULL
              AND sla_resolution_met_at IS NULL
              AND sla_resolution_breached = false
              AND deleted_at IS NULL
        ');
    }

    public function down(): void
    {
        DB::statement('DROP INDEX IF EXISTS idx_tickets_sla_resolution');
        DB::statement('DROP INDEX IF EXISTS idx_tickets_sla_first_response');

        Schema::table('tickets', function (Blueprint $table) {
            $table->dropConstrainedForeignId('sla_policy_id');
            $table->dropColumn([
                'sla_first_response_deadline',
                'sla_resolution_deadline',
                'sla_first_response_met_at',
                'sla_resolution_met_at',
                'sla_first_response_breached',
                'sla_resolution_breached',
                'sla_paused_at',
                'sla_paused_seconds',
            ]);
        });
    }
};
