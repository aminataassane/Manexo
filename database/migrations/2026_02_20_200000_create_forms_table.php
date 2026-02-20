<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('forms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->string('name', 120);
            $table->string('slug', 140)->nullable();
            $table->text('description')->nullable();
            $table->string('status', 20)->default('draft'); // draft, published, archived
            $table->boolean('is_public')->default(false);
            $table->string('public_title', 160)->nullable();
            $table->text('public_description')->nullable();
            $table->text('public_thank_you')->nullable();
            $table->foreignId('ticket_category_id')->nullable()->constrained('ticket_categories')->nullOnDelete();
            $table->foreignId('target_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->unsignedInteger('current_version')->default(1);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['organization_id', 'status']);
            $table->unique('slug');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('forms');
    }
};
