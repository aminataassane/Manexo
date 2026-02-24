<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('form_responses', function (Blueprint $table) {
            $table->string('respondent_name', 150)->nullable()->after('ip_address');
            $table->string('respondent_email', 255)->nullable()->after('respondent_name');
            $table->jsonb('base_fields')->nullable()->after('respondent_email');
            $table->string('submitted_from', 30)->default('internal')->after('base_fields');
            $table->string('public_form_slug', 150)->nullable()->after('submitted_from');

            $table->index('submitted_from');
            $table->index('respondent_email');
        });
    }

    public function down(): void
    {
        Schema::table('form_responses', function (Blueprint $table) {
            $table->dropIndex(['submitted_from']);
            $table->dropIndex(['respondent_email']);
            $table->dropColumn(['respondent_name', 'respondent_email', 'base_fields', 'submitted_from', 'public_form_slug']);
        });
    }
};
