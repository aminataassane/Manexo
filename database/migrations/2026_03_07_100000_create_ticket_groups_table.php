<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ticket_groups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->string('name', 120);
            $table->string('slug', 120);
            $table->string('color', 7)->nullable(); // hex color e.g. #3B82F6
            $table->string('icon', 80)->nullable(); // iconify icon name
            $table->boolean('is_active')->default(true);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();

            $table->unique(['organization_id', 'slug']);
            $table->index(['organization_id', 'is_active', 'sort_order']);
        });

        Schema::table('tickets', function (Blueprint $table) {
            $table->foreignId('ticket_group_id')->nullable()->after('ticket_priority_id')
                ->constrained('ticket_groups')->nullOnDelete();
            $table->index(['organization_id', 'ticket_group_id']);
        });
    }

    public function down(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->dropConstrainedForeignId('ticket_group_id');
        });

        Schema::dropIfExists('ticket_groups');
    }
};
