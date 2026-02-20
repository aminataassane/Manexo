<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('form_responses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('form_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('assignment_id')->nullable()->constrained('form_assignments')->nullOnDelete();
            $table->unsignedInteger('form_version');
            $table->jsonb('responses');
            $table->jsonb('field_snapshot')->nullable();
            $table->foreignId('ticket_id')->nullable()->constrained()->nullOnDelete();
            $table->string('ip_address', 45)->nullable();
            $table->timestamps();

            $table->index('form_id');
            $table->index('assignment_id');
            $table->index('ticket_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('form_responses');
    }
};
