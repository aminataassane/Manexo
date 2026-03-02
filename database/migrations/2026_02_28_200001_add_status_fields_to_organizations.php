<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('organizations', function (Blueprint $table) {
            if (! Schema::hasColumn('organizations', 'status')) {
                $table->string('status', 20)->default('active');
            }
            if (! Schema::hasColumn('organizations', 'suspended_at')) {
                $table->timestamp('suspended_at')->nullable();
            }
            if (! Schema::hasColumn('organizations', 'suspension_reason')) {
                $table->text('suspension_reason')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('organizations', function (Blueprint $table) {
            $table->dropColumn(['status', 'suspended_at', 'suspension_reason']);
        });
    }
};
