<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->boolean('requires_approval')->default(false);
            $table->string('approval_status', 20)->nullable();
            $table->string('approval_policy_approver_type', 20)->nullable();
            $table->unsignedBigInteger('approval_policy_approver_id')->nullable();

            $table->index(['organization_id', 'approval_status']);
        });
    }

    public function down(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->dropIndex(['organization_id', 'approval_status']);
            $table->dropColumn([
                'requires_approval',
                'approval_status',
                'approval_policy_approver_type',
                'approval_policy_approver_id',
            ]);
        });
    }
};
