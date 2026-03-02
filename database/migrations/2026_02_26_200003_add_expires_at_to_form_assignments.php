<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('form_assignments', function (Blueprint $table) {
            $table->timestamp('expires_at')->nullable()->after('due_date');
            $table->index(['expires_at', 'status'], 'idx_form_assignments_expires_at');
        });
    }

    public function down(): void
    {
        Schema::table('form_assignments', function (Blueprint $table) {
            $table->dropIndex('idx_form_assignments_expires_at');
            $table->dropColumn('expires_at');
        });
    }
};
