<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('discussion_threads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->string('name')->nullable();
            $table->boolean('is_group')->default(false);
            $table->timestamp('archived_at')->nullable()->index();
            $table->timestamps();

            $table->index(['organization_id', 'is_group']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('discussion_threads');
    }
};

