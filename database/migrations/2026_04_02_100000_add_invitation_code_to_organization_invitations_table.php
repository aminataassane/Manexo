<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('organization_invitations', function (Blueprint $table) {
            if (Schema::hasColumn('organization_invitations', 'email')) {
                $table->string('email', 255)->nullable()->change();
            }
            if (! Schema::hasColumn('organization_invitations', 'invitation_code')) {
                $table->string('invitation_code', 16)->nullable()->unique()->after('token');
            }
        });
    }

    public function down(): void
    {
        Schema::table('organization_invitations', function (Blueprint $table) {
            if (Schema::hasColumn('organization_invitations', 'invitation_code')) {
                $table->dropUnique(['invitation_code']);
                $table->dropColumn('invitation_code');
            }
            if (Schema::hasColumn('organization_invitations', 'email')) {
                $table->string('email', 255)->nullable(false)->change();
            }
        });
    }
};
