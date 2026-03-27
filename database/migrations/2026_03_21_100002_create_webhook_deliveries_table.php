<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('webhook_deliveries', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('webhook_endpoint_id');
            $table->uuid('event_id');
            $table->string('event_type', 80);
            $table->json('payload');
            $table->string('status', 20)->default('pending');
            $table->smallInteger('http_status_code')->nullable();
            $table->text('response_body')->nullable();
            $table->string('error_message', 1000)->nullable();
            $table->tinyInteger('attempt')->default(0);
            $table->timestamp('next_retry_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamps();

            $table->foreign('webhook_endpoint_id')
                ->references('id')
                ->on('webhook_endpoints')
                ->cascadeOnDelete();

            $table->index(['webhook_endpoint_id', 'status']);
            $table->index('event_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('webhook_deliveries');
    }
};
