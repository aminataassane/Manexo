<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('organization_role_permissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->string('role', 20);
            $table->string('permission', 60);
            $table->timestamps();

            $table->unique(['organization_id', 'role', 'permission'], 'org_role_perm_unique');
            $table->index(['organization_id', 'role'], 'org_role_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('organization_role_permissions');
    }
};
