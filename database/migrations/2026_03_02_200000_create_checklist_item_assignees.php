<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ticket_checklist_item_assignees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('checklist_item_id')
                ->constrained('ticket_checklist_items')
                ->cascadeOnDelete();
            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();
            $table->string('role', 20)->default('collaborator');
            $table->foreignId('assigned_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();
            $table->timestamps();
            $table->unique(['checklist_item_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ticket_checklist_item_assignees');
    }
};
