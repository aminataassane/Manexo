<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ticket_form_fields', function (Blueprint $table) {
            $table->id();
            $table->foreignId('template_id')->constrained('ticket_form_templates')->cascadeOnDelete();
            $table->string('key', 64);
            $table->string('label', 120);
            $table->string('type', 32); // text, textarea, select, checkbox, date, number, email
            $table->boolean('required')->default(false);
            $table->json('options')->nullable(); // for select/radio
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->unique(['template_id', 'key'], 'tff_template_key_unique');
            $table->index(['template_id', 'sort_order'], 'tff_template_sort_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ticket_form_fields');
    }
};

