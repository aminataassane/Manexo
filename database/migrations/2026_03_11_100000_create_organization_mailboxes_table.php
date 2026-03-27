<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('organization_mailboxes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('email', 255);
            $table->string('display_name', 120)->nullable();
            $table->string('imap_host', 255);
            $table->unsignedSmallInteger('imap_port')->default(993);
            $table->string('imap_username', 255);
            $table->text('imap_password'); // encrypted via model cast
            $table->string('imap_encryption', 10)->default('ssl'); // ssl, tls, none
            $table->string('imap_folder', 120)->default('INBOX');
            $table->foreignId('default_category_id')->nullable()->constrained('ticket_categories')->nullOnDelete();
            $table->foreignId('default_priority_id')->nullable()->constrained('ticket_priorities')->nullOnDelete();
            $table->foreignId('default_group_id')->nullable()->constrained('ticket_groups')->nullOnDelete();
            $table->boolean('is_active')->default(false);
            $table->timestamp('last_fetched_at')->nullable();
            $table->unsignedInteger('last_fetched_uid')->nullable();
            $table->timestamp('last_error_at')->nullable();
            $table->text('last_error_message')->nullable();
            $table->timestamps();

            $table->index(['is_active', 'last_fetched_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('organization_mailboxes');
    }
};
