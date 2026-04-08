<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Speeds up ILIKE filters on ticket_messages.body (tickets index deep search) when using pg_trgm.
     */
    public function up(): void
    {
        if (DB::getDriverName() !== 'pgsql') {
            return;
        }

        DB::statement('CREATE EXTENSION IF NOT EXISTS pg_trgm');

        DB::statement('
            CREATE INDEX IF NOT EXISTS idx_ticket_messages_body_trgm
            ON ticket_messages USING gin (body gin_trgm_ops)
        ');
    }

    public function down(): void
    {
        if (DB::getDriverName() !== 'pgsql') {
            return;
        }

        DB::statement('DROP INDEX IF EXISTS idx_ticket_messages_body_trgm');
    }
};
