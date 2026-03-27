<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inbound_email_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_mailbox_id')->constrained('organization_mailboxes')->cascadeOnDelete();
            $table->unsignedInteger('imap_uid')->nullable();
            $table->string('message_id', 512)->nullable();
            $table->string('from_email', 255);
            $table->string('from_name', 255)->nullable();
            $table->string('subject', 500)->nullable();
            $table->string('status', 30); // processed, skipped_loop, skipped_rate_limit, failed
            $table->foreignId('ticket_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('ticket_message_id')->nullable()->constrained('ticket_messages')->nullOnDelete();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['organization_mailbox_id', 'created_at']);
            $table->index('from_email');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inbound_email_logs');
    }
};
