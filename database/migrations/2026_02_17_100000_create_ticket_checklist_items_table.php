<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ticket_checklist_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ticket_id')->constrained('tickets')->cascadeOnDelete();
            $table->string('title');
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->date('due_date')->nullable();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->boolean('is_done')->default(false);
            $table->timestamp('done_at')->nullable();
            $table->foreignId('done_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('status', 20)->default('todo'); // todo, in_progress, blocked, done
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::table('ticket_checklist_items', function (Blueprint $table) {
            $table->index(['ticket_id', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ticket_checklist_items');
    }
};
