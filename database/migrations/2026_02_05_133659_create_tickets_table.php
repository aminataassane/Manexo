<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();

            $table->foreignId('ticket_category_id')->constrained()->cascadeOnDelete();
            $table->foreignId('ticket_priority_id')->constrained()->cascadeOnDelete();

            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();

            // open | in_progress | pending | resolved | closed
            $table->string('status', 20)->default('open');

            $table->string('subject');
            $table->text('description');

            $table->timestamps();

            $table->index(['organization_id', 'status']);
            $table->index(['organization_id', 'created_by']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
