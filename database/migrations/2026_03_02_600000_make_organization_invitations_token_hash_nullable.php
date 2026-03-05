<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('organization_invitations', 'token_hash')) {
            return;
        }

        // Application uses "token" column; token_hash may exist from another schema - allow NULL
        DB::statement('ALTER TABLE organization_invitations ALTER COLUMN token_hash DROP NOT NULL');
    }

    public function down(): void
    {
        if (! Schema::hasColumn('organization_invitations', 'token_hash')) {
            return;
        }

        DB::statement('ALTER TABLE organization_invitations ALTER COLUMN token_hash SET NOT NULL');
    }
};
