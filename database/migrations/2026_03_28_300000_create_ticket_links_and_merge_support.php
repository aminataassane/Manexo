<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Ticket links: relate tickets to each other (duplicate, related, parent/child)
        Schema::create('ticket_links', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ticket_id')->constrained('tickets')->cascadeOnDelete();
            $table->foreignId('linked_ticket_id')->constrained('tickets')->cascadeOnDelete();
            $table->string('link_type', 30)->default('related'); // related, duplicate, merged_into
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['ticket_id', 'linked_ticket_id']);
            $table->index('linked_ticket_id');
        });

        // Merge tracking on the ticket itself
        Schema::table('tickets', function (Blueprint $table) {
            $table->foreignId('merged_into_ticket_id')->nullable()->after('closed_at')->constrained('tickets')->nullOnDelete();
            $table->timestamp('merged_at')->nullable()->after('merged_into_ticket_id');
            $table->foreignId('merged_by')->nullable()->after('merged_at')->constrained('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->dropConstrainedForeignId('merged_into_ticket_id');
            $table->dropColumn(['merged_at', 'merged_by']);
        });

        Schema::dropIfExists('ticket_links');
    }
};
