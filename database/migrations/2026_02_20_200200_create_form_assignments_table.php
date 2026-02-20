<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('form_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('form_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('organization_function_id')->nullable()->constrained('organization_functions')->nullOnDelete();
            $table->foreignId('assigned_by')->constrained('users')->cascadeOnDelete();
            $table->string('status', 20)->default('pending'); // pending, submitted, overdue
            $table->date('due_date')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->unsignedInteger('form_version')->default(1);
            $table->timestamps();

            $table->index(['form_id', 'status']);
            $table->index(['user_id', 'status']);
            $table->index(['due_date', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('form_assignments');
    }
};
