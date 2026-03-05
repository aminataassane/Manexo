<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('support_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->text('reason');
            $table->smallInteger('duration_minutes')->unsigned();
            $table->timestamp('started_at');
            $table->timestamp('expires_at');
            $table->timestamp('ended_at')->nullable();
            $table->string('ip_address', 45);
            $table->timestamps();

            $table->index(['user_id', 'ended_at']);
            $table->index('organization_id');
            $table->index('expires_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('support_sessions');
    }
};
