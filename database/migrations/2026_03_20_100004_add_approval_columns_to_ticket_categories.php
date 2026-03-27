<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ticket_categories', function (Blueprint $table) {
            $table->boolean('requires_approval')->default(false);
            $table->string('approval_type', 20)->nullable();
            $table->foreignId('approval_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('approval_role', 40)->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('ticket_categories', function (Blueprint $table) {
            $table->dropConstrainedForeignId('approval_user_id');
            $table->dropColumn([
                'requires_approval',
                'approval_type',
                'approval_role',
            ]);
        });
    }
};
