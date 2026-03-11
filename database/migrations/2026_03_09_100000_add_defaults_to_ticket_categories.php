<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ticket_categories', function (Blueprint $table) {
            $table->foreignId('default_ticket_group_id')->nullable()->after('is_active')
                ->constrained('ticket_groups')->nullOnDelete();
            $table->foreignId('default_form_id')->nullable()->after('default_ticket_group_id')
                ->constrained('forms')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('ticket_categories', function (Blueprint $table) {
            $table->dropConstrainedForeignId('default_form_id');
            $table->dropConstrainedForeignId('default_ticket_group_id');
        });
    }
};
