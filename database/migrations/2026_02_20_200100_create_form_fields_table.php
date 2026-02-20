<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('form_fields', function (Blueprint $table) {
            $table->id();
            $table->foreignId('form_id')->constrained()->cascadeOnDelete();
            $table->string('type', 32); // text, textarea, select, checkbox, date, number, email, radio, datetime, file, section
            $table->string('label', 120);
            $table->string('key', 64);
            $table->boolean('required')->default(false);
            $table->unsignedInteger('sort_order')->default(0);
            $table->jsonb('configuration')->default('{}');
            $table->unsignedInteger('form_version')->default(1);
            $table->timestamps();

            $table->unique(['form_id', 'key']);
            $table->index(['form_id', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('form_fields');
    }
};
