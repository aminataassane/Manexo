<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ticket_form_templates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->foreignId('ticket_category_id')->nullable()->constrained('ticket_categories')->nullOnDelete();
            $table->string('name', 120);
            $table->string('request_type', 120)->nullable();
            // Optional: show this form only to one person (client)
            $table->foreignId('target_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['organization_id', 'ticket_category_id', 'target_user_id'], 'tft_org_cat_target_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ticket_form_templates');
    }
};

