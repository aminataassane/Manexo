<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ticket_checklist_items', function (Blueprint $table) {
            $table->foreignId('assigned_to_function_id')
                ->nullable()
                ->after('assigned_to')
                ->constrained('organization_functions')
                ->nullOnDelete();
        });

        Schema::create('ticket_checklist_item_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ticket_checklist_item_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('action', 20); // 'done', 'undone', 'claimed'
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ticket_checklist_item_logs');

        Schema::table('ticket_checklist_items', function (Blueprint $table) {
            $table->dropConstrainedForeignId('assigned_to_function_id');
        });
    }
};
