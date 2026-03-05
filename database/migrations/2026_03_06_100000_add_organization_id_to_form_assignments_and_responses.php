<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add organization_id to form_assignments
        Schema::table('form_assignments', function (Blueprint $table) {
            $table->foreignId('organization_id')
                ->nullable()
                ->after('id')
                ->constrained('organizations')
                ->cascadeOnDelete();

            $table->index('organization_id');
        });

        // Backfill from forms table
        DB::statement('
            UPDATE form_assignments
            SET organization_id = forms.organization_id
            FROM forms
            WHERE form_assignments.form_id = forms.id
              AND form_assignments.organization_id IS NULL
        ');

        // Make non-nullable after backfill
        Schema::table('form_assignments', function (Blueprint $table) {
            $table->foreignId('organization_id')->nullable(false)->change();
        });

        // Add organization_id to form_responses
        Schema::table('form_responses', function (Blueprint $table) {
            $table->foreignId('organization_id')
                ->nullable()
                ->after('id')
                ->constrained('organizations')
                ->cascadeOnDelete();

            $table->index('organization_id');
        });

        // Backfill from forms table
        DB::statement('
            UPDATE form_responses
            SET organization_id = forms.organization_id
            FROM forms
            WHERE form_responses.form_id = forms.id
              AND form_responses.organization_id IS NULL
        ');

        // Make non-nullable after backfill
        Schema::table('form_responses', function (Blueprint $table) {
            $table->foreignId('organization_id')->nullable(false)->change();
        });
    }

    public function down(): void
    {
        Schema::table('form_assignments', function (Blueprint $table) {
            $table->dropConstrainedForeignId('organization_id');
        });

        Schema::table('form_responses', function (Blueprint $table) {
            $table->dropConstrainedForeignId('organization_id');
        });
    }
};
