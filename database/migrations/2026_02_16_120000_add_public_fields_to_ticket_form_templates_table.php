<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ticket_form_templates', function (Blueprint $table) {
            $table->boolean('is_public')->default(false)->after('is_active');
            $table->string('public_slug', 140)->nullable()->after('is_public');
            $table->string('public_title', 160)->nullable()->after('public_slug');
            $table->text('public_description')->nullable()->after('public_title');
            $table->text('public_thank_you')->nullable()->after('public_description');

            $table->unique(['public_slug'], 'tft_public_slug_unique');
            $table->index(['organization_id', 'is_public'], 'tft_org_public_idx');
        });
    }

    public function down(): void
    {
        Schema::table('ticket_form_templates', function (Blueprint $table) {
            $table->dropIndex('tft_org_public_idx');
            $table->dropUnique('tft_public_slug_unique');
            $table->dropColumn(['is_public', 'public_slug', 'public_title', 'public_description', 'public_thank_you']);
        });
    }
};

