<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_email_verification_otps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete()->unique();
            $table->string('code_hash');
            $table->timestamp('expires_at');
            $table->unsignedInteger('attempts')->default(0);
            $table->timestamp('last_sent_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'expires_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_email_verification_otps');
    }
};
