<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('organization_memberships', function (Blueprint $table) {
            $table->string('role', 40)->default('member')->change();
        });

        Schema::table('organization_role_permissions', function (Blueprint $table) {
            $table->string('role', 40)->change();
        });
    }

    public function down(): void
    {
        Schema::table('organization_memberships', function (Blueprint $table) {
            $table->string('role', 20)->default('member')->change();
        });

        Schema::table('organization_role_permissions', function (Blueprint $table) {
            $table->string('role', 20)->change();
        });
    }
};
